@extends('layouts.app')
@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
@php
    $role           = auth()->user()->role;
    $monthName      = \Carbon\Carbon::create($calYear, $calMonth)->format('F Y');
    $daysInMonth    = \Carbon\Carbon::create($calYear, $calMonth)->daysInMonth;
    $firstDayOfWeek = \Carbon\Carbon::create($calYear, $calMonth, 1)->dayOfWeek;
    $today          = now()->format('Y-m-d');
    $prevMonth      = \Carbon\Carbon::create($calYear, $calMonth)->subMonth();
    $nextMonth      = \Carbon\Carbon::create($calYear, $calMonth)->addMonth();
    $apptsByDate    = $calendarAppointments->groupBy('date');
@endphp

<div class="page-content">

    <div class="page-header-row">
        <h1 class="page-heading">Appointments</h1>
        <div class="d-flex gap-2 align-items-center">
            <div class="view-toggle-group">
                <button class="view-toggle-btn" id="btnCalendar" onclick="switchView('calendar')">
                    <i class="bi bi-calendar3"></i> Calendar
                </button>
                <button class="view-toggle-btn" id="btnList" onclick="switchView('list')">
                    <i class="bi bi-list-ul"></i> List
                </button>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
                <i class="bi bi-calendar-plus-fill me-1"></i>Book Appointment
            </button>
        </div>
    </div>

    {{-- Secretary notice --}}
    @if($role === 'secretary')
        <div class="alert alert-info d-flex gap-2 align-items-start mb-3" style="font-size:13px">
            <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
            <span>
                Appointments you book are set to <strong>Pending</strong> by default.
                Only the <strong>Doctor</strong> can confirm or cancel appointments.
            </span>
        </div>
    @endif

    {{-- ── CALENDAR VIEW ────────────────────────────────────────── --}}
    <div id="calendarView" style="display:none">
        <div class="card-panel">
            <div class="cal-header">
                <a href="?cal_month={{ $prevMonth->month }}&cal_year={{ $prevMonth->year }}" class="cal-nav-btn">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <h2 class="cal-month-title">{{ $monthName }}</h2>
                <a href="?cal_month={{ $nextMonth->month }}&cal_year={{ $nextMonth->year }}" class="cal-nav-btn">
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="?cal_month={{ now()->month }}&cal_year={{ now()->year }}"
                   class="btn btn-sm btn-outline-primary ms-3">Today</a>
            </div>

            <div class="cal-grid cal-day-labels">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div class="cal-day-label">{{ $day }}</div>
                @endforeach
            </div>

            <div class="cal-grid cal-body">
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="cal-cell cal-cell-empty"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr  = \Carbon\Carbon::create($calYear, $calMonth, $day)->format('Y-m-d');
                        $dayAppts = $apptsByDate->get($dateStr, collect());
                        $isToday  = $dateStr === $today;
                    @endphp
                    <div class="cal-cell {{ $isToday ? 'cal-cell-today' : '' }}">
                        <div class="cal-day-num {{ $isToday ? 'cal-today-num' : '' }}">{{ $day }}</div>
                        @foreach($dayAppts->take(3) as $appt)
                            <div class="cal-appt cal-appt-{{ $appt['status'] }}"
                                 onclick="showApptDetail({{ json_encode($appt) }})"
                                 title="{{ $appt['time'] }} — {{ $appt['patient'] }}">
                                <span class="cal-appt-time">{{ $appt['time'] }}</span>
                                <span class="cal-appt-name">{{ \Illuminate\Support\Str::limit($appt['patient'], 14) }}</span>
                            </div>
                        @endforeach
                        @if($dayAppts->count() > 3)
                            <div class="cal-more" onclick="showDayAppts('{{ $dateStr }}', {{ $dayAppts->toJson() }})">
                                +{{ $dayAppts->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <div class="cal-legend">
                <span class="cal-legend-item"><span class="cal-dot cal-appt-pending"></span> Pending</span>
                <span class="cal-legend-item"><span class="cal-dot cal-appt-accepted"></span> Confirmed</span>
                <span class="cal-legend-item"><span class="cal-dot cal-appt-completed"></span> Completed</span>
                <span class="cal-legend-item"><span class="cal-dot cal-appt-cancelled"></span> Cancelled</span>
            </div>
        </div>
    </div>

    {{-- ── LIST VIEW ────────────────────────────────────────────── --}}
    <div id="listView" style="display:none">
        <div class="card-panel mb-3">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-end" style="padding:16px 20px">
                <input type="hidden" name="view" value="list">
                <div>
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all"       {{ $status === 'all'       ? 'selected' : '' }}>All</option>
                        <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="accepted"  {{ $status === 'accepted'  ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="form-label small mb-1">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ request()->url() }}?view=list" class="btn btn-sm btn-outline-secondary">Reset</a>
            </form>
        </div>

        <div class="card-panel">
            <div class="table-responsive">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Scheduled</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                            <tr>
                                <td>
                                    <a href="{{ route("{$role}.patients.show", $appt->patient_id) }}"
                                       class="fw-semibold text-decoration-none">
                                        {{ $appt->patient->full_name ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $appt->doctor->name ?? '—' }}</td>
                                <td>{{ $appt->scheduled_at->format('M j, Y H:i') }}</td>
                                <td>{{ Str::limit($appt->reason, 45) }}</td>
                                <td>
                                    <span class="badge {{ $appt->statusBadgeClass() }}">
                                        {{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        <a href="{{ route("{$role}.patients.show", $appt->patient_id) }}"
                                           class="btn btn-xs btn-outline-primary">
                                            <i class="bi bi-person"></i>
                                        </a>
                                        {{-- DOCTOR ONLY actions --}}
                                        @if($role === 'doctor')
                                            @if($appt->isPending())
                                                <form method="POST"
                                                      action="{{ route('doctor.appointments.confirm', $appt) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs btn-success" title="Confirm">
                                                        <i class="bi bi-check2"></i> Confirm
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                      action="{{ route('doctor.appointments.cancel', $appt) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs btn-outline-danger" title="Cancel">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </form>
                                            @elseif($appt->isAccepted())
                                                <form method="POST"
                                                      action="{{ route('doctor.appointments.complete', $appt) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs btn-outline-success" title="Mark Complete">
                                                        <i class="bi bi-check2-all"></i> Complete
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        {{-- SECRETARY sees status only, no action buttons --}}
                                        @if($role === 'secretary' && $appt->isPending())
                                            <span class="text-muted small">
                                                <i class="bi bi-lock me-1"></i>Awaiting doctor
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No appointments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
                <div class="card-panel-footer">{{ $appointments->links() }}</div>
            @endif
        </div>
    </div>

</div>

{{-- Appointment Detail Modal --}}
<div class="modal fade" id="apptDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar2-event me-2"></i>Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="apptDetailBody"></div>
            <div class="modal-footer" id="apptDetailFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Day Overflow Modal --}}
<div class="modal fade" id="dayApptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayApptModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dayApptModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Book Appointment Modal --}}
<div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Book Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route("{$role}.appointments.store") }}">
                @csrf
                @if($role === 'secretary')
                    <div class="alert alert-info mx-3 mt-3 mb-0 py-2" style="font-size:13px">
                        <i class="bi bi-info-circle me-1"></i>
                        This appointment will be set to <strong>Pending</strong>. Doctor must confirm.
                    </div>
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">Select patient...</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                            @endforeach
                        </select>
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
.view-toggle-group { display:flex; background:#f1f5f9; border-radius:8px; padding:3px; gap:2px; }
.view-toggle-btn { background:none; border:none; padding:5px 14px; font-size:13px; font-weight:500; color:#64748b; border-radius:6px; cursor:pointer; transition:all 0.2s; }
.view-toggle-btn.active { background:white; color:#2563eb; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
.cal-header { display:flex; align-items:center; padding:16px 20px; border-bottom:1px solid #e2e8f0; gap:12px; }
.cal-month-title { font-size:16px; font-weight:700; flex:1; text-align:center; margin:0; }
.cal-nav-btn { width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e2e8f0; color:#475569; text-decoration:none; font-size:14px; transition:all 0.2s; }
.cal-nav-btn:hover { background:#f1f5f9; color:#2563eb; }
.cal-grid { display:grid; grid-template-columns:repeat(7, 1fr); }
.cal-day-labels { border-bottom:1px solid #e2e8f0; }
.cal-day-label { padding:8px 0; text-align:center; font-size:11px; font-weight:600; text-transform:uppercase; color:#94a3b8; letter-spacing:0.5px; }
.cal-body { border-left:1px solid #f1f5f9; }
.cal-cell { min-height:100px; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; padding:6px; }
.cal-cell:hover { background:#fafbfc; }
.cal-cell-empty { background:#fafbfc; }
.cal-cell-today { background:#eff6ff; }
.cal-day-num { font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; width:22px; height:22px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
.cal-today-num { background:#2563eb; color:white; }
.cal-appt { display:flex; align-items:center; gap:4px; font-size:11px; padding:2px 5px; border-radius:4px; margin-bottom:2px; cursor:pointer; overflow:hidden; white-space:nowrap; }
.cal-appt:hover { opacity:0.8; }
.cal-appt-pending   { background:#fef3c7; color:#92400e; border-left:3px solid #f59e0b; }
.cal-appt-accepted  { background:#e0f2fe; color:#075985; border-left:3px solid #0ea5e9; }
.cal-appt-completed { background:#dcfce7; color:#166534; border-left:3px solid #22c55e; }
.cal-appt-cancelled { background:#fee2e2; color:#991b1b; border-left:3px solid #ef4444; opacity:0.6; }
.cal-appt-time { font-weight:700; flex-shrink:0; }
.cal-appt-name { overflow:hidden; text-overflow:ellipsis; }
.cal-more { font-size:11px; color:#2563eb; font-weight:600; cursor:pointer; padding:1px 4px; }
.cal-more:hover { text-decoration:underline; }
.cal-legend { display:flex; gap:16px; padding:12px 20px; border-top:1px solid #e2e8f0; flex-wrap:wrap; }
.cal-legend-item { display:flex; align-items:center; gap:6px; font-size:12px; color:#64748b; }
.cal-dot { width:10px; height:10px; border-radius:3px; display:inline-block; }
.appt-detail-row { display:flex; gap:10px; margin-bottom:12px; align-items:flex-start; }
.appt-detail-icon { width:32px; height:32px; background:#eff6ff; color:#2563eb; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.appt-detail-label { font-size:11px; color:#94a3b8; }
.appt-detail-value { font-size:13.5px; font-weight:500; }
</style>
@endpush

@push('scripts')
<script>
const role = '{{ $role }}';

function switchView(view) {
    const isCalendar = view === 'calendar';
    document.getElementById('calendarView').style.display = isCalendar ? 'block' : 'none';
    document.getElementById('listView').style.display     = isCalendar ? 'none'  : 'block';
    document.getElementById('btnCalendar').classList.toggle('active', isCalendar);
    document.getElementById('btnList').classList.toggle('active', !isCalendar);
    localStorage.setItem('apptView', view);
}

document.addEventListener('DOMContentLoaded', function () {
    const urlView   = new URLSearchParams(window.location.search).get('view');
    const savedView = localStorage.getItem('apptView') || 'calendar';
    switchView(urlView || savedView);
});

function statusLabel(status) {
    return status === 'accepted' ? 'Confirmed' : status.charAt(0).toUpperCase() + status.slice(1);
}

function statusBadge(status) {
    const map = { pending:'badge-warning', accepted:'badge-info', completed:'badge-success', cancelled:'badge-danger' };
    return `<span class="badge ${map[status] || 'badge-secondary'}">${statusLabel(status)}</span>`;
}

function showApptDetail(appt) {
    const csrf       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const patientUrl = `/${role}/patients/${appt.patient_id}`;

    document.getElementById('apptDetailBody').innerHTML = `
        <div class="appt-detail-row">
            <div class="appt-detail-icon"><i class="bi bi-person"></i></div>
            <div><div class="appt-detail-label">Patient</div><div class="appt-detail-value">${appt.patient}</div></div>
        </div>
        <div class="appt-detail-row">
            <div class="appt-detail-icon"><i class="bi bi-calendar2"></i></div>
            <div><div class="appt-detail-label">Date & Time</div><div class="appt-detail-value">${appt.date} at ${appt.time}</div></div>
        </div>
        <div class="appt-detail-row">
            <div class="appt-detail-icon"><i class="bi bi-person-badge"></i></div>
            <div><div class="appt-detail-label">Doctor</div><div class="appt-detail-value">${appt.doctor}</div></div>
        </div>
        <div class="appt-detail-row">
            <div class="appt-detail-icon"><i class="bi bi-chat-text"></i></div>
            <div><div class="appt-detail-label">Reason</div><div class="appt-detail-value">${appt.reason}</div></div>
        </div>
        <div class="appt-detail-row">
            <div class="appt-detail-icon"><i class="bi bi-activity"></i></div>
            <div><div class="appt-detail-label">Status</div><div class="appt-detail-value">${statusBadge(appt.status)}</div></div>
        </div>
    `;

    let footer = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="${patientUrl}" class="btn btn-outline-primary"><i class="bi bi-person me-1"></i>View Patient</a>
    `;

    // Doctor only gets action buttons
    if (role === 'doctor') {
        if (appt.status === 'pending') {
            footer += `
                <form method="POST" action="/doctor/appointments/${appt.id}/confirm" class="d-inline">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Confirm</button>
                </form>
                <form method="POST" action="/doctor/appointments/${appt.id}/cancel" class="d-inline">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <button class="btn btn-outline-danger">Cancel</button>
                </form>
            `;
        } else if (appt.status === 'accepted') {
            footer += `
                <form method="POST" action="/doctor/appointments/${appt.id}/complete" class="d-inline">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <button class="btn btn-outline-success"><i class="bi bi-check2-all me-1"></i>Complete</button>
                </form>
            `;
        }
    }

    document.getElementById('apptDetailFooter').innerHTML = footer;
    new bootstrap.Modal(document.getElementById('apptDetailModal')).show();
}

function showDayAppts(dateStr, appts) {
    const label = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric' });
    document.getElementById('dayApptModalTitle').textContent = label;
    document.getElementById('dayApptModalBody').innerHTML = appts.map(a => `
        <div class="d-flex align-items-center gap-3 py-2 border-bottom" style="cursor:pointer"
             onclick="bootstrap.Modal.getInstance(document.getElementById('dayApptModal')).hide();
                      setTimeout(()=>showApptDetail(${JSON.stringify(a).replace(/"/g,'&quot;')}),300)">
            <div class="cal-appt cal-appt-${a.status}" style="font-size:12px;padding:3px 8px">
                <span class="cal-appt-time">${a.time}</span>
                <span>${a.patient}</span>
            </div>
            <div class="flex-1 small text-muted">${a.reason}</div>
            <i class="bi bi-chevron-right text-muted"></i>
        </div>
    `).join('');
    new bootstrap.Modal(document.getElementById('dayApptModal')).show();
}
</script>
@endpush

@endsection