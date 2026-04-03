@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
@php $me = auth()->user(); @endphp

<div class="page-content">
    <div class="page-header-row">
        <h1 class="page-heading">Settings</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i>Add Account
        </button>
    </div>

    <div class="row g-4">

        {{-- ── LEFT: My Account ───────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-person-gear me-2"></i>My Account</div>
                </div>
                <div class="card-panel-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="full_name"
                                   class="form-control @error('full_name') is-invalid @enderror"
                                   value="{{ old('full_name', $me->full_name) }}" required>
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $me->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                New Password
                                <span class="text-muted fw-normal">(leave blank to keep)</span>
                            </label>
                            <input type="password" name="password" id="ownPassword"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password"
                                   oninput="checkPasswordStrength(this.value, 'ownChecklist')">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="ownChecklist" class="pw-checklist mt-2" style="display:none">
                                <div class="pw-check" id="ownChecklist-len"><i class="bi bi-x-circle-fill"></i> At least 8 characters</div>
                                <div class="pw-check" id="ownChecklist-upper"><i class="bi bi-x-circle-fill"></i> At least 3 uppercase letters</div>
                                <div class="pw-check" id="ownChecklist-number"><i class="bi bi-x-circle-fill"></i> At least 3 numbers</div>
                                <div class="pw-check" id="ownChecklist-symbol"><i class="bi bi-x-circle-fill"></i> At least 3 symbols (!@#$%...)</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100"
                                onclick="return validatePasswordBeforeSubmit('ownPassword', 'ownChecklist')">
                            <i class="bi bi-check2 me-1"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: All Accounts ────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-people me-2"></i>System Accounts
                    </div>
                    <span class="badge bg-secondary">{{ $users->count() }} accounts</span>
                </div>
                <div class="card-panel-body p-0">
                    <table class="table data-table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="{{ !$user->is_active ? 'table-row-inactive' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="user-avatar-sm">
                                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->full_name }}</div>
                                                @if($user->is_protected)
                                                    <span class="badge bg-dark" style="font-size:9px">
                                                        <i class="bi bi-shield-fill me-1"></i>MAIN ADMIN
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $user->username }}</td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td>
                                        <span class="role-pill role-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            {{-- Edit --}}
                                            <button class="btn btn-xs btn-outline-primary"
                                                    onclick="openEditModal({{ $user->toJson() }})"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            {{-- Activate / Deactivate (not for protected or self) --}}
                                            @if(!$user->is_protected && $user->id !== $me->id)
                                                <form method="POST"
                                                      action="{{ route('admin.users.toggle', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="bi {{ $user->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Delete (not for protected or self) --}}
                                            @if(!$user->is_protected && $user->id !== $me->id)
                                                <button class="btn btn-xs btn-outline-danger"
                                                        onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form method="POST" id="deleteForm-{{ $user->id }}"
                                                      action="{{ route('admin.users.destroy', $user) }}">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif

                                            {{-- Lock icon for protected main admin --}}
                                            @if($user->is_protected)
                                                <span class="btn btn-xs btn-outline-secondary disabled"
                                                      title="Main admin — cannot be deleted">
                                                    <i class="bi bi-lock-fill"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── ADD USER MODAL ──────────────────────────────────────────── --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required
                                   value="{{ old('full_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required
                                   value="{{ old('username') }}" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="">Select role...</option>
                                <option value="doctor"    {{ old('role') === 'doctor'    ? 'selected' : '' }}>Doctor</option>
                                <option value="secretary" {{ old('role') === 'secretary' ? 'selected' : '' }}>Secretary</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required
                                   value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="createPassword" class="form-control" required
                                   autocomplete="new-password"
                                   oninput="checkPasswordStrength(this.value, 'createChecklist')">
                            <div id="createChecklist" class="pw-checklist mt-2" style="display:none">
                                <div class="pw-check" id="createChecklist-len"><i class="bi bi-x-circle-fill"></i> At least 8 characters</div>
                                <div class="pw-check" id="createChecklist-upper"><i class="bi bi-x-circle-fill"></i> At least 3 uppercase letters</div>
                                <div class="pw-check" id="createChecklist-number"><i class="bi bi-x-circle-fill"></i> At least 3 numbers</div>
                                <div class="pw-check" id="createChecklist-symbol"><i class="bi bi-x-circle-fill"></i> At least 3 symbols (!@#$%...)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"
                            onclick="return validatePasswordBeforeSubmit('createPassword', 'createChecklist')">
                        <i class="bi bi-person-check me-1"></i>Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── EDIT USER MODAL ─────────────────────────────────────────── --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editUserForm" novalidate>
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="editFullName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="editUsername" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <div class="form-control-plaintext d-flex align-items-center gap-2">
                                <span id="editRoleBadge" class="role-pill"></span>
                                <span class="text-muted small">
                                    <i class="bi bi-lock me-1"></i>Cannot be changed
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <div class="section-label-divider">Reset Password <span class="text-muted fw-normal">(optional)</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="password" id="editPassword" class="form-control"
                                   autocomplete="new-password"
                                   oninput="checkPasswordStrength(this.value, 'editChecklist')">
                            <div id="editChecklist" class="pw-checklist mt-2" style="display:none">
                                <div class="pw-check" id="editChecklist-len"><i class="bi bi-x-circle-fill"></i> At least 8 characters</div>
                                <div class="pw-check" id="editChecklist-upper"><i class="bi bi-x-circle-fill"></i> At least 3 uppercase letters</div>
                                <div class="pw-check" id="editChecklist-number"><i class="bi bi-x-circle-fill"></i> At least 3 numbers</div>
                                <div class="pw-check" id="editChecklist-symbol"><i class="bi bi-x-circle-fill"></i> At least 3 symbols (!@#$%...)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"
                            onclick="return validatePasswordBeforeSubmit('editPassword', 'editChecklist')">
                        <i class="bi bi-check2 me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── DELETE CONFIRM MODAL ────────────────────────────────────── --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete
                    <strong id="deleteUserName"></strong>'s account?
                    This cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.table-row-inactive td { opacity: 0.55; }
.user-avatar-sm {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.section-label-divider {
    font-size: 12px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
}

/* ── Password Strength Checklist ─────────────────────────── */
.pw-checklist {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.pw-check {
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: color 0.2s;
}
.pw-check i { font-size: 13px; transition: color 0.2s; }
.pw-check.passed { color: #16a34a; font-weight: 600; }
.pw-check.passed i { color: #16a34a; }
.pw-check.passed i::before { content: "\F26A"; } /* bi-check-circle-fill */
.pw-check.failed { color: #dc2626; }
.pw-check.failed i { color: #dc2626; }

/* Strength bar */
.pw-strength-bar {
    height: 5px;
    border-radius: 3px;
    margin-top: 6px;
    background: #e2e8f0;
    overflow: hidden;
}
.pw-strength-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s, background 0.3s;
    width: 0%;
}
</style>
@endpush

@push('scripts')
<script>
// ── Password Strength Checker ─────────────────────────────────
function checkPasswordStrength(value, checklistId) {
    const checklist = document.getElementById(checklistId);
    if (!checklist) return;

    // Show checklist when user starts typing
    checklist.style.display = value.length > 0 ? 'flex' : 'none';

    const rules = {
        len:    value.length >= 8,
        upper:  (value.match(/[A-Z]/g) || []).length >= 3,
        number: (value.match(/[0-9]/g) || []).length >= 3,
        symbol: (value.match(/[^A-Za-z0-9]/g) || []).length >= 3,
    };

    Object.entries(rules).forEach(([key, passed]) => {
        const el = document.getElementById(`${checklistId}-${key}`);
        if (!el) return;
        el.classList.toggle('passed', passed);
        el.classList.toggle('failed', !passed && value.length > 0);
        const icon = el.querySelector('i');
        if (passed) {
            icon.className = 'bi bi-check-circle-fill';
        } else {
            icon.className = 'bi bi-x-circle-fill';
        }
    });

    // Update strength bar if it exists
    const bar = document.getElementById(`${checklistId}-bar`);
    if (bar) {
        const passedCount = Object.values(rules).filter(Boolean).length;
        const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
        const widths = ['25%', '50%', '75%', '100%'];
        bar.style.width     = passedCount > 0 ? widths[passedCount - 1] : '0%';
        bar.style.background = passedCount > 0 ? colors[passedCount - 1] : '#e2e8f0';
    }
}

// Block form submission if password doesn't meet requirements
function validatePasswordBeforeSubmit(passwordInputId, checklistId) {
    const pw = document.getElementById(passwordInputId)?.value;
    if (!pw) return true; // blank = optional (edit forms)

    const rules = {
        len:    pw.length >= 8,
        upper:  (pw.match(/[A-Z]/g) || []).length >= 3,
        number: (pw.match(/[0-9]/g) || []).length >= 3,
        symbol: (pw.match(/[^A-Za-z0-9]/g) || []).length >= 3,
    };

    const allPassed = Object.values(rules).every(Boolean);
    if (!allPassed) {
        // Show checklist so user can see what's missing
        const checklist = document.getElementById(checklistId);
        if (checklist) checklist.style.display = 'flex';
        checkPasswordStrength(pw, checklistId);

        // Shake the input
        const input = document.getElementById(passwordInputId);
        input?.classList.add('is-invalid');
        setTimeout(() => input?.classList.remove('is-invalid'), 2000);
        return false;
    }
    return true;
}

// Open edit modal and pre-fill fields
function openEditModal(user) {
    document.getElementById('editFullName').value = user.full_name;
    document.getElementById('editUsername').value  = user.username;
    document.getElementById('editEmail').value     = user.email;

    // Show role as read-only badge — cannot be changed after creation
    const badge = document.getElementById('editRoleBadge');
    badge.textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
    badge.className = `role-pill role-${user.role}`;

    // Set form action to the correct user update route
    document.getElementById('editUserForm').action = `/admin/users/${user.id}`;

    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

// Open delete confirmation modal
let pendingDeleteId = null;
function confirmDelete(userId, userName) {
    pendingDeleteId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (pendingDeleteId) {
        document.getElementById('deleteForm-' + pendingDeleteId).submit();
    }
});

// Re-open add modal if validation failed (old input present)
@if($errors->any() && old('full_name'))
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addUserModal')).show();
    });
@endif
</script>
@endpush

@endsection