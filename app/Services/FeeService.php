<?php

namespace App\Services;

use App\Models\FeeStructure;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class FeeService
{
    // Generate invoices for all students in a class for a given month
    public function generateMonthlyInvoices(
        int $schoolId,
        int $classId,
        int $month,
        int $year
    ): int {
        $activeYear = AcademicYear::where('school_id', $schoolId)
                                  ->where('is_active', true)
                                  ->firstOrFail();

        $structures = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('academic_year_id', $activeYear->id)
            ->where('frequency', 'monthly')
            ->get();

        if ($structures->isEmpty()) return 0;

        // Get all active students in this class
        $enrollments = Enrollment::where('status', 'active')
            ->whereHas('section', fn($q) => $q->where('class_id', $classId))
            ->where('academic_year_id', $activeYear->id)
            ->get();

        $count = 0;

        DB::transaction(function () use (
            $structures, $enrollments, $schoolId, $activeYear, $month, $year, &$count
        ) {
            foreach ($enrollments as $enrollment) {
                foreach ($structures as $structure) {
                    // Skip if already generated
                    $exists = FeeInvoice::where('student_id', $enrollment->student_id)
                        ->where('fee_structure_id', $structure->id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->exists();

                    if ($exists) continue;

                    $dueDate = \Carbon\Carbon::createFromDate($year, $month, $structure->due_day);

                    FeeInvoice::create([
                        'student_id'       => $enrollment->student_id,
                        'school_id'        => $schoolId,
                        'academic_year_id' => $activeYear->id,
                        'fee_structure_id' => $structure->id,
                        'receipt_number'   => $this->generateReceiptNumber($schoolId),
                        'fee_type'         => $structure->fee_type,
                        'month'            => $month,
                        'year'             => $year,
                        'amount'           => $structure->amount,
                        'fine_amount'      => 0,
                        'discount_amount'  => 0,
                        'net_amount'       => $structure->amount,
                        'status'           => 'unpaid',
                        'due_date'         => $dueDate,
                    ]);

                    $count++;
                }
            }
        });

        return $count;
    }

    public function collectPayment(FeeInvoice $invoice, array $data): FeePayment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $payment = FeePayment::create([
                'fee_invoice_id' => $invoice->id,
                'collected_by'   => auth()->id(),
                'amount'         => (int)($data['amount'] * 100),
                'payment_method' => $data['payment_method'],
                'reference'      => $data['reference'] ?? null,
                'paid_at'        => now(),
            ]);

            // Mark invoice as paid
            $invoice->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            return $payment;
        });
    }

    public function generateReceiptNumber(int $schoolId): string
    {
        $count = FeeInvoice::where('school_id', $schoolId)->count() + 1;
        return 'RCP-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function getInvoices(int $schoolId, array $filters = [])
    {
        $query = FeeInvoice::forSchool($schoolId)
            ->with(['student.user', 'feeStructure'])
            ->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['month'])) {
            $query->where('month', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->paginate(20);
    }

    public function getDefaulters(int $schoolId)
    {
        return FeeInvoice::forSchool($schoolId)
            ->where('status', 'unpaid')
            ->where('due_date', '<', today())
            ->with(['student.user'])
            ->orderBy('due_date')
            ->get();
    }

    public function getSummary(int $schoolId): array
    {
        $totalInvoiced = FeeInvoice::forSchool($schoolId)->sum('net_amount');
        $totalCollected = FeeInvoice::forSchool($schoolId)
            ->where('status', 'paid')->sum('net_amount');
        $totalPending = FeeInvoice::forSchool($schoolId)
            ->where('status', 'unpaid')->sum('net_amount');
        $defaulters = FeeInvoice::forSchool($schoolId)
            ->where('status', 'unpaid')
            ->where('due_date', '<', today())
            ->count();

        return [
            'total_invoiced'  => $totalInvoiced,
            'total_collected' => $totalCollected,
            'total_pending'   => $totalPending,
            'defaulters'      => $defaulters,
        ];
    }
}