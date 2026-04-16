@extends('layouts.admin')
@section('title', 'Exams & Results')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Exams & Results</h1>
        <p class="page-subtitle">Manage exams, enter marks and publish results</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
        + Create Exam
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($exams->count() > 0)
<div class="row g-3">
    @foreach($exams as $exam)
    <div class="col-md-4">
        <div class="educore-card h-100"
            style="border-top: 3px solid {{ $exam->status === 'published' ? 'var(--color-success)' : ($exam->status === 'active' ? 'var(--color-primary)' : ($exam->status === 'completed' ? 'var(--color-purple)' : 'var(--color-text-mid)')) }}">

            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <h3 style="font-size:16px;font-weight:700">{{ $exam->name }}</h3>
                    <div class="text-muted-sm">{{ $exam->academicYear->name }}</div>
                </div>
                <span class="status-badge badge-{{ $exam->status === 'published' ? 'active' : ($exam->status === 'active' ? 'draft' : ($exam->status === 'completed' ? 'excused' : 'scheduled')) }}">
                    {{ ucfirst($exam->status) }}
                </span>
            </div>

            <div class="d-flex gap-3 mb-4" style="font-size:13px;color:var(--color-text-sub)">
                <div>
                    <div class="text-muted-sm">Start</div>
                    <div class="fw-semibold">{{ $exam->start_date->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="text-muted-sm">End</div>
                    <div class="fw-semibold">{{ $exam->end_date->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="text-muted-sm">Subjects</div>
                    <div class="fw-semibold">{{ $exam->examSubjects->count() }}</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-auto">

                {{-- Manage --}}
                <a href="{{ route('admin.exams.show', $exam) }}"
                    class="btn btn-sm flex-fill"
                    style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;font-weight:500;text-align:center;padding:6px 12px">
                    Manage
                </a>

                {{-- Mark Complete --}}
                @if($exam->status === 'draft' || $exam->status === 'active')
                <form method="POST" action="{{ route('admin.exams.complete', $exam) }}">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm"
                        style="background:rgba(124,58,237,0.08);color:var(--color-purple);border:1px solid rgba(124,58,237,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:6px 12px">
                        ✓ Complete
                    </button>
                </form>
                @endif

                {{-- Publish --}}
                @if($exam->status === 'completed')
                <form method="POST" action="{{ route('admin.exams.publish', $exam) }}">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm"
                        style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:6px 12px">
                        Publish
                    </button>
                </form>
                @endif

                {{-- Delete --}}
                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                    onsubmit="return confirm('Delete {{ $exam->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="btn btn-sm"
                        style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:6px 12px"
                        title="Delete Exam">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>

            </div>
        </div>
    </div>
    @endforeach
</div>

@else
<div class="educore-card" style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
        style="margin-bottom:16px;opacity:0.35">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    <p class="fw-semibold" style="font-size:16px">No exams yet</p>
    <p class="text-muted-sm mb-3">Create your first exam to get started.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
        + Create First Exam
    </button>
</div>
@endif

{{-- Create Exam Modal --}}
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Create New Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.exams.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            placeholder="e.g. Mid Term Exam, Final Term 2025" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection