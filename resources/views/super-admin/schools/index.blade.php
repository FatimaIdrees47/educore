@extends('layouts.super-admin')
@section('title', 'Manage Schools')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Schools</h1>
        <p class="page-subtitle">Manage all schools on the platform</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
        + Add School
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

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalSchools }}</div>
                <div class="stat-label">Total Schools</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $activeSchools }}</div>
                <div class="stat-label">Active</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalTeachers }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
        </div>
    </div>
</div>

<div class="educore-card">
    @if($schools->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>School</th>
                <th>Admin</th>
                <th>Students</th>
                <th>Teachers</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schools as $school)
            <tr>
                <td>
                    <div class="fw-semibold">{{ $school->name }}</div>
                    <div class="text-muted-sm" style="font-size:12px">
                        {{ $school->email ?? '—' }}
                    </div>
                </td>
                <td class="text-muted-sm" style="font-size:13px">
                    {{ $school->admin?->name ?? '—' }}
                </td>
                <td>
                    <span class="fw-semibold">{{ $school->students_count }}</span>
                    <span class="text-muted-sm" style="font-size:12px">/ {{ $school->max_students }}</span>
                </td>
                <td>
                    <span class="fw-semibold">{{ $school->teachers_count }}</span>
                    <span class="text-muted-sm" style="font-size:12px">/ {{ $school->max_teachers }}</span>
                </td>
                <td>
                    <span class="status-badge badge-{{ $school->status === 'active' ? 'active' : 'absent' }}">
                        {{ ucfirst($school->status) }}
                    </span>
                </td>
                <td class="text-muted-sm">{{ $school->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('super-admin.schools.show', $school) }}"
                           class="btn btn-sm"
                           style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                            View
                        </a>
                        <form method="POST"
                              action="{{ route('super-admin.schools.toggle-status', $school) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background:{{ $school->status === 'active' ? 'rgba(234,88,12,0.08)' : 'rgba(5,150,105,0.08)' }};
                                           color:{{ $school->status === 'active' ? 'var(--color-orange)' : 'var(--color-success)' }};
                                           border:1px solid {{ $school->status === 'active' ? 'rgba(234,88,12,0.2)' : 'rgba(5,150,105,0.2)' }};
                                           border-radius:7px;font-size:12px;padding:5px 10px">
                                {{ $school->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @if($school->id !== 1)
                        <form method="POST"
                              action="{{ route('super-admin.schools.destroy', $school) }}"
                              onsubmit="return confirm('Delete {{ $school->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:5px 8px">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $schools->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No schools yet.</p>
    </div>
    @endif
</div>

{{-- Add School Modal --}}
<div class="modal fade" id="addSchoolModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add New School</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('super-admin.schools.store') }}">
                @csrf
                <div class="modal-body">
                    <h6 class="fw-semibold mb-3" style="color:var(--color-primary)">School Information</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">School Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. Beacon House School" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Principal Name</label>
                            <input type="text" name="principal_name" class="form-control"
                                   placeholder="e.g. Dr. Ahmed Khan">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="info@school.edu.pk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="+92 300 0000000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                               placeholder="School address...">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Max Students</label>
                            <input type="number" name="max_students" class="form-control"
                                   value="500" min="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Teachers</label>
                            <input type="number" name="max_teachers" class="form-control"
                                   value="50" min="1">
                        </div>
                    </div>

                    <hr style="border-color:var(--color-border)">
                    <h6 class="fw-semibold mb-3 mt-3" style="color:var(--color-success)">
                        Admin Account
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" name="admin_name" class="form-control"
                                   placeholder="School Administrator" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control"
                                   placeholder="admin@school.edu.pk" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Password <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password" class="form-control"
                               placeholder="Min 8 characters" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Create School</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection