<?php

namespace App\Http\Controllers\EduParent;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\ExamMark;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $children = $user->children()->get();

        $selectedStudentId = $request->get(
            'student_id',
            session('selected_child_id', $children->first()?->id)
        );
        $selectedStudent   = $children->firstWhere('id', $selectedStudentId) ?? $children->first();

        $reportCards = $selectedStudent
            ? ReportCard::where('student_id', $selectedStudent->id)
            ->whereNotNull('published_at')
            ->with('exam')
            ->latest()
            ->get()
            : collect();

        return view('parent.results', compact(
            'children',
            'selectedStudent',
            'reportCards'
        ));
    }

    public function show(Request $request, ReportCard $reportCard)
    {
        $user     = auth()->user();
        $children = $user->children()->pluck('students.id');

        if (!$children->contains($reportCard->student_id)) {
            abort(403);
        }

        $reportCard->load('exam', 'student.user');

        $marks = ExamMark::where('exam_id', $reportCard->exam_id)
            ->where('student_id', $reportCard->student_id)
            ->with('examSubject.subject')
            ->get();

        return view('parent.result-detail', compact('reportCard', 'marks'));
    }
}
