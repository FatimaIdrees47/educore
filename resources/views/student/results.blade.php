@extends('layouts.student')
@section('title', 'My Results')

@section('content')

<div class="page-header">
    <h1 class="page-title">My Results</h1>
    <p class="page-subtitle">View your exam results and report cards</p>
</div>

<div class="educore-card">
    @if($reportCards->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Exam</th>
                <th>Total Marks</th>
                <th>Obtained</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Position</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportCards as $card)
            <tr>
                <td class="fw-semibold">{{ $card->exam->name }}</td>
                <td>{{ $card->total_marks }}</td>
                <td>{{ $card->obtained_marks }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:60px;height:6px;border-radius:3px;background:var(--color-border)">
                            <div style="width:{{ $card->percentage }}%;height:100%;border-radius:3px;
                                        background:{{ $card->percentage >= 60 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                            </div>
                        </div>
                        <span>{{ $card->percentage }}%</span>
                    </div>
                </td>
                <td>
                    <span class="status-badge badge-{{ in_array($card->grade, ['A+','A','B']) ? 'active' : (in_array($card->grade, ['C','D']) ? 'late' : 'absent') }}">
                        {{ $card->grade }}
                    </span>
                </td>
                <td class="fw-semibold">#{{ $card->position ?? '—' }}</td>
                <td>
                    <a href="{{ route('student.results.show', $card) }}"
                       class="btn btn-sm"
                       style="background:rgba(124,58,237,0.08);color:var(--color-purple);border:1px solid rgba(124,58,237,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:5px 10px">
                        View Detail
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No results published yet.</p>
        <p class="text-muted-sm">Check back after your exams are graded.</p>
    </div>
    @endif
</div>

@endsection