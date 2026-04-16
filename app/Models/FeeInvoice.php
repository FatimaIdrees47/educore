<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id', 'school_id', 'academic_year_id',
        'fee_structure_id', 'receipt_number', 'fee_type',
        'month', 'year', 'amount', 'fine_amount',
        'discount_amount', 'net_amount', 'status',
        'due_date', 'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function getNetAmountInRupeesAttribute(): string
    {
        return number_format($this->net_amount / 100, 0);
    }

    public function getAmountInRupeesAttribute(): string
    {
        return number_format($this->amount / 100, 0);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}