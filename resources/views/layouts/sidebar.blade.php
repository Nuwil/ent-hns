@php
    $role    = auth()->user()->role;
    $prefix  = $role; // 'admin' | 'secretary' | 'doctor'

    // Helper: is current route active?
    $isActive = fn(string $routeName) => request()->routeIs($routeName . '*') ? 'active' : '';
@endphp

<aside class="app-sidebar" id="appSidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <i class="bi bi-ear"></i>
        </div>
        <span class="sidebar-brand-name">CarePoint</span>
        <span class="sidebar-brand-sub">ENT Clinic</span>
    </div>

    {{-- Role badge --}}
    <div class="sidebar-role-badge">
        <span class="role-pill role-{{ $role }}">{{ ucfirst($role) }}</span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- ── ADMIN ──────────────────────────────────────── --}}
        @if($role === 'admin')
            <div class="nav-section-label">Main</div>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ $isActive('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-label">Insights</div>
            <a href="{{ route('admin.analytics') }}"
               class="nav-item {{ $isActive('admin.analytics') }}">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Analytics</span>
            </a>

            <div class="nav-section-label">System</div>
            <a href="{{ route('admin.settings') }}"
               class="nav-item {{ $isActive('admin.settings') }}">
                <i class="bi bi-gear-fill"></i>
                <span>Settings</span>
            </a>
        @endif

        {{-- ── SECRETARY ───────────────────────────────────── --}}
        @if($role === 'secretary')
            <div class="nav-section-label">Main</div>
            <a href="{{ route('secretary.dashboard') }}"
               class="nav-item {{ $isActive('secretary.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-label">Clinic</div>
            <a href="{{ route('secretary.patients.index') }}"
               class="nav-item {{ $isActive('secretary.patients') }}">
                <i class="bi bi-people-fill"></i>
                <span>Patients</span>
            </a>
            <a href="{{ route('secretary.appointments.index') }}"
               class="nav-item {{ $isActive('secretary.appointments') }}">
                <i class="bi bi-calendar2-week-fill"></i>
                <span>Appointments</span>
            </a>
        @endif

        {{-- ── DOCTOR ──────────────────────────────────────── --}}
        @if($role === 'doctor')
            <div class="nav-section-label">Main</div>
            <a href="{{ route('doctor.dashboard') }}"
               class="nav-item {{ $isActive('doctor.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-label">Clinic</div>
            <a href="{{ route('doctor.patients.index') }}"
               class="nav-item {{ $isActive('doctor.patients') }}">
                <i class="bi bi-people-fill"></i>
                <span>Patients</span>
            </a>
            <a href="{{ route('doctor.appointments.index') }}"
               class="nav-item {{ $isActive('doctor.appointments') }}">
                <i class="bi bi-calendar2-week-fill"></i>
                <span>Appointments</span>
            </a>

            <div class="nav-section-label">Insights</div>
            <a href="{{ route('doctor.analytics') }}"
               class="nav-item {{ $isActive('doctor.analytics') }}">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>Analytics</span>
            </a>
        @endif

    </nav>

    {{-- Sidebar footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user-info">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-details">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ ucfirst($role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>

</aside>