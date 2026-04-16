@extends('layouts.admin')
@section('title', 'Teachers')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Teachers</h1>
        <p class="page-subtitle">{{ $teachers->total() }} staff members</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
        + Add Teacher
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
    @if($teachers->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Employee ID</th>
                <th>Phone</th>
                <th>Joining Date</th>
                <th>Salary</th>
                <th>Leave Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $teacher)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-initials-sm"
                             style="background:var(--portal-teacher)">
                            {{ strtoupper(substr($teacher->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:14px">{{ $teacher->user->name }}</div>
                            <div class="text-muted-sm">{{ $teacher->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="font-mono" style="font-size:13px">
                        {{ $teacher->employee_id ?? '—' }}
                    </span>
                </td>
                <td class="text-muted-sm">{{ $teacher->user->phone ?? '—' }}</td>
                <td class="text-muted-sm">
                    {{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : '—' }}
                </td>
                <td>
                    @if($teacher->salary > 0)
                        <span class="fw-semibold">PKR {{ $teacher->salary_in_rupees }}</span>
                    @else
                        <span class="text-muted-sm">—</span>
                    @endif
                </td>
                <td>
                    <span class="status-badge {{ $teacher->leave_balance > 5 ? 'badge-active' : 'badge-late' }}">
                        {{ $teacher->leave_balance }} days
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.teachers.show', $teacher) }}"
                           class="btn btn-sm"
                           style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;padding:5px 8px"
                           title="View Profile">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('admin.teachers.edit', $teacher) }}"
                           class="btn btn-sm"
                           style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;padding:5px 8px"
                           title="Edit">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                              onsubmit="return confirm('Remove {{ $teacher->user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;padding:5px 8px"
                                    title="Delete">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $teachers->links() }}</div>

    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <p class="fw-semibold" style="font-size:16px">No teachers yet</p>
        <p class="text-muted-sm mb-3">Add your first staff member.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            + Add First Teacher
        </button>
    </div>
    @endif
</div>

{{-- Add Teacher Modal --}}
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add New Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.teachers.store') }}">
                @csrf
                <div class="modal-body">

                    <p style="font-size:11px;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;color:var(--color-text-mid)" class="mb-3">
                        Personal Information
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qualifications</label>
                            <input type="text" name="qualifications" class="form-control"
                                   placeholder="e.g. M.Sc Mathematics, B.Ed">
                        </div>
                    </div>

                    <hr style="border-color:var(--color-border)">
                    <p style="font-size:11px;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;color:var(--color-text-mid)" class="mb-3 mt-3">
                        Employment Details
                    </p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control font-mono"
                                   value="{{ $nextEmployeeId }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Leave Balance (days)</label>
                            <input type="number" name="leave_balance" class="form-control"
                                   value="21" min="0" max="365">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monthly Salary (PKR)</label>
                            <input type="number" name="salary" class="form-control"
                                   placeholder="e.g. 50000" min="0">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection