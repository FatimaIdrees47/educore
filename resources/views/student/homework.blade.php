@extends('layouts.student')
@section('title', 'Homework')

@section('content')

<div class="page-header">
    <h1 class="page-title">My Homework</h1>
    <p class="page-subtitle">View and submit your assignments</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($homeworks->count() > 0)
<div class="row g-3">
    @foreach($homeworks as $homework)
    @php
        $submission = $homework->submissions->first();
        $isSubmitted = $submission && $submission->submitted_at;
        $isGraded = $submission && $submission->marks_obtained !== null;
    @endphp
    <div class="col-md-6">
        <div class="educore-card h-100"
             style="border-left: 4px solid {{ $homework->is_overdue && !$isSubmitted ? 'var(--color-danger)' : ($isSubmitted ? 'var(--color-success)' : 'var(--color-primary)') }}">

            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <div class="fw-semibold" style="font-size:15px">{{ $homework->title }}</div>
                    <div class="text-muted-sm" style="font-size:12px;margin-top:3px">
                        {{ $homework->subject->name }}
                        @if($isGraded)
                        · <span style="color:var(--color-success);font-weight:600">
                            Graded: {{ $submission->marks_obtained }}/{{ $homework->total_marks }}
                          </span>
                        @endif
                    </div>
                </div>
                @if($isSubmitted)
                    <span class="status-badge badge-active">Submitted</span>
                @elseif($homework->is_overdue)
                    <span class="status-badge badge-absent">Overdue</span>
                @else
                    <span class="status-badge badge-draft">Pending</span>
                @endif
            </div>

            @if($homework->description)
            <p style="font-size:13px;color:var(--color-text-sub);margin-bottom:12px">
                {{ $homework->description }}
            </p>
            @endif

            <div class="d-flex gap-3 mb-3" style="font-size:12px;color:var(--color-text-mid)">
                <span>
                    <strong>Due:</strong>
                    <span class="{{ $homework->is_overdue ? 'text-danger' : '' }}">
                        {{ $homework->due_date->format('M d, Y') }}
                    </span>
                </span>
                <span><strong>Marks:</strong> {{ $homework->total_marks }}</span>
            </div>

            @if($isGraded && $submission->feedback)
            <div class="p-2 mb-3"
                 style="background:rgba(5,150,105,0.06);border-radius:8px;font-size:13px;
                        color:var(--color-success);border:1px solid rgba(5,150,105,0.15)">
                <strong>Teacher Feedback:</strong> {{ $submission->feedback }}
            </div>
            @endif

            @if(!$isSubmitted)
            <button class="btn btn-primary btn-sm w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#submitModal"
                    data-homework-id="{{ $homework->id }}"
                    data-homework-title="{{ $homework->title }}">
                Submit Homework
            </button>
            @elseif(!$isGraded)
            <div class="text-center text-muted-sm" style="font-size:12px;padding:6px 0">
                ✓ Submitted · Awaiting grade
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="educore-card" style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="margin-bottom:16px;opacity:0.35">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
    <p class="fw-semibold">No homework assigned yet</p>
    <p class="text-muted-sm">Check back later for assignments.</p>
</div>
@endif

{{-- Submit Homework Modal --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Submit Homework</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="submitForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 p-3" style="background:var(--color-bg);border-radius:8px">
                        <div class="text-muted-sm">Assignment</div>
                        <div class="fw-semibold" id="submitHomeworkTitle"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes / Answer</label>
                        <textarea name="notes" class="form-control" rows="4"
                                  placeholder="Write your answer or notes here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('submitModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('submitHomeworkTitle').textContent = btn.getAttribute('data-homework-title');
        document.getElementById('submitForm').action =
            '/student/homework/' + btn.getAttribute('data-homework-id') + '/submit';
    });
});
</script>

@endsection