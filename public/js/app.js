/**
 * CarePoint ENT — Application JavaScript
 *
 * Handles:
 * - Mobile sidebar toggle
 * - CSRF token injection for all fetch/XHR requests
 * - No unexpected logouts (session-safe approach)
 */

(function () {
    'use strict';

    // ── CSRF Token Setup ──────────────────────────────────────────
    // Read the meta tag set in layouts/app.blade.php
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    // Inject CSRF into all fetch requests automatically
    const _fetch = window.fetch;
    window.fetch = function (url, options = {}) {
        if (csrfToken && typeof url === 'string' && !url.startsWith('http')) {
            options.headers = {
                'X-CSRF-TOKEN': csrfToken,
                ...(options.headers || {}),
            };
        }
        return _fetch(url, options);
    };

    // ── Mobile Sidebar Toggle ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar        = document.getElementById('appSidebar');
        const toggleBtn      = document.getElementById('sidebarToggle');
        const panel          = document.getElementById('appPanel');

        if (!sidebar || !toggleBtn) return;

        // Create overlay for mobile
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.id = 'sidebarOverlay';
        document.body.appendChild(overlay);

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        toggleBtn.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        // Close sidebar on nav item click (mobile)
        sidebar.querySelectorAll('.nav-item').forEach(function (item) {
            item.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // ── Auto-dismiss alerts ────────────────────────────────
        document.querySelectorAll('.alert').forEach(function (alert) {
            setTimeout(function () {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            }, 5000);
        });

        // ── Auto-dismiss toasts ────────────────────────────────
        document.querySelectorAll('.toast.show').forEach(function (toastEl) {
            // Auto-hide after 4 seconds
            setTimeout(function () {
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(12px)';
                setTimeout(function () { toastEl.remove(); }, 300);
            }, 4000);
        });
    });

})();