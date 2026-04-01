@extends('layouts.app')
@section('title', $patient->full_name)
@section('page-title', 'Patient Profile')

@section('content')
    @php
        $role = auth()->user()->role;
        $entComplaints = \App\Http\Controllers\VisitController::entComplaintsList();
    @endphp

    <div class="page-content">

        {{-- Patient Header --}}
        <div class="card-panel mb-2">
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
                        @if($patient->occupation)
                            <span><i class="bi bi-briefcase me-1"></i>{{ $patient->occupation }}</span>
                        @endif
                        @if($patient->city || $patient->province)
                            <span><i
                                    class="bi bi-geo-alt me-1"></i>{{ collect([$patient->city, $patient->province])->filter()->implode(', ') }}</span>
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
                        <a href="{{ route('secretary.patients.edit', $patient) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#bookAppointmentModal">
                            <i class="bi bi-calendar-plus me-1"></i>Book Appointment
                        </button>
                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addNotesModal">
                            <i class="bi bi-sticky me-1"></i>Add Note
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#intakeVisitModal">
                            <i class="bi bi-clipboard2-plus me-1"></i>New Visit Entry
                        </button>
                    @endif
                    @if($role === 'doctor')
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#bookAppointmentModal">
                            <i class="bi bi-calendar-plus me-1"></i>Book Appointment
                        </button>
                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addNotesModal">
                            <i class="bi bi-sticky me-1"></i>Add Note
                        </button>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                            <i class="bi bi-clipboard2-pulse me-1"></i>New Visit
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Collapsible General Information --}}
        <div class="card-panel mb-4" style="border-top: none; border-radius: 0 0 12px 12px; margin-top: -8px;">
            <div class="patient-info-toggle" data-bs-toggle="collapse" data-bs-target="#patientGeneralInfo"
                style="cursor:pointer; display:flex; align-items:center; justify-content:space-between; padding: 10px 4px; user-select:none;">
                <span
                    style="font-size:12.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>General Information
                </span>
                <i class="bi bi-chevron-down patient-info-chevron text-muted"
                    style="font-size:13px; transition: transform 0.2s;"></i>
            </div>

            <div class="collapse" id="patientGeneralInfo">
                <hr class="mt-0 mb-3">
                <div class="row g-3" style="font-size:13.5px;">

                    {{-- Personal Details --}}
                    <div class="col-md-4">
                        <div class="patient-info-section-label">Personal Details</div>
                        <div class="patient-info-row">
                            <span class="patient-info-key"><i class="bi bi-cake2 me-1"></i>Birthday</span>
                            <span class="patient-info-val">{{ $patient->date_of_birth->format('F j, Y') }}</span>
                        </div>
                        <div class="patient-info-row">
                            <span class="patient-info-key"><i class="bi bi-calendar me-1"></i>Age</span>
                            <span class="patient-info-val">{{ $patient->age }} years old</span>
                        </div>
                        <div class="patient-info-row">
                            <span class="patient-info-key"><i class="bi bi-gender-ambiguous me-1"></i>Gender</span>
                            <span class="patient-info-val">{{ ucfirst($patient->gender) }}</span>
                        </div>
                        <div class="patient-info-row">
                            <span class="patient-info-key"><i class="bi bi-telephone me-1"></i>Phone</span>
                            <span class="patient-info-val">{{ $patient->phone }}</span>
                        </div>
                        @if($patient->occupation)
                            <div class="patient-info-row">
                                <span class="patient-info-key"><i class="bi bi-briefcase me-1"></i>Occupation</span>
                                <span class="patient-info-val">{{ $patient->occupation }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Address & Insurance --}}
                    <div class="col-md-4">
                        <div class="patient-info-section-label">Address & Insurance</div>
                        @if($patient->address || $patient->city || $patient->province)
                            <div class="patient-info-row">
                                <span class="patient-info-key"><i class="bi bi-geo-alt me-1"></i>Address</span>
                                <span class="patient-info-val">
                                    {{ collect([$patient->address, $patient->city, $patient->province])->filter()->implode(', ') }}
                                </span>
                            </div>
                        @else
                            <div class="patient-info-row">
                                <span class="patient-info-key"><i class="bi bi-geo-alt me-1"></i>Address</span>
                                <span class="patient-info-val text-muted">Not provided</span>
                            </div>
                        @endif
                        @if($patient->insurance_info)
                            <div class="patient-info-row">
                                <span class="patient-info-key"><i class="bi bi-shield-check me-1"></i>Insurance</span>
                                <span class="patient-info-val">{{ $patient->insurance_info }}</span>
                            </div>
                        @endif

                        {{-- Latest Vitals from most recent visit --}}
                        @php
                            $latestVitals = $patient->visits
                                ->filter(fn($v) => $v->weight || $v->height || $v->blood_pressure)
                                ->sortByDesc('visited_at')
                                ->first();
                        @endphp
                        @if($latestVitals)
                            <div class="patient-info-section-label mt-3">Latest Vitals
                                <span class="text-muted fw-normal" style="text-transform:none;font-size:10px">
                                    ({{ $latestVitals->visited_at->setTimezone('Asia/Manila')->format('M j, Y') }})
                                </span>
                            </div>
                            @if($latestVitals->blood_pressure)
                                <div class="patient-info-row">
                                    <span class="patient-info-key"><i class="bi bi-activity me-1"></i>Blood Pressure</span>
                                    <span class="patient-info-val">{{ $latestVitals->blood_pressure }}</span>
                                </div>
                            @endif
                            @if($latestVitals->weight)
                                <div class="patient-info-row">
                                    <span class="patient-info-key"><i class="bi bi-moisture me-1"></i>Weight</span>
                                    <span class="patient-info-val">{{ $latestVitals->weight }} kg</span>
                                </div>
                            @endif
                            @if($latestVitals->height)
                                <div class="patient-info-row">
                                    <span class="patient-info-key"><i class="bi bi-arrows-vertical me-1"></i>Height</span>
                                    <span class="patient-info-val">{{ $latestVitals->height }} cm</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Medical History & Allergies --}}
                    <div class="col-md-4">
                        <div class="patient-info-section-label">Medical Background</div>
                        @if($patient->allergies)
                            <div class="patient-info-row align-items-start">
                                <span class="patient-info-key"><i
                                        class="bi bi-exclamation-triangle me-1 text-danger"></i>Allergies</span>
                                <span class="patient-info-val text-danger fw-semibold">{{ $patient->allergies }}</span>
                            </div>
                        @endif
                        @if($patient->medical_history)
                            <div class="patient-info-row align-items-start mt-2">
                                <span class="patient-info-key" style="min-width:unset"><i
                                        class="bi bi-clipboard2-pulse me-1"></i>Medical & Vaccine History</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size:12.5px; white-space:pre-line; line-height:1.6;">
                                {{ $patient->medical_history }}
                            </div>
                        @else
                            <div class="patient-info-row">
                                <span class="patient-info-key"><i class="bi bi-clipboard2 me-1"></i>Medical History</span>
                                <span class="patient-info-val text-muted">Not recorded</span>
                            </div>
                        @endif
                    </div>

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
                        <div class="d-flex align-items-center gap-3">
                            {{-- Legend --}}
                            <div class="d-flex gap-2 align-items-center" style="font-size:11px;color:#64748b">
                                <span class="d-flex align-items-center gap-1">
                                    <span
                                        style="width:10px;height:10px;background:#f59e0b;border-radius:2px;display:inline-block"></span>Pending
                                </span>
                                <span class="d-flex align-items-center gap-1">
                                    <span
                                        style="width:10px;height:10px;background:#0ea5e9;border-radius:2px;display:inline-block"></span>In
                                    Progress
                                </span>
                                <span class="d-flex align-items-center gap-1">
                                    <span
                                        style="width:10px;height:10px;background:#22c55e;border-radius:2px;display:inline-block"></span>Completed
                                </span>
                            </div>
                            <span class="badge bg-secondary">{{ $patient->visits->count() }} visits</span>
                        </div>
                    </div>
                    <div class="card-panel-body p-0">
                        @forelse($patient->visits->sortByDesc('visited_at') as $loop_visit => $visit)
                            @php $isFirst = $loop_visit === 0; @endphp
                            <div class="visit-timeline-item">
                                {{-- Status stripe --}}
                                <div class="visit-status-stripe visit-stripe-{{ $visit->status }}"></div>

                                <div class="visit-timeline-inner">
                                    {{-- Collapsible header row (always visible) --}}
                                    <div class="visit-header visit-collapse-trigger" data-bs-toggle="collapse"
                                        data-bs-target="#visitBody{{ $visit->id }}" style="cursor:pointer">
                                        <div class="visit-header-left">
                                            <i class="bi bi-chevron-{{ $isFirst ? 'up' : 'down' }} visit-chevron me-1 text-muted"
                                                style="font-size:11px"></i>
                                            <div class="visit-date">
                                                {{ $visit->visited_at->setTimezone('Asia/Manila')->format('M j, Y') }}
                                            </div>
                                            @if($visit->ent_classification)
                                                <span class="visit-ent-tag">{{ $visit->ent_classification }}</span>
                                            @endif
                                            <span class="text-muted small">{{ $visit->chief_complaint }}</span>
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

                                    {{-- Collapsible body — first visit open, rest collapsed --}}
                                    <div class="collapse {{ $isFirst ? 'show' : '' }}" id="visitBody{{ $visit->id }}">

                                        {{-- Vitals --}}
                                        @if($visit->blood_pressure || $visit->weight || $visit->height)
                                            <div class="visit-vitals mt-2 d-flex flex-wrap gap-2">
                                                @if($visit->blood_pressure)
                                                    <span class="vital-pill"><i class="bi bi-activity me-1"></i>BP:
                                                        {{ $visit->blood_pressure }}</span>
                                                @endif
                                                @if($visit->weight)
                                                    <span class="vital-pill"><i class="bi bi-moisture me-1"></i>Wt: {{ $visit->weight }}
                                                        kg</span>
                                                @endif
                                                @if($visit->height)
                                                    <span class="vital-pill"><i class="bi bi-arrows-vertical me-1"></i>Ht:
                                                        {{ $visit->height }} cm</span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Doctor clinical content --}}
                                        @if($role === 'doctor' || $visit->isFinalized())
                                            @if($visit->history)
                                                <div class="visit-section mt-2">
                                                    <div class="visit-field-label">History</div>
                                                    <div class="visit-field-text">{{ $visit->history }}</div>
                                                </div>
                                            @endif
                                            @if($visit->physical_exam)
                                                <div class="visit-section mt-2">
                                                    <div class="visit-field-label">Physical Exam</div>
                                                    <div class="visit-field-text">{{ $visit->physical_exam }}</div>
                                                </div>
                                            @endif
                                            @if($visit->diagnosis)
                                                <div class="visit-section visit-diagnosis mt-2">
                                                    <div class="visit-field-label">Diagnosis</div>
                                                    <div class="visit-field-text fw-semibold">{{ $visit->diagnosis }}</div>
                                                </div>
                                            @endif
                                            @if(!empty($visit->prescriptions))
                                                <div class="visit-section mt-2">
                                                    <div class="visit-field-label"><i class="bi bi-capsule me-1"></i>Prescriptions</div>
                                                    <div class="prescription-pills mt-1">
                                                        @foreach($visit->prescriptions as $rx)
                                                            <span class="prescription-pill">
                                                                {{ $rx['drug'] ?? '' }}
                                                                @if(!empty($rx['dosage'])) — {{ $rx['dosage'] }} @endif
                                                                @if(!empty($rx['quantity'])) · Qty: {{ $rx['quantity'] }} @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($visit->plan_instructions)
                                                <div class="visit-section mt-2">
                                                    <div class="visit-field-label"><i class="bi bi-journal-text me-1"></i>Instructions
                                                    </div>
                                                    <div class="visit-field-text">{{ $visit->plan_instructions }}</div>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Notes --}}
                                        @if($visit->notes)
                                            <div class="visit-vitals mt-2">
                                                <i class="bi bi-sticky me-1 text-muted"></i>
                                                <span class="text-muted small">{{ $visit->notes }}</span>
                                            </div>
                                        @endif

                                        {{-- Secretary locked notice --}}
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
                                                    {{ $visit->finalized_at?->setTimezone('Asia/Manila')->format('M j, Y') }}
                                                @endif
                                            </div>
                                            <div class="visit-footer-actions">
                                                @if($visit->follow_up_date)
                                                    <span class="visit-followup">
                                                        <i class="bi bi-arrow-repeat me-1"></i>
                                                        Follow-up: {{ $visit->follow_up_date->format('M j, Y') }}
                                                    </span>
                                                @endif
                                                @if($role === 'secretary' && $visit->secretaryCanEdit())
                                                    <button class="btn btn-xs btn-outline-secondary"
                                                        onclick="openSecretaryEdit({{ $visit->toJson() }})">
                                                        <i class="bi bi-pencil me-1"></i>Edit Intake
                                                    </button>
                                                @endif
                                                @if($role === 'doctor' && $visit->doctorCanEdit())
                                                    <button class="btn btn-xs btn-primary"
                                                        onclick="openCompleteVisitModal({{ $visit->toJson() }})">
                                                        <i class="bi bi-clipboard2-pulse me-1"></i>
                                                        {{ $visit->isInProgress() ? 'Continue Visit' : 'Complete Visit' }}
                                                    </button>
                                                @endif
                                                @if($visit->isFinalized())
                                                    <span class="text-success small fw-semibold">
                                                        <i class="bi bi-lock-fill me-1"></i>Locked
                                                    </span>
                                                @endif
                                                @if($visit->isFinalized() && !empty($visit->prescriptions))
                                                    <a href="{{ route('visits.prescription.print', [$patient, $visit]) }}"
                                                        target="_blank" class="btn btn-xs btn-outline-secondary">
                                                        <i class="bi bi-printer me-1"></i>Print Rx
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>{{-- end collapse --}}
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
                                    {{ $appt->scheduled_at->format('M j, Y') }}
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

                <div class="card-panel">
                    <div class="card-panel-header">
                        <div class="card-panel-title"><i class="bi bi-sticky me-2"></i>Patient Notes</div>
                        <span class="badge bg-secondary">
                            {{ $patient->notes ? count(json_decode($patient->notes, true) ?? []) : 0 }}
                        </span>
                    </div>
                    <div class="card-panel-body p-0">
                        @php
                            $notesData = $patient->notes ? json_decode($patient->notes, true) : null;
                            $isJson = is_array($notesData);
                            $notesList = $isJson ? array_reverse($notesData) : [];
                        @endphp

                        @if(!$isJson && $patient->notes)
                            {{-- Legacy plain text note --}}
                            <div class="patient-note-item">
                                <div class="patient-note-meta">
                                    <i class="bi bi-person me-1"></i>Legacy note
                                </div>
                                <div class="patient-note-text">{{ $patient->notes }}</div>
                            </div>
                        @elseif($isJson && count($notesList))
                            @foreach($notesList as $note)
                                <div class="patient-note-item">
                                    <div class="patient-note-meta">
                                        <span><i class="bi bi-person me-1"></i>{{ $note['author'] ?? '—' }}</span>
                                        <span
                                            class="text-muted">{{ \Carbon\Carbon::parse($note['created_at'])->format('M j, Y') }}</span>
                                    </div>
                                    <div class="patient-note-text">{{ $note['text'] }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state-sm p-4">
                                <i class="bi bi-sticky"></i><span>No notes yet</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
    ADD NOTE MODAL — Both roles
    ================================================================ --}}
    <div class="modal fade" id="addNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-sticky me-2"></i>Add Note — {{ $patient->full_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route("{$role}.patients.note", $patient) }}">
                    @csrf
                    <div class="modal-body">
                        @if($patient->notes && is_array(json_decode($patient->notes, true)))
                            @php $existing = array_reverse(json_decode($patient->notes, true)); @endphp
                            <div class="mb-3 p-3 rounded"
                                style="background:#f8fafc;border:1px solid #e2e8f0;max-height:160px;overflow-y:auto">
                                <div class="text-muted small fw-semibold mb-2">Previous Notes</div>
                                @foreach($existing as $n)
                                    <div class="mb-2 pb-2 border-bottom">
                                        <div style="font-size:11px;color:#94a3b8">
                                            {{ $n['author'] ?? '' }} ·
                                            {{ \Carbon\Carbon::parse($n['created_at'])->format('M j, Y H:i') }}
                                        </div>
                                        <div style="font-size:13px;color:#334155">{{ $n['text'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <label class="form-label fw-semibold">New Note <span class="text-danger">*</span></label>
                        <textarea name="note_text" class="form-control" rows="4" placeholder="Type your note here..."
                            required></textarea>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-info-circle me-1"></i>Notes are appended — existing notes are never overwritten.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="cvPrintRxBtn" onclick="cvPrintRx()"
                            style="display:none">
                            <i class="bi bi-printer me-1"></i>Print Rx
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-sticky me-1"></i>Add Note
                        </button>
                    </div>
                </form>
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
                                <span>This visit entry will be marked <strong>Awaiting Doctor</strong>. The doctor will complete
                                    the clinical details.</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Assign Doctor <span
                                            class="text-danger">*</span></label>
                                    <select name="doctor_id" class="form-select" required>
                                        <option value="">Select doctor...</option>
                                        @foreach($doctors as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ENT Classification <span
                                            class="text-danger">*</span></label>
                                    <select name="ent_classification" id="intakeEntClass" class="form-select" required>
                                        <option value="">Auto-filled from complaint...</option>
                                        @foreach($entComplaints as $cat => $complaints)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Chief Complaint <span
                                            class="text-danger">*</span></label>
                                    <select name="chief_complaint" id="intakeCcSelect" class="form-select" required
                                        onchange="intakeCcChanged(this.value)">
                                        <option value="">Select complaint...</option>
                                        @foreach($entComplaints as $cat => $complaints)
                                            <optgroup label="{{ $cat }}">
                                                @foreach($complaints as $c)
                                                    <option value="{{ $c }}" data-cat="{{ $cat }}">{{ $c }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    <input type="text" name="chief_complaint_other" id="intakeCcOther" class="form-control mt-2"
                                        placeholder="Describe complaint..." style="display:none">
                                </div>

                                <div class="col-12">
                                    <div class="section-divider"><span>Vital Signs <span
                                                class="text-muted fw-normal small">(optional)</span></span></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Blood Pressure</label>
                                    <input type="text" name="blood_pressure" class="form-control" placeholder="e.g. 120/80">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Weight</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" class="form-control" placeholder="60" step="0.1"
                                            min="1" max="500">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Height <span class="text-muted small fw-normal">(if
                                            applicable)</span></label>
                                    <div class="input-group">
                                        <input type="number" name="height" class="form-control" placeholder="160" step="0.1"
                                            min="30" max="250">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Additional Notes for Doctor</label>
                                    <textarea name="intake_notes" class="form-control" rows="2"
                                        placeholder="Anything the doctor should know..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" id="cvPrintRxBtn" onclick="cvPrintRx()"
                                style="display:none">
                                <i class="bi bi-printer me-1"></i>Print Rx
                            </button>
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
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Visit Intake</h5>
                            <div class="text-muted small">You can only edit while status is <strong>Awaiting Doctor</strong>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editIntakeForm" onsubmit="return false">
                        @csrf @method('PUT')
                        <div class="modal-body">

                            {{-- Doctor selector --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assigned Doctor <span class="text-danger">*</span></label>
                                <select name="doctor_id" id="editDoctorSelect" class="form-select" required>
                                    <option value="">Select doctor...</option>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="editIntakeTabs">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#eiComplaint">
                                        <i class="bi bi-chat-square-text me-1"></i>Chief Complaint
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#eiVitals">
                                        <i class="bi bi-heart-pulse me-1"></i>Vital Signs
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- Complaint Tab --}}
                                <div class="tab-pane fade show active" id="eiComplaint">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">Chief Complaint <span
                                                    class="text-danger">*</span></label>
                                            <select id="editCcDropdown" class="form-select"
                                                onchange="editCcChanged(this.value)">
                                                <option value="">Select complaint...</option>
                                                @foreach($entComplaints as $cat => $complaints)
                                                    @if($cat !== 'Others')
                                                        <optgroup label="{{ $cat }}">
                                                            @foreach($complaints as $c)
                                                                <option value="{{ $c }}" data-cat="{{ $cat }}">{{ $c }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                                <option value="Others">— Others (specify manually) —</option>
                                            </select>
                                            <input type="text" id="editCcOther" class="form-control mt-2"
                                                placeholder="Describe complaint..." style="display:none">
                                            <input type="hidden" name="chief_complaint" id="editCcHidden" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">ENT Classification <span
                                                    class="text-danger">*</span></label>
                                            <select name="ent_classification" id="editEntClass" class="form-select" required>
                                                <option value="">Auto-filled from complaint...</option>
                                                @foreach($entComplaints as $cat => $complaints)
                                                    @if($cat !== 'Others')
                                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                                    @endif
                                                @endforeach
                                                <option value="Others">Others</option>
                                            </select>
                                            <div id="editEntAutoNote" class="text-muted mt-1"
                                                style="font-size:11px;display:none">
                                                <i class="bi bi-magic me-1 text-primary"></i>Auto-filled from complaint
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Vitals Tab --}}
                                <div class="tab-pane fade" id="eiVitals">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Blood Pressure</label>
                                            <input type="text" name="blood_pressure" id="editBp" class="form-control"
                                                placeholder="e.g. 120/80">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Weight</label>
                                            <div class="input-group">
                                                <input type="number" name="weight" id="editWeight" class="form-control"
                                                    placeholder="60" step="0.1" min="1" max="500">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Height <span
                                                    class="text-muted small fw-normal">(if applicable)</span></label>
                                            <div class="input-group">
                                                <input type="number" name="height" id="editHeight" class="form-control"
                                                    placeholder="160" step="0.1" min="30" max="250">
                                                <span class="input-group-text">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Notes for Doctor</label>
                                            <textarea name="intake_notes" id="editIntakeNotes" class="form-control" rows="2"
                                                placeholder="Anything the doctor should know..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" id="cvPrintRxBtn" onclick="cvPrintRx()"
                                style="display:none">
                                <i class="bi bi-printer me-1"></i>Print Rx
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="submitEditIntake()">
                                <i class="bi bi-check2 me-1"></i>Save Changes
                            </button>
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
                    <form id="dvVisitForm" method="POST" action="{{ route('doctor.visits.store', $patient) }}"
                        onsubmit="return false">
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
                                        <i class="bi bi-clipboard2-check me-1"></i>Assessment
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dvRx">
                                        <i class="bi bi-capsule me-1"></i>Plan
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="dvSubjective">
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">Chief Complaint <span
                                                    class="text-danger">*</span></label>
                                            <select id="dvCcDropdown" class="form-select" onchange="dvCcChanged(this.value)">
                                                <option value="">Select complaint...</option>
                                                @foreach($entComplaints as $cat => $complaints)
                                                    @if($cat !== 'Others')
                                                        <optgroup label="{{ $cat }}">
                                                            @foreach($complaints as $c)
                                                                <option value="{{ $c }}">{{ $c }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                                <option value="Others">— Others (specify manually) —</option>
                                            </select>
                                            {{-- Shown only when "Others" is selected --}}
                                            <input type="text" id="dvCcOtherInput" class="form-control mt-2"
                                                placeholder="Describe complaint..." style="display:none">
                                            <input type="hidden" name="chief_complaint" id="dvCcInput" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">ENT Classification <span
                                                    class="text-danger">*</span></label>
                                            <select name="ent_classification" id="dvEntClass" class="form-select" required>
                                                <option value="">Auto-filled from complaint...</option>
                                                @foreach($entComplaints as $cat => $complaints)
                                                    @if($cat !== 'Others')
                                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                                    @endif
                                                @endforeach
                                                <option value="Others">Others</option>
                                            </select>
                                            <div id="dvEntAutoNote" class="text-muted mt-1" style="font-size:11px;display:none">
                                                <i class="bi bi-magic me-1 text-primary"></i>Auto-filled from complaint
                                            </div>
                                            <div id="dvEntManualNote" class="text-muted mt-1"
                                                style="font-size:11px;display:none">
                                                <i class="bi bi-pencil me-1 text-warning"></i>Select classification manually
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">History</label>
                                            <textarea name="history" class="form-control" rows="4"
                                                placeholder="Onset, duration, character, aggravating/relieving factors..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="dvObjective">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Blood Pressure</label>
                                            <input type="text" name="blood_pressure" class="form-control"
                                                placeholder="e.g. 120/80">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Weight</label>
                                            <div class="input-group">
                                                <input type="number" name="weight" class="form-control" placeholder="60"
                                                    step="0.1" min="1" max="500">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Height</label>
                                            <div class="input-group">
                                                <input type="number" name="height" class="form-control" placeholder="160"
                                                    step="0.1" min="30" max="250">
                                                <span class="input-group-text">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Physical Exam</label>
                                            <textarea name="physical_exam" class="form-control" rows="5"
                                                placeholder="ENT exam findings..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="dvAssessment">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Diagnosis <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="diagnosis" class="form-control" rows="4"
                                                placeholder="Primary diagnosis..." required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="dvRx">
                                    <div class="row g-3">
                                        <div class="col-lg-5">
                                            <div class="rx-checklist-panel">
                                                <div class="rx-checklist-header">
                                                    <i class="bi bi-list-check me-1"></i>Common ENT Medicines
                                                    <input type="text" id="dvRxSearch" class="form-control form-control-sm mt-2"
                                                        placeholder="Search medicines..." oninput="dvFilterRx(this.value)">
                                                </div>
                                                <div class="rx-checklist-body" id="dvRxChecklistBody"></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <label class="form-label fw-semibold"><i class="bi bi-pencil me-1"></i>Manual
                                                Entry</label>
                                            <div class="row g-2 align-items-end mb-3">
                                                <div class="col-5">
                                                    <input type="text" id="dvRxDrug" class="form-control form-control-sm"
                                                        placeholder="Drug name">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" id="dvRxDosage" class="form-control form-control-sm"
                                                        placeholder="Dosage/Sig">
                                                </div>
                                                <div class="col-2">
                                                    <input type="number" id="dvRxQty" class="form-control form-control-sm"
                                                        placeholder="Qty" min="1">
                                                </div>
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                        onclick="dvAddRx()"><i class="bi bi-plus"></i></button>
                                                </div>
                                            </div>
                                            <label class="form-label fw-semibold">
                                                Prescriptions Added <span id="dvRxCount" class="badge bg-primary ms-1">0</span>
                                            </label>
                                            <div id="dvRxList"></div>
                                            <input type="hidden" name="prescriptions" id="dvRxJson">
                                            <div class="mt-3">
                                                <label class="form-label fw-semibold"><i
                                                        class="bi bi-journal-text me-1"></i>Instructions</label>
                                                <textarea name="plan_instructions" class="form-control" rows="2"
                                                    placeholder="Follow-up instructions, lifestyle advice..."></textarea>
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label fw-semibold">Follow-up Date</label>
                                                <input type="date" id="dvFollowUpDate" class="form-control"
                                                    style="max-width:200px" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                                    onchange="document.getElementById('dvFollowUpHidden').value = this.value">
                                                <input type="hidden" name="follow_up_date" id="dvFollowUpHidden">
                                                <div class="text-muted small mt-1">
                                                    <i class="bi bi-calendar-check me-1 text-success"></i>
                                                    Setting a follow-up date will auto-book an appointment.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" id="dvPrintRxBtn" onclick="dvPrintRx()"
                                style="display:none">
                                <i class="bi bi-printer me-1"></i>Print Rx
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="dvConfirmFinalize()">
                                <i class="bi bi-lock me-1"></i>Save & Finalize Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================
    DOCTOR — COMPLETE VISIT MODAL (pre-filled from existing visit)
    Same layout as Add Visit modal
    ================================================================ --}}
    @if($role === 'doctor')
        <div class="modal fade" id="completeVisitModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="completeVisitModalTitle">
                                <i class="bi bi-clipboard2-pulse me-2"></i>Complete Visit
                            </h5>
                            <div class="text-muted small" id="completeVisitModalMeta"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Intake summary banner --}}
                    <div id="completeVisitIntakeSummary" class="mx-3 mt-3 alert alert-info d-flex gap-3 align-items-start"
                        style="display:none!important">
                        <i class="bi bi-clipboard2 fs-5 flex-shrink-0 mt-1"></i>
                        <div id="completeVisitIntakeText" class="small"></div>
                    </div>

                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3" id="cvTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cvSubjective">
                                    <i class="bi bi-chat-square-text me-1"></i>Subjective
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cvObjective">
                                    <i class="bi bi-eye me-1"></i>Objective
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cvAssessment">
                                    <i class="bi bi-clipboard2-check me-1"></i>Assessment
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cvPlan">
                                    <i class="bi bi-capsule me-1"></i>Plan
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">

                            {{-- SUBJECTIVE --}}
                            <div class="tab-pane fade show active" id="cvSubjective">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">ENT Classification <span
                                                class="text-danger">*</span></label>
                                        <select id="cvEntClass" class="form-select" required>
                                            <option value="">Auto-filled from complaint...</option>
                                            @foreach($entComplaints as $cat => $complaints)
                                                <option value="{{ $cat }}">{{ $cat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Chief Complaint <span
                                                class="text-danger">*</span></label>
                                        <select id="cvCcDropdown" class="form-select" style="max-width:400px"
                                            onchange="cvCcChanged(this.value)">
                                            <option value="">Select complaint...</option>
                                            @foreach($entComplaints as $cat => $complaints)
                                                <optgroup label="{{ $cat }}">
                                                    @foreach($complaints as $c)
                                                        <option value="{{ $c }}">{{ $c }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <input type="text" id="cvCcInput" class="form-control mt-2"
                                            placeholder="Or type freely..." required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">History</label>
                                        <textarea id="cvHistory" class="form-control" rows="4"
                                            placeholder="Onset, duration, character, aggravating/relieving factors..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- OBJECTIVE --}}
                            <div class="tab-pane fade" id="cvObjective">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Blood Pressure</label>
                                        <input type="text" id="cvBloodPressure" class="form-control" placeholder="e.g. 120/80">
                                        <div id="cvBpNote" class="text-muted" style="font-size:11px;margin-top:3px"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Weight</label>
                                        <input type="text" id="cvWeightDisplay" class="form-control" readonly
                                            style="background:#f8fafc">
                                        <div class="text-muted" style="font-size:11px;margin-top:3px">Collected by secretary
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Height</label>
                                        <input type="text" id="cvHeightDisplay" class="form-control" readonly
                                            style="background:#f8fafc">
                                        <div class="text-muted" style="font-size:11px;margin-top:3px">Collected by secretary
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Physical Exam</label>
                                        <textarea id="cvPhysicalExam" class="form-control" rows="6"
                                            placeholder="ENT exam: ear canal, tympanic membrane, nasal cavity, oropharynx, neck..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- ASSESSMENT --}}
                            <div class="tab-pane fade" id="cvAssessment">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Diagnosis <span
                                                class="text-danger">*</span></label>
                                        <textarea id="cvDiagnosis" class="form-control" rows="4"
                                            placeholder="Primary diagnosis, ICD codes, differential diagnoses..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- PLAN --}}
                            <div class="tab-pane fade" id="cvPlan">
                                <div class="row g-3">
                                    <div class="col-lg-5">
                                        <label class="form-label fw-semibold"><i class="bi bi-list-check me-1"></i>Medicine
                                            Checklist</label>
                                        <div class="rx-checklist-panel">
                                            <div class="rx-checklist-header">
                                                Common ENT Medicines
                                                <input type="text" id="cvRxSearch" class="form-control form-control-sm mt-2"
                                                    placeholder="Search medicines..." oninput="cvFilterRx(this.value)">
                                            </div>
                                            <div class="rx-checklist-body" id="cvRxChecklistBody"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <label class="form-label fw-semibold"><i class="bi bi-pencil me-1"></i>Manual
                                            Entry</label>
                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-5">
                                                <input type="text" id="cvRxDrug" class="form-control form-control-sm"
                                                    placeholder="Drug name">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" id="cvRxDosage" class="form-control form-control-sm"
                                                    placeholder="Dosage/Sig">
                                            </div>
                                            <div class="col-2">
                                                <input type="number" id="cvRxQty" class="form-control form-control-sm"
                                                    placeholder="Qty" min="1">
                                            </div>
                                            <div class="col-1">
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                    onclick="cvAddRx()">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <label class="form-label fw-semibold">
                                            Prescriptions Added <span id="cvRxCount" class="badge bg-primary ms-1">0</span>
                                        </label>
                                        <div id="cvRxList"></div>
                                        <div class="mt-3">
                                            <label class="form-label fw-semibold"><i
                                                    class="bi bi-journal-text me-1"></i>Instructions</label>
                                            <textarea id="cvPlanInstructions" class="form-control" rows="2"
                                                placeholder="Follow-up instructions, lifestyle advice..."></textarea>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label fw-semibold">Follow-up Date</label>
                                            <input type="date" id="cvFollowUp" class="form-control" style="max-width:200px"
                                                min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="cvPrintRxBtn" onclick="cvPrintRx()"
                                style="display:none">
                                <i class="bi bi-printer me-1"></i>Print Rx
                            </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-outline-primary" onclick="cvSubmit('save')">
                            <i class="bi bi-floppy me-1"></i>Save Progress
                        </button>
                        <button type="button" class="btn btn-success" onclick="cvSubmit('finalize')">
                            <i class="bi bi-lock me-1"></i>Finalize & Lock Visit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden forms for complete visit modal --}}
        <form method="POST" id="cvSaveForm" onsubmit="return false">
            @csrf @method('PUT')
            <input type="hidden" name="ent_classification" id="cvf_ent">
            <input type="hidden" name="chief_complaint" id="cvf_cc">
            <input type="hidden" name="history" id="cvf_history">
            <input type="hidden" name="blood_pressure" id="cvf_bp">
            <input type="hidden" name="physical_exam" id="cvf_exam">
            <input type="hidden" name="diagnosis" id="cvf_diagnosis">
            <input type="hidden" name="plan_instructions" id="cvf_instructions">
            <input type="hidden" name="follow_up_date" id="cvf_followup">
            <input type="hidden" name="prescriptions" id="cvf_prescriptions">
        </form>
        <form method="POST" id="cvFinalizeForm" onsubmit="return false">
            @csrf @method('PATCH')
            <input type="hidden" name="ent_classification" id="cvff_ent">
            <input type="hidden" name="chief_complaint" id="cvff_cc">
            <input type="hidden" name="history" id="cvff_history">
            <input type="hidden" name="blood_pressure" id="cvff_bp">
            <input type="hidden" name="physical_exam" id="cvff_exam">
            <input type="hidden" name="diagnosis" id="cvff_diagnosis">
            <input type="hidden" name="plan_instructions" id="cvff_instructions">
            <input type="hidden" name="follow_up_date" id="cvff_followup">
            <input type="hidden" name="prescriptions" id="cvff_prescriptions">
        </form>
    @endif
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
                        {{-- Patient phone quick reference --}}
                        <div class="alert alert-light d-flex gap-2 py-2 mb-3"
                            style="font-size:13px;border:1px solid #e2e8f0">
                            <i class="bi bi-person-circle text-primary mt-1"></i>
                            <span>
                                <strong>{{ $patient->full_name }}</strong>
                                &nbsp;·&nbsp;<i class="bi bi-telephone me-1"></i>{{ $patient->phone }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Doctor <span class="text-danger">*</span></label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select doctor...</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">Date & Time <span class="text-danger">*</span></label>
                            <input type="date" name="scheduled_at" id="apptDatetime" class="form-control" required
                                min="{{ now()->addDay()->format('Y-m-d') }}">
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
                        <button type="button" class="btn btn-outline-secondary" id="cvPrintRxBtn" onclick="cvPrintRx()"
                            style="display:none">
                            <i class="bi bi-printer me-1"></i>Print Rx
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .vital-pill {
                display: inline-flex;
                align-items: center;
                background: #f0f9ff;
                border: 1px solid #bae6fd;
                border-radius: 20px;
                padding: 2px 10px;
                font-size: 12px;
                font-weight: 600;
                color: #0369a1;
            }

            /* ── Patient General Info Panel ──────────────────────────────── */
            .patient-info-section-label {
                font-size: 10.5px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .patient-info-row {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 5px 0;
                border-bottom: 1px solid #f1f5f9;
                font-size: 13px;
            }

            .patient-info-row:last-child {
                border-bottom: none;
            }

            .patient-info-key {
                min-width: 120px;
                flex-shrink: 0;
                color: #64748b;
                font-weight: 600;
                font-size: 12.5px;
            }

            .patient-info-val {
                color: #1e293b;
                flex: 1;
            }

            .patient-info-toggle:hover {
                opacity: 0.8;
            }

            .patient-info-chevron.rotated {
                transform: rotate(180deg);
            }

            /* ── Patient Notes ───────────────────────────────────────────── */
            .patient-note-item {
                padding: 12px 16px;
                border-bottom: 1px solid #f1f5f9;
            }

            .patient-note-item:last-child {
                border-bottom: none;
            }

            .patient-note-meta {
                display: flex;
                justify-content: space-between;
                font-size: 11px;
                color: #94a3b8;
                margin-bottom: 4px;
                font-weight: 600;
            }

            .patient-note-text {
                font-size: 13px;
                color: #334155;
                line-height: 1.5;
                white-space: pre-line;
            }

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

            .rx-cat-group {
                margin-bottom: 2px;
            }

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

            .rx-checklist-item:hover {
                background: #f1f5f9;
            }

            .rx-checklist-item.rx-checked {
                background: #eff6ff;
            }

            .rx-checkbox {
                flex-shrink: 0;
                accent-color: #2563eb;
                cursor: pointer;
                width: 15px;
                height: 15px;
            }

            .rx-checklist-item {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 5px 14px;
                transition: background 0.1s;
                width: 100%;
                flex-wrap: nowrap;
            }

            .rx-checklist-item:hover {
                background: #f1f5f9;
            }

            .rx-checklist-item.rx-checked {
                background: #eff6ff;
            }

            .rx-checkbox {
                flex-shrink: 0;
                accent-color: #2563eb;
                cursor: pointer;
                width: 15px;
                height: 15px;
            }

            .rx-item-name {
                font-size: 12px;
                font-weight: 600;
                color: #1e293b;
                line-height: 1.3;
                flex: 1;
                min-width: 80px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .rx-sig-input {
                width: 120px;
                flex-shrink: 0;
                font-size: 11px !important;
                padding: 2px 6px !important;
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

            .rx-qty-input {
                width: 50px;
                flex-shrink: 0;
                font-size: 11px !important;
                padding: 2px 4px !important;
                height: auto !important;
                border-color: #cbd5e1;
                border-radius: 5px !important;
                text-align: center;
            }

            .rx-qty-input:disabled {
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

            .rx-added-info {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 2px;
            }

            .visit-timeline-item {
                display: flex;
                border-bottom: 1px solid #f1f5f9;
                position: relative;
            }

            .visit-timeline-item:last-child {
                border-bottom: none;
            }

            .visit-status-stripe {
                width: 4px;
                flex-shrink: 0;
                border-radius: 0;
            }

            .visit-stripe-pending {
                background: #f59e0b;
            }

            .visit-stripe-in_progress {
                background: #0ea5e9;
            }

            .visit-stripe-finalized {
                background: #22c55e;
            }

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

            .visit-header-left {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .visit-date {
                font-weight: 700;
                font-size: 14px;
                color: #1e293b;
            }

            .visit-date span {
                font-weight: 400;
                color: #94a3b8;
                margin-left: 4px;
                font-size: 12px;
            }

            .visit-ent-tag {
                background: #eff6ff;
                color: #2563eb;
                font-size: 11px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 20px;
                border: 1px solid #bfdbfe;
            }

            .visit-lock-icon {
                color: #22c55e;
                font-size: 14px;
            }

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

            .visit-diagnosis {
                background: #f0fdf4;
                padding: 8px 10px;
                border-radius: 6px;
            }

            .visit-vitals {
                font-size: 12px;
            }

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

            .visit-meta-info {
                font-size: 11.5px;
                color: #94a3b8;
            }

            .visit-footer-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .visit-followup {
                font-size: 12px;
                color: #0891b2;
            }

            .section-divider {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 12px;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .section-divider::before,
            .section-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e2e8f0;
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
            const ccToEntMap = @json(\App\Http\Controllers\VisitController::ccToEntMap());

            // Secretary — CC→ENT auto-link
            function intakeCcChanged(value) {
                const entSel = document.getElementById('intakeEntClass');
                const otherBox = document.getElementById('intakeCcOther');
                const ccSelect = document.getElementById('intakeCcSelect');

                if (value === 'Others') {
                    otherBox.style.display = 'block';
                    otherBox.required = true;
                    ccSelect.name = '';           // disable original name
                    otherBox.name = 'chief_complaint';
                    entSel.value = '';
                    entSel.disabled = false;
                } else {
                    otherBox.style.display = 'none';
                    otherBox.required = false;
                    ccSelect.name = 'chief_complaint';
                    otherBox.name = 'chief_complaint_other';
                    // Auto-fill ENT classification
                    if (ccToEntMap[value]) {
                        entSel.value = ccToEntMap[value];
                    }
                }
            }

            // Secretary — edit intake modal
            function openSecretaryEdit(visit) {
                const form = document.getElementById('editIntakeForm');
                form.action = `/secretary/patients/{{ $patient->id }}/visits/${visit.id}`;

                // Doctor
                const docSel = document.getElementById('editDoctorSelect');
                if (docSel && visit.doctor_id) docSel.value = visit.doctor_id;

                // Chief Complaint
                const ccDropdown = document.getElementById('editCcDropdown');
                const ccHidden = document.getElementById('editCcHidden');
                const ccOther = document.getElementById('editCcOther');
                const entSel = document.getElementById('editEntClass');
                const autoNote = document.getElementById('editEntAutoNote');

                // Try to find complaint in dropdown options
                let found = false;
                if (visit.chief_complaint) {
                    Array.from(ccDropdown.options).forEach(opt => {
                        if (opt.value === visit.chief_complaint) { found = true; }
                    });
                }

                if (found) {
                    ccDropdown.value = visit.chief_complaint;
                    ccHidden.value = visit.chief_complaint;
                    ccOther.style.display = 'none';
                    entSel.value = visit.ent_classification || '';
                    entSel.disabled = true;
                    autoNote.style.display = 'block';
                } else if (visit.chief_complaint) {
                    // It's a custom "Others" complaint
                    ccDropdown.value = 'Others';
                    ccOther.style.display = 'block';
                    ccOther.value = visit.chief_complaint;
                    ccHidden.value = visit.chief_complaint;
                    entSel.value = visit.ent_classification || '';
                    entSel.disabled = false;
                    autoNote.style.display = 'none';
                }

                // Vitals
                document.getElementById('editBp').value = visit.blood_pressure || '';
                document.getElementById('editWeight').value = visit.weight || '';
                document.getElementById('editHeight').value = visit.height || '';
                document.getElementById('editIntakeNotes').value = visit.notes || '';

                new bootstrap.Modal(document.getElementById('editIntakeModal')).show();
            }

            function editCcChanged(value) {
                const ccHidden = document.getElementById('editCcHidden');
                const ccOther = document.getElementById('editCcOther');
                const entSel = document.getElementById('editEntClass');
                const autoNote = document.getElementById('editEntAutoNote');

                if (value === 'Others') {
                    ccOther.style.display = 'block';
                    ccOther.required = true;
                    ccHidden.value = '';
                    ccOther.oninput = () => { ccHidden.value = ccOther.value; };
                    entSel.value = '';
                    entSel.disabled = false;
                    autoNote.style.display = 'none';
                } else if (value) {
                    ccOther.style.display = 'none';
                    ccOther.required = false;
                    ccHidden.value = value;
                    if (ccToEntMap[value]) {
                        entSel.value = ccToEntMap[value];
                        entSel.disabled = true;
                        autoNote.style.display = 'block';
                    }
                } else {
                    ccOther.style.display = 'none';
                    ccHidden.value = '';
                    entSel.value = '';
                    entSel.disabled = false;
                    autoNote.style.display = 'none';
                }
            }

            function submitEditIntake() {
                const form = document.getElementById('editIntakeForm');
                const ccVal = document.getElementById('editCcHidden').value.trim();
                const entSel = document.getElementById('editEntClass');

                if (!ccVal) {
                    document.querySelector('[data-bs-target="#eiComplaint"]').click();
                    document.getElementById('editCcDropdown').classList.add('is-invalid');
                    setTimeout(() => document.getElementById('editCcDropdown').classList.remove('is-invalid'), 3000);
                    return;
                }
                if (!entSel.value) {
                    document.querySelector('[data-bs-target="#eiComplaint"]').click();
                    entSel.classList.add('is-invalid');
                    setTimeout(() => entSel.classList.remove('is-invalid'), 3000);
                    return;
                }

                // Re-enable ENT select so it submits
                entSel.disabled = false;

                form.onsubmit = null;
                form.submit();
            }

            // Doctor — modal prescription builder
            let dvPrescriptions = [];

            const entMedicines = {
                'Antibiotics': [
                    { name: 'Amoxicillin 500mg', usual: '1 cap TID x 7 days' },
                    { name: 'Amoxicillin-Clavulanate 625mg', usual: '1 tab BID x 7 days' },
                    { name: 'Azithromycin 500mg', usual: '1 tab OD x 3 days' },
                    { name: 'Ciprofloxacin 500mg', usual: '1 tab BID x 7 days' },
                    { name: 'Clindamycin 300mg', usual: '1 cap TID x 7 days' },
                    { name: 'Cefalexin 500mg', usual: '1 cap QID x 7 days' },
                ],
                'Antihistamines': [
                    { name: 'Cetirizine 10mg', usual: '1 tab OD at night' },
                    { name: 'Loratadine 10mg', usual: '1 tab OD' },
                    { name: 'Fexofenadine 120mg', usual: '1 tab OD' },
                    { name: 'Chlorphenamine 4mg', usual: '1 tab TID' },
                    { name: 'Diphenhydramine 25mg', usual: '1 cap TID PRN' },
                ],
                'Decongestants': [
                    { name: 'Pseudoephedrine 60mg', usual: '1 tab TID x 5 days' },
                    { name: 'Phenylephrine 10mg', usual: '1 tab TID x 5 days' },
                    { name: 'Oxymetazoline nasal spray', usual: '2 sprays each nostril BID x 3 days' },
                    { name: 'Xylometazoline nasal spray', usual: '2 sprays each nostril TID x 3 days' },
                ],
                'Nasal Steroids': [
                    { name: 'Fluticasone nasal spray', usual: '2 sprays each nostril OD' },
                    { name: 'Mometasone nasal spray', usual: '2 sprays each nostril OD' },
                    { name: 'Budesonide nasal spray', usual: '2 sprays each nostril BID' },
                    { name: 'Beclomethasone nasal spray', usual: '2 sprays each nostril BID' },
                ],
                'Analgesics / Anti-inflammatory': [
                    { name: 'Ibuprofen 400mg', usual: '1 tab TID after meals' },
                    { name: 'Mefenamic acid 500mg', usual: '1 cap TID after meals' },
                    { name: 'Paracetamol 500mg', usual: '1–2 tabs every 4–6 hrs PRN' },
                    { name: 'Celecoxib 200mg', usual: '1 cap BID after meals' },
                    { name: 'Prednisone 20mg', usual: '1 tab OD in the morning x 5 days' },
                ],
                'Ear / Otic': [
                    { name: 'Ciprofloxacin otic drops', usual: '4 drops affected ear BID x 7 days' },
                    { name: 'Ofloxacin otic drops', usual: '10 drops affected ear BID x 7 days' },
                    { name: 'Hydrocortisone-Neomycin drops', usual: '3 drops affected ear TID' },
                    { name: 'Carbamide peroxide ear drops', usual: '5 drops affected ear BID x 4 days' },
                ],
                'Mucolytics / Expectorants': [
                    { name: 'Carbocisteine 500mg', usual: '1 cap TID' },
                    { name: 'Bromhexine 8mg', usual: '1 tab TID' },
                    { name: 'Guaifenesin 200mg', usual: '1–2 tabs every 4 hrs' },
                    { name: 'N-Acetylcysteine 600mg', usual: '1 sachet dissolved in water OD' },
                ],
                'Antifungals': [
                    { name: 'Clotrimazole ear drops', usual: '4 drops affected ear BID x 14 days' },
                    { name: 'Fluconazole 150mg', usual: '1 tab OD x 7 days' },
                    { name: 'Nystatin oral suspension', usual: 'Gargle and swallow QID' },
                ],
                'Antacids / GI protection': [
                    { name: 'Omeprazole 20mg', usual: '1 cap OD before breakfast' },
                    { name: 'Esomeprazole 40mg', usual: '1 cap OD before breakfast' },
                    { name: 'Sucralfate suspension', usual: '10ml QID before meals' },
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
                        const existing = dvPrescriptions.find(p => p.drug === med.name);
                        const checked = existing ? 'checked' : '';
                        const sigVal = existing ? existing.dosage : med.usual;
                        const qtyVal = existing ? (existing.quantity || '') : '';
                        const inputDisabled = existing ? '' : 'disabled';
                        const safeName = med.name.replace(/'/g, "\\'");
                        html += `
                                            <div class="rx-checklist-item ${checked ? 'rx-checked' : ''}">
                                                <input type="checkbox" class="dv-rx-checkbox rx-checkbox" value="${med.name}"
                                                       data-usual="${med.usual}" ${checked} onchange="dvToggleChecklist(this)">
                                                <span class="rx-item-name" title="${med.name}">${med.name}</span>
                                                <input type="text" class="rx-sig-input form-control form-control-sm"
                                                       value="${sigVal}" placeholder="Dosage..."
                                                       ${inputDisabled}
                                                       oninput="dvUpdateSigInline(this, '${safeName}')">
                                                <input type="number" class="rx-qty-input form-control form-control-sm"
                                                       value="${qtyVal}" placeholder="Qty" min="1"
                                                       ${inputDisabled}
                                                       oninput="dvUpdateQtyInline(this, '${safeName}')">
                                            </div>`;
                    });
                    html += `</div>`;
                });
                body.innerHTML = html || '<div class="text-muted small p-3">No medicines found.</div>';
            }

            function dvToggleChecklist(cb) {
                const drug = cb.value;
                const row = cb.closest('.rx-checklist-item');
                const input = row.querySelector('.rx-sig-input');
                const qtyIn = row.querySelector('.rx-qty-input');
                const sig = input ? input.value.trim() || cb.dataset.usual : cb.dataset.usual;
                const qty = qtyIn ? qtyIn.value.trim() : '';

                if (cb.checked) {
                    if (!dvPrescriptions.find(p => p.drug === drug)) {
                        dvPrescriptions.push({ drug, dosage: sig, quantity: qty });
                        dvRenderRx();
                    }
                    row.classList.add('rx-checked');
                    if (input) { input.disabled = false; input.focus(); }
                    if (qtyIn) { qtyIn.disabled = false; }
                } else {
                    const idx = dvPrescriptions.findIndex(p => p.drug === drug);
                    if (idx !== -1) { dvPrescriptions.splice(idx, 1); dvRenderRx(); }
                    row.classList.remove('rx-checked');
                    if (input) { input.disabled = true; input.value = cb.dataset.usual; }
                    if (qtyIn) { qtyIn.disabled = true; qtyIn.value = ''; }
                }
            }

            function dvUpdateSigInline(input, drug) {
                const rx = dvPrescriptions.find(p => p.drug === drug);
                if (rx) rx.dosage = input.value;
            }

            function dvUpdateQtyInline(input, drug) {
                const rx = dvPrescriptions.find(p => p.drug === drug);
                if (rx) rx.quantity = input.value;
            }

            function dvSyncChecklist() {
                document.querySelectorAll('.dv-rx-checkbox').forEach(cb => {
                    const rx = dvPrescriptions.find(p => p.drug === cb.value);
                    const active = !!rx;
                    const row = cb.closest('.rx-checklist-item');
                    const input = row?.querySelector('.rx-sig-input');
                    const qtyIn = row?.querySelector('.rx-qty-input');
                    cb.checked = active;
                    row?.classList.toggle('rx-checked', active);
                    if (input) { input.disabled = !active; if (active && rx) input.value = rx.dosage; }
                    if (qtyIn) { qtyIn.disabled = !active; if (active && rx) qtyIn.value = rx.quantity || ''; }
                });
                const count = document.getElementById('dvRxCount');
                if (count) count.textContent = dvPrescriptions.length;
            }

            function dvFilterRx(val) { dvBuildChecklist(val); }

            function dvAddRx() {
                const drug = document.getElementById('dvRxDrug').value.trim();
                const dosage = document.getElementById('dvRxDosage').value.trim();
                const qty = document.getElementById('dvRxQty').value.trim();
                if (!drug) return;
                if (!dvPrescriptions.find(p => p.drug === drug)) {
                    dvPrescriptions.push({ drug, dosage, quantity: qty });
                    dvRenderRx();
                    dvSyncChecklist();
                }
                document.getElementById('dvRxDrug').value = '';
                document.getElementById('dvRxDosage').value = '';
                document.getElementById('dvRxQty').value = '';
            }
            function dvRemoveRx(i) { dvPrescriptions.splice(i, 1); dvRenderRx(); dvSyncChecklist(); }
            function dvPrintRx() {
                alert('Please save and finalize the visit first, then use the Print Rx button on the saved visit.');
            }
            function dvRenderRx() {
                const list = document.getElementById('dvRxList');
                const count = document.getElementById('dvRxCount');
                const printBtn = document.getElementById('dvPrintRxBtn');
                if (count) count.textContent = dvPrescriptions.length;
                if (printBtn) {
                    printBtn.style.display = dvPrescriptions.length > 0 ? 'inline-flex' : 'none';
                }
                list.innerHTML = dvPrescriptions.length === 0
                    ? '<div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No prescriptions added.</div>'
                    : dvPrescriptions.map((rx, i) => `
                                            <div class="rx-added-item">
                                                <div class="rx-added-info">
                                                    <i class="bi bi-capsule-pill me-1 text-primary"></i>
                                                    <strong>${rx.drug}</strong>
                                                    ${rx.dosage ? '<span class="text-muted ms-1">— ' + rx.dosage + '</span>' : ''}
                                                    ${rx.quantity ? '<span class="badge bg-secondary ms-1">Qty: ' + rx.quantity + '</span>' : ''}
                                                </div>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                                                        onclick="dvRemoveRx(${i})"><i class="bi bi-x-circle-fill"></i></button>
                                            </div>`).join('');
                document.getElementById('dvRxJson').value = JSON.stringify(dvPrescriptions);
            }

            // Doctor modal — CC→ENT auto-link
            function dvCcChanged(value) {
                const ccHidden = document.getElementById('dvCcInput');
                const ccOther = document.getElementById('dvCcOtherInput');
                const entSel = document.getElementById('dvEntClass');
                const autoNote = document.getElementById('dvEntAutoNote');
                const manualNote = document.getElementById('dvEntManualNote');

                if (value === 'Others') {
                    // Show free text input, reset ENT to manual
                    ccOther.style.display = 'block';
                    ccOther.required = true;
                    ccHidden.value = '';
                    ccOther.oninput = () => { ccHidden.value = ccOther.value; };
                    entSel.value = '';
                    entSel.disabled = false;
                    autoNote.style.display = 'none';
                    manualNote.style.display = 'block';
                } else if (value) {
                    // Auto-fill from map
                    ccOther.style.display = 'none';
                    ccOther.required = false;
                    ccHidden.value = value;
                    if (ccToEntMap[value]) {
                        entSel.value = ccToEntMap[value];
                        entSel.disabled = true;
                        autoNote.style.display = 'block';
                        manualNote.style.display = 'none';
                    }
                } else {
                    ccOther.style.display = 'none';
                    ccOther.required = false;
                    ccHidden.value = '';
                    entSel.value = '';
                    entSel.disabled = false;
                    autoNote.style.display = 'none';
                    manualNote.style.display = 'none';
                }
            }

            function dvConfirmFinalize() {
                const form = document.getElementById('dvVisitForm');
                const ccInput = document.getElementById('dvCcInput');
                const diagInput = form.querySelector('textarea[name="diagnosis"]');
                const entSelect = document.getElementById('dvEntClass');

                if (!entSelect?.value) {
                    document.querySelector('[data-bs-target="#dvSubjective"]').click();
                    entSelect.classList.add('is-invalid');
                    setTimeout(() => entSelect.classList.remove('is-invalid'), 3000);
                    return;
                }
                if (!ccInput?.value.trim()) {
                    document.querySelector('[data-bs-target="#dvSubjective"]').click();
                    ccInput.focus(); ccInput.classList.add('is-invalid');
                    setTimeout(() => ccInput.classList.remove('is-invalid'), 3000);
                    return;
                }
                if (!diagInput?.value.trim()) {
                    document.querySelector('[data-bs-target="#dvAssessment"]').click();
                    diagInput.focus(); diagInput.classList.add('is-invalid');
                    setTimeout(() => diagInput.classList.remove('is-invalid'), 3000);
                    return;
                }

                if (!confirm('Finalize this visit? It will be permanently locked and cannot be edited.')) return;

                // Re-enable ENT select in case it was disabled by auto-fill
                if (entSelect) entSelect.disabled = false;

                // Sync chief complaint from Other input if needed
                const ccOther = document.getElementById('dvCcOtherInput');
                if (ccOther && ccOther.style.display !== 'none' && ccOther.value.trim()) {
                    ccInput.value = ccOther.value.trim();
                }

                // Sync prescriptions
                document.getElementById('dvRxJson').value = JSON.stringify(dvPrescriptions);

                // ── Explicitly ensure follow_up_date is in the form ──────────
                // The Plan tab may not be active, so we force-inject the value
                const fuDate = document.getElementById('dvFollowUpDate');
                let fuHidden = form.querySelector('input[name="follow_up_date"]');
                if (!fuHidden) {
                    fuHidden = document.createElement('input');
                    fuHidden.type = 'hidden';
                    fuHidden.name = 'follow_up_date';
                    form.appendChild(fuHidden);
                }
                fuHidden.value = fuDate ? fuDate.value : '';

                // Submit
                form.onsubmit = null;
                form.submit();
            }

            // Build checklist when modal opens
            document.getElementById('addVisitModal')?.addEventListener('shown.bs.modal', () => {
                dvBuildChecklist();
                dvRenderRx();
            });

            // Reset state when modal closes
            document.getElementById('addVisitModal')?.addEventListener('hidden.bs.modal', () => {
                const entSel = document.getElementById('dvEntClass');
                if (entSel) { entSel.disabled = false; entSel.value = ''; }
                const ccOther = document.getElementById('dvCcOtherInput');
                if (ccOther) { ccOther.style.display = 'none'; ccOther.value = ''; }
                const ccHidden = document.getElementById('dvCcInput');
                if (ccHidden) ccHidden.value = '';
                document.getElementById('dvCcDropdown').value = '';
                document.getElementById('dvEntAutoNote').style.display = 'none';
                document.getElementById('dvEntManualNote').style.display = 'none';
                const fuDate = document.getElementById('dvFollowUpDate');
                if (fuDate) fuDate.value = '';
                const fuHidden = document.getElementById('dvFollowUpHidden');
                if (fuHidden) fuHidden.value = '';
                dvPrescriptions = [];
            });

            // ── Complete Visit Modal ──────────────────────────────────────
            let cvPrescriptions = [];
            let cvVisitId = null;

            function openCompleteVisitModal(visit) {
                cvVisitId = visit.id;
                cvPrescriptions = Array.isArray(visit.prescriptions) ? visit.prescriptions : [];

                // Set modal title & meta
                document.getElementById('completeVisitModalTitle').innerHTML =
                    `<i class="bi bi-clipboard2-pulse me-2"></i>${visit.status === 'in_progress' ? 'Continue Visit' : 'Complete Visit'}`;
                document.getElementById('completeVisitModalMeta').textContent =
                    `Started: ${visit.visited_at ? new Date(visit.visited_at).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : ''}`;

                // Show intake summary if secretary created it
                const summaryEl = document.getElementById('completeVisitIntakeSummary');
                const summaryTxt = document.getElementById('completeVisitIntakeText');
                const hasSummary = visit.blood_pressure || visit.weight || visit.height || visit.notes;
                if (hasSummary) {
                    let parts = [];
                    if (visit.blood_pressure) parts.push(`<strong>BP:</strong> ${visit.blood_pressure}`);
                    if (visit.weight) parts.push(`<strong>Wt:</strong> ${visit.weight} kg`);
                    if (visit.height) parts.push(`<strong>Ht:</strong> ${visit.height} cm`);
                    if (visit.notes) parts.push(`<em>${visit.notes}</em>`);
                    summaryTxt.innerHTML = `<strong>Secretary Intake:</strong> ${parts.join(' · ')}`;
                    summaryEl.style.display = 'flex';
                } else {
                    summaryEl.style.display = 'none';
                }

                // Pre-fill Subjective
                const entSel = document.getElementById('cvEntClass');
                if (entSel && visit.ent_classification) entSel.value = visit.ent_classification;
                const ccInput = document.getElementById('cvCcInput');
                if (ccInput) ccInput.value = visit.chief_complaint || '';
                const histInput = document.getElementById('cvHistory');
                if (histInput) histInput.value = visit.history || '';

                // Pre-fill Objective
                const bpInput = document.getElementById('cvBloodPressure');
                if (bpInput) bpInput.value = visit.blood_pressure || '';
                const bpNote = document.getElementById('cvBpNote');
                if (bpNote) bpNote.textContent = visit.blood_pressure
                    ? `Secretary recorded: ${visit.blood_pressure}` : 'Not yet recorded';
                document.getElementById('cvWeightDisplay').value = visit.weight ? visit.weight + ' kg' : '—';
                document.getElementById('cvHeightDisplay').value = visit.height ? visit.height + ' cm' : '—';
                const examInput = document.getElementById('cvPhysicalExam');
                if (examInput) examInput.value = visit.physical_exam || '';

                // Pre-fill Assessment
                const diagInput = document.getElementById('cvDiagnosis');
                if (diagInput) diagInput.value = visit.diagnosis || '';

                // Pre-fill Plan
                const instrInput = document.getElementById('cvPlanInstructions');
                if (instrInput) instrInput.value = visit.plan_instructions || '';
                const fuInput = document.getElementById('cvFollowUp');
                if (fuInput) fuInput.value = visit.follow_up_date || '';

                // Set form actions
                const base = `/doctor/patients/{{ $patient->id }}/visits/${visit.id}`;
                document.getElementById('cvSaveForm').action = base;
                document.getElementById('cvFinalizeForm').action = base + '/finalize';

                // Mark as in_progress via AJAX
                if (visit.status === 'pending') {
                    fetch(`${base}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({
                            _method: 'PUT', status_only: true,
                            ent_classification: visit.ent_classification || '',
                            chief_complaint: visit.chief_complaint || '',
                            prescriptions: '[]'
                        })
                    });
                }

                cvBuildChecklist();
                cvRenderRx();

                new bootstrap.Modal(document.getElementById('completeVisitModal')).show();
            }

            function cvCcChanged(value) {
                if (!value) return;
                document.getElementById('cvCcInput').value = value;
                if (ccToEntMap[value]) {
                    const entSel = document.getElementById('cvEntClass');
                    if (entSel) entSel.value = ccToEntMap[value];
                }
            }

            function cvCollect(prefix) {
                document.getElementById(`${prefix}_ent`).value = document.getElementById('cvEntClass').value;
                document.getElementById(`${prefix}_cc`).value = document.getElementById('cvCcInput').value;
                document.getElementById(`${prefix}_history`).value = document.getElementById('cvHistory').value;
                document.getElementById(`${prefix}_bp`).value = document.getElementById('cvBloodPressure').value;
                document.getElementById(`${prefix}_exam`).value = document.getElementById('cvPhysicalExam').value;
                document.getElementById(`${prefix}_diagnosis`).value = document.getElementById('cvDiagnosis').value;
                document.getElementById(`${prefix}_instructions`).value = document.getElementById('cvPlanInstructions').value;
                document.getElementById(`${prefix}_followup`).value = document.getElementById('cvFollowUp').value;
                document.getElementById(`${prefix}_prescriptions`).value = JSON.stringify(cvPrescriptions);
            }

            function cvSubmit(action) {
                if (action === 'save') {
                    cvCollect('cvf');
                    const form = document.getElementById('cvSaveForm');
                    form.onsubmit = null; form.submit();
                } else {
                    const diag = document.getElementById('cvDiagnosis').value.trim();
                    if (!diag) {
                        document.querySelector('[data-bs-target="#cvAssessment"]').click();
                        const d = document.getElementById('cvDiagnosis');
                        d.focus(); d.classList.add('is-invalid');
                        setTimeout(() => d.classList.remove('is-invalid'), 3000);
                        return;
                    }
                    if (!confirm('Finalize this visit? It will be permanently locked and cannot be edited.')) return;
                    cvCollect('cvff');
                    const form = document.getElementById('cvFinalizeForm');
                    form.onsubmit = null; form.submit();
                }
            }

            // Rx for complete visit modal
            function cvAddRx() {
                const drug = document.getElementById('cvRxDrug').value.trim();
                const dosage = document.getElementById('cvRxDosage').value.trim();
                const qty = document.getElementById('cvRxQty').value.trim();
                if (!drug || cvPrescriptions.find(p => p.drug === drug)) return;
                cvPrescriptions.push({ drug, dosage, quantity: qty });
                document.getElementById('cvRxDrug').value = '';
                document.getElementById('cvRxDosage').value = '';
                document.getElementById('cvRxQty').value = '';
                cvRenderRx(); cvSyncRxChecklist();
            }
            function cvRemoveRx(i) { cvPrescriptions.splice(i, 1); cvRenderRx(); cvSyncRxChecklist(); }
            function cvRenderRx() {
                const list = document.getElementById('cvRxList');
                const count = document.getElementById('cvRxCount');
                const printBtn = document.getElementById('cvPrintRxBtn');
                if (count) count.textContent = cvPrescriptions.length;
                if (printBtn) {
                    printBtn.style.display = cvPrescriptions.length > 0 ? 'inline-flex' : 'none';
                }
                if (!list) return;
                list.innerHTML = cvPrescriptions.length === 0
                    ? '<div class="text-muted small py-2"><i class="bi bi-info-circle me-1"></i>No prescriptions added yet.</div>'
                    : cvPrescriptions.map((rx, i) => `
                                            <div class="rx-added-item">
                                                <div class="rx-added-info">
                                                    <i class="bi bi-capsule-pill me-1 text-primary"></i>
                                                    <strong>${rx.drug}</strong>
                                                    ${rx.dosage ? '<span class="text-muted ms-1">— ' + rx.dosage + '</span>' : ''}
                                                    ${rx.quantity ? '<span class="badge bg-secondary ms-1">Qty: ' + rx.quantity + '</span>' : ''}
                                                </div>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                                                        onclick="cvRemoveRx(${i})"><i class="bi bi-x-circle-fill"></i></button>
                                            </div>`).join('');
            }

            function cvPrintRx() {
                if (!cvVisitId) {
                    alert('Please save the visit before printing the prescription.');
                    return;
                }
                window.open(`/patients/{{ $patient->id }}/visits/${cvVisitId}/prescription/print`, '_blank');
            }

            function cvBuildChecklist(filter = '') {
                const body = document.getElementById('cvRxChecklistBody');
                if (!body) return;
                const fl = filter.toLowerCase();
                let html = '';
                Object.entries(entMedicines).forEach(([cat, meds]) => {
                    const filtered = fl ? meds.filter(m => m.name.toLowerCase().includes(fl)) : meds;
                    if (!filtered.length) return;
                    html += `<div class="rx-cat-group"><div class="rx-cat-label">${cat}</div>`;
                    filtered.forEach(med => {
                        const existing = cvPrescriptions.find(p => p.drug === med.name);
                        const checked = existing ? 'checked' : '';
                        const sigVal = existing ? existing.dosage : med.usual;
                        const qtyVal = existing ? (existing.quantity || '') : '';
                        const disabled = existing ? '' : 'disabled';
                        const safe = med.name.replace(/'/g, "\\'");
                        html += `
                                            <div class="rx-checklist-item ${checked ? 'rx-checked' : ''}">
                                                <input type="checkbox" class="cv-rx-checkbox rx-checkbox" value="${med.name}"
                                                       data-usual="${med.usual}" ${checked} onchange="cvToggleRx(this)">
                                                <span class="rx-item-name" title="${med.name}">${med.name}</span>
                                                <input type="text" class="rx-sig-input form-control form-control-sm"
                                                       value="${sigVal}" placeholder="Dosage..." ${disabled}
                                                       oninput="cvUpdateSig(this,'${safe}')">
                                                <input type="number" class="rx-qty-input form-control form-control-sm"
                                                       value="${qtyVal}" placeholder="Qty" min="1" ${disabled}
                                                       oninput="cvUpdateQty(this,'${safe}')">
                                            </div>`;
                    });
                    html += `</div>`;
                });
                body.innerHTML = html || '<div class="text-muted small p-3">No medicines match.</div>';
            }

            function cvToggleRx(cb) {
                const drug = cb.value;
                const row = cb.closest('.rx-checklist-item');
                const input = row.querySelector('.rx-sig-input');
                const qtyIn = row.querySelector('.rx-qty-input');
                const sig = input?.value.trim() || cb.dataset.usual;
                const qty = qtyIn?.value.trim() || '';
                if (cb.checked) {
                    if (!cvPrescriptions.find(p => p.drug === drug)) {
                        cvPrescriptions.push({ drug, dosage: sig, quantity: qty });
                        cvRenderRx();
                    }
                    row.classList.add('rx-checked');
                    if (input) { input.disabled = false; input.focus(); }
                    if (qtyIn) qtyIn.disabled = false;
                } else {
                    const idx = cvPrescriptions.findIndex(p => p.drug === drug);
                    if (idx !== -1) { cvPrescriptions.splice(idx, 1); cvRenderRx(); }
                    row.classList.remove('rx-checked');
                    if (input) { input.disabled = true; input.value = cb.dataset.usual; }
                    if (qtyIn) { qtyIn.disabled = true; qtyIn.value = ''; }
                }
            }
            function cvUpdateSig(input, drug) { const rx = cvPrescriptions.find(p => p.drug === drug); if (rx) rx.dosage = input.value; }
            function cvUpdateQty(input, drug) { const rx = cvPrescriptions.find(p => p.drug === drug); if (rx) rx.quantity = input.value; }
            function cvSyncRxChecklist() {
                document.querySelectorAll('.cv-rx-checkbox').forEach(cb => {
                    const rx = cvPrescriptions.find(p => p.drug === cb.value);
                    const active = !!rx;
                    const row = cb.closest('.rx-checklist-item');
                    const input = row?.querySelector('.rx-sig-input');
                    const qtyIn = row?.querySelector('.rx-qty-input');
                    cb.checked = active;
                    row?.classList.toggle('rx-checked', active);
                    if (input) { input.disabled = !active; if (active && rx) input.value = rx.dosage; }
                    if (qtyIn) { qtyIn.disabled = !active; if (active && rx) qtyIn.value = rx.quantity || ''; }
                });
                const count = document.getElementById('cvRxCount');
                if (count) count.textContent = cvPrescriptions.length;
            }
            function cvFilterRx(val) { cvBuildChecklist(val); }

            // ── Collapse chevron toggle ───────────────────────────────────
            document.addEventListener('DOMContentLoaded', () => {
                // Visit timeline chevrons
                document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
                    const targetId = trigger.getAttribute('data-bs-target');
                    const target = document.querySelector(targetId);
                    const chevron = trigger.querySelector('.visit-chevron');
                    if (!target || !chevron) return;
                    target.addEventListener('show.bs.collapse', () => chevron.classList.replace('bi-chevron-down', 'bi-chevron-up'));
                    target.addEventListener('hide.bs.collapse', () => chevron.classList.replace('bi-chevron-up', 'bi-chevron-down'));
                });

                // General info panel chevron
                const infoPanel = document.getElementById('patientGeneralInfo');
                const infoChevron = document.querySelector('.patient-info-chevron');
                if (infoPanel && infoChevron) {
                    infoPanel.addEventListener('show.bs.collapse', () => infoChevron.classList.add('rotated'));
                    infoPanel.addEventListener('hide.bs.collapse', () => infoChevron.classList.remove('rotated'));
                }
            });
        </script>
    @endpush

@endsection