@extends('layouts.admin')
@section('title', 'Timetable')

@section('content')

<div class="page-header">
    <h1 class="page-title">Timetable Builder</h1>
    <p class="page-subtitle">Build weekly timetables for each section</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Section Selector --}}
<div class="educore-card mb-4">
    <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
        <div style="min-width:200px">
            <label class="form-label">Select Class & Section</label>
            <select name="section_id" class="form-select" onchange="this.form.submit()">
                <option value="">Choose Section</option>
                @foreach($classes as $class)
                    <optgroup label="{{ $class->name }}">
                        @foreach($class->sections as $section)
                        <option value="{{ $section->id }}"
                            {{ $selectedSectionId == $section->id ? 'selected' : '' }}>
                            {{ $class->name }} — {{ $section->name }}
                        </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        @if($selectedSection)
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addSlotModal">
            + Add Slot
        </button>
        @endif
    </form>
</div>

@if($selectedSection)

{{-- Timetable Grid --}}
<div class="educore-card">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="card-title">
            {{ $selectedSection->schoolClass->name }} — Section {{ $selectedSection->name }}
        </h2>
        <span class="text-muted-sm">Weekly Timetable</span>
    </div>

    @php
        $days = \App\Models\TimetableSlot::daysOfWeek();
        $hasAnySlot = collect($timetable)->flatten()->count() > 0;
    @endphp

    @if($hasAnySlot)
    <div class="row g-3">
        @foreach($days as $day)
        @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
        <div class="col-md-4">
            <div style="border:1px solid var(--color-border);border-radius:10px;overflow:hidden">
                <div style="background:var(--color-bg);padding:10px 16px;border-bottom:1px solid var(--color-border)">
                    <span class="fw-semibold" style="font-size:14px;text-transform:capitalize">{{ $day }}</span>
                    <span class="text-muted-sm ms-2" style="font-size:12px">
                        {{ $timetable[$day]->count() }} {{ Str::plural('class', $timetable[$day]->count()) }}
                    </span>
                </div>
                @foreach($timetable[$day]->sortBy('start_time') as $slot)
                <div class="d-flex align-items-center justify-content-between px-3 py-2"
                     style="border-bottom:1px solid var(--color-border)">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:3px;height:36px;border-radius:2px;background:var(--color-primary);flex-shrink:0"></div>
                        <div>
                            <div class="fw-semibold" style="font-size:13px">{{ $slot->subject->name }}</div>
                            <div class="text-muted-sm" style="font-size:11px">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                — {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                @if($slot->room) · {{ $slot->room }} @endif
                            </div>
                            <div class="text-muted-sm" style="font-size:11px;color:var(--portal-teacher)">
                                {{ $slot->teacher->name }}
                            </div>
                        </div>
                    </div>
                    <form method="POST"
                          action="{{ route('admin.timetable.destroy', $slot) }}"
                          onsubmit="return confirm('Remove this slot?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
                        <button type="submit"
                                class="btn btn-sm"
                                style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:6px;padding:3px 7px">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Empty days --}}
    @php $emptyDays = collect($days)->filter(fn($d) => !isset($timetable[$d]) || $timetable[$d]->count() === 0); @endphp
    @if($emptyDays->count() > 0)
    <div class="mt-3">
        <span class="text-muted-sm" style="font-size:13px">
            No slots yet for:
            @foreach($emptyDays as $d)
                <span class="status-badge badge-draft" style="font-size:11px;text-transform:capitalize">{{ $d }}</span>
            @endforeach
        </span>
    </div>
    @endif

    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="fw-semibold">No timetable slots yet</p>
        <p class="text-muted-sm mb-3">Click "+ Add Slot" to build the timetable.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlotModal">
            + Add First Slot
        </button>
    </div>
    @endif
</div>

@else
<div class="educore-card" style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="margin-bottom:16px;opacity:0.35">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="fw-semibold">Select a section to view or build its timetable</p>
</div>
@endif

{{-- Add Slot Modal --}}
@if($selectedSection)
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    Add Timetable Slot —
                    {{ $selectedSection->schoolClass->name }} {{ $selectedSection->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.timetable.store') }}">
                @csrf
                <input type="hidden" name="section_id" value="{{ $selectedSection->id }}">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->user_id }}">
                                    {{ $teacher->user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Day <span class="text-danger">*</span></label>
                        <select name="day_of_week" class="form-select" required>
                            @foreach(\App\Models\TimetableSlot::daysOfWeek() as $day)
                            <option value="{{ $day }}" style="text-transform:capitalize">
                                {{ ucfirst($day) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room (Optional)</label>
                        <input type="text" name="room" class="form-control"
                               placeholder="e.g. Room 12, Lab 2">
                    </div>
                    <div class="alert alert-info" style="font-size:13px">
                        Conflict detection is automatic — if the teacher or section is already
                        booked at this time, the slot will be rejected.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Keep section_id on delete --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[action*="timetable"]').forEach(function (form) {
        if (form.querySelector('[name="_method"]')?.value === 'DELETE') {
            form.addEventListener('submit', function () {
                const sectionInput = document.createElement('input');
                sectionInput.type  = 'hidden';
                sectionInput.name  = 'section_id';
                sectionInput.value = '{{ $selectedSectionId }}';
            });
        }
    });
});
</script>

@endsection