<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function markBulk(
        int $sectionId,
        string $date,
        array $records,
        int $markedBy,
        int $schoolId
    ): void {
        $activeYear = AcademicYear::where('school_id', $schoolId)
                                  ->where('is_active', true)
                                  ->firstOrFail();

        DB::transaction(function () use ($sectionId, $date, $records, $markedBy, $activeYear) {
            foreach ($records as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date'       => $date,
                    ],
                    [
                        'section_id'       => $sectionId,
                        'academic_year_id' => $activeYear->id,
                        'marked_by'        => $markedBy,
                        'status'           => $data['status'],
                        'remarks'          => $data['remarks'] ?? null,
                    ]
                );
            }
        });
    }

    public function getAttendanceForSection(int $sectionId, string $date): array
    {
        return Attendance::where('section_id', $sectionId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id')
            ->toArray();
    }

    public function getMonthlyReport(int $studentId, int $month, int $year): array
    {
        $records = Attendance::where('student_id', $studentId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();

        return [
            'records'     => $records,
            'total'       => $total,
            'present'     => $present,
            'absent'      => $absent,
            'late'        => $late,
            'percentage'  => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    public function isAlreadyMarked(int $sectionId, string $date): bool
    {
        return Attendance::where('section_id', $sectionId)
                         ->where('date', $date)
                         ->exists();
    }
}