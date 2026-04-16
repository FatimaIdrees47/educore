<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;

class AttendanceController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        // Teacher sees all classes — not just their assigned ones
        $classes = SchoolClass::forSchool($schoolId)
            ->with('sections')
            ->orderBy('numeric_order')
            ->get();

        return view('teacher.attendance', compact('classes'));
    }
}