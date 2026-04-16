<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $applications = LeaveApplication::where('teacher_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('teacher.leave', compact('applications', 'teacher'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:today',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|in:sick,casual,emergency,other',
            'reason'    => 'required|string|max:500',
        ]);

        // Check for overlapping applications
        $overlap = LeaveApplication::where('teacher_id', auth()->id())
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($request) {
                $q->whereBetween('from_date', [$request->from_date, $request->to_date])
                  ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('from_date', '<=', $request->from_date)
                        ->where('to_date', '>=', $request->to_date);
                  });
            })->exists();

        if ($overlap) {
            return back()->with('error', 'You already have a leave application for overlapping dates.');
        }

        LeaveApplication::create([
            'teacher_id' => auth()->id(),
            'school_id'  => auth()->user()->school_id,
            'from_date'  => $request->from_date,
            'to_date'    => $request->to_date,
            'type'       => $request->type,
            'reason'     => $request->reason,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Leave application submitted successfully.');
    }

    public function destroy(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->teacher_id !== auth()->id()) abort(403);
        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be withdrawn.');
        }
        $leaveApplication->delete();
        return back()->with('success', 'Leave application withdrawn.');
    }
}