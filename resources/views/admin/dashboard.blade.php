@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="page-header">
    <h1 class="page-title">School Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalStudents }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalTeachers }}</div>
                <div class="stat-label">Teachers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalClasses }}</div>
                <div class="stat-label">Classes</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalSections }}</div>
                <div class="stat-label">Sections</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Recent Enrollments</h2>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>

            @if(isset($recentStudents) && $recentStudents->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentStudents as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials-sm">
                                    {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                </div>
                                {{ $student->user->name }}
                            </div>
                        </td>
                        <td><span class="font-mono" style="font-size:13px">{{ $student->admission_number }}</span></td>
                        <td><span class="status-badge badge-{{ $student->status }}">{{ ucfirst($student->status) }}</span></td>
                        <td class="text-muted-sm">{{ $student->admission_date->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center; padding: 40px 0; color: var(--color-text-mid)">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin-bottom:12px; opacity:0.4">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p>No students enrolled yet.</p>
                <a href="{{ route('admin.students.index') }}" class="btn btn-primary btn-sm">Enroll First Student</a>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="educore-card mb-3">
            <h2 class="card-title mb-3">Academic Structure</h2>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center py-2"
                     style="border-bottom: 1px solid var(--color-border)">
                    <span style="font-size:14px; color:var(--color-text-sub)">Classes</span>
                    <span class="fw-semibold">{{ $totalClasses }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2"
                     style="border-bottom: 1px solid var(--color-border)">
                    <span style="font-size:14px; color:var(--color-text-sub)">Sections</span>
                    <span class="fw-semibold">{{ $totalSections }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2"
                     style="border-bottom: 1px solid var(--color-border)">
                    <span style="font-size:14px; color:var(--color-text-sub)">Subjects</span>
                    <span class="fw-semibold">{{ $totalSubjects }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span style="font-size:14px; color:var(--color-text-sub)">Teachers</span>
                    <span class="fw-semibold">{{ $totalTeachers }}</span>
                </div>
            </div>
        </div>

        <div class="educore-card">
            <h2 class="card-title mb-3">Quick Actions</h2>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn btn-primary">Enroll New Student</a>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-primary">Manage Classes</a>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-primary">Add Teacher</a>
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-primary">Mark Attendance</a>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-primary">Collect Fee</a>
                <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-primary">Post Notice</a>
            </div>
        </div>
    </div>
</div>

@endsection