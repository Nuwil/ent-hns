@extends('layouts.app')
@section('title', 'Complete Visit')
@section('page-title', 'Complete Visit')

@push('styles')
<style>
/* ── Prescription Checklist ──────────────────────────────── */
.rx-checklist-panel {
    border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
}
.rx-checklist-header {
    background: #f8fafc; padding: 10px 14px;
    font-size: 12px; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: 0.4px;
    border-bottom: 1px solid #e2e8f0;
}
.rx-checklist-body { max-height: 360px; overflow-y: auto; padding: 6px 0; }
.rx-cat-group { margin-bottom: 2px; }
.rx-cat-label {
    font-size: 10.5px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 6px 14px 2px;
}
.rx-checklist-item {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 14px; transition: background 0.1s; width: 100%;
}
.rx-checklist-item:hover  { background: #f1f5f9; }
.rx-checklist-item.rx-checked { background: #eff6ff; }
.rx-checkbox { flex-shrink: 0; accent-color: #2563eb; cursor: pointer; width:15px; height:15px; }
.rx-item-name {
    font-size: 12px; font-weight: 600; color: #1e293b;
    flex: 1; min-width: 80px; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.rx-sig-input {
    width: 120px; flex-shrink: 0;
    font-size: 11px !important; padding: 2px 6px !important;
    height: auto !important; color: #475569;
    border-color: #cbd5e1; border-radius: 5px !important; background: #fff;
}
.rx-sig-input:disabled { background: transparent; border-color: transparent; color: #94a3b8; cursor: default; }
.rx-qty-input {
    width: 50px; flex-shrink: 0;
    font-size: 11px !important; padding: 2px 4px !important;
    height: auto !important; border-color: #cbd5e1; border-radius: 5px !important;
    text-align: center;
}
.rx-qty-input:disabled { background: transparent; border-color: transparent; color: #94a3b8; cursor: default; }

/* Added prescription list */
.rx-added-item {
    display: flex; align-items: center; justify-content: space-between;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 7px; padding: 7px 12px; margin-bottom: 6px; font-size: 13px;
}
.rx-added-info { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
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

    {{-- Intake summary --}}
    <div class="alert alert-info d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-clipboard2 fs-5 flex-shrink-0 mt-1"></i>
        <div class="flex-grow-1">
            <div class="fw-semibold mb-1">Secretary Intake Summary</div>
            <div class="row g-2">
                <div class="col-auto"><strong>ENT:</strong> {{ $visit->ent_classification ?? '—' }}</div>
                <div class="col-auto"><strong>Chief Complaint:</strong> {{ $visit->chief_complaint }}</div>
                @if($visit->blood_pressure)
                    <div class="col-auto"><strong>BP:</strong> {{ $visit->blood_pressure }}</div>
                @endif
                @if($visit->weight)
                    <div class="col-auto"><strong>Wt:</strong> {{ $visit->weight }} kg</div>
                @endif
                @if($visit->height)
                    <div class="col-auto"><strong>Ht:</strong> {{ $visit->height }} cm</div>
                @endif
            </div>
            @if($visit->notes)
                <div class="text-muted small mt-1">{{ $visit->notes }}</div>
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
                <i class="bi bi-clipboard2-check me-1"></i>Assessment
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPlan">
                <i class="bi bi-capsule me-1"></i>Plan
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
                                <option value="{{ $cat }}" {{ $visit->ent_classification === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Chief Complaint <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <select id="ccDropdown" class="form-select" style="max-width:320px"
                                    onchange="fillChiefComplaint(this.value)">
                                <option value="">Quick-select...</option>
                                @foreach($entComplaints as $cat => $complaints)
                                    <optgroup label="{{ $cat }}">
                                        @foreach($complaints as $c)
                                            <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="text" id="chiefComplaintInput" class="form-control"
                                   value="{{ $visit->chief_complaint }}" placeholder="Or type freely...">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">History <span class="text-muted fw-normal small">(further explanation of chief complaint)</span></label>
                        <textarea id="historyInput" class="form-control" rows="5"
                                  placeholder="Onset, duration, character, aggravating/relieving factors, relevant history...">{{ $visit->history }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── OBJECTIVE ── --}}
            <div class="tab-pane fade" id="tabObjective">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Blood Pressure</label>
                        <input type="text" id="bpInput" class="form-control"
                               value="{{ $visit->blood_pressure }}" placeholder="e.g. 120/80">
                        <div class="text-muted" style="font-size:11px;margin-top:3px">
                            @if($visit->blood_pressure)
                                Secretary recorded: <strong>{{ $visit->blood_pressure }}</strong>
                            @else
                                Not yet recorded
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Weight</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $visit->weight ? $visit->weight.' kg' : '—' }}" readonly style="background:#f8fafc">
                        </div>
                        <div class="text-muted" style="font-size:11px;margin-top:3px">Collected by secretary</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Height</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $visit->height ? $visit->height.' cm' : '—' }}" readonly style="background:#f8fafc">
                        </div>
                        <div class="text-muted" style="font-size:11px;margin-top:3px">Collected by secretary</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Physical Exam <span class="text-muted fw-normal small">(up to doctor's interpretation)</span></label>
                        <textarea id="physicalExamInput" class="form-control" rows="7"
                                  placeholder="ENT exam: ear canal, tympanic membrane, nasal cavity, oropharynx, neck lymph nodes...">{{ $visit->physical_exam }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── ASSESSMENT ── --}}
            <div class="tab-pane fade" id="tabAssessment">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Diagnosis <span class="text-danger">*</span> <span class="text-muted fw-normal small">(up to doctor's interpretation)</span></label>
                        <textarea id="diagnosisInput" class="form-control" rows="4"
                                  placeholder="Primary diagnosis, ICD codes, differential diagnoses...">{{ $visit->diagnosis }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── PLAN ── --}}
            <div class="tab-pane fade" id="tabPlan">
                <div class="row g-3">
                    {{-- LEFT: Medicine checklist --}}
                    <div class="col-lg-5">
                        <label class="form-label fw-semibold"><i class="bi bi-list-check me-1"></i>Medicine Checklist</label>
                        <div class="rx-checklist-panel">
                            <div class="rx-checklist-header">
                                Common ENT Medicines
                                <input type="text" id="rxSearch" class="form-control form-control-sm mt-2"
                                       placeholder="Search medicines..." oninput="filterRxList(this.value)">
                            </div>
                            <div class="rx-checklist-body" id="rxChecklistBody"></div>
                        </div>
                    </div>

                    {{-- RIGHT: Added list + manual + instructions --}}
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold"><i class="bi bi-pencil me-1"></i>Manual Entry</label>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-5">
                                <input type="text" id="rxDrug" class="form-control form-control-sm" placeholder="Drug name">
                            </div>
                            <div class="col-4">
                                <input type="text" id="rxDosage" class="form-control form-control-sm" placeholder="Dosage/Sig">
                            </div>
                            <div class="col-2">
                                <input type="number" id="rxQty" class="form-control form-control-sm" placeholder="Qty" min="1">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="addPrescription()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <label class="form-label fw-semibold">
                            Prescriptions Added <span id="rxCount" class="badge bg-primary ms-1">0</span>
                        </label>
                        <div id="prescriptionList" class="mb-3"></div>

                        <label class="form-label fw-semibold">
                            <i class="bi bi-journal-text me-1"></i>Instructions
                            <span class="text-muted fw-normal small">(up to doctor's interpretation)</span>
                        </label>
                        <textarea id="planInstructionsInput" class="form-control" rows="3"
                                  placeholder="Follow-up instructions, lifestyle advice, diet, activity restrictions...">{{ $visit->plan_instructions }}</textarea>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Follow-up Date</label>
                            <input type="date" id="followUpInput" class="form-control"
                                   value="{{ $visit->follow_up_date?->format('Y-m-d') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" style="max-width:200px">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Action buttons --}}
        <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
            <a href="{{ route('doctor.patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="button" class="btn btn-outline-primary" onclick="submitVisit('save')">
                <i class="bi bi-floppy me-1"></i>Save Progress
            </button>
            <button type="button" class="btn btn-success" onclick="submitVisit('finalize')">
                <i class="bi bi-lock me-1"></i>Finalize & Lock Visit
            </button>
        </div>
    </div>

</div>

{{-- Hidden forms --}}
<form method="POST" id="saveForm" action="{{ route('doctor.visits.update', [$patient, $visit]) }}">
    @csrf @method('PUT')
    <input type="hidden" name="ent_classification"  id="f_ent_classification">
    <input type="hidden" name="chief_complaint"     id="f_chief_complaint">
    <input type="hidden" name="history"             id="f_history">
    <input type="hidden" name="blood_pressure"      id="f_blood_pressure">
    <input type="hidden" name="physical_exam"       id="f_physical_exam">
    <input type="hidden" name="diagnosis"           id="f_diagnosis">
    <input type="hidden" name="plan_instructions"   id="f_plan_instructions">
    <input type="hidden" name="follow_up_date"      id="f_follow_up_date">
    <input type="hidden" name="prescriptions"       id="f_prescriptions">
</form>

<form method="POST" id="finalizeForm" action="{{ route('doctor.visits.finalize', [$patient, $visit]) }}">
    @csrf @method('PATCH')
    <input type="hidden" name="ent_classification"  id="ff_ent_classification">
    <input type="hidden" name="chief_complaint"     id="ff_chief_complaint">
    <input type="hidden" name="history"             id="ff_history">
    <input type="hidden" name="blood_pressure"      id="ff_blood_pressure">
    <input type="hidden" name="physical_exam"       id="ff_physical_exam">
    <input type="hidden" name="diagnosis"           id="ff_diagnosis">
    <input type="hidden" name="plan_instructions"   id="ff_plan_instructions">
    <input type="hidden" name="follow_up_date"      id="ff_follow_up_date">
    <input type="hidden" name="prescriptions"       id="ff_prescriptions">
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
                <p class="mb-0">Once finalized, this visit record will be <strong>permanently locked</strong>. Are you sure?</p>
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
let prescriptions = @json($visit->prescriptions ?? []);
renderPrescriptions();

function syncEntClass(val) {}
function fillChiefComplaint(val) {
    if (val) document.getElementById('chiefComplaintInput').value = val;
}

function collectFields(prefix) {
    document.getElementById(`${prefix}_ent_classification`).value = document.getElementById('entClassSelect').value;
    document.getElementById(`${prefix}_chief_complaint`).value    = document.getElementById('chiefComplaintInput').value;
    document.getElementById(`${prefix}_history`).value            = document.getElementById('historyInput').value;
    document.getElementById(`${prefix}_blood_pressure`).value     = document.getElementById('bpInput').value;
    document.getElementById(`${prefix}_physical_exam`).value      = document.getElementById('physicalExamInput').value;
    document.getElementById(`${prefix}_diagnosis`).value          = document.getElementById('diagnosisInput').value;
    document.getElementById(`${prefix}_plan_instructions`).value  = document.getElementById('planInstructionsInput').value;
    document.getElementById(`${prefix}_follow_up_date`).value     = document.getElementById('followUpInput').value;
    document.getElementById(`${prefix}_prescriptions`).value      = JSON.stringify(prescriptions);
}

function submitVisit(action) {
    if (action === 'save') {
        collectFields('f');
        document.getElementById('saveForm').submit();
    } else {
        const diagnosis = document.getElementById('diagnosisInput').value.trim();
        if (!diagnosis) {
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

// ── Prescriptions ─────────────────────────────────────────────
function addPrescription(drug, dosage, qty) {
    const d = drug   || document.getElementById('rxDrug').value.trim();
    const s = dosage || document.getElementById('rxDosage').value.trim();
    const q = qty    || document.getElementById('rxQty').value.trim();
    if (!d) return;
    if (prescriptions.find(r => r.drug === d)) {
        document.getElementById('rxDrug').value = '';
        document.getElementById('rxDosage').value = '';
        document.getElementById('rxQty').value = '';
        return;
    }
    prescriptions.push({ drug: d, dosage: s, quantity: q });
    document.getElementById('rxDrug').value   = '';
    document.getElementById('rxDosage').value = '';
    document.getElementById('rxQty').value    = '';
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
    if (count) count.textContent = prescriptions.length;
    list.innerHTML = prescriptions.length === 0
        ? '<div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No prescriptions added yet.</div>'
        : prescriptions.map((rx, i) => `
            <div class="rx-added-item">
                <div class="rx-added-info">
                    <i class="bi bi-capsule-pill me-1 text-primary"></i>
                    <strong>${rx.drug}</strong>
                    ${rx.dosage ? '<span class="text-muted ms-1">— '+rx.dosage+'</span>' : ''}
                    ${rx.quantity ? '<span class="badge bg-secondary ms-1">Qty: '+rx.quantity+'</span>' : ''}
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                        onclick="removePrescription(${i})">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>`).join('');
}

// ── Medicine checklist with usual dosage ─────────────────────
const entMedicines = {
    'Antibiotics': [
        { name: 'Amoxicillin 500mg',             usual: '1 cap TID x 7 days' },
        { name: 'Amoxicillin-Clavulanate 625mg',  usual: '1 tab BID x 7 days' },
        { name: 'Azithromycin 500mg',             usual: '1 tab OD x 3 days' },
        { name: 'Ciprofloxacin 500mg',            usual: '1 tab BID x 7 days' },
        { name: 'Clindamycin 300mg',              usual: '1 cap TID x 7 days' },
        { name: 'Cefalexin 500mg',                usual: '1 cap QID x 7 days' },
    ],
    'Antihistamines': [
        { name: 'Cetirizine 10mg',                usual: '1 tab OD at night' },
        { name: 'Loratadine 10mg',                usual: '1 tab OD' },
        { name: 'Fexofenadine 120mg',             usual: '1 tab OD' },
        { name: 'Chlorphenamine 4mg',             usual: '1 tab TID' },
        { name: 'Diphenhydramine 25mg',           usual: '1 cap TID PRN' },
    ],
    'Decongestants': [
        { name: 'Pseudoephedrine 60mg',           usual: '1 tab TID x 5 days' },
        { name: 'Phenylephrine 10mg',             usual: '1 tab TID x 5 days' },
        { name: 'Oxymetazoline nasal spray',      usual: '2 sprays each nostril BID x 3 days' },
        { name: 'Xylometazoline nasal spray',     usual: '2 sprays each nostril TID x 3 days' },
    ],
    'Nasal Steroids': [
        { name: 'Fluticasone nasal spray',        usual: '2 sprays each nostril OD' },
        { name: 'Mometasone nasal spray',         usual: '2 sprays each nostril OD' },
        { name: 'Budesonide nasal spray',         usual: '2 sprays each nostril BID' },
        { name: 'Beclomethasone nasal spray',     usual: '2 sprays each nostril BID' },
    ],
    'Analgesics / Anti-inflammatory': [
        { name: 'Ibuprofen 400mg',                usual: '1 tab TID after meals' },
        { name: 'Mefenamic acid 500mg',           usual: '1 cap TID after meals' },
        { name: 'Paracetamol 500mg',              usual: '1–2 tabs every 4–6 hrs PRN' },
        { name: 'Celecoxib 200mg',                usual: '1 cap BID after meals' },
        { name: 'Prednisone 20mg',                usual: '1 tab OD in the morning x 5 days' },
    ],
    'Ear / Otic': [
        { name: 'Ciprofloxacin otic drops',       usual: '4 drops affected ear BID x 7 days' },
        { name: 'Ofloxacin otic drops',           usual: '10 drops affected ear BID x 7 days' },
        { name: 'Hydrocortisone-Neomycin drops',  usual: '3 drops affected ear TID' },
        { name: 'Carbamide peroxide ear drops',   usual: '5 drops affected ear BID x 4 days' },
    ],
    'Mucolytics / Expectorants': [
        { name: 'Carbocisteine 500mg',            usual: '1 cap TID' },
        { name: 'Bromhexine 8mg',                 usual: '1 tab TID' },
        { name: 'Guaifenesin 200mg',              usual: '1–2 tabs every 4 hrs' },
        { name: 'N-Acetylcysteine 600mg',         usual: '1 sachet dissolved in water OD' },
    ],
    'Antifungals': [
        { name: 'Clotrimazole ear drops',         usual: '4 drops affected ear BID x 14 days' },
        { name: 'Fluconazole 150mg',              usual: '1 tab OD x 7 days' },
        { name: 'Nystatin oral suspension',       usual: 'Gargle and swallow QID' },
    ],
    'Antacids / GI protection': [
        { name: 'Omeprazole 20mg',                usual: '1 cap OD before breakfast' },
        { name: 'Esomeprazole 40mg',              usual: '1 cap OD before breakfast' },
        { name: 'Sucralfate suspension',          usual: '10ml QID before meals' },
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
            const existing      = prescriptions.find(p => p.drug === med.name);
            const checked       = existing ? 'checked' : '';
            const sigVal        = existing ? existing.dosage : med.usual;
            const qtyVal        = existing ? (existing.quantity || '') : '';
            const inputDisabled = existing ? '' : 'disabled';
            const safeName      = med.name.replace(/'/g, "\\'");
            html += `
            <div class="rx-checklist-item ${checked ? 'rx-checked' : ''}">
                <input type="checkbox" class="rx-checkbox" value="${med.name}"
                       data-usual="${med.usual}" ${checked}
                       onchange="toggleChecklistItem(this)">
                <span class="rx-item-name" title="${med.name}">${med.name}</span>
                <input type="text" class="rx-sig-input form-control form-control-sm"
                       value="${sigVal}" placeholder="Dosage..."
                       ${inputDisabled}
                       oninput="updateSigInline(this, '${safeName}')">
                <input type="number" class="rx-qty-input form-control form-control-sm"
                       value="${qtyVal}" placeholder="Qty" min="1"
                       ${inputDisabled}
                       oninput="updateQtyInline(this, '${safeName}')">
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
    const qtyIn = row.querySelector('.rx-qty-input');
    const sig   = input ? input.value.trim() || checkbox.dataset.usual : checkbox.dataset.usual;
    const qty   = qtyIn ? qtyIn.value.trim() : '';

    if (checkbox.checked) {
        if (!prescriptions.find(p => p.drug === drug)) {
            prescriptions.push({ drug, dosage: sig, quantity: qty });
            renderPrescriptions();
        }
        row.classList.add('rx-checked');
        if (input)  { input.disabled  = false; input.focus(); }
        if (qtyIn)  { qtyIn.disabled  = false; }
    } else {
        const idx = prescriptions.findIndex(p => p.drug === drug);
        if (idx !== -1) { prescriptions.splice(idx, 1); renderPrescriptions(); }
        row.classList.remove('rx-checked');
        if (input)  { input.disabled  = true;  input.value  = checkbox.dataset.usual; }
        if (qtyIn)  { qtyIn.disabled  = true;  qtyIn.value  = ''; }
    }
}

function updateSigInline(input, drug) {
    const rx = prescriptions.find(p => p.drug === drug);
    if (rx) rx.dosage = input.value;
}

function updateQtyInline(input, drug) {
    const rx = prescriptions.find(p => p.drug === drug);
    if (rx) rx.quantity = input.value;
}

function syncChecklistState() {
    document.querySelectorAll('.rx-checkbox').forEach(cb => {
        const rx    = prescriptions.find(p => p.drug === cb.value);
        const active = !!rx;
        const row   = cb.closest('.rx-checklist-item');
        const input = row?.querySelector('.rx-sig-input');
        const qtyIn = row?.querySelector('.rx-qty-input');
        cb.checked = active;
        row?.classList.toggle('rx-checked', active);
        if (input) { input.disabled = !active; if (active && rx) input.value = rx.dosage; }
        if (qtyIn) { qtyIn.disabled = !active; if (active && rx) qtyIn.value = rx.quantity || ''; }
    });
    const count = document.getElementById('rxCount');
    if (count) count.textContent = prescriptions.length;
}

function filterRxList(val) { buildChecklist(val); }

document.addEventListener('DOMContentLoaded', () => {
    buildChecklist();
    renderPrescriptions();
});
</script>
@endpush
@endsection