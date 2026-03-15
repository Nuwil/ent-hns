@extends('layouts.app')
@section('title', 'Admin Dashboard')
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
                <div class="stat-card-label">Pending</div>
            </div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <div class="stat-card-body">
                <div class="stat-card-num">{{ $stats['total_visits'] }}</div>
                <div class="stat-card-label">Total Visits</div>
            </div>
        </div>
    </div>

    {{-- Dual widget row --}}
    <div class="row g-4 mt-1">

        {{-- LEFT: Activity Log (wider) --}}
        <div class="col-lg-7">
            @include('partials.activity-log', ['activityLogs' => $activityLogs])
        </div>

        {{-- RIGHT: Quick Stats widgets --}}
        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- Today's appointment list --}}
            <div class="card-panel flex-1">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-calendar2-day me-2"></i>Recent Appointments
                    </div>
                </div>
                <div class="card-panel-body p-0">
                    @forelse($recentAppointments as $appt)
                        <div class="dash-appt-item">
                            <div class="dash-appt-time">
                                {{ $appt->scheduled_at->format('M j') }}<br>
                                <small>{{ $appt->scheduled_at->format('H:i') }}</small>
                            </div>
                            <div class="dash-appt-info">
                                <div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px">
                                    {{ $appt->doctor->name ?? '—' }} · {{ Str::limit($appt->reason, 30) }}
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

        </div>
    </div>

</div>
@endsection