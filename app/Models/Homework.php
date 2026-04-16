<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Homework extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'homeworks';

    protected $fillable = [
        'school_id', 'teacher_id', 'section_id', 'subject_id',
        'title', 'description', 'file_path', 'due_date', 'total_marks',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast();
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}