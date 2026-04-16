<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — EduCore</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 20px;">

<div style="width:100%;max-width:440px">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:32px">
        <div style="display:inline-flex;align-items:center;gap:10px">
            <div style="width:44px;height:44px;background:var(--color-primary);border-radius:12px;
                        display:flex;align-items:center;justify-content:center">
                <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span style="font-size:26px;font-weight:800;color:#fff;letter-spacing:-0.5px">
                Edu<span style="color:var(--color-primary)">Core</span>
            </span>
        </div>
        <p style="color:#94A3B8;font-size:14px;margin-top:8px">
            School Management System
        </p>
    </div>

    {{-- Card --}}
    <div style="background:#fff;border-radius:16px;padding:36px;box-shadow:0 25px 50px rgba(0,0,0,0.25)">

        <h2 style="font-size:20px;font-weight:700;color:var(--color-text-dark);margin-bottom:4px">
            Welcome back
        </h2>
        <p style="font-size:14px;color:var(--color-text-mid);margin-bottom:28px">
            Sign in to your portal
        </p>

        {{-- Session Status --}}
        @if(session('status'))
        <div class="alert alert-success mb-3" style="font-size:13px">
            {{ session('status') }}
        </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="your@email.com"
                       autofocus required>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0">Password</label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       style="font-size:12px;color:var(--color-primary);text-decoration:none">
                        Forgot password?
                    </a>
                    @endif
                </div>
                <input type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required>
            </div>

            <div class="mb-4 d-flex align-items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                       class="form-check-input" style="margin:0">
                <label for="remember" style="font-size:13px;color:var(--color-text-sub);cursor:pointer">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100"
                    style="padding:12px;font-size:15px;font-weight:600">
                Sign In
            </button>
        </form>

        {{-- Role Hint --}}
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--color-border)">
            <p style="font-size:12px;color:var(--color-text-mid);text-align:center;margin-bottom:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px">
                Demo Accounts
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                @foreach([
                    ['label' => 'Super Admin', 'email' => 'superadmin@educore.com', 'color' => '#4338CA'],
                    ['label' => 'School Admin', 'email' => 'admin@democschool.com', 'color' => '#2563EB'],
                    ['label' => 'Teacher', 'email' => 'teacher@democschool.com', 'color' => '#059669'],
                    ['label' => 'Student', 'email' => 'ayesha@demo.com', 'color' => '#7C3AED'],
                ] as $demo)
                <button type="button"
                        onclick="document.querySelector('[name=email]').value='{{ $demo['email'] }}';document.querySelector('[name=password]').value='password'"
                        style="background:transparent;border:1px solid {{ $demo['color'] }}22;border-radius:8px;
                               padding:8px 10px;cursor:pointer;text-align:left;transition:all 0.15s">
                    <div style="font-size:11px;font-weight:600;color:{{ $demo['color'] }}">
                        {{ $demo['label'] }}
                    </div>
                    <div style="font-size:10px;color:var(--color-text-mid);margin-top:1px;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $demo['email'] }}
                    </div>
                </button>
                @endforeach
            </div>
            <p style="font-size:11px;color:var(--color-text-mid);text-align:center;margin-top:10px">
                Click any card to auto-fill · Password: <span class="font-mono">password</span>
            </p>
        </div>
    </div>

    <p style="text-align:center;color:#475569;font-size:12px;margin-top:20px">
        © {{ date('Y') }} EduCore. All rights reserved.
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>