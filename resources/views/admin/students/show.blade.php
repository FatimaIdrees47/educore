@extends('layouts.admin')
@section('title', $student->user->name)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
        <div>
            <h1 class="page-title">{{ $student->user->name }}</h1>
            <p class="page-subtitle">{{ $student->admission_number }}</p>
        </div>
    </div>
    <span class="status-badge badge-{{ $student->status }}" style="font-size:13px">
        {{ ucfirst($student->status) }}
    </span>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="educore-card text-center" style="padding: 32px 24px">
            <div class="avatar-initials-sm mx-auto mb-3"
                 style="width:72px;height:72px;font-size:26px;font-weight:700;background:var(--color-purple)">
                {{ strtoupper(substr($student->user->name, 0, 2)) }}
            </div>
            <h2 style="font-size:18px;font-weight:700">{{ $student->user->name }}</h2>
            <p class="text-muted-sm">{{ $student->user->email }}</p>
            <p class="font-mono mt-2" style="font-size:13px;color:var(--color-text-sub)">
                {{ $student->admission_number }}
            </p>
        </div>
    </div>

    <div class="col-md-8">
        <div class="educore-card mb-3">
            <h2 class="card-title mb-3">Personal Information</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted-sm">Gender</div>
                    <div class="fw-semibold">{{ ucfirst($student->gender ?? '—') }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Date of Birth</div>
                    <div class="fw-semibold">
                        {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '—' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Blood Group</div>
                    <div class="fw-semibold">{{ $student->blood_group ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Religion</div>
                    <div class="fw-semibold">{{ $student->religion ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Phone</div>
                    <div class="fw-semibold">{{ $student->user->phone ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted-sm">Admission Date</div>
                    <div class="fw-semibold">{{ $student->admission_date->format('M d, Y') }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted-sm">Address</div>
                    <div class="fw-semibold">{{ $student->address ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="educore-card">
            <h2 class="card-title mb-3">Enrollment History</h2>
            @if($student->enrollments->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Class / Section</th>
                        <th>Roll No.</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->enrollments as $enrollment)
                    <tr>
                        <td class="fw-semibold">{{ $enrollment->academicYear->name }}</td>
                        <td>
                            {{ $enrollment->section->schoolClass->name }}
                            — {{ $enrollment->section->name }}
                        </td>
                        <td class="font-mono" style="font-size:13px">
                            {{ $enrollment->roll_number ?? '—' }}
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $enrollment->status }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p class="text-muted-sm">No enrollment records found.</p>
            @endif
        </div>
    </div>
</div>

@endsection