@extends('layouts.parent')
@section('title', 'Results')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Results</h1>
        <p class="page-subtitle">{{ $selectedStudent?->user->name }}</p>
    </div>
    @if($children->count() > 1)
    <div class="d-flex gap-2">
        @foreach($children as $child)
        <a href="?student_id={{ $child->id }}"
           class="btn btn-sm"
           style="border-radius:8px;font-size:12px;font-weight:500;padding:6px 14px;
                  {{ $selectedStudent?->id === $child->id
                      ? 'background:var(--color-rose);color:#fff;border:1px solid var(--color-rose)'
                      : 'background:rgba(219,39,119,0.08);color:var(--color-rose);border:1px solid rgba(219,39,119,0.2)' }}">
            {{ $child->user->name }}
        </a>
        @endforeach
    </div>
    @endif
</div>

<div class="educore-card">
    @if($reportCards->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Exam</th>
                <th>Obtained</th>
                <th>Total</th>
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
                <td>{{ $card->obtained_marks }}</td>
                <td>{{ $card->total_marks }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:60px;height:6px;border-radius:3px;background:var(--color-border)">
                            <div style="width:{{ $card->percentage }}%;height:100%;border-radius:3px;
                                        background:{{ $card->percentage >= 60 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                            </div>
                        </div>
                        {{ $card->percentage }}%
                    </div>
                </td>
                <td>
                    <span class="status-badge badge-{{ in_array($card->grade, ['A+','A','B']) ? 'active' : (in_array($card->grade, ['C','D']) ? 'late' : 'absent') }}">
                        {{ $card->grade }}
                    </span>
                </td>
                <td class="fw-semibold">#{{ $card->position ?? '—' }}</td>
                <td>
                    <a href="{{ route('parent.results.show', $card) }}"
                       class="btn btn-sm"
                       style="background:rgba(219,39,119,0.08);color:var(--color-rose);border:1px solid rgba(219,39,119,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
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
    </div>
    @endif
</div>

@endsection