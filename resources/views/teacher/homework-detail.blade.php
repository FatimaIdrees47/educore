@extends('layouts.teacher')
@section('title', $homework->title)

@section('content')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('teacher.homework.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
    <div>
        <h1 class="page-title">{{ $homework->title }}</h1>
        <p class="page-subtitle">
            {{ $homework->subject->name }} ·
            {{ $homework->section->schoolClass->name }} — {{ $homework->section->name }} ·
            Due: {{ $homework->due_date->format('M d, Y') }}
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="educore-card">
            <h2 class="card-title mb-3">Homework Details</h2>
            <div class="mb-2">
                <div class="text-muted-sm">Subject</div>
                <div class="fw-semibold">{{ $homework->subject->name }}</div>
            </div>
            <div class="mb-2">
                <div class="text-muted-sm">Section</div>
                <div class="fw-semibold">
                    {{ $homework->section->schoolClass->name }} — {{ $homework->section->name }}
                </div>
            </div>
            <div class="mb-2">
                <div class="text-muted-sm">Due Date</div>
                <div class="fw-semibold {{ $homework->is_overdue ? 'text-danger' : '' }}">
                    {{ $homework->due_date->format('M d, Y') }}
                </div>
            </div>
            <div class="mb-2">
                <div class="text-muted-sm">Total Marks</div>
                <div class="fw-semibold">{{ $homework->total_marks }}</div>
            </div>
            <div class="mb-2">
                <div class="text-muted-sm">Submissions</div>
                <div class="fw-semibold">
                    {{ $submissions->count() }} / {{ $enrollments->count() }}
                </div>
            </div>
            @if($homework->description)
            <hr style="border-color:var(--color-border)">
            <div class="text-muted-sm" style="font-size:13px">{{ $homework->description }}</div>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="educore-card">
            <h2 class="card-title mb-3">Student Submissions</h2>
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Notes</th>
                        <th>Marks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    @php
                        $submission = $submissions->firstWhere('student_id', $enrollment->student_id);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials-sm" style="font-size:11px">
                                    {{ strtoupper(substr($enrollment->student->user->name, 0, 2)) }}
                                </div>
                                <span class="fw-semibold" style="font-size:13px">
                                    {{ $enrollment->student->user->name }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($submission)
                                <span class="status-badge badge-active">Submitted</span>
                            @else
                                <span class="status-badge badge-{{ $homework->is_overdue ? 'absent' : 'pending' }}">
                                    {{ $homework->is_overdue ? 'Missing' : 'Pending' }}
                                </span>
                            @endif
                        </td>
                        <td class="text-muted-sm" style="font-size:12px">
                            {{ $submission?->submitted_at?->format('M d, h:i A') ?? '—' }}
                        </td>
                        <td class="text-muted-sm" style="font-size:12px">
                            {{ $submission ? Str::limit($submission->notes, 40) : '—' }}
                        </td>
                        <td>
                            @if($submission?->marks_obtained !== null)
                                <span class="fw-semibold">
                                    {{ $submission->marks_obtained }}/{{ $homework->total_marks }}
                                </span>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                        <td>
                            @if($submission)
                            <button class="btn btn-sm"
                                    style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;padding:4px 10px"
                                    data-bs-toggle="modal"
                                    data-bs-target="#gradeModal"
                                    data-submission-id="{{ $submission->id }}"
                                    data-student="{{ $enrollment->student->user->name }}"
                                    data-marks="{{ $submission->marks_obtained ?? '' }}"
                                    data-feedback="{{ $submission->feedback ?? '' }}"
                                    data-total="{{ $homework->total_marks }}">
                                Grade
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Grade Modal --}}
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Grade Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="gradeForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 p-3" style="background:var(--color-bg);border-radius:8px">
                        <div class="text-muted-sm">Student</div>
                        <div class="fw-semibold" id="gradeStudentName"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Marks Obtained
                            <span class="text-muted-sm">(out of <span id="gradeTotalMarks"></span>)</span>
                        </label>
                        <input type="number" name="marks_obtained" id="gradeMarks"
                               class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback (Optional)</label>
                        <textarea name="feedback" id="gradeFeedback"
                                  class="form-control" rows="3"
                                  placeholder="Write feedback for the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('gradeModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('gradeStudentName').textContent = btn.getAttribute('data-student');
        document.getElementById('gradeMarks').value             = btn.getAttribute('data-marks');
        document.getElementById('gradeMarks').max               = btn.getAttribute('data-total');
        document.getElementById('gradeTotalMarks').textContent  = btn.getAttribute('data-total');
        document.getElementById('gradeFeedback').value          = btn.getAttribute('data-feedback');
        document.getElementById('gradeForm').action =
            '/teacher/homework/{{ $homework->id }}/submissions/' +
            btn.getAttribute('data-submission-id') + '/grade';
    });
});
</script>

@endsection