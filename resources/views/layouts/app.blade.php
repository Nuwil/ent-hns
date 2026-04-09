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

{{-- ── GLOBAL TOAST CONTAINER ──────────────────────────────────── --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:11000" id="globalToastContainer">

    @if(session('toast_error'))
        <div class="toast app-toast app-toast-error align-items-center show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('toast_error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(session('toast_success'))
        <div class="toast app-toast app-toast-success align-items-center show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('toast_success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    {{-- JS-triggered toast (hidden template) --}}
    <div class="toast app-toast align-items-center" id="jsToast" role="alert"
         data-bs-autohide="true" data-bs-delay="4000" style="display:none">
        <div class="d-flex">
            <div class="toast-body" id="jsToastBody"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

</div>

{{-- ── GLOBAL CONFIRM MODAL ─────────────────────────────────────── --}}
<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true" style="z-index:11001">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg" style="border-radius:16px;overflow:hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4" id="globalConfirmHeader">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div id="globalConfirmIcon"
                         style="width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
                    </div>
                    <h5 class="modal-title mb-0 fw-bold" id="globalConfirmTitle" style="font-size:15px"></h5>
                </div>
            </div>
            <div class="modal-body px-4 pt-2 pb-3">
                <p class="text-muted mb-0" id="globalConfirmMessage" style="font-size:13.5px;line-height:1.5"></p>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                <button type="button" class="btn btn-outline-secondary flex-fill"
                        data-bs-dismiss="modal" id="globalConfirmCancelBtn">Cancel</button>
                <button type="button" class="btn flex-fill fw-semibold"
                        id="globalConfirmOkBtn"></button>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- App JS --}}
<script src="{{ asset('js/app.js') }}"></script>

<style>
/* ── App Toasts ───────────────────────────────────────────────── */
.app-toast {
    min-width: 280px;
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    font-size: 13.5px;
    font-weight: 500;
}
.app-toast .toast-body { padding: 14px 16px; }
.app-toast-success { background: #16a34a; color: white; }
.app-toast-error   { background: #dc2626; color: white; }
.app-toast-warning { background: #d97706; color: white; }
.app-toast-info    { background: #2563eb; color: white; }
.app-toast .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
</style>

<script>
// ── Global Toast ──────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('globalToastContainer');
    const types = {
        success: { bg: '#16a34a', icon: 'bi-check-circle-fill' },
        error:   { bg: '#dc2626', icon: 'bi-x-circle-fill' },
        warning: { bg: '#d97706', icon: 'bi-exclamation-triangle-fill' },
        info:    { bg: '#2563eb', icon: 'bi-info-circle-fill' },
    };
    const t = types[type] || types.info;

    const el = document.createElement('div');
    el.className = 'toast app-toast align-items-center show';
    el.style.background = t.bg;
    el.style.color = 'white';
    el.setAttribute('data-bs-autohide', 'true');
    el.setAttribute('data-bs-delay', '4000');
    el.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${t.icon} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
    container.appendChild(el);
    const toast = new bootstrap.Toast(el);
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

// ── Global Confirm Modal ──────────────────────────────────────
// Usage: showConfirm({ title, message, type, okText, onConfirm })
// type: 'danger' | 'warning' | 'primary'
function showConfirm({ title, message, type = 'danger', okText = 'Confirm', cancelText = 'Cancel', onConfirm }) {
    const configs = {
        danger:  { bg: '#fee2e2', color: '#dc2626', icon: 'bi-exclamation-triangle-fill', btnClass: 'btn-danger' },
        warning: { bg: '#fef9c3', color: '#d97706', icon: 'bi-exclamation-circle-fill',   btnClass: 'btn-warning' },
        primary: { bg: '#dbeafe', color: '#2563eb', icon: 'bi-info-circle-fill',           btnClass: 'btn-primary' },
        success: { bg: '#dcfce7', color: '#16a34a', icon: 'bi-check-circle-fill',          btnClass: 'btn-success' },
    };
    const cfg = configs[type] || configs.danger;

    const iconEl   = document.getElementById('globalConfirmIcon');
    const titleEl  = document.getElementById('globalConfirmTitle');
    const msgEl    = document.getElementById('globalConfirmMessage');
    const okBtn    = document.getElementById('globalConfirmOkBtn');
    const cancelBtn = document.getElementById('globalConfirmCancelBtn');

    iconEl.style.background = cfg.bg;
    iconEl.style.color      = cfg.color;
    iconEl.innerHTML        = `<i class="bi ${cfg.icon}"></i>`;
    titleEl.textContent     = title;
    msgEl.textContent       = message;
    okBtn.textContent       = okText;
    okBtn.className         = `btn flex-fill fw-semibold ${cfg.btnClass}`;
    cancelBtn.textContent   = cancelText;

    const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));

    // Remove previous listeners
    const newOkBtn = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOkBtn, okBtn);
    newOkBtn.className = `btn flex-fill fw-semibold ${cfg.btnClass}`;
    newOkBtn.textContent = okText;

    newOkBtn.addEventListener('click', () => {
        modal.hide();
        if (typeof onConfirm === 'function') onConfirm();
    });

    modal.show();
}

// Auto-init server-side toasts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.app-toast.show').forEach(el => {
        new bootstrap.Toast(el).show();
    });
});
</script>

@stack('scripts')
</body>
</html>