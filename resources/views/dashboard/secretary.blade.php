@extends('layouts.app')
@section('title', 'Secretary Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-content">

    {{-- Stat Cards --}}
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['total_patients'] }}</div>
                <div class="stat-card-label">Total Patients</div>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-icon"><i class="bi bi-calendar2-check-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['today_appointments'] }}</div>
                <div class="stat-card-label">Today's Appointments</div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['pending_appointments'] }}</div>
                <div class="stat-card-label">Pending Confirmation</div>
            </div>
        </div>
        <div class="stat-card stat-card-teal">
            <div class="stat-card-icon"><i class="bi bi-calendar-week-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['upcoming_this_week'] }}</div>
                <div class="stat-card-label">This Week</div>
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
                        <i class="bi bi-calendar-check me-2"></i>Recent Appointments
                    </div>
                    <span class="badge bg-secondary">{{ $recentAppointments->count() }} entries</span>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($recentAppointments as $appt)
                        <div class="dash-appt-item" style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center;">
                            <div class="dash-appt-time" style="flex-shrink: 0; min-width: 70px;">
                                {{ $appt->scheduled_at->format('M j') }}<br>
                                <small>{{ $appt->scheduled_at->format('H:i') }}</small>
                            </div>
                            <div class="dash-appt-info" style="flex: 1; min-width: 0;">
                                <div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $appt->doctor->name ?? '—' }}</div>
                            </div>
                            <span class="badge {{ $appt->statusBadgeClass() }}" style="flex-shrink: 0;">
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
        </div>

        {{-- RIGHT: Today's schedule + Pending --}}
        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- Today's schedule --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-calendar2-day me-2"></i>Today's Schedule
                    </div>
                    <span class="text-muted small">{{ now()->format('M j, Y') }}</span>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($todayAppointments as $appt)
                        <div class="dash-appt-item">
                            <div class="dash-appt-time">
                                {{ $appt->scheduled_at->format('H:i') }}
                            </div>
                            <div class="dash-appt-info">
                                <div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">
                                    {{ $appt->doctor->name ?? '—' }}
                                </div>
                            </div>
                            <span class="badge {{ $appt->statusBadgeClass() }}">
                                {{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-state-sm p-4">
                            <i class="bi bi-calendar-x"></i><span>No appointments today</span>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pending confirmation --}}
            @if($pendingAppointments->count() > 0)
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-hourglass-split me-2 text-warning"></i>Awaiting Confirmation
                    </div>
                    <span class="badge bg-warning text-dark">{{ $pendingAppointments->count() }}</span>
                </div>
                <div class="card-panel-body p-0">
                    @foreach($pendingAppointments->take(5) as $appt)
                        <div class="dash-appt-item">
                            <div class="dash-appt-time">
                                {{ $appt->scheduled_at->format('M j') }}<br>
                                <small>{{ $appt->scheduled_at->format('H:i') }}</small>
                            </div>
                            <div class="dash-appt-info">
                                <div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">{{ Str::limit($appt->reason, 35) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection