<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    public function create(array $data, int $schoolId): Teacher
    {
        return DB::transaction(function () use ($data, $schoolId) {

            // 1. Create user account
            $user = User::create([
                'school_id' => $schoolId,
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password'] ?? 'password'),
                'phone'     => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('teacher');

            // 2. Create teacher record
            // Salary stored as paisas (multiply by 100)
            $teacher = Teacher::create([
                'user_id'        => $user->id,
                'school_id'      => $schoolId,
                'employee_id'    => $data['employee_id'] ?? null,
                'qualifications' => $data['qualifications'] ?? null,
                'joining_date'   => $data['joining_date'] ?? null,
                'salary'         => isset($data['salary']) ? (int)($data['salary'] * 100) : 0,
                'leave_balance'  => $data['leave_balance'] ?? 21,
            ]);

            return $teacher;
        });
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        // Update user
        $teacher->user->update([
            'name'  => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        // Update teacher record
        $teacher->update([
            'employee_id'    => $data['employee_id'] ?? null,
            'qualifications' => $data['qualifications'] ?? null,
            'joining_date'   => $data['joining_date'] ?? null,
            'salary'         => isset($data['salary']) ? (int)($data['salary'] * 100) : 0,
            'leave_balance'  => $data['leave_balance'] ?? 21,
        ]);

        return $teacher;
    }

    public function generateEmployeeId(int $schoolId): string
    {
        $count = Teacher::where('school_id', $schoolId)->count() + 1;
        return 'EMP-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getTeachers(int $schoolId)
    {
        return Teacher::forSchool($schoolId)
            ->with('user')
            ->latest()
            ->paginate(20);
    }
}