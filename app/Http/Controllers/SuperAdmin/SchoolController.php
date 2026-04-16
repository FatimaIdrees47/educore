<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount(['students', 'teachers', 'users'])
            ->latest()
            ->paginate(15);

        $totalSchools   = School::count();
        $activeSchools  = School::where('status', 'active')->count();
        $totalStudents  = \App\Models\Student::count();
        $totalTeachers  = \App\Models\Teacher::count();

        return view('super-admin.schools.index', compact(
            'schools', 'totalSchools', 'activeSchools',
            'totalStudents', 'totalTeachers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:200',
            'email'          => 'nullable|email|max:200',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string|max:500',
            'principal_name' => 'nullable|string|max:200',
            'max_students'   => 'required|integer|min:10',
            'max_teachers'   => 'required|integer|min:1',
            'admin_name'     => 'required|string|max:200',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            // Create school
            $school = School::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'principal_name' => $request->principal_name,
                'max_students'   => $request->max_students,
                'max_teachers'   => $request->max_teachers,
                'status'         => 'active',
            ]);

            // Create default academic year
            AcademicYear::create([
                'school_id'  => $school->id,
                'name'       => date('Y') . '-' . (date('Y') + 1),
                'start_date' => date('Y') . '-04-01',
                'end_date'   => (date('Y') + 1) . '-03-31',
                'is_active'  => true,
            ]);

            // Create school admin user
            $admin = User::create([
                'school_id' => $school->id,
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'password'  => Hash::make($request->admin_password),
                'is_active' => true,
            ]);

            $admin->assignRole('school-admin');
        });

        return back()->with('success', 'School created successfully.');
    }

    public function show(School $school)
    {
        $school->load('users');

        $stats = [
            'students'     => \App\Models\Student::where('school_id', $school->id)->count(),
            'teachers'     => \App\Models\Teacher::where('school_id', $school->id)->count(),
            'classes'      => \App\Models\SchoolClass::where('school_id', $school->id)->count(),
            'activeYear'   => AcademicYear::where('school_id', $school->id)
                                ->where('is_active', true)->first(),
        ];

        $admin = $school->admin;

        return view('super-admin.schools.show', compact('school', 'stats', 'admin'));
    }

    public function toggleStatus(School $school)
    {
        $newStatus = $school->status === 'active' ? 'inactive' : 'active';
        $school->update(['status' => $newStatus]);

        return back()->with('success',
            'School ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' successfully.'
        );
    }

    public function destroy(School $school)
    {
        // Prevent deleting the demo school
        if ($school->id === 1) {
            return back()->with('error', 'Cannot delete the primary demo school.');
        }
        $school->delete();
        return back()->with('success', 'School deleted.');
    }
}