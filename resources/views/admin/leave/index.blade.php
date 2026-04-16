@extends('layouts.admin')
@section('title', 'Leave Management')

@section('content')

<div class="page-header">
    <h1 class="page-title">Leave Management</h1>
    <p class="page-subtitle">Review and manage teacher leave applications</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $pendingCount }}</div>
                <div class="stat-label">Pending Applications</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $approvedCount }}</div>
                <div class="stat-label">Approved This Year</div>
            </div>
        </div>
    </div>
</div>

<div class="educore-card">
    @if($applications->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-initials-sm"
                             style="font-size:11px;background:var(--portal-teacher)">
                            {{ strtoupper(substr($application->teacher->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:13px">
                                {{ $application->teacher->name }}
                            </div>
                            <div class="text-muted-sm" style="font-size:11px">
                                Applied {{ $application->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </td>
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
                <td class="text-muted-sm" style="font-size:13px">
                    {{ Str::limit($application->reason, 50) }}
                </td>
                <td>
                    <span class="status-badge badge-{{ $application->status === 'approved' ? 'active' : ($application->status === 'rejected' ? 'absent' : 'pending') }}">
                        {{ ucfirst($application->status) }}
                    </span>
                    @if($application->status === 'rejected' && $application->rejection_reason)
                    <div class="text-muted-sm" style="font-size:11px;margin-top:2px">
                        {{ Str::limit($application->rejection_reason, 40) }}
                    </div>
                    @endif
                </td>
                <td>
                    @if($application->status === 'pending')
                    <div class="d-flex gap-1">
                        <form method="POST"
                              action="{{ route('admin.leave.approve', $application) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                                Approve
                            </button>
                        </form>
                        <button class="btn btn-sm"
                                style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:5px 10px"
                                data-bs-toggle="modal"
                                data-bs-target="#rejectModal"
                                data-id="{{ $application->id }}"
                                data-teacher="{{ $application->teacher->name }}">
                            Reject
                        </button>
                    </div>
                    @else
                    <span class="text-muted-sm" style="font-size:12px">
                        {{ $application->updated_at->format('M d') }}
                        @if($application->approvedBy)
                            by {{ $application->approvedBy->name }}
                        @endif
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
        <p class="fw-semibold">No leave applications yet.</p>
        <p class="text-muted-sm">Teacher leave requests will appear here.</p>
    </div>
    @endif
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Reject Leave Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 p-3" style="background:var(--color-bg);border-radius:8px">
                        <div class="text-muted-sm">Teacher</div>
                        <div class="fw-semibold" id="rejectTeacherName"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                                  placeholder="Explain why the leave is being rejected..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">Reject Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('rejectModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('rejectTeacherName').textContent = btn.getAttribute('data-teacher');
        document.getElementById('rejectForm').action =
            '/admin/leave/' + btn.getAttribute('data-id') + '/reject';
    });
});
</script>

@endsection