<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $totalClasses   = SchoolClass::where('school_id', $schoolId)->count();
        $totalSections  = Section::where('school_id', $schoolId)->count();
        $totalSubjects  = Subject::where('school_id', $schoolId)->count();
        $totalStudents  = Student::where('school_id', $schoolId)->count();
        $totalTeachers  = Teacher::where('school_id', $schoolId)->count();

        $recentStudents = Student::where('school_id', $schoolId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalClasses', 'totalSections', 'totalSubjects',
            'totalStudents', 'totalTeachers', 'recentStudents'
        ));
    }
}