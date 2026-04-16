<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    public function enroll(array $data, int $schoolId): Student
    {
        return DB::transaction(function () use ($data, $schoolId) {

            // 1. Create user account for student
            $user = User::create([
                'school_id' => $schoolId,
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password'] ?? 'password'),
                'phone'     => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('student');

            // 2. Create student record
            $student = Student::create([
                'user_id'          => $user->id,
                'school_id'        => $schoolId,
                'admission_number' => $data['admission_number'],
                'date_of_birth'    => $data['date_of_birth'] ?? null,
                'gender'           => $data['gender'] ?? null,
                'blood_group'      => $data['blood_group'] ?? null,
                'religion'         => $data['religion'] ?? null,
                'address'          => $data['address'] ?? null,
                'admission_date'   => $data['admission_date'],
                'status'           => 'active',
            ]);

            // 3. Create enrollment for active academic year
            $activeYear = AcademicYear::where('school_id', $schoolId)
                                      ->where('is_active', true)
                                      ->first();

            if ($activeYear && !empty($data['section_id'])) {
                Enrollment::create([
                    'student_id'       => $student->id,
                    'section_id'       => $data['section_id'],
                    'academic_year_id' => $activeYear->id,
                    'roll_number'      => $data['roll_number'] ?? null,
                    'status'           => 'active',
                ]);
            }

            return $student;
        });
    }

    public function generateAdmissionNumber(int $schoolId): string
    {
        $year  = date('Y');
        $count = Student::where('school_id', $schoolId)->count() + 1;
        return 'ADM-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getStudents(int $schoolId)
    {
        return Student::forSchool($schoolId)
            ->with(['user', 'currentEnrollment.section.schoolClass'])
            ->latest()
            ->paginate(20);
    }
}