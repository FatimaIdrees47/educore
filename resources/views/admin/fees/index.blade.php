@extends('layouts.admin')
@section('title', 'Fee Management')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Fee Management</h1>
        <p class="page-subtitle">Manage fee structures, invoices and collections</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
            Generate Invoices
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStructureModal">
            + Fee Structure
        </button>
    </div>
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

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number" style="font-size:22px">
                    PKR {{ number_format($summary['total_invoiced'] / 100, 0) }}
                </div>
                <div class="stat-label">Total Invoiced</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number" style="font-size:22px">
                    PKR {{ number_format($summary['total_collected'] / 100, 0) }}
                </div>
                <div class="stat-label">Collected</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number" style="font-size:22px">
                    PKR {{ number_format($summary['total_pending'] / 100, 0) }}
                </div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-rose">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $summary['defaulters'] }}</div>
                <div class="stat-label">Defaulters</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Fee Structures --}}
    <div class="col-md-4">
        <div class="educore-card">
            <h2 class="card-title mb-3">Fee Structures</h2>

            @forelse($structures as $structure)
            <div class="d-flex align-items-center justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <div>
                    <div class="fw-semibold" style="font-size:14px">{{ $structure->fee_type }}</div>
                    <div class="text-muted-sm">
                        {{ $structure->schoolClass->name }} ·
                        PKR {{ $structure->amount_in_rupees }} ·
                        {{ ucfirst($structure->frequency) }}
                    </div>
                </div>
                <form method="POST"
                      action="{{ route('admin.fees.structures.destroy', $structure) }}"
                      onsubmit="return confirm('Delete this fee structure?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="btn btn-sm"
                            style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;padding:5px 8px">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div style="text-align:center;padding:30px 0;color:var(--color-text-mid)">
                <p class="text-muted-sm">No fee structures yet.</p>
                <button class="btn btn-primary btn-sm mt-2"
                        data-bs-toggle="modal" data-bs-target="#addStructureModal">
                    + Add Structure
                </button>
            </div>
            @endforelse
        </div>

        {{-- Defaulters --}}
        @if($defaulters->count() > 0)
        <div class="educore-card mt-3">
            <h2 class="card-title mb-3" style="color:var(--color-danger)">
                Overdue Payments ({{ $defaulters->count() }})
            </h2>
            @foreach($defaulters->take(5) as $invoice)
            <div class="d-flex align-items-center justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <div>
                    <div class="fw-semibold" style="font-size:13px">
                        {{ $invoice->student->user->name }}
                    </div>
                    <div class="text-muted-sm">
                        {{ $invoice->fee_type }} · Due {{ $invoice->due_date->format('M d') }}
                    </div>
                </div>
                <span class="fw-semibold" style="font-size:13px;color:var(--color-danger)">
                    PKR {{ $invoice->net_amount_in_rupees }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Invoices --}}
    <div class="col-md-8">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Fee Invoices</h2>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width:auto"
                            onchange="window.location.href='?status='+this.value">
                        <option value="">All Status</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid"   {{ request('status') === 'paid'   ? 'selected' : '' }}>Paid</option>
                        <option value="partial"{{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>
            </div>

            @if($invoices->count() > 0)
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Receipt No.</th>
                        <th>Fee Type</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="font-size:13px">
                                {{ $invoice->student->user->name }}
                            </div>
                        </td>
                        <td>
                            <span class="font-mono" style="font-size:12px">
                                {{ $invoice->receipt_number }}
                            </span>
                        </td>
                        <td class="text-muted-sm">{{ $invoice->fee_type }}</td>
                        <td class="text-muted-sm">
                            {{ $invoice->month ? \Carbon\Carbon::createFromDate($invoice->year, $invoice->month, 1)->format('M Y') : '—' }}
                        </td>
                        <td class="fw-semibold">PKR {{ $invoice->net_amount_in_rupees }}</td>
                        <td class="text-muted-sm">{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="status-badge badge-{{ $invoice->status === 'paid' ? 'active' : ($invoice->status === 'partial' ? 'late' : 'absent') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td>
                            @if($invoice->status !== 'paid')
                            <button class="btn btn-sm"
                                    style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;padding:5px 10px;font-size:12px"
                                    data-bs-toggle="modal"
                                    data-bs-target="#collectModal"
                                    data-invoice-id="{{ $invoice->id }}"
                                    data-student="{{ $invoice->student->user->name }}"
                                    data-amount="{{ $invoice->net_amount / 100 }}"
                                    data-receipt="{{ $invoice->receipt_number }}">
                                Collect
                            </button>
                            @else
                            <span class="text-muted-sm" style="font-size:12px">
                                {{ $invoice->paid_at?->format('M d') }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">{{ $invoices->links() }}</div>

            @else
            <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin-bottom:16px;opacity:0.35">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="fw-semibold">No invoices yet</p>
                <p class="text-muted-sm mb-3">Create a fee structure and generate invoices.</p>
                <button class="btn btn-primary"
                        data-bs-toggle="modal" data-bs-target="#generateModal">
                    Generate Invoices
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Modals ────────────────────────────────────────────────── --}}

{{-- Add Fee Structure Modal --}}
<div class="modal fade" id="addStructureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.fees.structures.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                        <input type="text" name="fee_type" class="form-control"
                               placeholder="e.g. Tuition Fee, Transport, Lab Fee" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control"
                                   placeholder="e.g. 5000" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Day <span class="text-danger">*</span></label>
                            <input type="number" name="due_day" class="form-control"
                                   value="10" min="1" max="28">
                            <div class="form-text">Day of month payment is due</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency <span class="text-danger">*</span></label>
                        <select name="frequency" class="form-select" required>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                            <option value="one-time">One Time</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Generate Invoices Modal --}}
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Generate Monthly Invoices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.fees.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}"
                                        {{ $m == date('n') ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control"
                                   value="{{ date('Y') }}" min="2020" max="2030" required>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3" style="font-size:13px">
                        This will generate invoices for all active students in the selected class
                        based on the fee structures defined. Already generated invoices are skipped.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Collect Payment Modal --}}
<div class="modal fade" id="collectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Collect Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="collectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 p-3"
                         style="background:var(--color-bg);border-radius:8px">
                        <div class="text-muted-sm">Student</div>
                        <div class="fw-semibold" id="collectStudentName"></div>
                        <div class="text-muted-sm mt-1">Receipt: <span id="collectReceipt" class="font-mono"></span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="collectAmount"
                               class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference / Transaction ID</label>
                        <input type="text" name="reference" class="form-control"
                               placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('collectModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('collectStudentName').textContent = btn.getAttribute('data-student');
        document.getElementById('collectReceipt').textContent    = btn.getAttribute('data-receipt');
        document.getElementById('collectAmount').value           = btn.getAttribute('data-amount');
        document.getElementById('collectForm').action =
            '/admin/fees/' + btn.getAttribute('data-invoice-id') + '/collect';
    });
});
</script>

@endsection