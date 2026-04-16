@extends('layouts.admin')
@section('title', 'Edit Notice')

@section('content')

<div class="page-header d-flex align-items-center gap-3">
    <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-primary btn-sm">← Back</a>
    <div>
        <h1 class="page-title">Edit Notice</h1>
        <p class="page-subtitle">{{ $notice->title }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="educore-card">
            <form method="POST" action="{{ route('admin.notices.update', $notice) }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', $notice->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Body <span class="text-danger">*</span></label>
                    <textarea name="body" class="form-control" rows="5" required>{{ old('body', $notice->body) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select name="target_role" class="form-select" required>
                            @foreach(['all' => 'Everyone', 'teacher' => 'Teachers Only',
                                      'student' => 'Students Only', 'parent' => 'Parents Only',
                                      'school-admin' => 'Admin Only'] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('target_role', $notice->target_role) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Target Class</label>
                        <select name="target_class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ old('target_class_id', $notice->target_class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                               value="{{ old('published_at', $notice->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="datetime-local" name="expires_at" class="form-control"
                               value="{{ old('expires_at', $notice->expires_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-primary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection