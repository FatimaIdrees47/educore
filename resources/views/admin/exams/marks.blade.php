@extends('layouts.admin')
@section('title', 'Enter Marks')

@section('content')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-primary btn-sm">← Back</a>
    <div>
        <h1 class="page-title">Enter Marks — {{ $examSubject->subject->name }}</h1>
        <p class="page-subtitle">
            {{ $exam->name }} · {{ $examSubject->schoolClass->name }} ·
            Full Marks: {{ $examSubject->full_marks }} · Pass: {{ $examSubject->passing_marks }}
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="educore-card">
    @if($enrollments->count() > 0)
    <form method="POST" action="{{ route('admin.exams.marks.store', [$exam, $examSubject]) }}">
        @csrf
        <table class="educore-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Student</th>
                    <th style="width:180px">Marks (out of {{ $examSubject->full_marks }})</th>
                    <th style="width:100px">Absent</th>
                    <th style="width:80px">Grade</th>
                    <th style="width:90px">Report</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $index => $enrollment)
                @php
                    $existingMark = $existingMarks[$enrollment->student_id] ?? null;
                    $hasReportCard = \App\Models\ReportCard::where('exam_id', $exam->id)
                        ->where('student_id', $enrollment->student_id)
                        ->whereNotNull('published_at')
                        ->exists();
                @endphp
                <tr>
                    <td class="text-muted-sm">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initials-sm" style="font-size:11px">
                                {{ strtoupper(substr($enrollment->student->user->name, 0, 2)) }}
                            </div>
                            <span class="fw-semibold" style="font-size:14px">
                                {{ $enrollment->student->user->name }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="number"
                               name="marks[{{ $enrollment->student_id }}][marks]"
                               class="form-control form-control-sm"
                               value="{{ $existingMark?->marks_obtained ?? '' }}"
                               min="0" max="{{ $examSubject->full_marks }}"
                               style="width:120px">
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="marks[{{ $enrollment->student_id }}][is_absent]"
                                   value="1"
                                   class="form-check-input"
                                   {{ $existingMark?->is_absent ? 'checked' : '' }}>
                            <label class="form-check-label text-muted-sm">Absent</label>
                        </div>
                    </td>
                    <td>
                        @if($existingMark)
                        <span class="status-badge badge-{{ in_array($existingMark->grade, ['A+','A','B']) ? 'active' : (in_array($existingMark->grade, ['C','D']) ? 'late' : ($existingMark->grade === 'ABS' ? 'excused' : 'absent')) }}">
                            {{ $existingMark->grade }}
                        </span>
                        @else
                        <span class="text-muted-sm">—</span>
                        @endif
                    </td>
                    <td>
                        @if($hasReportCard)
                        <a href="{{ route('admin.exams.report-cards.pdf', [$exam, $enrollment->student]) }}"
                           class="btn btn-sm"
                           style="background:rgba(124,58,237,0.08);color:var(--color-purple);border:1px solid rgba(124,58,237,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                            ↓ PDF
                        </a>
                        @else
                        <span class="text-muted-sm" style="font-size:12px">Not ready</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-4 pt-3"
             style="border-top:1px solid var(--color-border)">
            <button type="submit" class="btn btn-primary">Save Marks</button>
        </div>
    </form>

    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No students enrolled in this class</p>
        <p class="text-muted-sm">Enroll students first from the Students module.</p>
    </div>
    @endif
</div>

@endsection