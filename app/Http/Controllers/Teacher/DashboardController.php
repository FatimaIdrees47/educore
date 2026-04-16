<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Notice;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return view('teacher.dashboard', ['teacher' => null]);
        }

        $schoolId = $user->school_id;

        // Sections where this teacher is class teacher
        $mySections = Section::where('class_teacher_id', $user->id)
            ->with('schoolClass')
            ->get();

        // Total students across my sections
        $myStudentCount = Enrollment::whereIn('section_id', $mySections->pluck('id'))
            ->where('status', 'active')
            ->count();

        // Today's attendance marked by this teacher
        $markedToday = Attendance::where('marked_by', $user->id)
            ->whereDate('date', today())
            ->count();

        // Notices for teachers
        $notices = Notice::forSchool($schoolId)
            ->active()
            ->where(function ($q) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', 'teacher');
            })
            ->latest()
            ->take(5)
            ->get();

        // Recent attendance marked by this teacher
        $recentAttendance = Attendance::where('marked_by', $user->id)
            ->with('student.user', 'section.schoolClass')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher', 'mySections', 'myStudentCount',
            'markedToday', 'notices', 'recentAttendance'
        ));
    }
}