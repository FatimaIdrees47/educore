@extends('layouts.parent')
@section('title', 'Dashboard')

@section('content')

@if(!$selectedStudent)
<div class="educore-card" style="text-align:center;padding:60px 0">
    <p class="fw-semibold">No children linked to your account.</p>
    <p class="text-muted-sm">Please contact your school administrator.</p>
</div>
@else

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Parent Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    {{-- Child Selector --}}
    @if($children->count() > 1)
    <div class="d-flex gap-2">
        @foreach($children as $child)
        <a href="{{ route('parent.switch-child', $child->id) }}"
           class="btn btn-sm"
           style="border-radius:8px;font-size:12px;font-weight:500;padding:6px 14px;
                  {{ $selectedStudent->id === $child->id
                      ? 'background:var(--color-rose);color:#fff;border:1px solid var(--color-rose)'
                      : 'background:rgba(219,39,119,0.08);color:var(--color-rose);border:1px solid rgba(219,39,119,0.2)' }}">
            {{ $child->user->name }}
        </a>
        @endforeach
    </div>
    @endif
</div>

{{-- Selected Child Info --}}
<div class="educore-card mb-4">
    <div class="d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(219,39,119,0.1);
                    display:flex;align-items:center;justify-content:center;font-size:18px;
                    font-weight:700;color:var(--color-rose)">
            {{ strtoupper(substr($selectedStudent->user->name, 0, 2)) }}
        </div>
        <div>
            <div class="fw-semibold" style="font-size:16px">{{ $selectedStudent->user->name }}</div>
            <div class="text-muted-sm">
                @if($selectedStudent->currentEnrollment)
                    {{ $selectedStudent->currentEnrollment->section->schoolClass->name }}
                    — Section {{ $selectedStudent->currentEnrollment->section->name }}
                    · {{ $selectedStudent->currentEnrollment->academicYear->name }}
                @else
                    Admission No: {{ $selectedStudent->admission_number }}
                @endif
            </div>
        </div>
        <div class="ms-auto">
            <span class="status-badge badge-active">Active</span>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-rose">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $attendancePct }}%</div>
                <div class="stat-label">Attendance</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">
                    {{ $latestResult ? $latestResult->percentage . '%' : '—' }}
                </div>
                <div class="stat-label">Latest Result</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">
                    {{ $pendingFees > 0 ? 'PKR ' . number_format($pendingFees / 100, 0) : 'Clear' }}
                </div>
                <div class="stat-label">Fee Due</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $children->count() }}</div>
                <div class="stat-label">Children</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent Attendance --}}
    <div class="col-md-6">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Recent Attendance</h2>
                <a href="{{ route('parent.attendance') }}"
                   class="text-muted-sm" style="font-size:13px">View All →</a>
            </div>
            @if($recentAttendance->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendance as $record)
                    <tr>
                        <td class="font-mono" style="font-size:13px">
                            {{ $record->date->format('M d, Y') }}
                        </td>
                        <td class="text-muted-sm">{{ $record->date->format('l') }}</td>
                        <td>
                            <span class="status-badge badge-{{ $record->status === 'present' ? 'active' : ($record->status === 'absent' ? 'absent' : 'late') }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted-sm">No attendance records yet.</p>
            @endif
        </div>
    </div>

    {{-- Notices + Quick Actions --}}
    <div class="col-md-6">
        <div class="educore-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Latest Notices</h2>
                <a href="{{ route('parent.notices') }}"
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
                <a href="{{ route('parent.attendance') }}" class="btn btn-outline-primary">
                    View Attendance
                </a>
                <a href="{{ route('parent.results') }}" class="btn btn-outline-primary">
                    View Results
                </a>
                <a href="{{ route('parent.fees') }}" class="btn btn-primary">
                    View Fees
                </a>
                <a href="{{ route('parent.notices') }}" class="btn btn-outline-primary">
                    View Notices
                </a>
            </div>
        </div>
    </div>
</div>

@endif

@endsection