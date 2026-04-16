@extends('layouts.admin')
@section('title', 'Edit Teacher')

@section('content')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
    <div>
        <h1 class="page-title">Edit Teacher</h1>
        <p class="page-subtitle">{{ $teacher->user->name }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="educore-card">
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
                @csrf @method('PUT')

                <p style="font-size:11px;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;color:var(--color-text-mid)" class="mb-3">
                    Personal Information
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $teacher->user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control"
                               value="{{ $teacher->user->email }}" disabled>
                        <div class="form-text">Email cannot be changed here.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $teacher->user->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Qualifications</label>
                        <input type="text" name="qualifications" class="form-control"
                               value="{{ old('qualifications', $teacher->qualifications) }}">
                    </div>
                </div>

                <hr style="border-color:var(--color-border)">
                <p style="font-size:11px;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;color:var(--color-text-mid)" class="mb-3 mt-3">
                    Employment Details
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control font-mono"
                               value="{{ old('employee_id', $teacher->employee_id) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control"
                               value="{{ old('joining_date', $teacher->joining_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Leave Balance (days)</label>
                        <input type="number" name="leave_balance" class="form-control"
                               value="{{ old('leave_balance', $teacher->leave_balance) }}" min="0" max="365">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monthly Salary (PKR)</label>
                        <input type="number" name="salary" class="form-control"
                               value="{{ old('salary', $teacher->salary / 100) }}" min="0">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-primary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection