@extends('layouts.admin')
@section('title', 'Settings')

@section('content')

<div class="page-header">
    <h1 class="page-title">School Settings</h1>
    <p class="page-subtitle">Configure your school information and preferences</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- School Information --}}
    <div class="col-md-8">
        <div class="educore-card">
            <h2 class="card-title mb-4">School Information</h2>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control"
                               value="{{ old('school_name', $settings->school_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Principal Name</label>
                        <input type="text" name="principal_name" class="form-control"
                               value="{{ old('principal_name', $settings->principal_name) }}"
                               placeholder="e.g. Dr. Ahmed Khan">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">School Email</label>
                        <input type="email" name="school_email" class="form-control"
                               value="{{ old('school_email', $settings->school_email) }}"
                               placeholder="info@school.edu.pk">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">School Phone</label>
                        <input type="text" name="school_phone" class="form-control"
                               value="{{ old('school_phone', $settings->school_phone) }}"
                               placeholder="+92 300 0000000">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">School Address</label>
                    <textarea name="school_address" class="form-control" rows="2"
                              placeholder="Full school address...">{{ old('school_address', $settings->school_address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Website</label>
                    <input type="url" name="school_website" class="form-control"
                           value="{{ old('school_website', $settings->school_website) }}"
                           placeholder="https://www.school.edu.pk">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="PKR" {{ $settings->currency === 'PKR' ? 'selected' : '' }}>PKR — Pakistani Rupee</option>
                            <option value="USD" {{ $settings->currency === 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                            <option value="EUR" {{ $settings->currency === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                            <option value="GBP" {{ $settings->currency === 'GBP' ? 'selected' : '' }}>GBP — British Pound</option>
                            <option value="AED" {{ $settings->currency === 'AED' ? 'selected' : '' }}>AED — UAE Dirham</option>
                            <option value="SAR" {{ $settings->currency === 'SAR' ? 'selected' : '' }}>SAR — Saudi Riyal</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="Asia/Karachi"  {{ $settings->timezone === 'Asia/Karachi'  ? 'selected' : '' }}>Asia/Karachi (PKT)</option>
                            <option value="Asia/Dubai"    {{ $settings->timezone === 'Asia/Dubai'    ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                            <option value="Asia/Riyadh"   {{ $settings->timezone === 'Asia/Riyadh'   ? 'selected' : '' }}>Asia/Riyadh (AST)</option>
                            <option value="Europe/London" {{ $settings->timezone === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                            <option value="America/New_York" {{ $settings->timezone === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                        </select>
                    </div>
                </div>

                <hr style="border-color:var(--color-border);margin-bottom:20px">
                <h3 style="font-size:14px;font-weight:600;margin-bottom:16px">Feature Preferences</h3>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between p-3"
                         style="background:var(--color-bg);border-radius:8px;border:1px solid var(--color-border)">
                        <div>
                            <div class="fw-semibold" style="font-size:14px">Parent-Teacher Messages</div>
                            <div class="text-muted-sm">Allow parents to message teachers directly</div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="allow_parent_messages"
                                   class="form-check-input" role="switch"
                                   style="width:44px;height:22px"
                                   {{ $settings->allow_parent_messages ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3"
                         style="background:var(--color-bg);border-radius:8px;border:1px solid var(--color-border)">
                        <div>
                            <div class="fw-semibold" style="font-size:14px">Show Class Positions</div>
                            <div class="text-muted-sm">Show student ranking on report cards</div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="show_positions"
                                   class="form-check-input" role="switch"
                                   style="width:44px;height:22px"
                                   {{ $settings->show_positions ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-4">

        {{-- Academic Years --}}
        <div class="educore-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="card-title">Academic Years</h2>
                <button class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#addYearModal">
                    + Add
                </button>
            </div>

            @foreach($academicYears as $year)
            <div class="d-flex align-items-center justify-content-between py-2"
                 style="border-bottom:1px solid var(--color-border)">
                <div>
                    <div class="fw-semibold" style="font-size:14px">{{ $year->name }}</div>
                    <div class="text-muted-sm" style="font-size:12px">
                        {{ \Carbon\Carbon::parse($year->start_date)->format('M Y') }}
                        — {{ \Carbon\Carbon::parse($year->end_date)->format('M Y') }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($year->is_active)
                    <span class="status-badge badge-active">Active</span>
                    @else
                    <form method="POST"
                          action="{{ route('admin.settings.academic-years.activate', $year) }}">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm"
                                style="background:rgba(37,99,235,0.08);color:var(--color-primary);border:1px solid rgba(37,99,235,0.2);border-radius:7px;font-size:11px;padding:3px 8px">
                            Activate
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Grading Scale --}}
        <div class="educore-card">
            <h2 class="card-title mb-3">Grading Scale</h2>
            <form method="POST" action="{{ route('admin.settings.grading') }}">
                @csrf
                @php
                    $scale = $settings->grading_scale ?? \App\Models\SchoolSetting::defaultGradingScale();
                @endphp
                <table class="educore-table" style="margin-bottom:16px">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Min %</th>
                            <th>Max %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scale as $i => $row)
                        <tr>
                            <td>
                                <input type="text" name="grades[{{ $i }}][grade]"
                                       class="form-control form-control-sm"
                                       value="{{ $row['grade'] }}" style="width:60px">
                            </td>
                            <td>
                                <input type="number" name="grades[{{ $i }}][min]"
                                       class="form-control form-control-sm"
                                       value="{{ $row['min'] }}" min="0" max="100"
                                       style="width:65px">
                            </td>
                            <td>
                                <input type="number" name="grades[{{ $i }}][max]"
                                       class="form-control form-control-sm"
                                       value="{{ $row['max'] }}" min="0" max="100"
                                       style="width:65px">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                    Update Grading Scale
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Add Academic Year Modal --}}
<div class="modal fade" id="addYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.settings.academic-years.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Year Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. 2026-2027" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1"
                               class="form-check-input" id="setActive">
                        <label class="form-check-label" for="setActive">
                            Set as active year
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Year</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection