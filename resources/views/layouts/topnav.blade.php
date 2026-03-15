<header class="app-topnav" id="appTopnav">
    <div class="topnav-left">
        {{-- Mobile sidebar toggle --}}
        <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        {{-- Page title (set per-page via @section) --}}
        <div class="topnav-breadcrumb">
            <span class="topnav-page-title">@yield('page-title', 'Dashboard')</span>
        </div>
    </div>

    <div class="topnav-right">
        {{-- Quick date display --}}
        <div class="topnav-date d-none d-md-flex">
            <i class="bi bi-calendar3 me-1"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>

        {{-- Divider --}}
        <div class="topnav-divider d-none d-md-block"></div>

        {{-- User dropdown --}}
        <div class="dropdown">
            <button class="topnav-user-btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <div class="topnav-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end topnav-dropdown">
                <li>
                    <div class="dropdown-header">
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-muted small">{{ auth()->user()->email }}</div>
                        <span class="badge bg-primary mt-1">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                @if(auth()->user()->role === 'admin')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.settings') }}">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
