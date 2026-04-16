<?php

namespace App\Http\Controllers\EduParent;

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
                  ->orWhere('target_role', 'parent');
            })
            ->latest()
            ->paginate(15);

        return view('parent.notices', compact('notices'));
    }
}