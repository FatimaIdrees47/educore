@extends('layouts.student')
@section('title', $reportCard->exam->name)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('student.results') }}" class="btn btn-outline-primary btn-sm">← Back</a>
        <div>
            <h1 class="page-title">{{ $reportCard->exam->name }}</h1>
            <p class="page-subtitle">Detailed Result</p>
        </div>
    </div>
    <a href="{{ route('student.results.pdf', $reportCard) }}"
       class="btn btn-primary btn-sm">
        ↓ Download PDF
    </a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="educore-card text-center" style="padding:32px 24px">
            <div style="width:80px;height:80px;border-radius:50%;margin:0 auto 16px;
                        display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;
                        background:{{ $reportCard->percentage >= 60 ? 'rgba(5,150,105,0.1)' : 'rgba(220,38,38,0.1)' }};
                        color:{{ $reportCard->percentage >= 60 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                {{ $reportCard->grade }}
            </div>
            <div class="stat-number">{{ $reportCard->percentage }}%</div>
            <div class="text-muted-sm mt-1">Overall Percentage</div>
            <div class="mt-3 d-flex justify-content-center gap-4">
                <div>
                    <div class="fw-semibold">{{ $reportCard->obtained_marks }}</div>
                    <div class="text-muted-sm" style="font-size:12px">Obtained</div>
                </div>
                <div>
                    <div class="fw-semibold">{{ $reportCard->total_marks }}</div>
                    <div class="text-muted-sm" style="font-size:12px">Total</div>
                </div>
                <div>
                    <div class="fw-semibold">#{{ $reportCard->position }}</div>
                    <div class="text-muted-sm" style="font-size:12px">Position</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="educore-card">
            <h2 class="card-title mb-3">Subject-wise Marks</h2>
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Full Marks</th>
                        <th>Obtained</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                    <tr>
                        <td class="fw-semibold">{{ $mark->examSubject->subject->name }}</td>
                        <td>{{ $mark->examSubject->full_marks }}</td>
                        <td>{{ $mark->is_absent ? '—' : $mark->marks_obtained }}</td>
                        <td>
                            @if(!$mark->is_absent)
                            {{ round(($mark->marks_obtained / $mark->examSubject->full_marks) * 100) }}%
                            @else
                            —
                            @endif
                        </td>
                        <td>
                            <span class="status-badge badge-{{ in_array($mark->grade, ['A+','A','B']) ? 'active' : ($mark->grade === 'ABS' ? 'excused' : (in_array($mark->grade, ['C','D']) ? 'late' : 'absent')) }}">
                                {{ $mark->grade }}
                            </span>
                        </td>
                        <td>
                            @if($mark->is_absent)
                            <span class="status-badge badge-excused">Absent</span>
                            @elseif($mark->marks_obtained >= $mark->examSubject->passing_marks)
                            <span class="status-badge badge-active">Pass</span>
                            @else
                            <span class="status-badge badge-absent">Fail</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection