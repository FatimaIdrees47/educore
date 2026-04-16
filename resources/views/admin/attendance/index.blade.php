@extends('layouts.admin')
@section('title', 'Attendance')

@section('content')

<div class="page-header">
    <h1 class="page-title">Attendance</h1>
    <p class="page-subtitle">Mark and track daily student attendance</p>
</div>

{{-- Today's Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $todayTotal }}</div>
                <div class="stat-label">Marked Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $todayPresent }}</div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $todayAbsent }}</div>
                <div class="stat-label">Absent Today</div>
            </div>
        </div>
    </div>
</div>

{{-- Livewire Attendance Marker --}}
@livewire('admin.attendance-marker', ['schoolId' => auth()->user()->school_id])

@endsection