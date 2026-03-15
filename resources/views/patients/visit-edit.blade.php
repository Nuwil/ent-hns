@extends('layouts.app')
@section('title', 'Complete Visit')
@section('page-title', 'Complete Visit')

@push('styles')
<style>
/* ── Prescription Checklist ──────────────────────────────── */
.rx-checklist-panel {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    height: 100%;
}
.rx-checklist-header {
    background: #f8fafc;
    padding: 10px 14px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 1px solid #e2e8f0;
}
.rx-checklist-body {
    max-height: 340px;
    overflow-y: auto;
    padding: 6px 0;
}
.rx-cat-group { margin-bottom: 2px; }
.rx-cat-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 6px 14px 2px;
}
.rx-checklist-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 14px;
    transition: background 0.1s;
    margin: 0;
    width: 100%;
}
.rx-checklist-item:hover { background: #f1f5f9; }
.rx-checklist-item.rx-checked { background: #eff6ff; }
.rx-checkbox { flex-shrink: 0; accent-color: #2563eb; cursor: pointer; width: 15px; height: 15px; }
.rx-item-name {
    font-size: 12.5px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.rx-sig-input {
    width: 160px;
    flex-shrink: 0;
    font-size: 11px !important;
    padding: 2px 7px !important;
    height: auto !important;
    color: #475569;
    border-color: #cbd5e1;
    border-radius: 5px !important;
    background: #fff;
}
.rx-sig-input:disabled {
    background: transparent;
    border-color: transparent;
    color: #94a3b8;
    cursor: default;
}
/* Added prescription list */
.rx-added-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 7px;
    padding: 7px 12px;
    margin-bottom: 6px;
    font-size: 13px;
}
.rx-added-info { display: flex; align-items: center; flex-wrap: wrap; gap: 2px; }
</style>
@endpush

@section('content')
@php
    $entComplaints = \App\Http\Controllers\VisitController::entComplaintsList();
@endphp

<div class="page-content">

    <div class="page-header-row">
        <div>
            <h1 class="page-heading">Complete Visit</h1>
            <div class="text-muted small mt-1">
                Patient: <strong>{{ $patient->full_name }}</strong> ·
                Started: {{ $visit->visited_at->format('M j, Y H:i') }} ·
                <span class="badge {{ $visit->statusBadgeClass() }}">{{ $visit->statusLabel() }}</span>
            </div>
        </div>
        <a href="{{ route('doctor.patients.show', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    {{-- Intake summary (what secretary filled in) --}}
    <div class="alert alert-info d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-clipboard2 fs-5 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-semibold mb-1">Secretary Intake Summary</div>
            <div><strong>ENT Classification:</strong> {{ $visit->ent_classification ?? '—' }}</div>
            <div><strong>Chief Complaint:</strong> {{ $visit->chief_complaint }}</div>
            @if($visit->notes)
                <div class="mt-1 text-muted">{{ $visit->notes }}</div>
            @endif
        </div>
    </div>

    {{-- SOAP TABS --}}
    <ul class="nav nav-tabs mb-0" id="soapTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabSubjective">
                <i class="bi bi-chat-square-text me-1"></i>Subjective
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabObjective">
                <i class="bi bi-eye me-1"></i>Objective
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAssessment">
                <i class="bi bi-clipboard2-check me-1"></i>Assessment & Plan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabRx">
                <i class="bi bi-capsule me-1"></i>Prescriptions
            </button>
        </li>
    </ul>

    <div class="card-panel" style="border-radius:0 0 12px 12px; border-top:none">
        <div class="tab-content" id="soapTabContent">

            {{-- ── SUBJECTIVE ── --}}
            <div class="tab-pane fade show active" id="tabSubjective">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">ENT Classification <span class="text-danger">*</span></label>
                        <select id="entClassSelect" class="form-select" onchange="syncEntClass(this.value)">
                            <option value="">Select category...</option>
                            @foreach($entComplaints as $cat => $complaints)
                                <option value="{{ $cat }}" {{ $visit->ent_classification === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Chief Complaint <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <select id="ccDropdown" class="form-select" style="max-width:320px"
                                    onchange="fillChiefComplaint(this.value)">
                                <option value="">Quick-select from list...</option>
                                @foreach($entComplaints as $cat => $complaints)
                                    <optgroup label="{{ $cat }}">
                                        @foreach($complaints as $c)
                                            <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="text" id="chiefComplaintInput" class="form-control"
                                   value="{{ $visit->chief_complaint }}"
                                   placeholder="Or type freely...">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">History of Presenting Illness</label>
                        <textarea id="historyInput" class="form-control" rows="5"
                                  placeholder="Onset, duration, character, aggravating/relieving factors...">{{ $visit->history_of_illness }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── OBJECTIVE ── --}}
            <div class="tab-pane fade" id="tabObjective">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Examination Findings</label>
                        <textarea id="examInput" class="form-control" rows="7"
                                  placeholder="ENT exam: ear canal, tympanic membrane, nasal cavity, oropharynx, neck...">{{ $visit->exam_findings }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── ASSESSMENT & PLAN ── --}}
            <div class="tab-pane fade" id="tabAssessment">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Diagnosis <span class="text-danger">*</span></label>
                        <textarea id="diagnosisInput" class="form-control" rows="3"
                                  placeholder="Primary diagnosis, differential diagnoses...">{{ $visit->diagnosis }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Treatment Plan</label>
                        <textarea id="treatmentInput" class="form-control" rows="3"
                                  placeholder="Procedures, referrals, lifestyle advice...">{{ $visit->treatment_plan }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Additional Notes</label>
                        <textarea id="notesInput" class="form-control" rows="2"
                                  placeholder="Other relevant notes...">{{ $visit->notes }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Follow-up Date</label>
                        <input type="date" id="followUpInput" class="form-control"
                               value="{{ $visit->follow_up_date?->format('Y-m-d') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                </div>
            </div>

            {{-- ── PRESCRIPTIONS ── --}}
            <div class="tab-pane fade" id="tabRx">
                <div class="row g-3">
                    {{-- LEFT: Medicine checklist --}}
                    <div class="col-lg-5">
                        <div class="rx-checklist-panel">
                            <div class="rx-checklist-header">
                                <i class="bi bi-list-check me-1"></i>Quick Select — Common ENT Medicines
                                <input type="text" id="rxSearch" class="form-control form-control-sm mt-2"
                                       placeholder="Search medicines..." oninput="filterRxList(this.value)">
                            </div>
                            <div class="rx-checklist-body" id="rxChecklistBody">
                                {{-- Rendered by JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Manual entry + added list --}}
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-pencil me-1"></i>Manual Entry
                        </label>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-5">
                                <input type="text" id="rxDrug" class="form-control"
                                       placeholder="Drug name (e.g. Amoxicillin 500mg)">
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="rxDosage" class="form-control"
                                       placeholder="Sig (e.g. 1 tab TID x 7 days)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-primary w-100"
                                        onclick="addPrescription()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <label class="form-label fw-semibold">
                            <i class="bi bi-clipboard2-check me-1"></i>
                            Prescriptions Added <span id="rxCount" class="badge bg-primary ms-1">0</span>
                        </label>
                        <div id="prescriptionList"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Action buttons --}}
        <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
            <a href="{{ route('doctor.patients.show', $patient) }}"
               class="btn btn-outline-secondary">
                Cancel
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="submitVisit('save')">
                <i class="bi bi-floppy me-1"></i>Save Progress
            </button>
            <button type="button" class="btn btn-success" onclick="submitVisit('finalize')">
                <i class="bi bi-lock me-1"></i>Finalize & Lock Visit
            </button>
        </div>
    </div>

</div>

{{-- Hidden forms for save and finalize --}}
<form method="POST" id="saveForm"
      action="{{ route('doctor.visits.update', [$patient, $visit]) }}">
    @csrf @method('PUT')
    <input type="hidden" name="ent_classification" id="f_ent_classification">
    <input type="hidden" name="chief_complaint"    id="f_chief_complaint">
    <input type="hidden" name="history_of_illness" id="f_history_of_illness">
    <input type="hidden" name="exam_findings"      id="f_exam_findings">
    <input type="hidden" name="diagnosis"          id="f_diagnosis">
    <input type="hidden" name="treatment_plan"     id="f_treatment_plan">
    <input type="hidden" name="notes"              id="f_notes">
    <input type="hidden" name="follow_up_date"     id="f_follow_up_date">
    <input type="hidden" name="prescriptions"      id="f_prescriptions">
</form>

<form method="POST" id="finalizeForm"
      action="{{ route('doctor.visits.finalize', [$patient, $visit]) }}">
    @csrf @method('PATCH')
    <input type="hidden" name="ent_classification" id="ff_ent_classification">
    <input type="hidden" name="chief_complaint"    id="ff_chief_complaint">
    <input type="hidden" name="history_of_illness" id="ff_history_of_illness">
    <input type="hidden" name="exam_findings"      id="ff_exam_findings">
    <input type="hidden" name="diagnosis"          id="ff_diagnosis">
    <input type="hidden" name="treatment_plan"     id="ff_treatment_plan">
    <input type="hidden" name="notes"              id="ff_notes">
    <input type="hidden" name="follow_up_date"     id="ff_follow_up_date">
    <input type="hidden" name="prescriptions"      id="ff_prescriptions">
</form>

{{-- Finalize confirmation modal --}}
<div class="modal fade" id="finalizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="bi bi-lock-fill me-2 text-success"></i>Finalize Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Once finalized, this visit record will be <strong>permanently locked</strong> and cannot be edited by anyone. Are you sure?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn btn-success" id="confirmFinalizeBtn">
                    <i class="bi bi-lock me-1"></i>Yes, Finalize
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Prescription state (pre-load existing)
let prescriptions = @json($visit->prescriptions ?? []);
renderPrescriptions();

function syncEntClass(val) {
    // no-op — just for UX
}

function fillChiefComplaint(val) {
    if (val) document.getElementById('chiefComplaintInput').value = val;
}

// Collect all form values into hidden fields then submit
function collectFields(prefix) {
    document.getElementById(`${prefix}_ent_classification`).value  = document.getElementById('entClassSelect').value;
    document.getElementById(`${prefix}_chief_complaint`).value     = document.getElementById('chiefComplaintInput').value;
    document.getElementById(`${prefix}_history_of_illness`).value  = document.getElementById('historyInput').value;
    document.getElementById(`${prefix}_exam_findings`).value       = document.getElementById('examInput').value;
    document.getElementById(`${prefix}_diagnosis`).value           = document.getElementById('diagnosisInput').value;
    document.getElementById(`${prefix}_treatment_plan`).value      = document.getElementById('treatmentInput').value;
    document.getElementById(`${prefix}_notes`).value               = document.getElementById('notesInput').value;
    document.getElementById(`${prefix}_follow_up_date`).value      = document.getElementById('followUpInput').value;
    document.getElementById(`${prefix}_prescriptions`).value       = JSON.stringify(prescriptions);
}

function submitVisit(action) {
    if (action === 'save') {
        collectFields('f');
        document.getElementById('saveForm').submit();
    } else {
        // Validate diagnosis before showing finalize modal
        const diagnosis = document.getElementById('diagnosisInput').value.trim();
        if (!diagnosis) {
            // Switch to Assessment tab and show error
            document.querySelector('[data-bs-target="#tabAssessment"]').click();
            document.getElementById('diagnosisInput').focus();
            document.getElementById('diagnosisInput').classList.add('is-invalid');
            setTimeout(() => document.getElementById('diagnosisInput').classList.remove('is-invalid'), 3000);
            return;
        }
        new bootstrap.Modal(document.getElementById('finalizeModal')).show();
    }
}

document.getElementById('confirmFinalizeBtn').addEventListener('click', function () {
    collectFields('ff');
    document.getElementById('finalizeForm').submit();
});

// Prescriptions
function addPrescription(drug, dosage) {
    const d = drug  || document.getElementById('rxDrug').value.trim();
    const s = dosage || document.getElementById('rxDosage').value.trim();
    if (!d) return;
    // Prevent duplicates
    if (prescriptions.find(r => r.drug === d)) {
        document.getElementById('rxDrug').value = '';
        document.getElementById('rxDosage').value = '';
        return;
    }
    prescriptions.push({ drug: d, dosage: s });
    document.getElementById('rxDrug').value   = '';
    document.getElementById('rxDosage').value = '';
    renderPrescriptions();
    syncChecklistState();
}

function removePrescription(idx) {
    prescriptions.splice(idx, 1);
    renderPrescriptions();
    syncChecklistState();
}

function renderPrescriptions() {
    const list = document.getElementById('prescriptionList');
    const count = document.getElementById('rxCount');
    if (!list) return;
    count && (count.textContent = prescriptions.length);
    list.innerHTML = prescriptions.length === 0
        ? '<div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No prescriptions added yet.</div>'
        : prescriptions.map((rx, i) => `
            <div class="rx-added-item">
                <div class="rx-added-info">
                    <i class="bi bi-capsule-pill me-1 text-primary"></i>
                    <strong>${rx.drug}</strong>
                    ${rx.dosage ? '<span class="text-muted ms-1">— ' + rx.dosage + '</span>' : ''}
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                        onclick="removePrescription(${i})" title="Remove">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
        `).join('');
}

// ── Medicine checklist ────────────────────────────────────────
const entMedicines = {
    'Antibiotics': [
        { name: 'Amoxicillin 500mg',              sig: '1 cap TID x 7 days' },
        { name: 'Amoxicillin-Clavulanate 625mg',  sig: '1 tab BID x 7 days' },
        { name: 'Azithromycin 500mg',             sig: '1 tab OD x 3 days' },
        { name: 'Ciprofloxacin 500mg',            sig: '1 tab BID x 7 days' },
        { name: 'Clindamycin 300mg',              sig: '1 cap TID x 7 days' },
        { name: 'Cefalexin 500mg',                sig: '1 cap QID x 7 days' },
    ],
    'Antihistamines': [
        { name: 'Cetirizine 10mg',                sig: '1 tab OD at night' },
        { name: 'Loratadine 10mg',                sig: '1 tab OD' },
        { name: 'Fexofenadine 120mg',             sig: '1 tab OD' },
        { name: 'Chlorphenamine 4mg',             sig: '1 tab TID' },
        { name: 'Diphenhydramine 25mg',           sig: '1 cap TID PRN' },
    ],
    'Decongestants': [
        { name: 'Pseudoephedrine 60mg',           sig: '1 tab TID x 5 days' },
        { name: 'Phenylephrine 10mg',             sig: '1 tab TID x 5 days' },
        { name: 'Oxymetazoline nasal spray',      sig: '2 sprays each nostril BID x 3 days' },
        { name: 'Xylometazoline nasal spray',     sig: '2 sprays each nostril TID x 3 days' },
    ],
    'Nasal Steroids': [
        { name: 'Fluticasone nasal spray',        sig: '2 sprays each nostril OD' },
        { name: 'Mometasone nasal spray',         sig: '2 sprays each nostril OD' },
        { name: 'Budesonide nasal spray',         sig: '2 sprays each nostril BID' },
        { name: 'Beclomethasone nasal spray',     sig: '2 sprays each nostril BID' },
    ],
    'Analgesics / Anti-inflammatory': [
        { name: 'Ibuprofen 400mg',                sig: '1 tab TID after meals' },
        { name: 'Mefenamic acid 500mg',           sig: '1 cap TID after meals' },
        { name: 'Paracetamol 500mg',              sig: '1–2 tabs every 4–6 hrs PRN' },
        { name: 'Celecoxib 200mg',                sig: '1 cap BID after meals' },
        { name: 'Prednisone 20mg',                sig: '1 tab OD in the morning x 5 days' },
    ],
    'Ear / Otic': [
        { name: 'Ciprofloxacin otic drops',       sig: '4 drops affected ear BID x 7 days' },
        { name: 'Ofloxacin otic drops',           sig: '10 drops affected ear BID x 7 days' },
        { name: 'Hydrocortisone-Neomycin drops',  sig: '3 drops affected ear TID' },
        { name: 'Carbamide peroxide ear drops',   sig: '5 drops affected ear BID x 4 days' },
    ],
    'Mucolytics / Expectorants': [
        { name: 'Carbocisteine 500mg',            sig: '1 cap TID' },
        { name: 'Bromhexine 8mg',                 sig: '1 tab TID' },
        { name: 'Guaifenesin 200mg',              sig: '1–2 tabs every 4 hrs' },
        { name: 'N-Acetylcysteine 600mg',         sig: '1 sachet dissolved in water OD' },
    ],
    'Antifungals': [
        { name: 'Clotrimazole ear drops',         sig: '4 drops affected ear BID x 14 days' },
        { name: 'Fluconazole 150mg',              sig: '1 tab OD x 7 days' },
        { name: 'Nystatin oral suspension',       sig: 'Gargle and swallow QID' },
    ],
    'Antacids / GI protection': [
        { name: 'Omeprazole 20mg',                sig: '1 cap OD before breakfast' },
        { name: 'Esomeprazole 40mg',              sig: '1 cap OD before breakfast' },
        { name: 'Sucralfate suspension',          sig: '10ml QID before meals' },
    ],
};

function buildChecklist(filter = '') {
    const body = document.getElementById('rxChecklistBody');
    if (!body) return;
    const fl = filter.toLowerCase();
    let html = '';
    Object.entries(entMedicines).forEach(([cat, meds]) => {
        const filtered = fl ? meds.filter(m => m.name.toLowerCase().includes(fl)) : meds;
        if (!filtered.length) return;
        html += `<div class="rx-cat-group"><div class="rx-cat-label">${cat}</div>`;
        filtered.forEach(med => {
            const existing = prescriptions.find(p => p.drug === med.name);
            const checked  = existing ? 'checked' : '';
            const sigVal   = existing ? existing.dosage : med.sig;
            const inputDisabled = existing ? '' : 'disabled';
            html += `
            <div class="rx-checklist-item ${checked ? 'rx-checked' : ''}">
                <input type="checkbox" class="rx-checkbox" value="${med.name}"
                       data-sig="${med.sig}" ${checked}
                       onchange="toggleChecklistItem(this)">
                <span class="rx-item-name">${med.name}</span>
                <input type="text"
                       class="rx-sig-input form-control form-control-sm"
                       value="${sigVal}"
                       placeholder="Instructions..."
                       ${inputDisabled}
                       oninput="updateSigInline(this, '${med.name.replace(/'/g, "\\'")}')">
            </div>`;
        });
        html += `</div>`;
    });
    body.innerHTML = html || '<div class="text-muted small p-3">No medicines match your search.</div>';
}

function toggleChecklistItem(checkbox) {
    const drug  = checkbox.value;
    const row   = checkbox.closest('.rx-checklist-item');
    const input = row.querySelector('.rx-sig-input');
    const sig   = input ? input.value.trim() || checkbox.dataset.sig : checkbox.dataset.sig;

    if (checkbox.checked) {
        if (!prescriptions.find(p => p.drug === drug)) {
            prescriptions.push({ drug, dosage: sig });
            renderPrescriptions();
        }
        row.classList.add('rx-checked');
        if (input) { input.disabled = false; input.focus(); }
    } else {
        const idx = prescriptions.findIndex(p => p.drug === drug);
        if (idx !== -1) { prescriptions.splice(idx, 1); renderPrescriptions(); }
        row.classList.remove('rx-checked');
        if (input) { input.disabled = true; input.value = checkbox.dataset.sig; }
    }
}

function updateSigInline(input, drug) {
    const rx = prescriptions.find(p => p.drug === drug);
    if (rx) { rx.dosage = input.value; }
}

function syncChecklistState() {
    document.querySelectorAll('.rx-checkbox').forEach(cb => {
        const rx     = prescriptions.find(p => p.drug === cb.value);
        const active = !!rx;
        const row    = cb.closest('.rx-checklist-item');
        const input  = row?.querySelector('.rx-sig-input');
        cb.checked = active;
        row?.classList.toggle('rx-checked', active);
        if (input) {
            input.disabled = !active;
            if (active && rx) input.value = rx.dosage;
        }
    });
    const count = document.getElementById('rxCount');
    if (count) count.textContent = prescriptions.length;
}

function filterRxList(val) { buildChecklist(val); }

// Build on page load
document.addEventListener('DOMContentLoaded', () => {
    buildChecklist();
    renderPrescriptions();
});
</script>
@endpush
@endsection