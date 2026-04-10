{{--
    ============================================================
    ADD THIS CARD to resources/views/settings/index.blade.php

    Place it INSIDE the <div class="row g-4"> block,
    as a new full-width row BELOW the existing two columns
    (My Account + System Accounts).

    i.e. after the closing </div> of the col-lg-8 system accounts card
    and before the closing </div> of the row.
    ============================================================
--}}

    {{-- ── BOTTOM ROW: Database Management ─────────────────── --}}
    <div class="col-12">
        <div class="card-panel">
            <div class="card-panel-header">
                <div class="card-panel-title">
                    <i class="bi bi-database me-2"></i>Database Management
                </div>
                <span class="badge bg-danger" style="font-size: 11px;">
                    <i class="bi bi-shield-exclamation me-1"></i>Admin Only
                </span>
            </div>
            <div class="card-panel-body">
                <div class="row g-4 align-items-center">

                    {{-- Description --}}
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <div class="db-icon-wrap">
                                <i class="bi bi-database-down"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1" style="font-size: 15px;">
                                    Export Full Database Backup
                                </div>
                                <p class="text-muted mb-2" style="font-size: 13px; line-height: 1.6;">
                                    Downloads a complete <code>.sql</code> dump of the entire database —
                                    all tables, structure, and data. Use this to create a restore point
                                    before major changes, or to duplicate the database to another server.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="db-tag"><i class="bi bi-check2 me-1"></i>All tables included</span>
                                    <span class="db-tag"><i class="bi bi-check2 me-1"></i>DROP + CREATE + INSERT statements</span>
                                    <span class="db-tag"><i class="bi bi-check2 me-1"></i>Foreign key safe</span>
                                    <span class="db-tag"><i class="bi bi-check2 me-1"></i>Logged to activity log</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action button --}}
                    <div class="col-lg-4 text-lg-end">
                        <button type="button"
                                class="btn btn-danger btn-lg px-4"
                                data-bs-toggle="modal"
                                data-bs-target="#backupConfirmModal">
                            <i class="bi bi-download me-2"></i>Download Backup
                        </button>
                        <div class="text-muted mt-2" style="font-size: 11px;">
                            <i class="bi bi-clock me-1"></i>
                            Backup will be named with today's date &amp; time
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


{{--
    ============================================================
    ADD THIS MODAL at the bottom of settings/index.blade.php,
    alongside the other modals (addUserModal, editUserModal, etc.)
    ============================================================
--}}

{{-- ── BACKUP CONFIRM MODAL ────────────────────────────────── --}}
<div class="modal fade" id="backupConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="bi bi-database-down me-2 text-danger"></i>Confirm Database Backup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="alert alert-warning d-flex gap-2 align-items-start" style="font-size: 13px;">
                    <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                    <div>
                        This will export the <strong>entire database</strong> including all patient records,
                        visit history, prescriptions, and user accounts.
                        Keep the downloaded file secure — it contains sensitive medical data.
                    </div>
                </div>
                <p class="mb-0 text-muted" style="font-size: 13px;">
                    The backup will be downloaded as a <code>.sql</code> file and this action
                    will be recorded in the activity log.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('admin.settings.backup.download') }}"
                   class="btn btn-danger">
                    <i class="bi bi-download me-2"></i>Yes, Download Backup
                </a>
            </div>
        </div>
    </div>
</div>


{{--
    ============================================================
    ADD THESE STYLES inside the existing @push('styles') block
    in settings/index.blade.php
    ============================================================
--}}
{{-- @push('styles') -- already open in your file, just add inside it: --}}
<style>
/* Database backup card */
.db-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #dc2626;
    flex-shrink: 0;
}

.db-tag {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    font-weight: 500;
}
</style>
{{-- end of styles to add --}