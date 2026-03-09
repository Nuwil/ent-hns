<!-- User Management Modal -->
<div id="user-modal-overlay" class="modal-overlay">
    <div id="user-modal" class="modal">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 id="userModalTitle">Create New User</h2>
            <button type="button" class="modal-close" onclick="ModalManager.close('user-modal')">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input type="hidden" id="methodOverride" name="_method" value="POST">
                <input type="hidden" name="id" id="userId" value="">

                <div class="form-grid-two">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required />
                    </div>
                </div>

                <div class="form-grid-two">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="role">Role <span class="required">*</span></label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            <option value="admin">Administrator</option>
                            <option value="doctor">Doctor</option>
                            <option value="secretary">Secretary</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-two">
                    <div class="form-group">
                        <label for="userPassword">Password <span class="required">*</span><span id="passwordLabel"></span></label>
                        <input type="password" id="userPassword" name="password" class="form-control" required />
                        <small class="text-hint" id="passwordHint" style="display:block; margin-top:0.5rem; color: #666;">Password must be at least 6 characters.</small>
                    </div>
                    <div class="form-group">
                        <label for="userPasswordConfirm">Confirm Password <span class="required">*</span><span id="confirmLabel"></span></label>
                        <input type="password" id="userPasswordConfirm" name="password_confirmation" class="form-control" required />
                        <small id="passwordError" style="display:none; color:#dc3545; margin-top:0.5rem;"></small>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="ModalManager.close('user-modal')">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary" form="userForm" id="userFormSubmit">
                Create User
            </button>
        </div>
    </div>
</div>