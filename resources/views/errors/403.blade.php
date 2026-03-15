@extends('layouts.guest')
@section('title', '403 Unauthorized')

@section('content')
<div class="error-page">
    <div class="error-card">
        <div class="error-icon text-danger">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Access Denied</h2>
        <p class="error-message">
            You don't have permission to access this page.<br>
            Please contact your administrator if you believe this is an error.
        </p>
        @auth
            <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
            </a>
        @endauth
    </div>
</div>
@endsection
