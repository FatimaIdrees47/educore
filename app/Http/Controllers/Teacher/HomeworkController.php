<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        $homeworks = Homework::where('teacher_id', $user->id)
            ->where('school_id', $schoolId)
            ->with(['section.schoolClass', 'subject', 'submissions'])
            ->latest()
            ->paginate(15);

        $classes = SchoolClass::forSchool($schoolId)
            ->with('sections')
            ->orderBy('numeric_order')
            ->get();

        $subjects = Subject::forSchool($schoolId)->orderBy('name')->get();

        return view('teacher.homework', compact('homeworks', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'required|exists:subjects,id',
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'due_date'    => 'required|date|after:today',
            'total_marks' => 'required|integer|min:1',
        ]);

        Homework::create([
            'school_id'   => auth()->user()->school_id,
            'teacher_id'  => auth()->id(),
            'section_id'  => $request->section_id,
            'subject_id'  => $request->subject_id,
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date,
            'total_marks' => $request->total_marks,
        ]);

        return back()->with('success', 'Homework assigned successfully.');
    }

    public function show(Homework $homework)
    {
        $this->authorizeTeacher($homework);

        $homework->load(['section.schoolClass', 'subject']);

        $submissions = HomeworkSubmission::where('homework_id', $homework->id)
            ->with('student.user')
            ->get();

        // Get all enrolled students for this section
        $enrollments = \App\Models\Enrollment::where('section_id', $homework->section_id)
            ->where('status', 'active')
            ->with('student.user')
            ->get();

        return view('teacher.homework-detail', compact(
            'homework', 'submissions', 'enrollments'
        ));
    }

    public function grade(Request $request, Homework $homework, HomeworkSubmission $submission)
    {
        $this->authorizeTeacher($homework);

        $request->validate([
            'marks_obtained' => 'required|integer|min:0|max:' . $homework->total_marks,
            'feedback'       => 'nullable|string|max:500',
        ]);

        $submission->update([
            'marks_obtained' => $request->marks_obtained,
            'feedback'       => $request->feedback,
            'graded_at'      => now(),
        ]);

        return back()->with('success', 'Submission graded.');
    }

    public function destroy(Homework $homework)
    {
        $this->authorizeTeacher($homework);
        $homework->delete();
        return back()->with('success', 'Homework deleted.');
    }

    private function authorizeTeacher(Homework $homework): void
    {
        if ($homework->teacher_id !== auth()->id()) abort(403);
    }
}