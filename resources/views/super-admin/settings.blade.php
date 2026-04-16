@extends('layouts.super-admin')
@section('title', 'Settings')

@section('content')

<div class="page-header">
    <h1 class="page-title">Super Admin Settings</h1>
    <p class="page-subtitle">System configuration and account settings</p>
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

<div class="row g-4">
    {{-- Change Password --}}
    <div class="col-md-6">
        <div class="educore-card">
            <h2 class="card-title mb-4">Change Password</h2>
            <form method="POST" action="{{ route('super-admin.settings.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password"
                           class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="new_password"
                           class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" name="new_password_confirmation"
                           class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>

    {{-- System Info --}}
    <div class="col-md-6">
        <div class="educore-card">
            <h2 class="card-title mb-4">System Information</h2>
            <table class="educore-table">
                <tbody>
                    @foreach($systemStats as $key => $value)
                    <tr>
                        <td class="text-muted-sm" style="text-transform:capitalize">
                            {{ str_replace('_', ' ', $key) }}
                        </td>
                        <td>
                            <span class="font-mono" style="font-size:13px">{{ $value }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Super Admin Accounts --}}
    <div class="col-md-12">
        <div class="educore-card">
            <h2 class="card-title mb-3">Super Admin Accounts</h2>
            <table class="educore-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Last Login</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($superAdmins as $admin)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:8px;
                                            background:rgba(67,56,202,0.1);color:#4338CA;
                                            display:flex;align-items:center;justify-content:center;
                                            font-weight:700;font-size:12px">
                                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                                </div>
                                <span class="fw-semibold">{{ $admin->name }}</span>
                                @if($admin->id === auth()->id())
                                <span class="status-badge badge-active" style="font-size:10px">You</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-muted-sm">{{ $admin->email }}</td>
                        <td class="text-muted-sm">{{ $admin->created_at->format('M d, Y') }}</td>
                        <td class="text-muted-sm">
                            {{ $admin->last_login_at
                                ? \Carbon\Carbon::parse($admin->last_login_at)->diffForHumans()
                                : 'Never' }}
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $admin->is_active ? 'active' : 'absent' }}">
                                {{ $admin->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection