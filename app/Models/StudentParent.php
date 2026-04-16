<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    protected $table = 'student_parents';

    protected $fillable = [
        'student_id', 'parent_user_id', 'relationship', 'is_primary',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}