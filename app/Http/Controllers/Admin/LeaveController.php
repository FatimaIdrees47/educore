<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $applications = LeaveApplication::forSchool($schoolId)
            ->with(['teacher', 'approvedBy'])
            ->latest()
            ->paginate(20);

        $pendingCount  = LeaveApplication::forSchool($schoolId)->where('status', 'pending')->count();
        $approvedCount = LeaveApplication::forSchool($schoolId)->where('status', 'approved')->count();

        return view('admin.leave.index', compact(
            'applications', 'pendingCount', 'approvedCount'
        ));
    }

    public function approve(LeaveApplication $leaveApplication)
    {
        $this->authorizeSchool($leaveApplication->school_id);

        $leaveApplication->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        $teacher = Teacher::where('user_id', $leaveApplication->teacher_id)->first();
        if ($teacher) {
            $teacher->decrement('leave_balance', $leaveApplication->days);
        }

        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, LeaveApplication $leaveApplication)
    {
        $this->authorizeSchool($leaveApplication->school_id);

        $request->validate([
            'rejection_reason' => 'required|string|max:300',
        ]);

        $leaveApplication->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) abort(403);
    }
}