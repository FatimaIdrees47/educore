<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\TimetableService;

class TimetableController extends Controller
{
    public function __construct(protected TimetableService $timetableService) {}

    public function index()
    {
        $student    = auth()->user()->student;
        $schoolId   = auth()->user()->school_id;
        $enrollment = $student?->currentEnrollment()->with('section')->first();

        $timetable = [];
        $section   = null;

        if ($enrollment) {
            $section   = $enrollment->section;
            $timetable = $this->timetableService->getSectionTimetable(
                $section->id, $schoolId
            );
        }

        return view('student.timetable', compact('timetable', 'section'));
    }
}