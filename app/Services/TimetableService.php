<?php

namespace App\Services;

use App\Models\TimetableSlot;
use App\Models\AcademicYear;

class TimetableService
{
    public function createSlot(array $data, int $schoolId): TimetableSlot
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
                                  ->where('is_active', true)
                                  ->firstOrFail();

        // Check teacher conflict
        $teacherConflict = TimetableSlot::where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('academic_year_id', $activeYear->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function ($q) use ($data) {
                      $q->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>=', $data['end_time']);
                  });
            })->exists();

        if ($teacherConflict) {
            throw new \Exception('Teacher already has a class at this time slot.');
        }

        // Check section conflict
        $sectionConflict = TimetableSlot::where('section_id', $data['section_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('academic_year_id', $activeYear->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function ($q) use ($data) {
                      $q->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>=', $data['end_time']);
                  });
            })->exists();

        if ($sectionConflict) {
            throw new \Exception('This section already has a class at this time slot.');
        }

        return TimetableSlot::create([
            'school_id'        => $schoolId,
            'section_id'       => $data['section_id'],
            'subject_id'       => $data['subject_id'],
            'teacher_id'       => $data['teacher_id'],
            'academic_year_id' => $activeYear->id,
            'day_of_week'      => $data['day_of_week'],
            'start_time'       => $data['start_time'],
            'end_time'         => $data['end_time'],
            'room'             => $data['room'] ?? null,
        ]);
    }

    public function getSectionTimetable(int $sectionId, int $schoolId): array
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
                                  ->where('is_active', true)->first();

        if (!$activeYear) return [];

        $slots = TimetableSlot::where('section_id', $sectionId)
            ->where('academic_year_id', $activeYear->id)
            ->with(['subject', 'teacher'])
            ->orderBy('start_time')
            ->get();

        $timetable = [];
        foreach (TimetableSlot::daysOfWeek() as $day) {
            $timetable[$day] = $slots->where('day_of_week', $day)->values();
        }

        return $timetable;
    }

    public function getTeacherTimetable(int $teacherId, int $schoolId): array
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
                                  ->where('is_active', true)->first();

        if (!$activeYear) return [];

        $slots = TimetableSlot::where('teacher_id', $teacherId)
            ->where('academic_year_id', $activeYear->id)
            ->with(['subject', 'section.schoolClass'])
            ->orderBy('start_time')
            ->get();

        $timetable = [];
        foreach (TimetableSlot::daysOfWeek() as $day) {
            $timetable[$day] = $slots->where('day_of_week', $day)->values();
        }

        return $timetable;
    }
}