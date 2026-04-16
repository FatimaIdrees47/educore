@extends('layouts.admin')
@section('title', 'Notices')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Notice Board</h1>
        <p class="page-subtitle">Post and manage school announcements</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
        + Post Notice
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalNotices }}</div>
                <div class="stat-label">Total Notices</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $activeNotices }}</div>
                <div class="stat-label">Active Notices</div>
            </div>
        </div>
    </div>
</div>

{{-- Notices List --}}
<div class="educore-card">
    @if($notices->count() > 0)

    @foreach($notices as $notice)
    <div class="d-flex gap-3 py-3"
         style="border-bottom: 1px solid var(--color-border)">

        {{-- Role Color Bar --}}
        <div style="width:4px;border-radius:4px;flex-shrink:0;
                    background:{{ $notice->target_role === 'all' ? 'var(--color-primary)' :
                                 ($notice->target_role === 'teacher' ? 'var(--portal-teacher)' :
                                 ($notice->target_role === 'student' ? 'var(--portal-student)' :
                                 ($notice->target_role === 'parent' ? 'var(--portal-parent)' :
                                 'var(--color-text-mid)'))) }}">
        </div>

        <div class="flex-fill">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                    <div class="fw-semibold" style="font-size:15px">{{ $notice->title }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="status-badge badge-{{ $notice->is_active ? 'active' : 'draft' }}"
                              style="font-size:11px">
                            {{ $notice->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-muted-sm">
                            For:
                            <strong>{{ $notice->target_role === 'all' ? 'Everyone' : ucfirst($notice->target_role) }}</strong>
                            @if($notice->targetClass)
                                · {{ $notice->targetClass->name }}
                            @endif
                        </span>
                        <span class="text-muted-sm">·</span>
                        <span class="text-muted-sm">
                            Posted {{ $notice->created_at->diffForHumans() }}
                            by {{ $notice->postedBy->name }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('admin.notices.edit', $notice) }}"
                       class="btn btn-sm"
                       style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:5px 10px">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}"
                          onsubmit="return confirm('Delete this notice?')">
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
            </div>

            <p style="font-size:13px;color:var(--color-text-sub);margin-top:8px;
                      display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                {{ $notice->body }}
            </p>

            @if($notice->expires_at)
            <div class="text-muted-sm mt-1" style="font-size:12px">
                Expires: {{ $notice->expires_at->format('M d, Y') }}
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-3">{{ $notices->links() }}</div>

    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
        </svg>
        <p class="fw-semibold" style="font-size:16px">No notices yet</p>
        <p class="text-muted-sm mb-3">Post your first announcement.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
            + Post First Notice
        </button>
    </div>
    @endif
</div>

{{-- Post Notice Modal --}}
<div class="modal fade" id="addNoticeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Post New Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.notices.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="e.g. School Holiday Notice" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="4"
                                  placeholder="Write the notice content here..." required></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                            <select name="target_role" class="form-select" required>
                                <option value="all">Everyone</option>
                                <option value="teacher">Teachers Only</option>
                                <option value="student">Students Only</option>
                                <option value="parent">Parents Only</option>
                                <option value="school-admin">Admin Only</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target Class (Optional)</label>
                            <select name="target_class_id" class="form-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Publish Date</label>
                            <input type="datetime-local" name="published_at"
                                   class="form-control"
                                   value="{{ now()->format('Y-m-d\TH:i') }}">
                            <div class="form-text">Leave as now to publish immediately</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date (Optional)</label>
                            <input type="datetime-local" name="expires_at" class="form-control">
                            <div class="form-text">Leave empty for no expiry</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection