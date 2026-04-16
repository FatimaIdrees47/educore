<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'principal_name', 'status', 'subdomain',
        'max_students', 'max_teachers',
        'subscription_expires_at',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function settings()
    {
        return $this->hasOne(SchoolSetting::class);
    }

    public function getAdminAttribute()
    {
        return $this->users()
            ->whereHas('roles', fn($q) => $q->where('name', 'school-admin'))
            ->first();
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getTeacherCountAttribute(): int
    {
        return $this->teachers()->count();
    }
}