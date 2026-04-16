<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\FeeInvoice;
use App\Models\Notice;
use App\Models\ReportCard;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            return view('student.dashboard', ['student' => null]);
        }

        $schoolId = $user->school_id;

        // Current enrollment
        $enrollment = $student->currentEnrollment()->with([
            'section.schoolClass',
            'academicYear',
        ])->first();

        // Attendance summary this month
        $attendanceThisMonth = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $totalDays    = $attendanceThisMonth->count();
        $presentDays  = $attendanceThisMonth->where('status', 'present')->count();
        $attendancePct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // Pending fees
        $pendingFees = FeeInvoice::where('student_id', $student->id)
            ->where('status', 'unpaid')
            ->sum('net_amount');

        // Latest result
        $latestResult = ReportCard::where('student_id', $student->id)
            ->whereNotNull('published_at')
            ->latest()
            ->first();

        // Recent notices
        $notices = Notice::forSchool($schoolId)
            ->active()
            ->where(function ($q) use ($enrollment) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', 'student');
            })
            ->latest()
            ->take(5)
            ->get();

        // Recent attendance
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->latest('date')
            ->take(7)
            ->get();

        return view('student.dashboard', compact(
            'student', 'enrollment', 'attendancePct',
            'presentDays', 'totalDays', 'pendingFees',
            'latestResult', 'notices', 'recentAttendance'
        ));
    }
}