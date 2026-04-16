<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'school_id', 'admission_number',
        'date_of_birth', 'gender', 'blood_group',
        'religion', 'address', 'admission_date', 'status',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'admission_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(Enrollment::class)
                    ->where('status', 'active')
                    ->latest();
    }

    public function parents()
    {
        return $this->belongsToMany(
            User::class,
            'student_parents',
            'student_id',
            'parent_user_id'
        )->withPivot('relationship', 'is_primary');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}