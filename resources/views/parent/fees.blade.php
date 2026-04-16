@extends('layouts.parent')
@section('title', 'Fees')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Fee Status</h1>
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

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">PKR {{ number_format($totalPaid / 100, 0) }}</div>
                <div class="stat-label">Total Paid</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">PKR {{ number_format($totalPending / 100, 0) }}</div>
                <div class="stat-label">Total Pending</div>
            </div>
        </div>
    </div>
</div>

<div class="educore-card">
    @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator && $invoices->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Receipt No.</th>
                <th>Fee Type</th>
                <th>Month</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td><span class="font-mono" style="font-size:13px">{{ $invoice->receipt_number }}</span></td>
                <td class="fw-semibold">{{ $invoice->fee_type }}</td>
                <td class="text-muted-sm">
                    {{ $invoice->month ? \Carbon\Carbon::createFromDate($invoice->year, $invoice->month, 1)->format('M Y') : '—' }}
                </td>
                <td class="fw-semibold">PKR {{ number_format($invoice->net_amount / 100, 0) }}</td>
                <td class="text-muted-sm">{{ $invoice->due_date->format('M d, Y') }}</td>
                <td>
                    <span class="status-badge badge-{{ $invoice->status === 'paid' ? 'active' : ($invoice->status === 'partial' ? 'late' : 'absent') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $invoices->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No fee invoices yet.</p>
    </div>
    @endif
</div>

@endsection