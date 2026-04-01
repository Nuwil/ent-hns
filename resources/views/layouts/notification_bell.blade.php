{{-- ================================================================
     NOTIFICATION BELL — paste this inside topnav.blade.php
     Place it just before the user dropdown
     ================================================================ --}}

<div class="notification-wrapper" id="notifWrapper">

    {{-- Bell button --}}
    <button class="notif-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown()" title="Notifications">
        <i class="bi bi-bell-fill"></i>
        <span class="notif-badge" id="notifBadge" style="display:none">0</span>
    </button>

    {{-- Dropdown --}}
    <div class="notif-dropdown" id="notifDropdown" style="display:none">
        <div class="notif-dropdown-header">
            <span class="notif-dropdown-title">
                <i class="bi bi-bell me-2"></i>Notifications
            </span>
            <button class="notif-mark-all-btn" onclick="markAllRead()" title="Mark all as read">
                <i class="bi bi-check2-all"></i> Clear all
            </button>
        </div>
        <div class="notif-list" id="notifList">
            <div class="notif-empty">
                <i class="bi bi-bell-slash d-block mb-2 fs-4"></i>
                No new notifications
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ── Notification Bell ───────────────────────────────────────── */
.notification-wrapper {
    position: relative;
    display: inline-block;
    margin-right: 8px;
}
.notif-bell-btn {
    position: relative;
    background: none;
    border: none;
    padding: 6px 10px;
    border-radius: 8px;
    color: #64748b;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s;
}
.notif-bell-btn:hover { background: #f1f5f9; color: #2563eb; }
.notif-bell-btn.has-unread { color: #2563eb; animation: bellRing 1s ease 1; }

@keyframes bellRing {
    0%,100% { transform: rotate(0); }
    20%      { transform: rotate(-15deg); }
    40%      { transform: rotate(15deg); }
    60%      { transform: rotate(-10deg); }
    80%      { transform: rotate(10deg); }
}

.notif-badge {
    position: absolute;
    top: 2px; right: 4px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 700;
    min-width: 17px;
    height: 17px;
    border-radius: 10px;
    display: flex !important;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid white;
}

/* ── Dropdown ────────────────────────────────────────────────── */
.notif-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 360px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    border: 1px solid #e2e8f0;
    z-index: 9999;
    overflow: hidden;
    animation: notifFadeIn 0.15s ease;
}
@keyframes notifFadeIn {
    from { opacity:0; transform: translateY(-8px); }
    to   { opacity:1; transform: translateY(0); }
}

.notif-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}
.notif-dropdown-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
}
.notif-mark-all-btn {
    background: none;
    border: none;
    font-size: 12px;
    color: #2563eb;
    cursor: pointer;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    transition: background 0.15s;
}
.notif-mark-all-btn:hover { background: #eff6ff; }

.notif-list {
    max-height: 380px;
    overflow-y: auto;
}

/* ── Single notification item ────────────────────────────────── */
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f8fafc;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}
.notif-item:hover { background: #f8fafc; }
.notif-item.unread { background: #eff6ff; }
.notif-item.unread:hover { background: #dbeafe; }

.notif-icon-wrap {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.notif-icon-primary  { background: #dbeafe; color: #2563eb; }
.notif-icon-success  { background: #dcfce7; color: #16a34a; }
.notif-icon-warning  { background: #fef9c3; color: #ca8a04; }
.notif-icon-danger   { background: #fee2e2; color: #dc2626; }

.notif-content { flex: 1; min-width: 0; }
.notif-title {
    font-size: 13px; font-weight: 700;
    color: #1e293b; margin-bottom: 2px;
}
.notif-message {
    font-size: 12px; color: #64748b;
    line-height: 1.4;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.notif-time {
    font-size: 11px; color: #94a3b8;
    margin-top: 3px;
}
.notif-unread-dot {
    width: 8px; height: 8px;
    background: #2563eb;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

.notif-empty {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
    font-size: 13px;
}
</style>
@endpush

@push('scripts')
<script>
// ── Notification System ───────────────────────────────────────
let notifOpen = false;

function toggleNotifDropdown() {
    notifOpen = !notifOpen;
    document.getElementById('notifDropdown').style.display = notifOpen ? 'block' : 'none';
}

// Close when clicking outside
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        notifOpen = false;
        document.getElementById('notifDropdown').style.display = 'none';
    }
});

function renderNotifications(data) {
    const badge    = document.getElementById('notifBadge');
    const list     = document.getElementById('notifList');
    const bellBtn  = document.getElementById('notifBellBtn');
    const count    = data.unread_count;
    const notifs   = data.notifications;

    // Update badge
    if (count > 0) {
        badge.style.display = 'flex';
        badge.textContent   = count > 99 ? '99+' : count;
        bellBtn.classList.add('has-unread');
    } else {
        badge.style.display = 'none';
        bellBtn.classList.remove('has-unread');
    }

    // Render list
    if (!notifs.length) {
        list.innerHTML = `
            <div class="notif-empty">
                <i class="bi bi-bell-slash d-block mb-2 fs-4"></i>
                No new notifications
            </div>`;
        return;
    }

    list.innerHTML = notifs.map(n => `
        <a href="/notifications/${n.id}/read"
           class="notif-item ${n.unread ? 'unread' : ''}"
           onclick="return handleNotifClick(event, '${n.id}', '${n.url}')">
            <div class="notif-icon-wrap notif-icon-${n.color}">
                <i class="bi ${n.icon}"></i>
            </div>
            <div class="notif-content">
                <div class="notif-title">${n.title}</div>
                <div class="notif-message">${n.message}</div>
                <div class="notif-time"><i class="bi bi-clock me-1"></i>${n.created_at}</div>
            </div>
            ${n.unread ? '<div class="notif-unread-dot"></div>' : ''}
        </a>
    `).join('');
}

function handleNotifClick(e, id, url) {
    e.preventDefault();
    // Mark as read via AJAX then redirect
    fetch(`/notifications/${id}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        window.location.href = url || '/';
    });
    return false;
}

function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        pollNotifications();
        notifOpen = false;
        document.getElementById('notifDropdown').style.display = 'none';
    });
}

function pollNotifications() {
    fetch('/notifications/poll')
        .then(r => r.json())
        .then(data => renderNotifications(data))
        .catch(() => {}); // silent fail
}

// Poll on load + every 30 seconds
document.addEventListener('DOMContentLoaded', () => {
    pollNotifications();
    setInterval(pollNotifications, 30000);
});
</script>
@endpush