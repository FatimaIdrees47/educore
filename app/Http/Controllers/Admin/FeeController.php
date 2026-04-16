<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\FeeInvoice;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Services\FeeService;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function __construct(protected FeeService $feeService) {}

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $summary    = $this->feeService->getSummary($schoolId);
        $invoices   = $this->feeService->getInvoices($schoolId, $request->only('status', 'month', 'year'));
        $defaulters = $this->feeService->getDefaulters($schoolId);

        $classes = SchoolClass::forSchool($schoolId)
            ->orderBy('numeric_order')->get();

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)->first();

        $structures = FeeStructure::forSchool($schoolId)
            ->with('schoolClass')
            ->get();

        return view('admin.fees.index', compact(
            'summary', 'invoices', 'defaulters',
            'classes', 'activeYear', 'structures'
        ));
    }

    public function storeStructure(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'fee_type'  => 'required|string|max:100',
            'amount'    => 'required|numeric|min:1',
            'frequency' => 'required|in:monthly,quarterly,yearly,one-time',
            'due_day'   => 'required|integer|min:1|max:28',
        ]);

        $schoolId   = auth()->user()->school_id;
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)->firstOrFail();

        FeeStructure::create([
            'school_id'        => $schoolId,
            'class_id'         => $request->class_id,
            'academic_year_id' => $activeYear->id,
            'fee_type'         => $request->fee_type,
            'amount'           => (int)($request->amount * 100), // store in paisas
            'frequency'        => $request->frequency,
            'due_day'          => $request->due_day,
        ]);

        return back()->with('success', 'Fee structure created successfully.');
    }

    public function destroyStructure(FeeStructure $feeStructure)
    {
        if ($feeStructure->school_id !== auth()->user()->school_id) abort(403);
        $feeStructure->delete();
        return back()->with('success', 'Fee structure deleted.');
    }

    public function generateInvoices(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'month'    => 'required|integer|min:1|max:12',
            'year'     => 'required|integer|min:2020|max:2030',
        ]);

        $count = $this->feeService->generateMonthlyInvoices(
            schoolId: auth()->user()->school_id,
            classId:  $request->class_id,
            month:    $request->month,
            year:     $request->year,
        );

        return back()->with('success', "$count invoices generated successfully.");
    }

    public function collectPayment(Request $request, FeeInvoice $feeInvoice)
    {
        if ($feeInvoice->school_id !== auth()->user()->school_id) abort(403);

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
            'reference'      => 'nullable|string|max:100',
        ]);

        $this->feeService->collectPayment($feeInvoice, $request->all());

        return back()->with('success', 'Payment collected successfully.');
    }
}