<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LibraryBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id', 'title', 'author', 'isbn',
        'publisher', 'published_year', 'category',
        'shelf_location', 'total_copies',
        'available_copies', 'description',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function issues()
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    public function activeIssues()
    {
        return $this->hasMany(BookIssue::class, 'book_id')
                    ->whereNull('return_date');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0;
    }
}