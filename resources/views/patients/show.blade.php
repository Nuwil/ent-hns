@extends('layouts.app')
@section('title', $patient->full_name)
@section('page-title', 'Patient Profile')

@section('content')
@php
    $role = auth()->user()->role;
    $entComplaints = [
        'Otology (Ear)' => [
            'Ear pain (Otalgia)', 'Hearing loss', 'Ear discharge (Otorrhea)',
            'Tinnitus (Ringing in ear)', 'Ear fullness / blocked ear',
            'Vertigo / Dizziness', 'Itchy ear', 'Foreign body in ear',
        ],
        'Rhinology (Nose)' => [
            'Nasal congestion / Blocked nose', 'Runny nose (Rhinorrhea)',
            'Nosebleed (Epistaxis)', 'Loss of smell (Anosmia)',
            'Facial pain / pressure', 'Post-nasal drip', 'Sneezing',
            'Foreign body in nose',
        ],
        'Laryngology (Throat / Voice)' => [
            'Sore throat', 'Hoarseness / Voice change',
            'Difficulty swallowing (Dysphagia)', 'Throat clearing',
            'Cough', 'Globus sensation (lump in throat)', 'Stridor / Noisy breathing',
        ],
        'Head & Neck' => [
            'Neck mass / lump', 'Neck pain', 'Facial swelling',
            'Mouth sore / ulcer', 'Difficulty opening mouth (Trismus)',
            'Salivary gland swelling', 'Thyroid swelling',
        ],
    ];
@endphp

<div class="page-content">

    {{-- Patient Header --}}
    <div class="card-panel mb-4">
        <div class="patient-header-inner">
            <div class="patient-avatar-lg">
                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
            </div>
            <div class="patient-header-info">
                <h2 class="patient-full-name">{{ $patient->full_name }}</h2>
                <div class="patient-meta">
                    <span><i class="bi bi-gender-ambiguous me-1"></i>{{ ucfirst($patient->gender) }}</span>
                    <span><i class="bi bi-calendar me-1"></i>Age {{ $patient->age }}</span>
                    <span><i class="bi bi-telephone me-1"></i>{{ $patient->phone }}</span>
                    @if($patient->email)
                        <span><i class="bi bi-envelope me-1"></i>{{ $patient->email }}</span>
                    @endif
                    @if($patient->blood_type)
                        <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                    @endif
                </div>
                @if($patient->allergies)
                    <div class="patient-allergy-warning mt-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Allergies:</strong> {{ $patient->allergies }}
                    </div>
                @endif
            </div>
            <div class="patient-header-actions">
                @if($role === 'secretary')
                    <a href="{{ route('secretary.patients.edit', $patient) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <button class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
                        <i class="bi bi-calendar-plus me-1"></i>Book Appointment
                    </button>
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#intakeVisitModal">
                        <i class="bi bi-clipboard2-plus me-1"></i>New Visit Entry
                    </button>
                @endif
                @if($role === 'doctor')
                    <button class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
                        <i class="bi bi-calendar-plus me-1"></i>Book Appointment
                    </button>
                    <button class="btn btn-success btn-sm"
                            data-bs-toggle="modal" data-bs-target="#addVisitModal">
                        <i class="bi bi-clipboard2-pulse me-1"></i>New Visit
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Visit Timeline --}}
        <div class="col-lg-8">
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-clock-history me-2"></i>Visit Timeline
                    </div>
                    <span class="badge bg-secondary">{{ $patient->visits->count() }} visits</span>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($patient->visits->sortByDesc('visited_at') as $visit)
                        <div class="visit-timeline-item">

                            {{-- Status stripe --}}
                            <div class="visit-status-stripe visit-stripe-{{ $visit->status }}"></div>

                            <div class="visit-timeline-inner">
                                {{-- Header row --}}
                                <div class="visit-header">
                                    <div class="visit-header-left">
                                        <div class="visit-date">
                                            {{ $visit->visited_at->format('M j, Y') }}
                                            <span class="text-muted">{{ $visit->visited_at->format('H:i') }}</span>
                                        </div>
                                        @if($visit->ent_classification)
                                            <span class="visit-ent-tag">{{ $visit->ent_classification }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $visit->statusBadgeClass() }}">
                                            {{ $visit->statusLabel() }}
                                        </span>
                                        @if($visit->isLocked())
                                            <span class="visit-lock-icon" title="Finalized — read only">
                                                <i class="bi bi-lock-fill"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Chief Complaint --}}
                                <div class="visit-complaint">
                                    <span class="visit-field-label">Chief Complaint:</span>
                                    {{ $visit->chief_complaint }}
                                </div>

                                {{-- Doctor clinical content (visible to doctor only, or if finalized) --}}
                                @if($role === 'doctor' || $visit->isFinalized())
                                    @if($visit->history_of_illness)
                                        <div class="visit-section mt-2">
                                            <div class="visit-field-label">History of Illness</div>
                                            <div class="visit-field-text">{{ $visit->history_of_illness }}</div>
                                        </div>
                                    @endif
                                    @if($visit->exam_findings)
                                        <div class="visit-section mt-2">
                                            <div class="visit-field-label">Exam Findings</div>
                                            <div class="visit-field-text">{{ $visit->exam_findings }}</div>
                                        </div>
                                    @endif
                                    @if($visit->diagnosis)
                                        <div class="visit-section visit-diagnosis mt-2">
                                            <div class="visit-field-label">Diagnosis</div>
                                            <div class="visit-field-text fw-semibold">{{ $visit->diagnosis }}</div>
                                        </div>
                                    @endif
                                    @if($visit->treatment_plan)
                                        <div class="visit-section mt-2">
                                            <div class="visit-field-label">Treatment Plan</div>
                                            <div class="visit-field-text">{{ $visit->treatment_plan }}</div>
                                        </div>
                                    @endif
                                    @if(!empty($visit->prescriptions))
                                        <div class="visit-section mt-2">
                                            <div class="visit-field-label">
                                                <i class="bi bi-capsule me-1"></i>Prescriptions
                                            </div>
                                            <div class="prescription-pills mt-1">
                                                @foreach($visit->prescriptions as $rx)
                                                    <span class="prescription-pill">
                                                        {{ $rx['drug'] ?? '' }}
                                                        @if(!empty($rx['dosage'])) — {{ $rx['dosage'] }} @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                {{-- Vitals/intake notes (shown to both roles) --}}
                                @if($visit->notes && $visit->recorded_by === 'secretary')
                                    <div class="visit-vitals mt-2">
                                        <i class="bi bi-heart-pulse me-1 text-muted"></i>
                                        <span class="text-muted small">{{ $visit->notes }}</span>
                                    </div>
                                @endif

                                {{-- Secretary: locked notice for clinical fields --}}
                                @if($role === 'secretary' && !$visit->isFinalized())
                                    <div class="visit-locked-notice">
                                        <i class="bi bi-lock me-1"></i>
                                        Clinical findings, diagnosis and prescriptions are visible to doctors only.
                                    </div>
                                @endif

                                {{-- Footer --}}
                                <div class="visit-footer">
                                    <div class="visit-meta-info">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $visit->recorded_by === 'secretary' ? 'Intake by Secretary' : 'By Dr. ' . ($visit->doctor->name ?? '—') }}
                                        @if($visit->isFinalized() && $visit->finalizedBy)
                                            · Finalized by {{ $visit->finalizedBy->name }}
                                            {{ $visit->finalized_at?->format('M j, Y H:i') }}
                                        @endif
                                    </div>
                                    <div class="visit-footer-actions">
                                        @if($visit->follow_up_date)
                                            <span class="visit-followup">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                Follow-up: {{ $visit->follow_up_date->format('M j, Y') }}
                                            </span>
                                        @endif

                                        {{-- Secretary: edit only if still pending --}}
                                        @if($role === 'secretary' && $visit->secretaryCanEdit())
                                            <button class="btn btn-xs btn-outline-secondary"
                                                    onclick="openSecretaryEdit({{ $visit->toJson() }})">
                                                <i class="bi bi-pencil me-1"></i>Edit Intake
                                            </button>
                                        @endif

                                        {{-- Doctor: continue/complete if not finalized --}}
                                        @if($role === 'doctor' && $visit->doctorCanEdit())
                                            <a href="{{ route('doctor.visits.edit', [$patient, $visit]) }}"
                                               class="btn btn-xs btn-primary">
                                                <i class="bi bi-clipboard2-pulse me-1"></i>
                                                {{ $visit->isInProgress() ? 'Continue Visit' : 'Complete Visit' }}
                                            </a>
                                        @endif

                                        {{-- Finalized badge --}}
                                        @if($visit->isFinalized())
                                            <span class="text-success small fw-semibold">
                                                <i class="bi bi-lock-fill me-1"></i>Locked
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-sm p-5">
                            <i class="bi bi-clipboard2-x fs-2 d-block mb-2 text-muted"></i>
                            <span class="text-muted">No visits recorded yet</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card-panel mb-4">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-calendar2 me-2"></i>Appointments</div>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($patient->appointments->take(6) as $appt)
                        <div class="dash-appt-item">
                            <div class="dash-appt-time">
                                {{ $appt->scheduled_at->format('M j') }}<br>
                                <small>{{ $appt->scheduled_at->format('H:i') }}</small>
                            </div>
                            <div class="dash-appt-info">
                                <div class="small fw-semibold">{{ Str::limit($appt->reason, 32) }}</div>
                                <div class="text-muted" style="font-size:11px">
                                    @if($appt->doctor) {{ $appt->doctor->name }} @endif
                                </div>
                            </div>
                            <span class="badge {{ $appt->statusBadgeClass() }}">
                                {{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-state-sm p-4">
                            <i class="bi bi-calendar-x"></i><span>No appointments</span>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($patient->notes)
                <div class="card-panel">
                    <div class="card-panel-header">
                        <div class="card-panel-title"><i class="bi bi-sticky me-2"></i>Notes</div>
                    </div>
                    <div class="card-panel-body">
                        <p class="text-muted small mb-0">{{ $patient->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ================================================================
     SECRETARY — NEW INTAKE VISIT MODAL
     ================================================================ --}}
@if($role === 'secretary')
<div class="modal fade" id="intakeVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard2-plus me-2"></i>New Visit Entry — {{ $patient->full_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('secretary.visits.store', $patient) }}">
                @csrf
                <input type="hidden" name="appointment_id" value="{{ session('appointment_id') }}">
                <div class="modal-body">
                    <div class="alert alert-info d-flex gap-2 py-2 mb-3" style="font-size:13px">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <span>This visit entry will be marked <strong>Awaiting Doctor</strong>. The doctor will complete the clinical details.</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">ENT Classification <span class="text-danger">*</span></label>
                            <select name="ent_classification" class="form-select" required
                                    onchange="updateComplaintList(this.value)">
                                <option value="">Select category...</option>
                                @foreach($entComplaints as $cat => $complaints)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Chief Complaint <span class="text-danger">*</span></label>
                            <select name="chief_complaint" id="chiefComplaintSelect" class="form-select" required>
                                <option value="">Select ENT classification first...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="section-divider"><span>Vital Signs <span class="text-muted fw-normal small">(optional)</span></span></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Blood Pressure</label>
                            <input type="text" name="blood_pressure" class="form-control" placeholder="120/80">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Heart Rate</label>
                            <div class="input-group">
                                <input type="number" name="heart_rate" class="form-control" placeholder="72" min="20" max="300">
                                <span class="input-group-text">bpm</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Temperature</label>
                            <div class="input-group">
                                <input type="number" name="temperature" class="form-control" placeholder="36.5" step="0.1">
                                <span class="input-group-text">°C</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Weight</label>
                            <div class="input-group">
                                <input type="number" name="weight" class="form-control" placeholder="60" step="0.1">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Allergies</label>
                            <input type="text" name="allergies_note" class="form-control"
                                   placeholder="Leave blank if no change"
                                   value="{{ $patient->allergies }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Additional Notes for Doctor</label>
                            <textarea name="intake_notes" class="form-control" rows="2"
                                      placeholder="Anything the doctor should know..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i>Create Visit Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Secretary edit intake modal --}}
<div class="modal fade" id="editIntakeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Visit Intake</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editIntakeForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning d-flex gap-2 py-2 mb-3" style="font-size:13px">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <span>You can only edit this entry while it's <strong>Awaiting Doctor</strong>. Once the doctor opens it, editing is locked.</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">ENT Classification <span class="text-danger">*</span></label>
                            <select name="ent_classification" id="editEntClass" class="form-select" required
                                    onchange="updateEditComplaintList(this.value)">
                                <option value="">Select category...</option>
                                @foreach($entComplaints as $cat => $complaints)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Chief Complaint <span class="text-danger">*</span></label>
                            <select name="chief_complaint" id="editComplaintSelect" class="form-select" required>
                                <option value="">Select ENT classification first...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ================================================================
     DOCTOR — FULL SOAP VISIT MODAL (new visit, no intake)
     ================================================================ --}}
@if($role === 'doctor')
<div class="modal fade" id="addVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard2-pulse me-2"></i>New Visit — {{ $patient->full_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('doctor.visits.store', $patient) }}">
                @csrf
                <input type="hidden" name="appointment_id" value="{{ session('appointment_id') }}">
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="doctorVisitTabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dvSubjective">
                                <i class="bi bi-chat-square-text me-1"></i>Subjective
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dvObjective">
                                <i class="bi bi-eye me-1"></i>Objective
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dvAssessment">
                                <i class="bi bi-clipboard2-check me-1"></i>Assessment & Plan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dvRx">
                                <i class="bi bi-capsule me-1"></i>Prescriptions
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="dvSubjective">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">ENT Classification <span class="text-danger">*</span></label>
                                    <select name="ent_classification" class="form-select" required>
                                        <option value="">Select category...</option>
                                        @foreach($entComplaints as $cat => $complaints)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Chief Complaint <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        <select id="dvCcDropdown" class="form-select" style="max-width:300px"
                                                onchange="document.getElementById('dvCcInput').value = this.value">
                                            <option value="">Quick select...</option>
                                            @foreach($entComplaints as $cat => $complaints)
                                                <optgroup label="{{ $cat }}">
                                                    @foreach($complaints as $c)
                                                        <option value="{{ $c }}">{{ $c }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <input type="text" name="chief_complaint" id="dvCcInput"
                                               class="form-control" placeholder="Or type freely..." required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">History of Presenting Illness</label>
                                    <textarea name="history_of_illness" class="form-control" rows="4"
                                              placeholder="Onset, duration, character, aggravating/relieving factors..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dvObjective">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Examination Findings</label>
                                    <textarea name="exam_findings" class="form-control" rows="6"
                                              placeholder="ENT exam findings..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dvAssessment">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Diagnosis <span class="text-danger">*</span></label>
                                    <textarea name="diagnosis" class="form-control" rows="3"
                                              placeholder="Primary diagnosis..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Treatment Plan</label>
                                    <textarea name="treatment_plan" class="form-control" rows="3"
                                              placeholder="Procedures, referrals..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" class="form-control"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dvRx">
                            <div class="row g-3">
                                {{-- LEFT: checklist --}}
                                <div class="col-lg-5">
                                    <div class="rx-checklist-panel">
                                        <div class="rx-checklist-header">
                                            <i class="bi bi-list-check me-1"></i>Quick Select — Common ENT Medicines
                                            <input type="text" id="dvRxSearch" class="form-control form-control-sm mt-2"
                                                   placeholder="Search medicines..." oninput="dvFilterRx(this.value)">
                                        </div>
                                        <div class="rx-checklist-body" id="dvRxChecklistBody"></div>
                                    </div>
                                </div>
                                {{-- RIGHT: manual + added list --}}
                                <div class="col-lg-7">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-pencil me-1"></i>Manual Entry
                                    </label>
                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-md-5">
                                            <input type="text" id="dvRxDrug" class="form-control" placeholder="Drug name">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" id="dvRxDosage" class="form-control" placeholder="Sig">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-primary w-100"
                                                    onclick="dvAddRx()"><i class="bi bi-plus"></i></button>
                                        </div>
                                    </div>
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-clipboard2-check me-1"></i>
                                        Prescriptions Added <span id="dvRxCount" class="badge bg-primary ms-1">0</span>
                                    </label>
                                    <div id="dvRxList"></div>
                                    <input type="hidden" name="prescriptions" id="dvRxJson">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-lock me-1"></i>Save & Finalize Visit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Book Appointment Modal (Both roles) --}}
<div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Book Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route("{$role}.appointments.store") }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">Select doctor...</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required
                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    max-height: 300px;
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
.visit-timeline-item {
    display: flex;
    border-bottom: 1px solid #f1f5f9;
    position: relative;
}
.visit-timeline-item:last-child { border-bottom: none; }

.visit-status-stripe {
    width: 4px;
    flex-shrink: 0;
    border-radius: 0;
}
.visit-stripe-pending     { background: #f59e0b; }
.visit-stripe-in_progress { background: #0ea5e9; }
.visit-stripe-finalized   { background: #22c55e; }

.visit-timeline-inner {
    flex: 1;
    padding: 16px 20px;
}

.visit-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 8px;
}
.visit-header-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.visit-date {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
}
.visit-date span { font-weight: 400; color: #94a3b8; margin-left: 4px; font-size: 12px; }

.visit-ent-tag {
    background: #eff6ff;
    color: #2563eb;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    border: 1px solid #bfdbfe;
}

.visit-lock-icon { color: #22c55e; font-size: 14px; }

.visit-complaint {
    font-size: 13.5px;
    color: #334155;
    margin-bottom: 4px;
}
.visit-field-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #94a3b8;
    margin-bottom: 2px;
}
.visit-field-text {
    font-size: 13px;
    color: #334155;
    white-space: pre-line;
}
.visit-diagnosis { background: #f0fdf4; padding: 8px 10px; border-radius: 6px; }
.visit-vitals    { font-size: 12px; }

.visit-locked-notice {
    font-size: 11.5px;
    color: #94a3b8;
    margin-top: 8px;
    padding: 4px 8px;
    background: #f8fafc;
    border-radius: 4px;
    border: 1px dashed #e2e8f0;
}

.visit-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}
.visit-meta-info { font-size: 11.5px; color: #94a3b8; }
.visit-footer-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.visit-followup { font-size: 12px; color: #0891b2; }

.section-divider {
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.section-divider::before, .section-divider::after {
    content: ''; flex: 1; height: 1px; background: #e2e8f0;
}
</style>
@endpush

@push('scripts')
<script>
// Auto-open visit modal if redirected from appointment confirm
@if(session('open_visit_modal'))
    document.addEventListener('DOMContentLoaded', function () {
        @if(auth()->user()->role === 'doctor')
            new bootstrap.Modal(document.getElementById('addVisitModal')).show();
        @else
            new bootstrap.Modal(document.getElementById('intakeVisitModal')).show();
        @endif
    });
@endif

// ENT complaint list data
const entComplaints = @json($entComplaints);

// Secretary — new intake: update complaint dropdown when category changes
function updateComplaintList(category) {
    const select = document.getElementById('chiefComplaintSelect');
    select.innerHTML = '<option value="">Select complaint...</option>';
    if (entComplaints[category]) {
        entComplaints[category].forEach(c => {
            const opt = document.createElement('option');
            opt.value = c; opt.textContent = c;
            select.appendChild(opt);
        });
    }
}

// Secretary — edit intake modal
function openSecretaryEdit(visit) {
    const form = document.getElementById('editIntakeForm');
    form.action = `/secretary/patients/{{ $patient->id }}/visits/${visit.id}`;

    const entSel = document.getElementById('editEntClass');
    entSel.value = visit.ent_classification || '';
    updateEditComplaintList(visit.ent_classification, visit.chief_complaint);

    new bootstrap.Modal(document.getElementById('editIntakeModal')).show();
}

function updateEditComplaintList(category, selected = '') {
    const select = document.getElementById('editComplaintSelect');
    select.innerHTML = '<option value="">Select complaint...</option>';
    if (entComplaints[category]) {
        entComplaints[category].forEach(c => {
            const opt = document.createElement('option');
            opt.value = c; opt.textContent = c;
            if (c === selected) opt.selected = true;
            select.appendChild(opt);
        });
    }
}

// Doctor — modal prescription builder
let dvPrescriptions = [];

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

function dvBuildChecklist(filter = '') {
    const body = document.getElementById('dvRxChecklistBody');
    if (!body) return;
    const fl = filter.toLowerCase();
    let html = '';
    Object.entries(entMedicines).forEach(([cat, meds]) => {
        const filtered = fl ? meds.filter(m => m.name.toLowerCase().includes(fl)) : meds;
        if (!filtered.length) return;
        html += `<div class="rx-cat-group"><div class="rx-cat-label">${cat}</div>`;
        filtered.forEach(med => {
            const existing      = dvPrescriptions.find(p => p.drug === med.name);
            const checked       = existing ? 'checked' : '';
            const sigVal        = existing ? existing.dosage : med.sig;
            const inputDisabled = existing ? '' : 'disabled';
            html += `
            <div class="rx-checklist-item ${checked ? 'rx-checked' : ''}">
                <input type="checkbox" class="dv-rx-checkbox rx-checkbox" value="${med.name}"
                       data-sig="${med.sig}" ${checked} onchange="dvToggleChecklist(this)">
                <span class="rx-item-name">${med.name}</span>
                <input type="text"
                       class="rx-sig-input form-control form-control-sm"
                       value="${sigVal}"
                       placeholder="Instructions..."
                       ${inputDisabled}
                       oninput="dvUpdateSigInline(this, '${med.name.replace(/'/g, "\\'")}')">
            </div>`;
        });
        html += `</div>`;
    });
    body.innerHTML = html || '<div class="text-muted small p-3">No medicines found.</div>';
}

function dvToggleChecklist(cb) {
    const drug  = cb.value;
    const row   = cb.closest('.rx-checklist-item');
    const input = row.querySelector('.rx-sig-input');
    const sig   = input ? input.value.trim() || cb.dataset.sig : cb.dataset.sig;

    if (cb.checked) {
        if (!dvPrescriptions.find(p => p.drug === drug)) {
            dvPrescriptions.push({ drug, dosage: sig });
            dvRenderRx();
        }
        row.classList.add('rx-checked');
        if (input) { input.disabled = false; input.focus(); }
    } else {
        const idx = dvPrescriptions.findIndex(p => p.drug === drug);
        if (idx !== -1) { dvPrescriptions.splice(idx, 1); dvRenderRx(); }
        row.classList.remove('rx-checked');
        if (input) { input.disabled = true; input.value = cb.dataset.sig; }
    }
}

function dvUpdateSigInline(input, drug) {
    const rx = dvPrescriptions.find(p => p.drug === drug);
    if (rx) { rx.dosage = input.value; }
}

function dvSyncChecklist() {
    document.querySelectorAll('.dv-rx-checkbox').forEach(cb => {
        const rx     = dvPrescriptions.find(p => p.drug === cb.value);
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
    const count = document.getElementById('dvRxCount');
    if (count) count.textContent = dvPrescriptions.length;
}

function dvFilterRx(val) { dvBuildChecklist(val); }

function dvAddRx() {
    const drug   = document.getElementById('dvRxDrug').value.trim();
    const dosage = document.getElementById('dvRxDosage').value.trim();
    if (!drug) return;
    if (!dvPrescriptions.find(p => p.drug === drug)) {
        dvPrescriptions.push({ drug, dosage });
        dvRenderRx();
        dvSyncChecklist();
    }
    document.getElementById('dvRxDrug').value   = '';
    document.getElementById('dvRxDosage').value = '';
}
function dvRemoveRx(i) { dvPrescriptions.splice(i, 1); dvRenderRx(); dvSyncChecklist(); }
function dvRenderRx() {
    const list  = document.getElementById('dvRxList');
    const count = document.getElementById('dvRxCount');
    if (count) count.textContent = dvPrescriptions.length;
    list.innerHTML = dvPrescriptions.length === 0
        ? '<div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No prescriptions added.</div>'
        : dvPrescriptions.map((rx, i) => `
            <div class="rx-added-item">
                <div class="rx-added-info">
                    <i class="bi bi-capsule-pill me-1 text-primary"></i>
                    <strong>${rx.drug}</strong>
                    ${rx.dosage ? '<span class="text-muted ms-1">— ' + rx.dosage + '</span>' : ''}
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                        onclick="dvRemoveRx(${i})"><i class="bi bi-x-circle-fill"></i></button>
            </div>`).join('');
    document.getElementById('dvRxJson').value = JSON.stringify(dvPrescriptions);
}

// Build checklist when modal opens
document.getElementById('addVisitModal')?.addEventListener('shown.bs.modal', () => {
    dvBuildChecklist();
    dvRenderRx();
});
</script>
@endpush

@endsection