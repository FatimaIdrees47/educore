@extends('layouts.student')
@section('title', 'My Attendance')

@section('content')

<div class="page-header">
    <h1 class="page-title">My Attendance</h1>
    <p class="page-subtitle">Track your daily attendance record</p>
</div>

{{-- Month Filter --}}
<div class="educore-card mb-4">
    <form method="GET" class="d-flex gap-3 align-items-end">
        <div>
            <label class="form-label">Month</label>
            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($months as $m)
                <option value="{{ $m['value'] }}" {{ $m['value'] == $month ? 'selected' : '' }}>
                    {{ $m['label'] }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Year</label>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach(range(now()->year, now()->year - 2) as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $total }}</div>
                <div class="stat-label">Total Days</div>
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
                <div class="stat-number">{{ $present }}</div>
                <div class="stat-label">Present</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $absent }}</div>
                <div class="stat-label">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $percentage }}%</div>
                <div class="stat-label">Percentage</div>
            </div>
        </div>
    </div>
</div>

{{-- Records Table --}}
<div class="educore-card">
    @if($records->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
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
                <td class="text-muted-sm">{{ $record->remarks ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:40px 0;color:var(--color-text-mid)">
        <p>No attendance records for this month.</p>
    </div>
    @endif
</div>

@endsection