<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ENT Clinic') — CarePoint</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- App Styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="app-body">

{{-- ================================================================
     LAYOUT STRUCTURE
     sidebar | [topnav + content]
     The sidebar NEVER re-renders. Only #main-content changes.
     ================================================================ --}}

<div class="app-wrapper">

    {{-- ── SIDEBAR ──────────────────────────────────────────────── --}}
    @include('layouts.sidebar')

    {{-- ── RIGHT PANEL ──────────────────────────────────────────── --}}
    <div class="app-panel" id="appPanel">

        {{-- ── TOP NAVBAR ───────────────────────────────────────── --}}
        @include('layouts.topnav')

        {{-- ── MAIN CONTENT ─────────────────────────────────────── --}}
        <main class="app-content" id="mainContent">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Page content --}}
            @yield('content')

        </main>
    </div>
</div>

{{-- Toast Notifications --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">

    @if(session('toast_error'))
        <div class="toast toast-error align-items-center show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-shield-exclamation-fill me-2"></i>{{ session('toast_error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(session('toast_success'))
        <div class="toast toast-success align-items-center show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('toast_success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

</div>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- App JS --}}
<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts')
</body>
</html>