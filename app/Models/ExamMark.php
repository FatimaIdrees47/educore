<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'exam_subject_id', 'student_id',
        'entered_by', 'marks_obtained', 'grade', 'is_absent',
    ];

    protected $casts = [
        'is_absent' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSubject()
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}