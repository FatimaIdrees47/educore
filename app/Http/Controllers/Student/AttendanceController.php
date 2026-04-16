<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student;

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $records = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $late    = $records->where('status', 'late')->count();
        $percentage = $total > 0 ? round(($present / $total) * 100) : 0;

        $months = collect(range(1, 12))->map(fn($m) => [
            'value' => $m,
            'label' => \Carbon\Carbon::createFromDate(null, $m, 1)->format('F'),
        ]);

        return view('student.attendance', compact(
            'records', 'total', 'present', 'absent',
            'late', 'percentage', 'month', 'year', 'months'
        ));
    }
}