@extends('layouts.student')
@section('title', 'Dashboard')

@section('content')

@if(!$student)
<div class="educore-card" style="text-align:center;padding:60px 0">
    <p class="fw-semibold">Your student profile is not set up yet.</p>
    <p class="text-muted-sm">Please contact your school administrator.</p>
</div>
@else

<div class="page-header">
    <h1 class="page-title">Welcome, {{ auth()->user()->name }}</h1>
    <p class="page-subtitle">
        @if($enrollment)
            {{ $enrollment->section->schoolClass->name }} — Section {{ $enrollment->section->name }}
            · {{ $enrollment->academicYear->name }}
        @else
            No active enrollment found
        @endif
    </p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $attendancePct }}%</div>
                <div class="stat-label">Attendance This Month</div>
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
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $presentDays }}/{{ $totalDays }}</div>
                <div class="stat-label">Days Present</div>
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
                <a href="{{ route('student.attendance') }}"
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
                            <span class="status-badge badge-{{ $record->status === 'present' ? 'active' : ($record->status === 'absent' ? 'absent' : ($record->status === 'late' ? 'late' : 'draft')) }}">
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

    {{-- Notices --}}
    <div class="col-md-6">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Latest Notices</h2>
                <a href="{{ route('student.notices') }}"
                   class="text-muted-sm" style="font-size:13px">View All →</a>
            </div>

            @if($notices->count() > 0)
            @foreach($notices as $notice)
            <div class="py-2" style="border-bottom:1px solid var(--color-border)">
                <div class="fw-semibold" style="font-size:14px">{{ $notice->title }}</div>
                <div class="text-muted-sm" style="font-size:12px;margin-top:3px">
                    {{ $notice->created_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
            @else
            <p class="text-muted-sm">No notices at the moment.</p>
            @endif
        </div>
    </div>
</div>

@endif

@endsection