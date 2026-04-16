<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $notices = Notice::forSchool($schoolId)
            ->active()
            ->where(function ($q) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', 'student');
            })
            ->latest()
            ->paginate(15);

        return view('student.notices', compact('notices'));
    }
}