@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="login-page">
    <div class="login-left">
        <div class="login-brand">
            <div class="login-logo"><i class="bi bi-ear"></i></div>
            <h1 class="login-brand-name">CarePoint</h1>
            <p class="login-brand-tagline">ENT Clinic Management System</p>
        </div>
        <div class="login-illustration">
            <div class="login-stat-card">
                <i class="bi bi-people-fill"></i>
                <div>
                    <div class="stat-num">1,200+</div>
                    <div class="stat-label">Patients Served</div>
                </div>
            </div>
            <div class="login-stat-card">
                <i class="bi bi-calendar-check-fill"></i>
                <div>
                    <div class="stat-num">98%</div>
                    <div class="stat-label">Appointment Rate</div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-right">
        <div class="login-form-wrapper">
            <div class="login-form-header">
                <h2>Welcome back</h2>
                <p>Sign in to your clinic account</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control input-with-icon @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="doctor@entclinic.com"
                            autofocus
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control input-with-icon @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordEyeIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login w-100">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('passwordEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
