@extends('layouts.teacher')
@section('title', 'Leave Applications')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Leave Applications</h1>
        <p class="page-subtitle">
            Leave Balance:
            <span class="fw-semibold" style="color:var(--color-success)">
                {{ $teacher?->leave_balance ?? 0 }} days remaining
            </span>
        </p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
        + Apply for Leave
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="educore-card">
    @if($applications->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
            <tr>
                <td>
                    <span class="status-badge badge-draft" style="text-transform:capitalize">
                        {{ ucfirst($application->type) }}
                    </span>
                </td>
                <td class="font-mono" style="font-size:13px">
                    {{ $application->from_date->format('M d, Y') }}
                </td>
                <td class="font-mono" style="font-size:13px">
                    {{ $application->to_date->format('M d, Y') }}
                </td>
                <td class="fw-semibold">{{ $application->days }}</td>
                <td class="text-muted-sm">{{ Str::limit($application->reason, 50) }}</td>
                <td>
                    <span class="status-badge badge-{{ $application->status === 'approved' ? 'active' : ($application->status === 'rejected' ? 'absent' : 'pending') }}">
                        {{ ucfirst($application->status) }}
                    </span>
                    @if($application->status === 'rejected' && $application->rejection_reason)
                    <div class="text-muted-sm" style="font-size:11px;margin-top:3px">
                        {{ Str::limit($application->rejection_reason, 40) }}
                    </div>
                    @endif
                </td>
                <td>
                    @if($application->status === 'pending')
                    <form method="POST"
                          action="{{ route('teacher.leave.destroy', $application) }}"
                          onsubmit="return confirm('Withdraw this application?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm"
                                style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                            Withdraw
                        </button>
                    </form>
                    @else
                    <span class="text-muted-sm" style="font-size:12px">
                        {{ $application->updated_at->format('M d') }}
                    </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $applications->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="fw-semibold">No leave applications yet</p>
        <p class="text-muted-sm mb-3">Apply for leave when needed.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            Apply for Leave
        </button>
    </div>
    @endif
</div>

{{-- Apply Leave Modal --}}
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Apply for Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('teacher.leave.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="casual">Casual Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="emergency">Emergency Leave</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" class="form-control"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" class="form-control"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                                  placeholder="Briefly explain the reason for leave..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection