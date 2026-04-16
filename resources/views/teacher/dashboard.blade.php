@extends('layouts.teacher')
@section('title', 'Dashboard')

@section('content')

@if(!$teacher)
<div class="educore-card" style="text-align:center;padding:60px 0">
    <p class="fw-semibold">Your teacher profile is not set up yet.</p>
    <p class="text-muted-sm">Please contact your school administrator.</p>
</div>
@else

<div class="page-header">
    <h1 class="page-title">Welcome, {{ auth()->user()->name }}</h1>
    <p class="page-subtitle">
        {{ $teacher->employee_id ?? 'Teacher' }}
        @if($mySections->count() > 0)
            · Class Teacher of
            @foreach($mySections as $section)
                {{ $section->schoolClass->name }} — {{ $section->name }}@if(!$loop->last), @endif
            @endforeach
        @endif
    </p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $myStudentCount }}</div>
                <div class="stat-label">My Students</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $mySections->count() }}</div>
                <div class="stat-label">My Sections</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $markedToday }}</div>
                <div class="stat-label">Marked Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $teacher->leave_balance }}</div>
                <div class="stat-label">Leave Balance</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- My Sections --}}
    <div class="col-md-6">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">My Sections</h2>
                <a href="{{ route('teacher.attendance.index') }}"
                   class="btn btn-primary btn-sm">Mark Attendance</a>
            </div>

            @if($mySections->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mySections as $section)
                    <tr>
                        <td class="fw-semibold">{{ $section->schoolClass->name }}</td>
                        <td>{{ $section->name }}</td>
                        <td>
                            {{ \App\Models\Enrollment::where('section_id', $section->id)
                                ->where('status', 'active')->count() }}
                        </td>
                        <td>
                            <a href="{{ route('teacher.attendance.index') }}"
                               class="btn btn-sm"
                               style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;padding:4px 10px">
                                Attendance
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center;padding:30px 0;color:var(--color-text-mid)">
                <p class="text-muted-sm">No sections assigned yet.</p>
                <p class="text-muted-sm" style="font-size:12px">
                    Contact admin to assign you as class teacher.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Notices + Quick Actions --}}
    <div class="col-md-6">
        <div class="educore-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Latest Notices</h2>
                <a href="{{ route('teacher.notices.index') }}"
                   class="text-muted-sm" style="font-size:13px">View All →</a>
            </div>
            @if($notices->count() > 0)
            @foreach($notices as $notice)
            <div class="py-2" style="border-bottom:1px solid var(--color-border)">
                <div class="fw-semibold" style="font-size:14px">{{ $notice->title }}</div>
                <div class="text-muted-sm" style="font-size:12px">
                    {{ $notice->created_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
            @else
            <p class="text-muted-sm">No notices at the moment.</p>
            @endif
        </div>

        <div class="educore-card">
            <h2 class="card-title mb-3">Quick Actions</h2>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary">
                    Mark Attendance
                </a>
                <a href="{{ route('teacher.homework.index') }}" class="btn btn-outline-primary">
                    Assign Homework
                </a>
                <a href="{{ route('teacher.timetable') }}" class="btn btn-outline-primary">
                    My Timetable
                </a>
                <a href="{{ route('teacher.leave.index') }}" class="btn btn-outline-primary">
                    Apply for Leave
                </a>
                <a href="{{ route('teacher.notices.index') }}" class="btn btn-outline-primary">
                    View Notices
                </a>
            </div>
        </div>
    </div>
</div>

@endif

@endsection