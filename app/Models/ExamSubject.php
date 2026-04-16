<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'subject_id', 'class_id',
        'exam_date', 'full_marks', 'passing_marks',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function marks()
    {
        return $this->hasMany(ExamMark::class);
    }
}