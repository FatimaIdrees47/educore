@extends('layouts.super-admin')
@section('title', $school->name)

@section('content')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('super-admin.schools.index') }}"
       class="btn btn-outline-primary btn-sm">← Back</a>
    <div class="flex-fill">
        <h1 class="page-title">{{ $school->name }}</h1>
        <p class="page-subtitle">School Detail View</p>
    </div>
    <form method="POST"
          action="{{ route('super-admin.schools.toggle-status', $school) }}">
        @csrf
        <button type="submit"
                class="btn btn-sm"
                style="background:{{ $school->status === 'active' ? 'rgba(234,88,12,0.08)' : 'rgba(5,150,105,0.08)' }};
                       color:{{ $school->status === 'active' ? 'var(--color-orange)' : 'var(--color-success)' }};
                       border:1px solid {{ $school->status === 'active' ? 'rgba(234,88,12,0.2)' : 'rgba(5,150,105,0.2)' }};
                       border-radius:7px;padding:6px 16px;font-size:13px">
            {{ $school->status === 'active' ? 'Deactivate School' : 'Activate School' }}
        </button>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $stats['students'] }}</div>
                <div class="stat-label">Students</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $stats['teachers'] }}</div>
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
                <div class="stat-number">{{ $stats['classes'] }}</div>
                <div class="stat-label">Classes</div>
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
                <div class="stat-number">{{ $stats['activeYear']?->name ?? '—' }}</div>
                <div class="stat-label">Active Year</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="educore-card">
            <h2 class="card-title mb-3">School Details</h2>
            <table class="educore-table">
                <tbody>
                    <tr>
                        <td class="text-muted-sm">Name</td>
                        <td class="fw-semibold">{{ $school->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Principal</td>
                        <td>{{ $school->principal_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Email</td>
                        <td>{{ $school->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Phone</td>
                        <td>{{ $school->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Address</td>
                        <td>{{ $school->address ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Status</td>
                        <td>
                            <span class="status-badge badge-{{ $school->status === 'active' ? 'active' : 'absent' }}">
                                {{ ucfirst($school->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Max Students</td>
                        <td>{{ $school->max_students }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Max Teachers</td>
                        <td>{{ $school->max_teachers }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted-sm">Created</td>
                        <td>{{ $school->created_at->format('M d, Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="educore-card">
            <h2 class="card-title mb-3">Admin Account</h2>
            @if($admin)
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:48px;height:48px;border-radius:12px;
                            background:rgba(37,99,235,0.1);color:var(--color-primary);
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;font-size:18px">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ $admin->name }}</div>
                    <div class="text-muted-sm">{{ $admin->email }}</div>
                </div>
                <span class="status-badge badge-{{ $admin->is_active ? 'active' : 'absent' }} ms-auto">
                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @else
            <p class="text-muted-sm">No admin assigned yet.</p>
            @endif
        </div>
    </div>
</div>

@endsection