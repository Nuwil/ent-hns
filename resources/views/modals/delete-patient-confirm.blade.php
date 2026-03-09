<!-- Delete Patient Confirmation Modal -->
<div id="delete-patient-confirm-overlay" class="modal-overlay">
    <div id="delete-patient-confirm-modal" class="modal" style="max-width: 400px;">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Delete Patient</h2>
            <button type="button" class="modal-close" onclick="closeDeleteConfirmModal()">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <p style="margin: 0 0 10px 0; font-size: 16px;">
                Are you sure you want to delete this patient?
            </p>
            <p style="margin: 0; color: var(--color-text-muted); font-size: 14px;">
                <strong>Patient:</strong> <span id="delete-patient-name"></span>
            </p>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; color: #dc3545; font-size: 13px;">
                <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteConfirmModal()">
                Cancel
            </button>
            <button type="button" class="btn btn-danger" onclick="confirmDeletePatient()">
                Delete Patient
            </button>
        </div>
    </div>
</div>
