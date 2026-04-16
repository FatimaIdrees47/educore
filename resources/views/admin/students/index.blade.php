@extends('layouts.admin')
@section('title', 'Students')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Students</h1>
        <p class="page-subtitle">{{ $students->total() }} students enrolled</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal">
        + Enroll Student
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

<div class="educore-card">
    @if($students->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Admission No.</th>
                <th>Class / Section</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Admitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-initials-sm">
                            {{ strtoupper(substr($student->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:14px">{{ $student->user->name }}</div>
                            <div class="text-muted-sm">{{ $student->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="font-mono" style="font-size:13px">{{ $student->admission_number }}</span></td>
                <td>
                    @if($student->currentEnrollment)
                        {{ $student->currentEnrollment->section->schoolClass->name }}
                        — {{ $student->currentEnrollment->section->name }}
                    @else
                        <span class="text-muted-sm">Not assigned</span>
                    @endif
                </td>
                <td class="text-muted-sm">{{ ucfirst($student->gender ?? '—') }}</td>
                <td><span class="status-badge badge-{{ $student->status }}">{{ ucfirst($student->status) }}</span></td>
                <td class="text-muted-sm">{{ $student->admission_date->format('M d, Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.students.show', $student) }}"
                           class="table-action-btn btn-view" title="View Profile">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                              onsubmit="return confirm('Remove {{ $student->user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action-btn btn-delete" title="Delete">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <div class="mt-3">
        {{ $students->links() }}
    </div>

    @else
    <div style="text-align:center; padding:60px 0; color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px; opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="fw-semibold" style="font-size:16px">No students enrolled yet</p>
        <p class="text-muted-sm mb-3">Get started by enrolling your first student.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal">
            Enroll First Student
        </button>
    </div>
    @endif
</div>

{{-- ── Enroll Student Modal ──────────────────────────────────── --}}
<div class="modal fade" id="enrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Enroll New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.students.store') }}">
                @csrf
                <div class="modal-body">

                    <p class="text-muted-sm mb-3" style="font-size:12px; text-transform:uppercase; font-weight:600; letter-spacing:0.5px">
                        Personal Information
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>

                    <hr style="border-color: var(--color-border)">
                    <p class="text-muted-sm mb-3 mt-3" style="font-size:12px; text-transform:uppercase; font-weight:600; letter-spacing:0.5px">
                        Admission Details
                    </p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                            <input type="text" name="admission_number" class="form-control font-mono"
                                   value="{{ $nextAdmissionNumber }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select" id="classSelect">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Section</label>
                            <select name="section_id" class="form-select" id="sectionSelect">
                                <option value="">Select Section</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control font-mono">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Class → Section dynamic loader --}}
<script>
const classSections = @json($classes->map(fn($c) => [
    'id'       => $c->id,
    'sections' => $c->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])
])->keyBy('id'));

document.getElementById('classSelect').addEventListener('change', function () {
    const classId = this.value;
    const sectionSelect = document.getElementById('sectionSelect');
    sectionSelect.innerHTML = '<option value="">Select Section</option>';

    if (classId && classSections[classId]) {
        classSections[classId].sections.forEach(function (section) {
            const opt = document.createElement('option');
            opt.value = section.id;
            opt.textContent = section.name;
            sectionSelect.appendChild(opt);
        });
    }
});
</script>

@endsection