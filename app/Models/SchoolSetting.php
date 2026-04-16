<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_id', 'school_name', 'school_email',
        'school_phone', 'school_address', 'school_website',
        'principal_name', 'logo_path', 'grading_scale',
        'currency', 'timezone', 'allow_parent_messages',
        'show_positions',
    ];

    protected $casts = [
        'grading_scale'         => 'array',
        'allow_parent_messages' => 'boolean',
        'show_positions'        => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public static function defaultGradingScale(): array
    {
        return [
            ['grade' => 'A+', 'min' => 90, 'max' => 100],
            ['grade' => 'A',  'min' => 80, 'max' => 89],
            ['grade' => 'B',  'min' => 70, 'max' => 79],
            ['grade' => 'C',  'min' => 60, 'max' => 69],
            ['grade' => 'D',  'min' => 50, 'max' => 59],
            ['grade' => 'F',  'min' => 0,  'max' => 49],
        ];
    }
}