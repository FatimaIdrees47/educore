<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(protected TeacherService $teacherService) {}

    public function index()
    {
        $schoolId           = auth()->user()->school_id;
        $teachers           = $this->teacherService->getTeachers($schoolId);
        $nextEmployeeId     = $this->teacherService->generateEmployeeId($schoolId);

        return view('admin.teachers.index', compact('teachers', 'nextEmployeeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'employee_id'  => 'nullable|string|max:30',
            'joining_date' => 'nullable|date',
            'salary'       => 'nullable|numeric|min:0',
            'leave_balance'=> 'nullable|integer|min:0|max:365',
            'qualifications' => 'nullable|string|max:500',
        ]);

        $this->teacherService->create($request->all(), auth()->user()->school_id);

        return back()->with('success', 'Teacher added successfully.');
    }

    public function show(Teacher $teacher)
    {
        $this->authorizeSchool($teacher->school_id);
        $teacher->load('user');
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $this->authorizeSchool($teacher->school_id);
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->authorizeSchool($teacher->school_id);

        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'employee_id'    => 'nullable|string|max:30',
            'joining_date'   => 'nullable|date',
            'salary'         => 'nullable|numeric|min:0',
            'leave_balance'  => 'nullable|integer|min:0|max:365',
            'qualifications' => 'nullable|string|max:500',
        ]);

        $this->teacherService->update($teacher, $request->all());

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorizeSchool($teacher->school_id);
        $teacher->delete();
        return back()->with('success', 'Teacher removed.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) {
            abort(403);
        }
    }
}