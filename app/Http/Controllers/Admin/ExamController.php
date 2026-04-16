<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Services\ExamService;
use Illuminate\Http\Request;
use App\Models\ReportCard;
use App\Models\ExamMark;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamController extends Controller
{
    public function __construct(protected ExamService $examService) {}

    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $exams = Exam::forSchool($schoolId)
            ->with('academicYear')
            ->latest()
            ->get();

        return view('admin.exams.index', compact('exams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $this->examService->createExam($request->all(), auth()->user()->school_id);

        return back()->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);

        $schoolId = auth()->user()->school_id;

        $exam->load(['examSubjects.subject', 'examSubjects.schoolClass']);

        $classes  = SchoolClass::forSchool($schoolId)->orderBy('numeric_order')->get();
        $subjects = Subject::forSchool($schoolId)->orderBy('name')->get();

        return view('admin.exams.show', compact('exam', 'classes', 'subjects'));
    }

    public function addSubject(Request $request, Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);

        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'class_id'      => 'required|exists:classes,id',
            'exam_date'     => 'nullable|date',
            'full_marks'    => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1',
        ]);

        $this->examService->addSubjectToExam([
            'exam_id'       => $exam->id,
            'subject_id'    => $request->subject_id,
            'class_id'      => $request->class_id,
            'exam_date'     => $request->exam_date,
            'full_marks'    => $request->full_marks,
            'passing_marks' => $request->passing_marks,
        ]);

        return back()->with('success', 'Subject added to exam.');
    }

    public function enterMarks(Exam $exam, ExamSubject $examSubject)
    {
        $this->authorizeSchool($exam->school_id);

        $examSubject->load(['subject', 'schoolClass', 'marks']);

        $activeYear = AcademicYear::where('school_id', $exam->school_id)
            ->where('is_active', true)->first();

        $enrollments = Enrollment::where('status', 'active')
            ->where('academic_year_id', $activeYear->id)
            ->whereHas('section', fn($q) => $q->where('class_id', $examSubject->class_id))
            ->with('student.user')
            ->get();

        $existingMarks = $examSubject->marks->keyBy('student_id');

        return view('admin.exams.marks', compact(
            'exam',
            'examSubject',
            'enrollments',
            'existingMarks'
        ));
    }

    public function saveMarks(Request $request, Exam $exam, ExamSubject $examSubject)
    {
        $this->authorizeSchool($exam->school_id);

        $this->examService->saveMarks($examSubject->id, $request->marks ?? []);

        return back()->with('success', 'Marks saved successfully.');
    }

    public function generateReportCards(Request $request, Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $count = $this->examService->generateReportCards($exam, $request->class_id);

        return back()->with('success', "$count report cards generated.");
    }

    public function publishResults(Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);
        $this->examService->publishResults($exam);
        return back()->with('success', 'Results published successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);
        $exam->delete();
        return back()->with('success', 'Exam deleted.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) abort(403);
    }

    public function markCompleted(Exam $exam)
    {
        $this->authorizeSchool($exam->school_id);
        $exam->update(['status' => 'completed']);
        return back()->with('success', 'Exam marked as completed.');
    }

    public function downloadReportCard(Exam $exam, Student $student)
    {
        $this->authorizeSchool($exam->school_id);

        $reportCard = ReportCard::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $marks = ExamMark::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->with('examSubject.subject')
            ->get();

        $enrollment = $student->currentEnrollment()
            ->with('section.schoolClass', 'academicYear')
            ->first();

        $school = auth()->user()->school;

        $pdf = Pdf::loadView('pdf.report-card', compact(
            'reportCard',
            'marks',
            'student',
            'enrollment',
            'exam',
            'school'
        ))->setPaper('a4', 'portrait');

        return $pdf->download(
            $student->user->name . '_' . $exam->name . '_ReportCard.pdf'
        );
    }
}
