@extends('layouts.super-admin')
@section('title', 'Super Admin Dashboard')

@section('content')

<div class="page-header">
    <h1 class="page-title">Super Admin Dashboard</h1>
    <p class="page-subtitle">System-wide overview — {{ now()->format('l, M d Y') }}</p>
</div>

{{-- Top Stats --}}
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
        <div class="stat-card stat-purple">
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
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">PKR {{ number_format($totalRevenue / 100, 0) }}</div>
                <div class="stat-label">Total Revenue Collected</div>
            </div>
        </div>
    </div>
</div>

{{-- Second Row Stats --}}
<div class="row g-3 mb-4">
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
                <div class="stat-label">Active Schools</div>
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
                <div class="stat-number">{{ $inactiveSchools }}</div>
                <div class="stat-label">Inactive Schools</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $todayAttendance }}</div>
                <div class="stat-label">Attendance Marked Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $pendingLeaves }}</div>
                <div class="stat-label">Pending Leave Requests</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Schools Table --}}
    <div class="col-md-8">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">All Schools</h2>
                <a href="{{ route('super-admin.schools.index') }}"
                   class="btn btn-primary btn-sm">Manage Schools</a>
            </div>
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Students</th>
                        <th>Teachers</th>
                        <th>Status</th>
                        <th>Action</th>
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
                        <td>
                            <span class="fw-semibold">{{ $school->students_count }}</span>
                            <span class="text-muted-sm">/{{ $school->max_students }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $school->teachers_count }}</span>
                            <span class="text-muted-sm">/{{ $school->max_teachers }}</span>
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $school->status === 'active' ? 'active' : 'absent' }}">
                                {{ ucfirst($school->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('super-admin.schools.show', $school) }}"
                               class="btn btn-sm"
                               style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;padding:4px 10px">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">
        {{-- Revenue Chart --}}
        <div class="educore-card mb-3">
            <h2 class="card-title mb-3">Revenue — Last 6 Months</h2>
            @php
                $maxRevenue = $monthlyRevenue->max('revenue') ?: 1;
            @endphp
            @foreach($monthlyRevenue as $item)
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:36px;font-size:12px;color:var(--color-text-mid)">
                    {{ $item['month'] }}
                </div>
                <div style="flex:1;background:var(--color-border);border-radius:4px;height:10px">
                    <div style="width:{{ $maxRevenue > 0 ? round(($item['revenue'] / $maxRevenue) * 100) : 0 }}%;
                                height:100%;border-radius:4px;background:var(--color-primary)">
                    </div>
                </div>
                <div style="width:80px;font-size:11px;color:var(--color-text-sub);text-align:right">
                    PKR {{ number_format($item['revenue'] / 100, 0) }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- System Info --}}
        <div class="educore-card">
            <h2 class="card-title mb-3">Quick Stats</h2>
            <div class="d-flex justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <span class="text-muted-sm">Total Users</span>
                <span class="fw-semibold">{{ $totalUsers }}</span>
            </div>
            <div class="d-flex justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <span class="text-muted-sm">Pending Fees</span>
                <span class="fw-semibold" style="color:var(--color-danger)">
                    PKR {{ number_format($pendingFees / 100, 0) }}
                </span>
            </div>
            <div class="d-flex justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <span class="text-muted-sm">Today's Attendance</span>
                <span class="fw-semibold">{{ $todayAttendance }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted-sm">Pending Leaves</span>
                <span class="fw-semibold" style="color:var(--color-orange)">
                    {{ $pendingLeaves }}
                </span>
            </div>
        </div>
    </div>
</div>

@endsection