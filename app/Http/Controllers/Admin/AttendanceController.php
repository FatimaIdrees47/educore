<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService) {}

    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $classes = SchoolClass::forSchool($schoolId)
            ->with('sections')
            ->orderBy('numeric_order')
            ->get();

        // Today's summary
        $todayTotal   = Attendance::whereDate('date', today())
            ->whereHas('section', fn($q) => $q->where('school_id', $schoolId))
            ->count();
        $todayPresent = Attendance::whereDate('date', today())
            ->where('status', 'present')
            ->whereHas('section', fn($q) => $q->where('school_id', $schoolId))
            ->count();
        $todayAbsent  = Attendance::whereDate('date', today())
            ->where('status', 'absent')
            ->whereHas('section', fn($q) => $q->where('school_id', $schoolId))
            ->count();

        return view('admin.attendance.index', compact(
            'classes', 'todayTotal', 'todayPresent', 'todayAbsent'
        ));
    }
}