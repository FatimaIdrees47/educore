@extends('layouts.student')
@section('title', 'My Timetable')

@section('content')

<div class="page-header">
    <h1 class="page-title">My Timetable</h1>
    <p class="page-subtitle">
        {{ $section ? $section->schoolClass->name . ' — Section ' . $section->name : 'No section assigned' }}
    </p>
</div>

@php $days = \App\Models\TimetableSlot::daysOfWeek(); @endphp

@if(collect($timetable)->flatten()->count() > 0)
<div class="row g-3">
    @foreach($days as $day)
    @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
    <div class="col-md-4">
        <div class="educore-card" style="padding:0;overflow:hidden">
            <div style="background:rgba(124,58,237,0.08);padding:12px 16px;
                        border-bottom:1px solid var(--color-border)">
                <span class="fw-semibold" style="font-size:14px;color:var(--color-purple);text-transform:capitalize">
                    {{ $day }}
                </span>
            </div>
            @foreach($timetable[$day]->sortBy('start_time') as $slot)
            <div class="d-flex align-items-center gap-3 px-4 py-3"
                 style="border-bottom:1px solid var(--color-border)">
                <div style="text-align:center;min-width:52px">
                    <div style="font-size:12px;font-weight:600;color:var(--color-primary)">
                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}
                    </div>
                    <div style="font-size:10px;color:var(--color-text-mid)">
                        {{ \Carbon\Carbon::parse($slot->start_time)->format('A') }}
                    </div>
                </div>
                <div style="width:2px;height:36px;background:var(--color-border);border-radius:2px"></div>
                <div>
                    <div class="fw-semibold" style="font-size:14px">{{ $slot->subject->name }}</div>
                    <div class="text-muted-sm" style="font-size:12px">
                        {{ $slot->teacher->name }}
                        @if($slot->room) · {{ $slot->room }} @endif
                    </div>
                    <div class="text-muted-sm" style="font-size:11px">
                        Until {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
</div>
@else
<div class="educore-card" style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="margin-bottom:16px;opacity:0.35">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="fw-semibold">No timetable set up yet</p>
    <p class="text-muted-sm">Your school admin will publish the timetable soon.</p>
</div>
@endif

@endsection