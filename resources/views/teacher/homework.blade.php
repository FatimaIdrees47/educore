@extends('layouts.teacher')
@section('title', 'Homework')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Homework</h1>
        <p class="page-subtitle">Assign and track homework for your sections</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHomeworkModal">
        + Assign Homework
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="educore-card">
    @if($homeworks->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Due Date</th>
                <th>Marks</th>
                <th>Submissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($homeworks as $homework)
            <tr>
                <td>
                    <div class="fw-semibold" style="font-size:14px">{{ $homework->title }}</div>
                    @if($homework->description)
                    <div class="text-muted-sm" style="font-size:12px;margin-top:2px">
                        {{ Str::limit($homework->description, 50) }}
                    </div>
                    @endif
                </td>
                <td class="text-muted-sm">{{ $homework->subject->name }}</td>
                <td class="text-muted-sm">
                    {{ $homework->section->schoolClass->name }} — {{ $homework->section->name }}
                </td>
                <td>
                    <span class="{{ $homework->is_overdue ? 'text-danger fw-semibold' : 'text-muted-sm' }}"
                          style="font-size:13px">
                        {{ $homework->due_date->format('M d, Y') }}
                        @if($homework->is_overdue)
                            <span class="status-badge badge-absent ms-1" style="font-size:10px">Overdue</span>
                        @endif
                    </span>
                </td>
                <td class="text-muted-sm">{{ $homework->total_marks }} marks</td>
                <td>
                    <span class="status-badge badge-draft">
                        {{ $homework->submissions->count() }} submitted
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('teacher.homework.show', $homework) }}"
                           class="btn btn-sm"
                           style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                            View
                        </a>
                        <form method="POST" action="{{ route('teacher.homework.destroy', $homework) }}"
                              onsubmit="return confirm('Delete this homework?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:5px 8px">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $homeworks->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="fw-semibold">No homework assigned yet</p>
        <p class="text-muted-sm mb-3">Click "+ Assign Homework" to get started.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHomeworkModal">
            + Assign First Homework
        </button>
    </div>
    @endif
</div>

{{-- Add Homework Modal --}}
<div class="modal fade" id="addHomeworkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Assign New Homework</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('teacher.homework.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Class & Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-select" required>
                                <option value="">Select Section</option>
                                @foreach($classes as $class)
                                    <optgroup label="{{ $class->name }}">
                                        @foreach($class->sections as $section)
                                        <option value="{{ $section->id }}">
                                            {{ $class->name }} — {{ $section->name }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="e.g. Chapter 5 Exercises" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Instructions or details..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                            <input type="number" name="total_marks" class="form-control"
                                   value="10" min="1" max="100" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Homework</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection