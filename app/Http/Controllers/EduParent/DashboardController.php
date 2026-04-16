<?php

namespace App\Http\Controllers\EduParent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use App\Models\Notice;
use App\Models\ReportCard;
use App\Models\StudentParent;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        // Get all children linked to this parent
        $children = $user->children()->with([
            'currentEnrollment.section.schoolClass',
            'currentEnrollment.academicYear',
        ])->get();

        // Selected child (first by default)
        $selectedStudentId = session('selected_child_id') ?? $children->first()?->id;
        $selectedStudent   = $children->firstWhere('id', $selectedStudentId) ?? $children->first();

        if (!$selectedStudent) {
            return view('parent.dashboard', [
                'children'         => collect(),
                'selectedStudent'  => null,
                'attendancePct'    => 0,
                'pendingFees'      => 0,
                'latestResult'     => null,
                'recentAttendance' => collect(),
                'notices'          => collect(),
            ]);
        }

        // Attendance this month
        $attendanceThisMonth = Attendance::where('student_id', $selectedStudent->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $totalDays     = $attendanceThisMonth->count();
        $presentDays   = $attendanceThisMonth->where('status', 'present')->count();
        $attendancePct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // Pending fees
        $pendingFees = FeeInvoice::where('student_id', $selectedStudent->id)
            ->where('status', 'unpaid')
            ->sum('net_amount');

        // Latest result
        $latestResult = ReportCard::where('student_id', $selectedStudent->id)
            ->whereNotNull('published_at')
            ->latest()
            ->first();

        // Recent attendance
        $recentAttendance = Attendance::where('student_id', $selectedStudent->id)
            ->latest('date')
            ->take(7)
            ->get();

        // Notices
        $notices = Notice::forSchool($schoolId)
            ->active()
            ->where(function ($q) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', 'parent');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('parent.dashboard', compact(
            'children', 'selectedStudent', 'attendancePct',
            'pendingFees', 'latestResult', 'recentAttendance', 'notices'
        ));
    }

    public function switchChild($studentId)
    {
        session(['selected_child_id' => $studentId]);
        return redirect()->route('parent.dashboard');
    }
}