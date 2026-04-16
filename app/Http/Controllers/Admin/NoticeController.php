<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $notices = Notice::forSchool($schoolId)
            ->with(['postedBy', 'targetClass'])
            ->latest()
            ->paginate(15);

        $classes = SchoolClass::forSchool($schoolId)
            ->orderBy('numeric_order')
            ->get();

        $totalNotices  = Notice::forSchool($schoolId)->count();
        $activeNotices = Notice::forSchool($schoolId)->active()->count();

        return view('admin.notices.index', compact(
            'notices', 'classes', 'totalNotices', 'activeNotices'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:200',
            'body'            => 'required|string',
            'target_role'     => 'required|in:all,school-admin,teacher,student,parent',
            'target_class_id' => 'nullable|exists:classes,id',
            'published_at'    => 'nullable|date',
            'expires_at'      => 'nullable|date|after:published_at',
        ]);

        Notice::create([
            'school_id'       => auth()->user()->school_id,
            'posted_by'       => auth()->id(),
            'title'           => $request->title,
            'body'            => $request->body,
            'target_role'     => $request->target_role,
            'target_class_id' => $request->target_class_id,
            'published_at'    => $request->published_at ?? now(),
            'expires_at'      => $request->expires_at,
        ]);

        return back()->with('success', 'Notice posted successfully.');
    }

    public function edit(Notice $notice)
    {
        $this->authorizeSchool($notice->school_id);
        $notice->load('postedBy', 'targetClass');

        $classes = SchoolClass::forSchool(auth()->user()->school_id)
            ->orderBy('numeric_order')->get();

        return view('admin.notices.edit', compact('notice', 'classes'));
    }

    public function update(Request $request, Notice $notice)
    {
        $this->authorizeSchool($notice->school_id);

        $request->validate([
            'title'           => 'required|string|max:200',
            'body'            => 'required|string',
            'target_role'     => 'required|in:all,school-admin,teacher,student,parent',
            'target_class_id' => 'nullable|exists:classes,id',
            'published_at'    => 'nullable|date',
            'expires_at'      => 'nullable|date',
        ]);

        $notice->update($request->only(
            'title', 'body', 'target_role',
            'target_class_id', 'published_at', 'expires_at'
        ));

        return redirect()->route('admin.notices.index')
                         ->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        $this->authorizeSchool($notice->school_id);
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) abort(403);
    }
}