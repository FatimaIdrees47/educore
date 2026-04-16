<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Services\ClassService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct(protected ClassService $classService) {}

    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $classes  = $this->classService->getClassesWithSections($schoolId);
        $subjects = $this->classService->getSubjects($schoolId);
        $teachers = \App\Models\Teacher::where('school_id', $schoolId)
            ->with('user')
            ->get();

        $totalClasses  = $classes->count();
        $totalSections = $classes->sum(fn($c) => $c->sections->count());
        $totalSubjects = $subjects->count();

        return view('admin.classes.index', compact(
            'classes',
            'subjects',
            'teachers',
            'totalClasses',
            'totalSections',
            'totalSubjects'
        ));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'numeric_order' => 'nullable|integer|min:0',
        ]);

        $this->classService->createClass([
            'school_id'     => auth()->user()->school_id,
            'name'          => $request->name,
            'numeric_order' => $request->numeric_order ?? 0,
        ]);

        return back()->with('success', 'Class created successfully.');
    }

    public function updateClass(Request $request, SchoolClass $class)
    {
        $this->authorizeSchool($class->school_id);

        $request->validate([
            'name'          => 'required|string|max:100',
            'numeric_order' => 'nullable|integer|min:0',
        ]);

        $this->classService->updateClass($class, $request->only('name', 'numeric_order'));

        return back()->with('success', 'Class updated successfully.');
    }

    public function destroyClass(SchoolClass $class)
    {
        $this->authorizeSchool($class->school_id);

        try {
            $this->classService->deleteClass($class);
            return back()->with('success', 'Class deleted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'class_id'         => 'required|exists:classes,id',
            'name'             => 'required|string|max:50',
            'capacity'         => 'nullable|integer|min:1|max:100',
            'class_teacher_id' => 'nullable|exists:users,id',
        ]);

        $this->classService->createSection([
            'school_id'        => auth()->user()->school_id,
            'class_id'         => $request->class_id,
            'name'             => $request->name,
            'capacity'         => $request->capacity ?? 30,
            'class_teacher_id' => $request->class_teacher_id,
        ]);

        return back()->with('success', 'Section created successfully.');
    }

    public function updateSection(Request $request, Section $section)
    {
        $this->authorizeSchool($section->school_id);

        $request->validate([
            'name'             => 'required|string|max:50',
            'capacity'         => 'nullable|integer|min:1|max:100',
            'class_teacher_id' => 'nullable|exists:users,id',
        ]);

        $this->classService->updateSection($section, $request->only(
            'name',
            'capacity',
            'class_teacher_id'
        ));

        return back()->with('success', 'Section updated successfully.');
    }

    public function destroySection(Section $section)
    {
        $this->authorizeSchool($section->school_id);
        $this->classService->deleteSection($section);
        return back()->with('success', 'Section deleted.');
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'type' => 'required|in:core,elective,lab',
        ]);

        $this->classService->createSubject([
            'school_id' => auth()->user()->school_id,
            'name'      => $request->name,
            'code'      => $request->code,
            'type'      => $request->type,
        ]);

        return back()->with('success', 'Subject created successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        $this->authorizeSchool($subject->school_id);
        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) {
            abort(403);
        }
    }
}
