<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\ExamMark;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $reportCards = ReportCard::where('student_id', $student->id)
            ->whereNotNull('published_at')
            ->with('exam')
            ->latest()
            ->get();

        return view('student.results', compact('reportCards'));
    }

    public function show(ReportCard $reportCard)
    {
        if ($reportCard->student_id !== auth()->user()->student->id) {
            abort(403);
        }

        $reportCard->load('exam');

        $marks = ExamMark::where('exam_id', $reportCard->exam_id)
            ->where('student_id', $reportCard->student_id)
            ->with('examSubject.subject')
            ->get();

        return view('student.result-detail', compact('reportCard', 'marks'));
    }

    public function downloadPdf(ReportCard $reportCard)
{
    if ($reportCard->student_id !== auth()->user()->student->id) {
        abort(403);
    }

    $reportCard->load('exam', 'student.user');

    $marks = ExamMark::where('exam_id', $reportCard->exam_id)
        ->where('student_id', $reportCard->student_id)
        ->with('examSubject.subject')
        ->get();

    $student    = $reportCard->student;
    $exam       = $reportCard->exam;
    $enrollment = $student->currentEnrollment()
        ->with('section.schoolClass', 'academicYear')
        ->first();

    $school = \App\Models\School::find(auth()->user()->school_id);

    $pdf = Pdf::loadView('pdf.report-card', compact(
        'reportCard', 'marks', 'student', 'enrollment', 'exam', 'school'
    ))->setPaper('a4', 'portrait');

    return $pdf->download(
        $student->user->name . '_' . $exam->name . '_ReportCard.pdf'
    );
}
}