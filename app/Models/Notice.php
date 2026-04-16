<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id', 'posted_by', 'title', 'body',
        'target_role', 'target_class_id',
        'published_at', 'expires_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function targetClass()
    {
        return $this->belongsTo(SchoolClass::class, 'target_class_id');
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeActive($query)
    {
        return $query->where('published_at', '<=', now())
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function getIsActiveAttribute(): bool
    {
        if (!$this->published_at) return false;
        if ($this->published_at > now()) return false;
        if ($this->expires_at && $this->expires_at < now()) return false;
        return true;
    }
}