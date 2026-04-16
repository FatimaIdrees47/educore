<?php

namespace App\Http\Controllers\EduParent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user      = auth()->user();
        $children  = $user->children()->with('currentEnrollment.section.schoolClass')->get();

        $selectedStudentId = $request->get('student_id',
            session('selected_child_id', $children->first()?->id));
        $selectedStudent   = $children->firstWhere('id', $selectedStudentId) ?? $children->first();

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $records = $selectedStudent
            ? Attendance::where('student_id', $selectedStudent->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date')
                ->get()
            : collect();

        $total      = $records->count();
        $present    = $records->where('status', 'present')->count();
        $absent     = $records->where('status', 'absent')->count();
        $percentage = $total > 0 ? round(($present / $total) * 100) : 0;

        $months = collect(range(1, 12))->map(fn($m) => [
            'value' => $m,
            'label' => \Carbon\Carbon::createFromDate(null, $m, 1)->format('F'),
        ]);

        return view('parent.attendance', compact(
            'children', 'selectedStudent', 'records',
            'total', 'present', 'absent', 'percentage',
            'month', 'year', 'months'
        ));
    }
}