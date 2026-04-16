<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function index()
    {
        $student    = auth()->user()->student;
        $enrollment = $student?->currentEnrollment()->first();

        if (!$enrollment) {
            return view('student.homework', ['homeworks' => collect()]);
        }

        $homeworks = Homework::where('section_id', $enrollment->section_id)
            ->with(['subject', 'submissions' => fn($q) => $q->where('student_id', $student->id)])
            ->latest()
            ->get();

        return view('student.homework', compact('homeworks', 'student'));
    }

    public function submit(Request $request, Homework $homework)
    {
        $student = auth()->user()->student;

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        HomeworkSubmission::updateOrCreate(
            [
                'homework_id' => $homework->id,
                'student_id'  => $student->id,
            ],
            [
                'notes'        => $request->notes,
                'submitted_at' => now(),
            ]
        );

        return back()->with('success', 'Homework submitted successfully.');
    }
}