<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use App\Services\TimetableService;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function __construct(protected TimetableService $timetableService) {}

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $classes = SchoolClass::forSchool($schoolId)
            ->with('sections')
            ->orderBy('numeric_order')
            ->get();

        $selectedSectionId = $request->get('section_id');
        $selectedSection   = null;
        $timetable         = [];

        if ($selectedSectionId) {
            $selectedSection = Section::with('schoolClass')->find($selectedSectionId);
            $timetable = $this->timetableService->getSectionTimetable($selectedSectionId, $schoolId);
        }

        $subjects = Subject::forSchool($schoolId)->orderBy('name')->get();
        $teachers = Teacher::where('school_id', $schoolId)->with('user')->get();

        return view('admin.timetable.index', compact(
            'classes', 'selectedSection', 'timetable',
            'subjects', 'teachers', 'selectedSectionId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id'  => 'required|exists:sections,id',
            'subject_id'  => 'required|exists:subjects,id',
            'teacher_id'  => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'  => 'required',
            'end_time'    => 'required|after:start_time',
            'room'        => 'nullable|string|max:50',
        ]);

        try {
            $this->timetableService->createSlot(
                $request->all(),
                auth()->user()->school_id
            );
            return redirect()->route('admin.timetable.index', [
                'section_id' => $request->section_id
            ])->with('success', 'Timetable slot added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.timetable.index', [
                'section_id' => $request->section_id
            ])->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, TimetableSlot $timetableSlot)
    {
        if ($timetableSlot->school_id !== auth()->user()->school_id) abort(403);
        $sectionId = $timetableSlot->section_id;
        $timetableSlot->delete();
        return redirect()->route('admin.timetable.index', ['section_id' => $sectionId])
                         ->with('success', 'Slot removed.');
    }
}