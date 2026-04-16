@extends('layouts.admin')
@section('title', 'Book Issues')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.library.index') }}" class="btn btn-outline-primary btn-sm">
            ← Books
        </a>
        <div>
            <h1 class="page-title">Book Issues</h1>
            <p class="page-subtitle">Issue and return books</p>
        </div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueBookModal">
        + Issue Book
    </button>
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

{{-- Filter Tabs --}}
<div class="d-flex gap-2 mb-4">
    @foreach(['all' => 'All', 'active' => 'Active', 'overdue' => 'Overdue', 'returned' => 'Returned'] as $val => $label)
    <a href="{{ route('admin.library.issues', ['filter' => $val]) }}"
       class="btn btn-sm"
       style="border-radius:8px;font-size:13px;padding:6px 16px;
              {{ $filter === $val
                  ? 'background:var(--color-primary);color:#fff;border:1px solid var(--color-primary)'
                  : 'background:transparent;color:var(--color-text-sub);border:1px solid var(--color-border)' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="educore-card">
    @if($issues->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Book</th>
                <th>Issued To</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Fine</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issues as $issue)
            <tr>
                <td>
                    <div class="fw-semibold" style="font-size:13px">
                        {{ $issue->book->title }}
                    </div>
                    <div class="text-muted-sm" style="font-size:11px">
                        {{ $issue->book->author ?? '' }}
                    </div>
                </td>
                <td>
                    <div class="fw-semibold" style="font-size:13px">
                        {{ $issue->issuedTo->name }}
                    </div>
                </td>
                <td class="font-mono" style="font-size:12px">
                    {{ $issue->issue_date->format('M d, Y') }}
                </td>
                <td class="font-mono" style="font-size:12px
                    {{ $issue->is_overdue ? ';color:var(--color-danger);font-weight:600' : '' }}">
                    {{ $issue->due_date->format('M d, Y') }}
                    @if($issue->is_overdue)
                    <div style="font-size:10px">{{ $issue->days_overdue }}d overdue</div>
                    @endif
                </td>
                <td class="font-mono" style="font-size:12px">
                    {{ $issue->return_date?->format('M d, Y') ?? '—' }}
                </td>
                <td>
                    <span class="status-badge badge-{{
                        $issue->status === 'returned' ? 'active' :
                        ($issue->status === 'overdue' ? 'absent' : 'draft') }}">
                        {{ ucfirst($issue->status) }}
                    </span>
                </td>
                <td class="text-muted-sm" style="font-size:13px">
                    @if($issue->fine_amount > 0)
                    <span style="color:var(--color-danger);font-weight:600">
                        PKR {{ number_format($issue->fine_amount / 100, 0) }}
                    </span>
                    @else
                    —
                    @endif
                </td>
                <td>
                    @if(!$issue->return_date)
                    <form method="POST"
                          action="{{ route('admin.library.issues.return', $issue) }}">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm"
                                style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;padding:5px 10px">
                            Return
                        </button>
                    </form>
                    @else
                    <span class="text-muted-sm" style="font-size:12px">Returned</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $issues->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No book issues found.</p>
    </div>
    @endif
</div>

{{-- Issue Book Modal --}}
<div class="modal fade" id="issueBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Issue Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.library.issues.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Book <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select" required>
                            <option value="">Select Book</option>
                            @foreach($books as $book)
                            <option value="{{ $book->id }}">
                                {{ $book->title }}
                                ({{ $book->available_copies }} available)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issue To <span class="text-danger">*</span></label>
                        <select name="issued_to" class="form-select" required>
                            <option value="">Select Member</option>
                            @foreach($members as $member)
                            <option value="{{ $member->id }}">
                                {{ $member->name }}
                                ({{ $member->getRoleNames()->first() }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                               required>
                        <div class="form-text">Default is 14 days from today</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control"
                               placeholder="Optional notes...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Issue Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection