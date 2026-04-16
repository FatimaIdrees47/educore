@extends('layouts.admin')
@section('title', $exam->name)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
        <div>
            <h1 class="page-title">{{ $exam->name }}</h1>
            <p class="page-subtitle">
                {{ $exam->start_date->format('M d') }} — {{ $exam->end_date->format('M d, Y') }}
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <span class="status-badge badge-{{ $exam->status === 'published' ? 'active' : 'draft' }}"
              style="font-size:13px">
            {{ ucfirst($exam->status) }}
        </span>
        @if($exam->status === 'completed')
        <form method="POST" action="{{ route('admin.exams.publish', $exam) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Publish Results</button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-8">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Subjects & Marks Entry</h2>
                <button class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    + Add Subject
                </button>
            </div>

            @if($exam->examSubjects->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Full Marks</th>
                        <th>Pass Marks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->examSubjects as $examSubject)
                    <tr>
                        <td class="fw-semibold">{{ $examSubject->subject->name }}</td>
                        <td>{{ $examSubject->schoolClass->name }}</td>
                        <td class="text-muted-sm">
                            {{ $examSubject->exam_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td>{{ $examSubject->full_marks }}</td>
                        <td>{{ $examSubject->passing_marks }}</td>
                        <td>
                            <a href="{{ route('admin.exams.marks.index', [$exam, $examSubject]) }}"
                               class="btn btn-sm"
                               style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:5px 12px">
                                Enter Marks
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted-sm">No subjects added yet. Click "+ Add Subject" to begin.</p>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="educore-card">
            <h2 class="card-title mb-3">Generate Report Cards</h2>
            <form method="POST" action="{{ route('admin.exams.report-cards.generate', $exam) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Select Class</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    Generate Report Cards
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Add Subject Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add Subject to Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.exams.subjects.store', $exam) }}">
                @csrf
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
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Exam Date</label>
                            <input type="date" name="exam_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Full Marks <span class="text-danger">*</span></label>
                            <input type="number" name="full_marks" class="form-control"
                                   value="100" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pass Marks <span class="text-danger">*</span></label>
                            <input type="number" name="passing_marks" class="form-control"
                                   value="40" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection