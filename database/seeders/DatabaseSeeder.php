<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\School;
use App\Models\AcademicYear;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create all 5 roles ──────────────────────────────────
        $roles = ['super-admin', 'school-admin', 'teacher', 'student', 'parent'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── 2. Create default school ───────────────────────────────
        $school = School::firstOrCreate(
            ['slug' => 'demo-school'],
            [
                'name'              => 'EduCore Demo School',
                'email'             => 'admin@democschool.com',
                'phone'             => '+92-300-0000000',
                'address'           => 'Karachi, Pakistan',
                'subscription_plan' => 'premium',
                'status'            => 'active',
                'currency'          => 'PKR',
                'timezone'          => 'Asia/Karachi',
            ]
        );

        // ── 3. Create active academic year ─────────────────────────
        AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2025-2026'],
            [
                'start_date' => '2025-04-01',
                'end_date'   => '2026-03-31',
                'is_active'  => true,
            ]
        );

        // ── 4. Create super admin (no school_id) ───────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@educore.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'school_id' => null,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // ── 5. Create school admin ─────────────────────────────────
        $schoolAdmin = User::firstOrCreate(
            ['email' => 'admin@democschool.com'],
            [
                'name'      => 'School Administrator',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'school_id' => $school->id,
            ]
        );
        $schoolAdmin->assignRole('school-admin');

        // ── 6. Create demo teacher ─────────────────────────────────
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@democschool.com'],
            [
                'name'      => 'Demo Teacher',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'school_id' => $school->id,
            ]
        );
        $teacher->assignRole('teacher');

        $this->command->info('✓ Roles created: ' . implode(', ', $roles));
        $this->command->info('✓ School: ' . $school->name);
        $this->command->info('✓ Super Admin: superadmin@educore.com / password');
        $this->command->info('✓ School Admin: admin@democschool.com / password');
        $this->command->info('✓ Teacher:      teacher@democschool.com / password');
    }
}