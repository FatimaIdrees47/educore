@extends('layouts.admin')
@section('title', $teacher->user->name)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
        <div>
            <h1 class="page-title">{{ $teacher->user->name }}</h1>
            <p class="page-subtitle">{{ $teacher->employee_id ?? 'No Employee ID' }}</p>
        </div>
    </div>
    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary">Edit Profile</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="educore-card text-center" style="padding:32px 24px">
            <div class="avatar-initials-sm mx-auto mb-3"
                 style="width:72px;height:72px;font-size:26px;font-weight:700;background:var(--portal-teacher)">
                {{ strtoupper(substr($teacher->user->name, 0, 2)) }}
            </div>
            <h2 style="font-size:18px;font-weight:700">{{ $teacher->user->name }}</h2>
            <p class="text-muted-sm">{{ $teacher->user->email }}</p>
            @if($teacher->employee_id)
            <p class="font-mono mt-2" style="font-size:13px;color:var(--color-text-sub)">
                {{ $teacher->employee_id }}
            </p>
            @endif
            <div class="mt-3">
                <span class="status-badge badge-active">Active</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="educore-card mb-3">
            <h2 class="card-title mb-3">Employment Details</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted-sm">Phone</div>
                    <div class="fw-semibold">{{ $teacher->user->phone ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Joining Date</div>
                    <div class="fw-semibold">
                        {{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : '—' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Monthly Salary</div>
                    <div class="fw-semibold">
                        {{ $teacher->salary > 0 ? 'PKR ' . $teacher->salary_in_rupees : '—' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Leave Balance</div>
                    <div class="fw-semibold">{{ $teacher->leave_balance }} days</div>
                </div>
                <div class="col-12">
                    <div class="text-muted-sm">Qualifications</div>
                    <div class="fw-semibold">{{ $teacher->qualifications ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="educore-card">
            <h2 class="card-title mb-3">Assigned Sections</h2>
            @if($teacher->sections->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teacher->sections as $section)
                    <tr>
                        <td class="fw-semibold">{{ $section->schoolClass->name }}</td>
                        <td>{{ $section->name }}</td>
                        <td>{{ $section->capacity }} students</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted-sm">Not assigned as class teacher to any section yet.</p>
            @endif
        </div>
    </div>
</div>

@endsection