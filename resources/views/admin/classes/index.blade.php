@extends('layouts.admin')
@section('title', 'Classes & Sections')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Classes & Sections</h1>
        <p class="page-subtitle">Manage your school's academic structure</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
        + Add Class
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalClasses }}</div>
                <div class="stat-label">Total Classes</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalSections }}</div>
                <div class="stat-label">Total Sections</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalSubjects }}</div>
                <div class="stat-label">Total Subjects</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="educore-card">
            <h2 class="card-title mb-4">Classes & Sections</h2>

            @forelse($classes as $class)
            <div class="mb-4" style="border: 1px solid var(--color-border); border-radius: 10px; overflow: hidden;">

                {{-- Class Header --}}
                <div class="d-flex align-items-center justify-content-between px-3 py-3"
                     style="background: var(--color-bg); border-bottom: 1px solid var(--color-border)">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:36px;height:36px;border-radius:8px;background:rgba(37,99,235,0.1);
                                    display:flex;align-items:center;justify-content:center;color:var(--color-primary)">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:15px">{{ $class->name }}</div>
                            <div class="text-muted-sm">{{ $class->sections->count() }} {{ Str::plural('section', $class->sections->count()) }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm d-flex align-items-center gap-1"
                                style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;padding:5px 10px;font-size:12px;font-weight:500"
                                data-bs-toggle="modal"
                                data-bs-target="#editClassModal"
                                data-id="{{ $class->id }}"
                                data-name="{{ $class->name }}"
                                data-order="{{ $class->numeric_order }}"
                                title="Edit Class">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>

                        <button class="btn btn-sm d-flex align-items-center gap-1"
                                style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;padding:5px 10px;font-size:12px;font-weight:500"
                                data-bs-toggle="modal"
                                data-bs-target="#addSectionModal"
                                data-class-id="{{ $class->id }}"
                                data-class-name="{{ $class->name }}"
                                title="Add Section">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Section
                        </button>

                        <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                              onsubmit="return confirm('Delete {{ $class->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm d-flex align-items-center gap-1"
                                    style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;padding:5px 10px;font-size:12px;font-weight:500"
                                    title="Delete Class">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Sections Table --}}
                @if($class->sections->count() > 0)
                <table class="educore-table" style="margin:0">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Class Teacher</th>
                            <th>Capacity</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($class->sections as $section)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:28px;height:28px;border-radius:6px;background:rgba(124,58,237,0.1);
                                                display:flex;align-items:center;justify-content:center;
                                                font-size:11px;font-weight:700;color:var(--color-purple)">
                                        {{ $section->name }}
                                    </div>
                                    <span class="fw-semibold">{{ $class->name }} — {{ $section->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($section->classTeacher)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initials-sm" style="font-size:11px;background:var(--portal-teacher)">
                                        {{ strtoupper(substr($section->classTeacher->name, 0, 2)) }}
                                    </div>
                                    <span style="font-size:13px">{{ $section->classTeacher->name }}</span>
                                </div>
                                @else
                                <span class="text-muted-sm">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <svg width="13" height="13" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" style="color:var(--color-text-mid)">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span style="font-size:13px">{{ $section->capacity }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Edit Section --}}
                                    <button class="btn btn-sm"
                                            style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:12px;padding:4px 8px"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSectionModal"
                                            data-id="{{ $section->id }}"
                                            data-name="{{ $section->name }}"
                                            data-capacity="{{ $section->capacity }}"
                                            data-teacher="{{ $section->class_teacher_id ?? '' }}"
                                            title="Edit Section">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete Section --}}
                                    <form method="POST"
                                          action="{{ route('admin.sections.destroy', $section) }}"
                                          onsubmit="return confirm('Delete section {{ $section->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm"
                                                style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:4px 8px"
                                                title="Delete Section">
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
                @else
                <div class="px-3 py-3 text-muted-sm" style="font-size:13px">
                    No sections yet —
                    <button class="btn btn-link p-0"
                            style="font-size:13px;color:var(--color-primary);text-decoration:none"
                            data-bs-toggle="modal"
                            data-bs-target="#addSectionModal"
                            data-class-id="{{ $class->id }}"
                            data-class-name="{{ $class->name }}">
                        add the first section
                    </button>
                </div>
                @endif

            </div>
            @empty
            <div style="text-align:center; padding: 60px 0; color: var(--color-text-mid)">
                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin-bottom:16px; opacity:0.35">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
                <p class="fw-semibold" style="font-size:16px">No classes yet</p>
                <p class="text-muted-sm mb-3">Get started by creating your first class.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    + Add First Class
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Subjects Panel --}}
    <div class="col-md-4">
        <div class="educore-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Subjects</h2>
                <button class="btn btn-outline-primary btn-sm"
                    data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    + Add Subject
                </button>
            </div>

            @forelse($subjects as $subject)
            <div class="d-flex align-items-center justify-content-between py-2"
                style="border-bottom: 1px solid var(--color-border)">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;border-radius:7px;flex-shrink:0;
                                display:flex;align-items:center;justify-content:center;
                                background:{{ $subject->type === 'core' ? 'rgba(5,150,105,0.1)' : ($subject->type === 'elective' ? 'rgba(37,99,235,0.1)' : 'rgba(217,119,6,0.1)') }};
                                color:{{ $subject->type === 'core' ? 'var(--color-success)' : ($subject->type === 'elective' ? 'var(--color-primary)' : 'var(--color-amber)') }}">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:14px">{{ $subject->name }}</div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            @if($subject->code)
                            <span class="font-mono text-muted-sm" style="font-size:11px">{{ $subject->code }}</span>
                            <span class="text-muted-sm">·</span>
                            @endif
                            <span class="status-badge badge-{{ $subject->type === 'core' ? 'active' : ($subject->type === 'elective' ? 'draft' : 'pending') }}"
                                style="font-size:10px;padding:2px 8px">
                                {{ ucfirst($subject->type) }}
                            </span>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                    onsubmit="return confirm('Delete {{ $subject->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="btn btn-sm"
                            style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;padding:5px 8px"
                            title="Delete Subject">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div style="text-align:center;padding:30px 0;color:var(--color-text-mid)">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin-bottom:10px;opacity:0.35">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-muted-sm">No subjects yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Modals ────────────────────────────────────────────────── --}}

{{-- Add Class Modal --}}
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.classes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            placeholder="e.g. Grade 5, A-Level Year 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="numeric_order" class="form-control"
                            placeholder="e.g. 5" min="0">
                        <div class="form-text">Used for sorting classes in order (1, 2, 3...)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Class Modal --}}
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editClassForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editClassName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="numeric_order" id="editClassOrder" class="form-control" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Section Modal --}}
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add Section to <span id="sectionClassName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.sections.store') }}">
                @csrf
                <input type="hidden" name="class_id" id="sectionClassId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. A, B, Blue, Red" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control"
                               value="30" min="1" max="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Teacher</label>
                        <select name="class_teacher_id" class="form-select">
                            <option value="">Not Assigned</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->user_id }}">
                                {{ $teacher->user->name }}
                                @if($teacher->employee_id) ({{ $teacher->employee_id }}) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Section Modal --}}
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editSectionForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editSectionName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" id="editSectionCapacity"
                               class="form-control" min="1" max="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Teacher</label>
                        <select name="class_teacher_id" id="editSectionTeacher" class="form-select">
                            <option value="">Not Assigned</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->user_id }}">
                                {{ $teacher->user->name }}
                                @if($teacher->employee_id) ({{ $teacher->employee_id }}) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Subject Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            placeholder="e.g. Mathematics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" name="code" class="form-control"
                            placeholder="e.g. MATH-01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="core">Core</option>
                            <option value="elective">Elective</option>
                            <option value="lab">Lab</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('addSectionModal').addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('sectionClassId').value = btn.getAttribute('data-class-id');
            document.getElementById('sectionClassName').textContent = btn.getAttribute('data-class-name');
        });

        document.getElementById('editClassModal').addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('editClassName').value  = btn.getAttribute('data-name');
            document.getElementById('editClassOrder').value = btn.getAttribute('data-order');
            document.getElementById('editClassForm').action =
                '/admin/classes/' + btn.getAttribute('data-id');
        });

        document.getElementById('editSectionModal').addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('editSectionName').value     = btn.getAttribute('data-name');
            document.getElementById('editSectionCapacity').value = btn.getAttribute('data-capacity');
            document.getElementById('editSectionTeacher').value  = btn.getAttribute('data-teacher');
            document.getElementById('editSectionForm').action    =
                '/admin/sections/' + btn.getAttribute('data-id');
        });
    });
</script>

@endsection