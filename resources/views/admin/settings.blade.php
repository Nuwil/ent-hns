@extends('layout')

@section('content')
<div class="admin-settings">
  <div class="settings-container">
    <!-- Header Section -->
    <div class="settings-header">
      <div>
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Welcome back, <strong>{{ session('user_name') ?? ($user->full_name ?? $user->username) }}</strong></p>
      </div>
    </div>

    <!-- User Management Card -->
    <div class="settings-card">
      <div class="card-header-section">
        <div class="card-header-content">
          <h2 class="card-title"><i class="fas fa-users"></i> User Management</h2>
          <p class="card-description">Create, update, and manage user accounts for the system</p>
        </div>
        <button type="button" id="openUserModalBtn" class="btn-create">+ Create User</button>
      </div>

      <div class="card-body-section">
        <div class="table-responsive">
          <table class="settings-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $u)
              <tr>
                <td><span class="username-badge">{{ $u['username'] ?? $u->username }}</span></td>
                <td>{{ $u['full_name'] ?? $u->full_name }}</td>
                <td>{{ $u['email'] ?? $u->email }}</td>
                <td><span class="role-badge role-{{ strtolower($u['role'] ?? $u->role) }}">{{ ucfirst($u['role'] ?? $u->role) }}</span></td>
                <td><span class="status-badge {{ (isset($u['is_active']) ? $u['is_active'] : $u->is_active) ? 'status-active' : 'status-inactive' }}">{{ (isset($u['is_active']) ? $u['is_active'] : $u->is_active) ? 'Active' : 'Inactive' }}</span></td>
                <td>
                  <div class="action-buttons">
                    @if( (isset($u['is_protected']) ? $u['is_protected'] : $u->is_protected) )
                      <span class="badge-protected">Protected</span>
                    @else
                      <form method="POST" action="{{ route('admin.users.destroy', [$u['id'] ?? $u->id]) }}" style="display:inline-block;" id="deleteForm_{{ $u['id'] ?? $u->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-action btn-delete" onclick="confirmDeleteUser('{{ $u['id'] ?? $u->id }}','{{ $u['username'] ?? $u->username }}')" title="Delete user"><i class="fas fa-trash"></i></button>
                      </form>
                    @endif

                    <button type="button" class="btn-action btn-edit" onclick="editUser('{{ $u['id'] ?? $u->id }}')" title="Edit user"><i class="fas fa-edit"></i></button>

                    @if( ! (isset($u['is_protected']) ? $u['is_protected'] : $u->is_protected) )
                      <form method="POST" action="{{ route('admin.users.toggle', [$u['id'] ?? $u->id]) }}" style="display:inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action {{ (isset($u['is_active']) ? $u['is_active'] : $u->is_active) ? 'btn-deactivate' : 'btn-activate' }}" title="{{ (isset($u['is_active']) ? $u['is_active'] : $u->is_active) ? 'Deactivate' : 'Activate' }} user"><i class="fas fa-{{ (isset($u['is_active']) ? $u['is_active'] : $u->is_active) ? 'ban' : 'check' }}"></i></button>
                      </form>
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

    <!-- Database Maintenance Card -->
    <div class="settings-card">
      <div class="card-header-section">
        <div class="card-header-content">
          <h2 class="card-title"><i class="fas fa-database"></i> Database Maintenance</h2>
          <p class="card-description">Manage database backups and system logs</p>
        </div>
      </div>
      <div class="card-body-section maintenance-body">
        <div class="maintenance-grid">
          <div class="maintenance-item">
            <div class="maintenance-icon backup"><i class="fas fa-save"></i></div>
            <h3>Backup Database</h3>
            <p>Create a backup of the entire system database</p>
            <button class="btn-secondary">Backup Now</button>
          </div>
          <div class="maintenance-item">
            <div class="maintenance-icon logs"><i class="fas fa-trash"></i></div>
            <h3>Clear Logs</h3>
            <p>Clear old system activity and error logs</p>
            <button class="btn-secondary">Clear Logs</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<style>
.admin-settings {
  background-color: #f9fafb;
  min-height: calc(100vh - 60px);
  padding: 0;
}

.settings-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px 24px;
}

.settings-header {
  margin-bottom: 32px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

/* Card Styles */
.settings-card {
  background: white;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  margin-bottom: 24px;
  overflow: hidden;
  transition: box-shadow 0.2s ease;
}

.settings-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.card-header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 24px;
  border-bottom: 1px solid #f3f4f6;
  gap: 16px;
}

.card-header-content {
  flex: 1;
}

.card-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 6px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.card-title i {
  color: #4f46e5;
  font-size: 20px;
}

.card-description {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}

.card-body-section {
  padding: 24px;
}

/* Button Styles */
.btn-create {
  background-color: #4f46e5;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-create:hover {
  background-color: #4338ca;
  transform: translateY(-1px);
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
  border-color: #d1d5db;
}

/* Table Styles */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.settings-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.settings-table thead th {
  background: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
  padding: 14px 16px;
  text-align: left;
  font-weight: 700;
  color: #374151;
  position: sticky;
  top: 0;
}

.settings-table tbody tr {
  border-bottom: 1px solid #f3f4f6;
  transition: background-color 0.2s ease;
}

.settings-table tbody tr:hover {
  background-color: #f9fafb;
}

.settings-table td {
  padding: 14px 16px;
  color: #1f2937;
}

.username-badge {
  font-weight: 600;
  color: #111827;
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.role-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 12px;
}

.role-admin {
  background: #dbeafe;
  color: #1e40af;
}

.role-doctor {
  background: #dcfce7;
  color: #15803d;
}

.role-secretary {
  background: #fef3c7;
  color: #92400e;
}

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 12px;
}

.status-active {
  background: #dcfce7;
  color: #15803d;
}

.status-inactive {
  background: #fee2e2;
  color: #991b1b;
}

.badge-protected {
  display: inline-block;
  background: #ede9fe;
  color: #6d28d9;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 12px;
}

.action-buttons {
  display: flex;
  gap: 0;
  padding: 0;
  justify-content: center;
  align-items: center;
}

.action-buttons form {
  background: none;
  border: none;
}

.btn-action.btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px !important;
    height: 36px !important;
    border: 1px solid #e5e7eb;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 1.5rem;
    align-items: center;
    padding: 5px;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: white;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  font-size: 14px;
}

.btn-action:hover {
  background: #f3f4f6;
  color: #111827;
}

.btn-delete:hover {
  background: #fee2e2;
  color: #991b1b;
  border-color: #fca5a5;
}

.btn-edit:hover {
  background: #dbeafe;
  color: #1e40af;
  border-color: #93c5fd;
}

.btn-deactivate:hover {
  background: #fef3c7;
  color: #92400e;
  border-color: #fed7aa;
}

.btn-activate:hover {
  background: #dcfce7;
  color: #15803d;
  border-color: #bbf7d0;
}

/* Maintenance Section */
.maintenance-body {
  padding: 32px 24px;
}

.maintenance-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.maintenance-item {
  text-align: center;
}

.maintenance-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 12px;
  font-size: 24px;
  margin: 0 auto 16px;
}

.maintenance-icon.backup {
  background: #dbeafe;
  color: #1e40af;
}

.maintenance-icon.logs {
  background: #fee2e2;
  color: #991b1b;
}

.maintenance-item h3 {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 8px 0;
}

.maintenance-item p {
  font-size: 13px;
  color: #6b7280;
  margin: 0 0 16px 0;
}

/* Form Grid Styling */
.form-grid-two {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.required {
  color: #dc3545;
}

.text-hint {
  font-size: 12px;
  color: #666;
  display: block;
}

/* Responsive */
@media (max-width: 768px) {
  .settings-container {
    padding: 16px;
  }

  .card-header-section {
    flex-direction: column;
    padding: 16px;
  }

  .btn-create {
    width: 100%;
  }

  .settings-table {
    font-size: 12px;
  }

  .settings-table td,
  .settings-table th {
    padding: 10px 12px;
  }

  .action-buttons {
    flex-wrap: wrap;
  }

  .form-grid-two {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
function confirmDeleteUser(id, username) {
  if (confirm('Delete user "' + username + '"? This action cannot be undone.')) {
    var f = document.getElementById('deleteForm_' + id);
    if (f) f.submit();
  }
}

function editUser(userId) {
  // Fetch user data
  fetch('/admin/users/' + userId + '/edit')
    .then(response => response.json())
    .then(user => {
      // Populate form with user data
      document.getElementById('userId').value = user.id;
      document.getElementById('username').value = user.username;
      document.getElementById('email').value = user.email;
      document.getElementById('full_name').value = user.full_name || '';
      document.getElementById('role').value = user.role;
      
      // Clear password fields
      document.getElementById('userPassword').value = '';
      document.getElementById('userPasswordConfirm').value = '';
      
      // Update form action and method
      var form = document.getElementById('userForm');
      form.method = 'POST';
      form.action = '/admin/users/' + userId;
      document.getElementById('methodOverride').value = 'PATCH';
      
      // Update modal title and button
      document.getElementById('userModalTitle').textContent = 'Edit User';
      document.getElementById('userFormSubmit').textContent = 'Update User';
      
      // Make password optional for edit
      document.getElementById('passwordLabel').innerHTML = '(optional - leave blank to keep current)';
      document.getElementById('userPassword').required = false;
      document.getElementById('userPasswordConfirm').required = false;
      var confirmLbl = document.getElementById('confirmLabel');
      if (confirmLbl) confirmLbl.textContent = '';
      
      // Show the modal
      showEditModal();
    })
    .catch(error => {
      console.error('Error fetching user data:', error);
      alert('Error loading user data');
    });
}

function showEditModal() {
  var overlay = document.getElementById('user-modal-overlay');
  if (overlay) {
    overlay.classList.add('active');
    var passwordError = document.getElementById('passwordError');
    if (passwordError) passwordError.style.display = 'none';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  var overlay = document.getElementById('user-modal-overlay');
  var openBtn = document.getElementById('openUserModalBtn');
  var form = document.getElementById('userForm');
  var pw = document.getElementById('userPassword');
  var pwc = document.getElementById('userPasswordConfirm');
  var passwordError = document.getElementById('passwordError');
  var confirmLabel = document.getElementById('confirmLabel');
  var passwordLabel = document.getElementById('passwordLabel');
  var passwordHint = document.getElementById('passwordHint');

  function showModal() {
    if (!overlay) return;
    overlay.classList.add('active');
    if (form) {
      form.reset();
      var idField = form.querySelector('input[name="id"]');
      if (idField) idField.value = '';
      document.getElementById('methodOverride').value = 'POST';
    }
    if (passwordError) passwordError.style.display = 'none';
    if (pw) {
      pw.required = true;
      pw.value = '';
    }
    if (pwc) {
      pwc.required = true;
      pwc.value = '';
    }
    if (confirmLabel) confirmLabel.textContent = '';
    if (passwordLabel) passwordLabel.textContent = '';
    if (passwordHint) passwordHint.textContent = 'Password must be at least 6 characters.';
    
    // Reset form action for create
    form.method = 'POST';
    form.action = '{{ route("admin.users.store") }}';
    document.getElementById('userModalTitle').textContent = 'Create New User';
    document.getElementById('userFormSubmit').textContent = 'Create User';
    
    setTimeout(function() {
      var el = form && form.querySelector('input[name="username"]');
      if (el) el.focus();
    }, 50);
  }

  function hideModal() {
    if (!overlay) return;
    overlay.classList.remove('active');
  }

  if (openBtn) openBtn.addEventListener('click', function(e) {
    e.preventDefault();
    showModal();
  });

  if (pw) {
    pw.addEventListener('input', function() {
      var isEditMode = document.getElementById('userId').value;
      
      if (pw.value.length > 0) {
        pw.required = true;
        if (pwc) pwc.required = true;
        if (confirmLabel) confirmLabel.textContent = '';
        if (passwordLabel) passwordLabel.textContent = '';
        if (passwordHint) passwordHint.textContent = 'Password must be at least 6 characters.';
      } else {
        // Only allow empty password in edit mode
        if (isEditMode) {
          pw.required = false;
          if (pwc) pwc.required = false;
          if (confirmLabel) confirmLabel.textContent = '';
          if (passwordLabel) passwordLabel.textContent = '(optional - leave blank to keep current)';
          if (passwordHint) passwordHint.textContent = '';
        } else {
          // In create mode, password is always required
          pw.required = true;
          if (pwc) pwc.required = true;
          if (confirmLabel) confirmLabel.textContent = '';
          if (passwordLabel) passwordLabel.textContent = '';
          if (passwordHint) passwordHint.textContent = 'Password must be at least 6 characters.';
        }
        if (passwordError) passwordError.style.display = 'none';
      }
    });
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      if (pw && pwc && pw.value !== pwc.value) {
        e.preventDefault();
        if (passwordError) {
          passwordError.textContent = 'Passwords do not match.';
          passwordError.style.display = 'block';
        }
        return false;
      }
    });
  }

  // Close modal on overlay click
  if (overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        ModalManager.close('user-modal');
      }
    });
  }
});
</script>

<!-- User Management Modal -->
@include('modals.add-account-users')

@endsection
