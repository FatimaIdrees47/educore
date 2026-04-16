<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'student_id', 'section_id', 'academic_year_id',
        'marked_by', 'date', 'status', 'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    // Status color helper for views
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present'  => 'active',
            'absent'   => 'absent',
            'late'     => 'late',
            'half-day' => 'draft',
            'excused'  => 'excused',
            default    => 'draft',
        };
    }
}