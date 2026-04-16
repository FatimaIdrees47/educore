<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class ClassService
{
    public function createClass(array $data): SchoolClass
    {
        return SchoolClass::create($data);
    }

    public function updateClass(SchoolClass $class, array $data): SchoolClass
    {
        $class->update($data);
        return $class;
    }

    public function deleteClass(SchoolClass $class): void
    {
        // Prevent deletion if sections have students enrolled
        if ($class->sections()->whereHas('enrollments')->exists()) {
            throw new \Exception('Cannot delete class with enrolled students.');
        }
        $class->delete();
    }

    public function createSection(array $data): Section
    {
        return Section::create($data);
    }

    public function updateSection(Section $section, array $data): Section
    {
        $section->update($data);
        return $section;
    }

    public function deleteSection(Section $section): void
    {
        $section->delete();
    }

    public function createSubject(array $data): Subject
    {
        return Subject::create($data);
    }

    public function getClassesWithSections(int $schoolId)
    {
        return SchoolClass::forSchool($schoolId)
            ->with(['sections.classTeacher'])
            ->orderBy('numeric_order')
            ->orderBy('name')
            ->get();
    }

    public function getSubjects(int $schoolId)
    {
        return Subject::forSchool($schoolId)
            ->orderBy('name')
            ->get();
    }
}