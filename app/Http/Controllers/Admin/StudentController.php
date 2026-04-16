<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(protected StudentService $studentService) {}

    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $students = $this->studentService->getStudents($schoolId);
        $classes  = SchoolClass::forSchool($schoolId)
                        ->with('sections')
                        ->orderBy('numeric_order')
                        ->get();

        $nextAdmissionNumber = $this->studentService->generateAdmissionNumber($schoolId);

        return view('admin.students.index', compact(
            'students', 'classes', 'nextAdmissionNumber'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'admission_number' => 'required|string|unique:students,admission_number',
            'admission_date'   => 'required|date',
            'gender'           => 'required|in:male,female,other',
            'section_id'       => 'nullable|exists:sections,id',
            'date_of_birth'    => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'blood_group'      => 'nullable|string|max:5',
            'religion'         => 'nullable|string|max:50',
            'address'          => 'nullable|string',
            'roll_number'      => 'nullable|string|max:20',
        ]);

        $this->studentService->enroll($request->all(), auth()->user()->school_id);

        return back()->with('success', 'Student enrolled successfully.');
    }

    public function show(Student $student)
    {
        $this->authorizeSchool($student->school_id);

        $student->load([
            'user',
            'enrollments.section.schoolClass',
            'enrollments.academicYear',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function destroy(Student $student)
    {
        $this->authorizeSchool($student->school_id);
        $student->delete();
        return back()->with('success', 'Student removed.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) {
            abort(403);
        }
    }
}