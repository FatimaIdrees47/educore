<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'exam_id', 'total_marks',
        'obtained_marks', 'percentage', 'grade',
        'position', 'remarks', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}