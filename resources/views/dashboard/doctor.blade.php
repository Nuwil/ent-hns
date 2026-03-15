@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-content">

    {{-- Stat Cards --}}
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-icon"><i class="bi bi-calendar2-check-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['my_today_appointments'] }}</div>
                <div class="stat-card-label">Today's Appointments</div>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['my_patients'] }}</div>
                <div class="stat-card-label">My Patients</div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['pending_appointments'] }}</div>
                <div class="stat-card-label">Awaiting Confirmation</div>
            </div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['visits_this_month'] }}</div>
                <div class="stat-card-label">Visits This Month</div>
            </div>
        </div>
    </div>

    {{-- Dual widget row --}}
    <div class="row g-4 mt-1">

        {{-- LEFT: Recent Appointments --}}
        <div class="col-lg-7">
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-calendar2-week me-2"></i>Recent Appointments
                    </div>
                    <a href="{{ route('doctor.appointments.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table data-table mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Scheduled</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments as $appt)
                                    <tr>
                                        <td class="fw-semibold">{{ $appt->patient->full_name ?? '—' }}</td>
                                        <td>{{ $appt->scheduled_at->format('M j, Y H:i') }}</td>
                                        <td>{{ Str::limit($appt->reason, 40) }}</td>
                                        <td>
                                            <span class="badge {{ $appt->statusBadgeClass() }}">
                                                {{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('doctor.patients.show', $appt->patient_id) }}"
                                               class="btn btn-xs btn-outline-primary">
                                                <i class="bi bi-person"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-x d-block fs-3 mb-2"></i>No appointments yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Today's appointments + Recent visits --}}
        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- Today's appointments --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-calendar2-day me-2"></i>My Schedule Today
                    </div>
                    <span class="text-muted small">{{ now()->format('M j, Y') }}</span>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($myAppointments as $appt)
                        <a href="{{ route('doctor.patients.show', $appt->patient_id) }}"
                           class="dash-appt-item dash-appt-link">
                            <div class="dash-appt-time">{{ $appt->scheduled_at->format('H:i') }}</div>
                            <div class="dash-appt-info">
                                <div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">{{ Str::limit($appt->reason, 35) }}</div>
                            </div>
                            <span class="badge {{ $appt->statusBadgeClass() }}">
                                {{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}
                            </span>
                        </a>
                    @empty
                        <div class="empty-state-sm p-4">
                            <i class="bi bi-calendar-check"></i>
                            <span>No appointments today</span>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent visits --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-clipboard2-pulse me-2"></i>Recent Visits
                    </div>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($recentVisits as $visit)
                        <a href="{{ route('doctor.patients.show', $visit->patient_id) }}"
                           class="dash-appt-item dash-appt-link">
                            <div class="dash-appt-time">
                                {{ $visit->visited_at->format('M j') }}<br>
                                <small>{{ $visit->visited_at->format('H:i') }}</small>
                            </div>
                            <div class="dash-appt-info">
                                <div class="fw-semibold small">{{ $visit->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">
                                    {{ Str::limit($visit->chief_complaint, 40) }}
                                </div>
                            </div>
                            @if($visit->isIntakeOnly())
                                <span class="badge bg-warning text-dark" style="font-size:10px">Intake</span>
                            @else
                                <span class="badge bg-success" style="font-size:10px">Done</span>
                            @endif
                        </a>
                    @empty
                        <div class="empty-state-sm p-4">
                            <i class="bi bi-clipboard2-x"></i><span>No visits yet</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection