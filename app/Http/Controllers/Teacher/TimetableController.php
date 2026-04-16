<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TimetableService;

class TimetableController extends Controller
{
    public function __construct(protected TimetableService $timetableService) {}

    public function index()
    {
        $schoolId  = auth()->user()->school_id;
        $timetable = $this->timetableService->getTeacherTimetable(
            auth()->id(), $schoolId
        );

        return view('teacher.timetable', compact('timetable'));
    }
}