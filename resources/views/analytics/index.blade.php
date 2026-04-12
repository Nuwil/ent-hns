@extends('layouts.app')
@section('title', 'Analytics & Insights')
@section('page-title', 'Analytics & Insights')

@push('styles')
<style>
/* ── Analytics Narrative ─────────────────────────────────────── */
.analytics-narrative {
    background: #f8fafc;
    border-left: 3px solid #3b82f6;
    border-radius: 0 6px 6px 0;
    padding: 10px 14px;
    font-size: 12.5px;
    color: #475569;
    line-height: 1.6;
}
.analytics-narrative strong { color: #1e293b; }

/* ── Filter Bar ──────────────────────────────────────────────── */
.filter-bar {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.filter-range-btn {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s;
}
.filter-range-btn:hover  { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.filter-range-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.filter-divider { width: 1px; height: 24px; background: #e2e8f0; margin: 0 4px; }
.custom-range-inputs { display: flex; align-items: center; gap: 8px; }
.custom-range-inputs input[type="date"] {
    font-size: 12.5px; padding: 5px 10px;
    border: 1px solid #e2e8f0; border-radius: 7px;
    color: #334155;
}
.date-range-label {
    font-size: 11.5px; color: #94a3b8; margin-left: auto;
}

/* ── Analytics Tabs ──────────────────────────────────────────── */
.analytics-tabs .nav-link {
    font-weight: 600; font-size: 13.5px;
    color: #64748b; border-radius: 8px 8px 0 0;
    padding: 10px 20px;
}
.analytics-tabs .nav-link.active { color: #2563eb; background: #fff; border-bottom-color: #fff; }

/* ── KPI Cards ───────────────────────────────────────────────── */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
.kpi-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 16px;
    display: flex; align-items: center; gap: 12px;
}
.kpi-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.kpi-val   { font-size: 24px; font-weight: 800; line-height: 1; }
.kpi-label { font-size: 11.5px; color: #64748b; margin-top: 2px; }
.kpi-sub   { font-size: 11px; font-weight: 600; margin-top: 3px; }
.kpi-blue   .kpi-icon { background:#dbeafe; color:#2563eb; }
.kpi-green  .kpi-icon { background:#dcfce7; color:#16a34a; }
.kpi-orange .kpi-icon { background:#fef3c7; color:#d97706; }
.kpi-purple .kpi-icon { background:#ede9fe; color:#7c3aed; }
.kpi-red    .kpi-icon { background:#fee2e2; color:#dc2626; }
.kpi-teal   .kpi-icon { background:#ccfbf1; color:#0d9488; }

/* ── Doctor Selector ─────────────────────────────────────────── */
.doctor-selector {
    display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
}
.doctor-pill {
    padding: 5px 14px; border-radius: 20px;
    border: 1.5px solid #e2e8f0; background: #f8fafc;
    font-size: 12.5px; font-weight: 600; color: #475569;
    cursor: pointer; transition: all 0.15s; user-select: none;
}
.doctor-pill:hover  { border-color: #93c5fd; color: #2563eb; }
.doctor-pill.active { background: #2563eb; border-color: #2563eb; color: #fff; }

/* ── Doctor Card ─────────────────────────────────────────────── */
.doctor-perf-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 20px;
    margin-bottom: 16px;
}
.doctor-perf-header {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 16px;
}
.doctor-avatar-sm {
    width: 36px; height: 36px; border-radius: 50%;
    background: #2563eb; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; flex-shrink: 0;
}
.stat-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: #f1f5f9; border-radius: 7px;
    padding: 5px 10px; font-size: 12px;
}
.stat-pill-val { font-weight: 800; font-size: 15px; color: #1e293b; }

/* ── Rate Bars ───────────────────────────────────────────────── */
.rate-bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12.5px; }
.rate-bar-label { width: 130px; flex-shrink: 0; color: #475569; font-weight: 600; }
.rate-bar-track { flex: 1; height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
.rate-bar-fill  { height: 100%; border-radius: 10px; transition: width 0.6s ease; }
.rate-bar-pct   { width: 38px; text-align: right; font-weight: 700; font-size: 13px; }

/* ── Loading overlay ─────────────────────────────────────────── */
.analytics-loading {
    position: absolute; inset: 0;
    background: rgba(255,255,255,0.8);
    display: flex; align-items: center; justify-content: center;
    border-radius: 12px; z-index: 10;
}
.chart-wrap { position: relative; }

/* ── Comparison bar legend ───────────────────────────────────── */
.comparison-legend {
    display: flex; flex-wrap: wrap; gap: 10px;
    margin-bottom: 10px; font-size: 12px;
}
.legend-dot {
    width: 10px; height: 10px; border-radius: 50%;
    display: inline-block; margin-right: 4px;
}
</style>
@endpush

@section('content')
<div class="page-content">

    {{-- ── FILTER BAR ──────────────────────────────────────────── --}}
    <div class="filter-bar">
        <span style="font-size:12px;font-weight:700;color:#64748b;white-space:nowrap">
            <i class="bi bi-funnel me-1"></i>Time Range:
        </span>
        @foreach(['today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year'] as $val => $label)
            <button class="filter-range-btn {{ $val === 'month' ? 'active' : '' }}"
                    data-range="{{ $val }}" onclick="setRange('{{ $val }}')">
                {{ $label }}
            </button>
        @endforeach
        <div class="filter-divider"></div>
        <button class="filter-range-btn" data-range="custom" onclick="setRange('custom')">
            <i class="bi bi-calendar-range me-1"></i>Custom
        </button>
        <div id="customRangeInputs" class="custom-range-inputs" style="display:none">
            <input type="date" id="customStart" onchange="applyCustom()">
            <span style="font-size:12px;color:#94a3b8">to</span>
            <input type="date" id="customEnd" onchange="applyCustom()">
        </div>
        <span class="date-range-label" id="dateRangeLabel"></span>
    </div>

    {{-- ── MAIN TABS ────────────────────────────────────────────── --}}
    <ul class="nav nav-tabs analytics-tabs mb-0" id="analyticsTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabClinic">
                <i class="bi bi-hospital me-2"></i>Clinic Performance
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDoctor">
                <i class="bi bi-person-badge me-2"></i>Doctor Performance
            </button>
        </li>
    </ul>

    <div class="tab-content" style="background:#fff;border:1px solid #dee2e6;border-top:none;border-radius:0 0 12px 12px;padding:24px">

        {{-- ════════════════════════════════════════════════════════
             TAB 1 — CLINIC PERFORMANCE
        ════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="tabClinic">
            <div id="clinicLoading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="text-muted small mt-2">Loading clinic data...</div>
            </div>
            <div id="clinicContent" style="display:none">

                {{-- KPIs --}}
                <div class="kpi-grid" id="clinicKpis"></div>

                <div class="row g-4">
                    {{-- Visit Trend --}}
                    <div class="col-lg-8">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-graph-up me-2"></i>Visit Trend</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="clinicTrendChart" height="90"></canvas>
                                <div id="clinicTrendNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Appointment Status --}}
                    <div class="col-lg-4">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-pie-chart me-2"></i>Appointment Status</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="apptStatusChart" height="200"></canvas>
                                <div id="apptStatusNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Visit Status --}}
                    <div class="col-lg-4">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-clipboard2-check me-2"></i>Visit Status</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="visitStatusChart" height="200"></canvas>
                                <div id="visitStatusNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                    {{-- ENT Classification --}}
                    <div class="col-lg-4">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-bar-chart-steps me-2"></i>ENT Classifications</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="entClassChart" height="200"></canvas>
                                <div id="entClassNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Doctor Workload --}}
                    <div class="col-lg-4">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-people me-2"></i>Doctor Workload</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="workloadChart" height="200"></canvas>
                                <div id="workloadNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Top Chief Complaints --}}
                    <div class="col-12">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-list-ol me-2"></i>Top Chief Complaints</div>
                            </div>
                            <div class="card-panel-body" id="clinicComplaints"></div>
                            <div class="card-panel-body pt-0" id="complaintsNarrative"></div>
                        </div>
                    </div>
                    {{-- New Patients Trend --}}
                    <div class="col-12">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title"><i class="bi bi-person-plus me-2"></i>New Patients Registered</div>
                            </div>
                            <div class="card-panel-body chart-wrap">
                                <canvas id="patientTrendChart" height="70"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Predictive Forecast --}}
                    <div class="col-12">
                        <div class="card-panel">
                            <div class="card-panel-header">
                                <div class="card-panel-title">
                                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                                    Predictive Forecast — Next 30 Days
                                    <span class="badge bg-primary ms-2" style="font-size:10px;font-weight:600">Advanced TS</span>
                                </div>
                                <span class="text-muted small">Advanced time-series forecasting with weekday seasonality and OLS trend analysis</span>
                            </div>
                            <div class="card-panel-body">
                                <div id="forecastLoading" class="text-center py-3" style="display:none">
                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                    <span class="text-muted small">Calculating forecast...</span>
                                </div>
                                <canvas id="forecastChart" height="70"></canvas>
                                <div id="forecastNarrative" class="analytics-narrative mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             TAB 2 — DOCTOR PERFORMANCE
        ════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tabDoctor">
            <div id="doctorLoading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="text-muted small mt-2">Loading doctor data...</div>
            </div>
            <div id="doctorContent" style="display:none">

                {{-- Doctor selector — Admin only --}}
                @if($isAdmin)
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span style="font-size:12px;font-weight:700;color:#64748b">
                            <i class="bi bi-person-badge me-1"></i>Select Doctors:
                        </span>
                        <button class="btn btn-xs btn-outline-secondary" onclick="selectAllDoctors()">All</button>
                        <button class="btn btn-xs btn-outline-secondary" onclick="clearDoctors()">Clear</button>
                    </div>
                    <div class="doctor-selector" id="doctorPills">
                        @foreach($doctors as $doc)
                            <div class="doctor-pill active"
                                 data-id="{{ $doc->id }}"
                                 onclick="toggleDoctor(this, {{ $doc->id }})">
                                {{ $doc->full_name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Comparison bar chart — Admin only --}}
                <div class="card-panel mb-4">
                    <div class="card-panel-header">
                        <div class="card-panel-title"><i class="bi bi-bar-chart me-2"></i>Doctor Comparison — Visits</div>
                    </div>
                    <div class="card-panel-body chart-wrap">
                        <canvas id="doctorCompareChart" height="80"></canvas>
                    </div>
                </div>
                @else
                {{-- Doctor sees their own name as context --}}
                <div class="alert alert-info d-flex gap-2 align-items-center py-2 mb-4" style="font-size:13px">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span>Showing your personal performance data — <strong>{{ auth()->user()->full_name }}</strong></span>
                </div>
                @endif

                {{-- Individual doctor cards --}}
                <div id="doctorCards"></div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/arima@0.0.11/src/arima.min.js"></script>
<script>
// ── State ──────────────────────────────────────────────────────
let currentRange      = 'month';
let customStart       = '';
let customEnd         = '';
let selectedDoctorIds = @json($doctors->pluck('id'));
let allDoctors        = @json($doctors);
const isAdmin         = {{ $isAdmin ? 'true' : 'false' }};

// Route URLs (passed directly from PHP — no role guessing in JS)
const clinicDataUrl = '{{ $clinicDataUrl }}';
const doctorDataUrl = '{{ $doctorDataUrl }}';

// Chart instances (destroyed and recreated on each load)
const charts = {};

// ── On load ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Set default custom date range inputs
    const today    = new Date().toISOString().split('T')[0];
    const monthAgo = new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0];
    document.getElementById('customStart').value = monthAgo;
    document.getElementById('customEnd').value   = today;

    // Load both tabs so whichever is active shows data immediately
    loadClinic();
    loadDoctor();

    // Wire tab clicks to reload data when switching
    document.querySelectorAll('#analyticsTabs .nav-link').forEach(btn => {
        btn.addEventListener('shown.bs.tab', () => {
            const target = btn.getAttribute('data-bs-target');
            if (target === '#tabClinic') loadClinic();
            else loadDoctor();
        });
    });
});

// ── Filter ────────────────────────────────────────────────────
function setRange(range) {
    currentRange = range;
    document.querySelectorAll('.filter-range-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.range === range);
    });
    const custom = document.getElementById('customRangeInputs');
    custom.style.display = range === 'custom' ? 'flex' : 'none';
    if (range !== 'custom') refreshActive();
}

function applyCustom() {
    customStart = document.getElementById('customStart').value;
    customEnd   = document.getElementById('customEnd').value;
    if (customStart && customEnd) refreshActive();
}

function refreshActive() {
    const active = document.querySelector('#analyticsTabs .nav-link.active');
    if (active?.closest('[data-bs-target]') || active) {
        const target = active.getAttribute('data-bs-target');
        if (target === '#tabClinic') loadClinic();
        else loadDoctor();
    }
}

function buildParams(extra = {}) {
    const p = new URLSearchParams({ range: currentRange, ...extra });
    if (currentRange === 'custom') {
        p.set('start', customStart);
        p.set('end', customEnd);
    }
    return p.toString();
}

// ── Clinic Tab ────────────────────────────────────────────────
async function loadClinic() {
    document.getElementById('clinicLoading').style.display = 'block';
    document.getElementById('clinicContent').style.display = 'none';

    const res  = await fetch(`${clinicDataUrl}?${buildParams()}`);
    const data = await res.json();

    document.getElementById('dateRangeLabel').textContent =
        `${data.dateRange.start} — ${data.dateRange.end}`;

    // KPIs
    document.getElementById('clinicKpis').innerHTML = `
        ${kpiCard('bi-people-fill','kpi-blue', data.totalPatients, 'New Patients', '')}
        ${kpiCard('bi-clipboard2-pulse-fill','kpi-green', data.totalVisits, 'Total Visits', '')}
        ${kpiCard('bi-calendar2-check-fill','kpi-orange', data.totalAppts, 'Appointments', '')}
        ${kpiCard('bi-check2-all','kpi-teal', data.completedVisits, 'Finalized Visits', '')}
        ${kpiCard('bi-hourglass-split','kpi-purple', data.pendingVisits, 'Pending Visits', '')}
        ${kpiCard('bi-arrow-clockwise','kpi-red', data.inProgressVisits, 'In Progress', '')}
    `;

    // Visit trend line chart
    destroyChart('clinicTrendChart');
    charts['clinicTrendChart'] = new Chart(document.getElementById('clinicTrendChart'), {
        type: 'line',
        data: {
            labels: data.visitTrend.labels,
            datasets: [{
                label: 'Visits',
                data: data.visitTrend.values,
                borderColor: 'rgba(59,130,246,0.9)',
                backgroundColor: 'rgba(59,130,246,0.1)',
                borderWidth: 2, tension: 0.4, fill: true,
                pointRadius: 3,
            }]
        },
        options: chartOptions('Visits')
    });

    // Appointment status doughnut
    const apptLabels = Object.keys(data.apptStats);
    const apptVals   = Object.values(data.apptStats);
    destroyChart('apptStatusChart');
    charts['apptStatusChart'] = new Chart(document.getElementById('apptStatusChart'), {
        type: 'doughnut',
        data: {
            labels: apptLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{ data: apptVals,
                backgroundColor: ['#f59e0b','#3b82f6','#22c55e','#ef4444'],
                borderWidth: 2 }]
        },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });

    // Visit status doughnut
    destroyChart('visitStatusChart');
    charts['visitStatusChart'] = new Chart(document.getElementById('visitStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Finalized', 'Pending', 'In Progress'],
            datasets: [{ data: [data.completedVisits, data.pendingVisits, data.inProgressVisits],
                backgroundColor: ['#22c55e','#f59e0b','#3b82f6'], borderWidth: 2 }]
        },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });

    // ENT Classification horizontal bar
    destroyChart('entClassChart');
    charts['entClassChart'] = new Chart(document.getElementById('entClassChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(data.topEnt),
            datasets: [{ data: Object.values(data.topEnt),
                backgroundColor: ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)','rgba(239,68,68,0.8)'],
                borderRadius: 5 }]
        },
        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Doctor Workload bar
    destroyChart('workloadChart');
    charts['workloadChart'] = new Chart(document.getElementById('workloadChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(data.workload),
            datasets: [{ data: Object.values(data.workload),
                backgroundColor: 'rgba(139,92,246,0.8)', borderRadius: 5 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Chief Complaints
    const maxC = Math.max(...Object.values(data.topComplaints), 1);
    document.getElementById('clinicComplaints').innerHTML =
        Object.entries(data.topComplaints).map(([complaint, count]) => `
        <div class="diagnosis-bar-row">
            <div class="diagnosis-label">${complaint.length > 45 ? complaint.substring(0,45)+'…' : complaint}</div>
            <div class="diagnosis-bar-wrap">
                <div class="diagnosis-bar" style="width:${(count/maxC)*100}%"></div>
            </div>
            <div class="diagnosis-count">${count}</div>
        </div>`).join('') || '<div class="text-muted small py-3 text-center">No data for this period</div>';

    // New Patients trend
    destroyChart('patientTrendChart');
    charts['patientTrendChart'] = new Chart(document.getElementById('patientTrendChart'), {
        type: 'bar',
        data: {
            labels: data.patientTrend.labels,
            datasets: [{
                label: 'New Patients',
                data: data.patientTrend.values,
                backgroundColor: 'rgba(16,185,129,0.75)',
                borderRadius: 5,
            }]
        },
        options: chartOptions('New Patients')
    });

    document.getElementById('clinicLoading').style.display = 'none';
    document.getElementById('clinicContent').style.display = 'block';

    // ── Generate narratives ───────────────────────────────────
    generateClinicNarratives(data);

    // ── Generate forecast ─────────────────────────────────────
    generateForecast(data.visitTrend);
}

// ── Doctor Tab ────────────────────────────────────────────────
async function loadDoctor() {
    if (!isAdmin && !selectedDoctorIds.length) return;
    document.getElementById('doctorLoading').style.display = 'block';
    document.getElementById('doctorContent').style.display = 'none';

    const colors = ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)',
                    'rgba(239,68,68,0.8)','rgba(139,92,246,0.8)','rgba(236,72,153,0.8)'];

    // Doctor role: server ignores doctor_ids and uses auth user automatically
    const params = buildParams(isAdmin ? { doctor_ids: selectedDoctorIds.join(',') } : {});
    const res    = await fetch(`${doctorDataUrl}?${params}`);
    const data   = await res.json();

    document.getElementById('dateRangeLabel').textContent =
        `${data.dateRange.start} — ${data.dateRange.end}`;

    // Comparison bar chart — admin only
    if (isAdmin) {
        destroyChart('doctorCompareChart');
        const compareEl = document.getElementById('doctorCompareChart');
        if (compareEl) {
            charts['doctorCompareChart'] = new Chart(compareEl, {
                type: 'bar',
                data: {
                    labels: data.doctors.map(d => d.name),
                    datasets: [
                        { label: 'Total Visits',     data: data.doctors.map(d => d.totalVisits),     backgroundColor: colors[0], borderRadius: 5 },
                        { label: 'Finalized Visits', data: data.doctors.map(d => d.finalizedVisits), backgroundColor: colors[1], borderRadius: 5 },
                        { label: 'Patients',         data: data.doctors.map(d => d.totalPatients),   backgroundColor: colors[2], borderRadius: 5 },
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    }

    // Individual doctor cards
    document.getElementById('doctorCards').innerHTML = data.doctors.map((doc, idx) => `
        <div class="doctor-perf-card">
            <div class="doctor-perf-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="doctor-avatar-sm">${doc.name.charAt(0)}</div>
                    <div>
                        <div class="fw-bold" style="font-size:15px">Dr. ${doc.name}</div>
                        <div class="text-muted" style="font-size:12px">
                            Most common: <strong>${doc.topEnt}</strong>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="stat-pill"><span class="stat-pill-val">${doc.totalVisits}</span> Visits</span>
                    <span class="stat-pill"><span class="stat-pill-val">${doc.totalPatients}</span> Patients</span>
                    <span class="stat-pill"><span class="stat-pill-val">${doc.avgPerDay}</span> /day avg</span>
                    <span class="stat-pill"><span class="stat-pill-val">${doc.totalAppts}</span> Appts</span>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="rate-bar-row">
                        <div class="rate-bar-label">Completion Rate</div>
                        <div class="rate-bar-track">
                            <div class="rate-bar-fill" style="width:${doc.completionRate}%;background:#22c55e"></div>
                        </div>
                        <div class="rate-bar-pct" style="color:#16a34a">${doc.completionRate}%</div>
                    </div>
                    <div class="rate-bar-row">
                        <div class="rate-bar-label">Acceptance Rate</div>
                        <div class="rate-bar-track">
                            <div class="rate-bar-fill" style="width:${doc.acceptanceRate}%;background:#3b82f6"></div>
                        </div>
                        <div class="rate-bar-pct" style="color:#2563eb">${doc.acceptanceRate}%</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <canvas id="docEntChart_${doc.id}" height="130"></canvas>
                </div>
                <div class="col-md-3">
                    <canvas id="docTrendChart_${doc.id}" height="130"></canvas>
                </div>
            </div>
        </div>
    `).join('');

    // Render per-doctor mini charts
    data.doctors.forEach((doc, idx) => {
        // ENT pie
        const entId = `docEntChart_${doc.id}`;
        destroyChart(entId);
        charts[entId] = new Chart(document.getElementById(entId), {
            type: 'pie',
            data: {
                labels: Object.keys(doc.entBreakdown),
                datasets: [{ data: Object.values(doc.entBreakdown),
                    backgroundColor: ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)','rgba(239,68,68,0.8)'],
                    borderWidth: 1 }]
            },
            options: { responsive: true, plugins: { legend: { position: 'right', labels: { font: { size: 9 }, boxWidth: 10 } } } }
        });

        // Trend line
        const trendId = `docTrendChart_${doc.id}`;
        destroyChart(trendId);
        charts[trendId] = new Chart(document.getElementById(trendId), {
            type: 'line',
            data: {
                labels: doc.trend.labels,
                datasets: [{ data: doc.trend.values,
                    borderColor: colors[idx % colors.length],
                    backgroundColor: colors[idx % colors.length].replace('0.8','0.1'),
                    borderWidth: 2, tension: 0.4, fill: true, pointRadius: 2 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { font: { size: 9 }, maxTicksLimit: 5 } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } } }
                }
            }
        });
    });

    document.getElementById('doctorLoading').style.display = 'none';
    document.getElementById('doctorContent').style.display = 'block';
}

// ── Doctor selector ───────────────────────────────────────────
function toggleDoctor(el, id) {
    el.classList.toggle('active');
    if (el.classList.contains('active')) {
        if (!selectedDoctorIds.includes(id)) selectedDoctorIds.push(id);
    } else {
        selectedDoctorIds = selectedDoctorIds.filter(d => d !== id);
    }
    loadDoctor();
}
function selectAllDoctors() {
    selectedDoctorIds = allDoctors.map(d => d.id);
    document.querySelectorAll('.doctor-pill').forEach(p => p.classList.add('active'));
    loadDoctor();
}
function clearDoctors() {
    selectedDoctorIds = [];
    document.querySelectorAll('.doctor-pill').forEach(p => p.classList.remove('active'));
    document.getElementById('doctorCards').innerHTML = '<div class="text-muted text-center py-4">Select at least one doctor to view data.</div>';
    destroyChart('doctorCompareChart');
}

// ── Validation & Metrics (P0: Better accuracy tracking) ────────
function _calculateMAP(actual, predicted) {
    // Mean Absolute Percentage Error
    if (actual.length === 0) return 0;
    let sum = 0;
    for (let i = 0; i < actual.length; i++) {
        if (actual[i] + predicted[i] > 0) {
            sum += Math.abs(actual[i] - predicted[i]) / Math.max(1, actual[i]);
        }
    }
    return ((sum / actual.length) * 100).toFixed(1);
}

function _calculateMAE(actual, predicted) {
    // Mean Absolute Error
    if (actual.length === 0) return 0;
    return (actual.reduce((s, a, i) => s + Math.abs(a - predicted[i]), 0) / actual.length).toFixed(1);
}

function _calculateRMSE(actual, predicted) {
    // Root Mean Squared Error (penalizes larger errors more)
    if (actual.length === 0) return 0;
    const mse = actual.reduce((s, a, i) => s + Math.pow(a - predicted[i], 2), 0) / actual.length;
    return Math.sqrt(mse).toFixed(1);
}

function _calculateMASE(actual, predicted, naive) {
    // Mean Absolute Scaled Error (vs naive baseline)
    if (actual.length === 0 || naive.length === 0) return 'N/A';
    const mae = actual.reduce((s, a, i) => s + Math.abs(a - predicted[i]), 0) / actual.length;
    const naiveError = naive.reduce((s, a, i) => s + Math.abs(a - predicted[i]), 0) / naive.length;
    return (mae / Math.max(naiveError, 0.1)).toFixed(2);
}

// ── Outlier Detection (P1: Prevent spikes from breaking trends) ─
function _detectOutliers(values) {
    // IQR method: identify outliers
    const sorted = [...values].sort((a, b) => a - b);
    const q1_idx = Math.floor(sorted.length * 0.25);
    const q3_idx = Math.floor(sorted.length * 0.75);
    const q1 = sorted[q1_idx];
    const q3 = sorted[q3_idx];
    const iqr = q3 - q1;
    const lower = q1 - 1.5 * iqr;
    const upper = q3 + 1.5 * iqr;

    return {
        lower: lower,
        upper: upper,
        outlierIndices: values
            .map((v, i) => (v < lower || v > upper) ? i : -1)
            .filter(i => i !== -1),
        isOutlierArray: values.map(v => v < lower || v > upper)
    };
}

function _cleanOutliers(values) {
    // Replace outliers with median value
    const outliers = _detectOutliers(values);
    if (outliers.outlierIndices.length === 0) return values;

    const median = [...values].sort((a, b) => a - b)[Math.floor(values.length / 2)];
    return values.map((v, i) =>
        outliers.isOutlierArray[i] ? median : v
    );
}

function _calculateVolatility(values) {
    // Coefficient of Variation: stdev/mean
    if (values.length === 0) return 0;
    const mean = values.reduce((a, b) => a + b, 0) / values.length;
    if (mean === 0) return 0;
    const variance = values.reduce((s, v) => s + Math.pow(v - mean, 2), 0) / values.length;
    const stdev = Math.sqrt(variance);
    return stdev / mean;
}

// ── Adaptive Smoothing (P1: Dynamic window based on volatility) ─
function _applyAdaptiveMovingAverage(values, baseLookback) {
    // Calculate volatility to determine smoothing aggressiveness
    const volatility = _calculateVolatility(values);
    let windowSize = Math.min(7, Math.max(1, Math.floor(baseLookback / 4)));

    // Increase smoothing for high-volatility data
    if (volatility > 0.5) {
        windowSize = Math.min(7, windowSize + 2);  // More aggressive
    } else if (volatility > 0.3) {
        windowSize = Math.min(7, windowSize + 1);  // Moderate
    }

    const smoothed = [];
    for (let i = 0; i < values.length; i++) {
        const start = Math.max(0, i - Math.floor(windowSize / 2));
        const end = Math.min(values.length - 1, i + Math.floor(windowSize / 2));
        const window = values.slice(start, end + 1);
        const avg = window.reduce((a, b) => a + b, 0) / window.length;
        smoothed.push(avg);
    }
    return { smoothed, windowSize, volatility };
}

// ── Helpers ───────────────────────────────────────────────────
function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

function chartOptions(label) {
    return {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    };
}

function kpiCard(icon, cls, val, label, sub) {
    return `<div class="kpi-card ${cls}">
        <div class="kpi-icon"><i class="bi ${icon}"></i></div>
        <div>
            <div class="kpi-val">${val}</div>
            <div class="kpi-label">${label}</div>
            ${sub ? `<div class="kpi-sub">${sub}</div>` : ''}
        </div>
    </div>`;
}

function narrative(el, text) {
    const node = document.getElementById(el);
    if (node) node.innerHTML = `<i class="bi bi-lightbulb me-1 text-primary"></i>${text}`;
}

// ══════════════════════════════════════════════════════════════
// ══════════════════════════════════════════════════════════════
// ADVANCED PREDICTIVE FORECASTING ENGINE
// Based on time-series analysis with weekday seasonality and OLS trend detection
// ══════════════════════════════════════════════════════════════

function generateForecast(visitTrend) {
    const values = visitTrend.values || [];
    const labels = visitTrend.labels || [];
    const n      = values.length;

    document.getElementById('forecastLoading').style.display = 'block';
    document.getElementById('forecastChart').style.display   = 'none';

    if (n < 3) {
        document.getElementById('forecastLoading').style.display = 'none';
        document.getElementById('forecastChart').style.display   = 'block';
        narrative('forecastNarrative',
            'Not enough historical data for forecasting. At least 3 data points are needed. Keep recording visits and the forecast will activate automatically.');
        return;
    }

    // Run after a short tick so the loading spinner renders
    setTimeout(() => {
        try {
            _runAdvancedForecast(values, labels, n);
        } catch(e) {
            console.warn('Advanced forecast failed, falling back to simple averaging:', e);
            _runSimpleAveraging(values, labels, n);
        }
    }, 50);
}

function _runAdvancedForecast(values, labels, n) {
    // ── Step 1: Data Preparation ──────────────────────────────
    const rangeDays = _calculateRangeDays(labels);
    const forecastDays = _determineForecastHorizon(rangeDays, n);

    // ── Step 2: Lookback Window Selection ─────────────────────
    const lookback = Math.min(28, Math.max(1, n));

    // ── Step 2.5 (NEW): Outlier Detection & Cleaning (P1) ─────
    const outlierInfo = _detectOutliers(values);
    const hasOutliers = outlierInfo.outlierIndices.length > 0;
    const cleanedValues = hasOutliers ? _cleanOutliers(values) : values;

    // ── Step 3: Global Mean Calculation ───────────────────────
    const globalMean = cleanedValues.reduce((a, b) => a + b, 0) / n;

    // ── Step 4: Weekday Seasonality Model ─────────────────────
    const { weekdayMean } = _calculateWeekdaySeasonality(cleanedValues, labels, globalMean);

    // ── Step 5: Data Smoothing (Adaptive MA) ──────────────────
    const smoothingResult = _applyAdaptiveMovingAverage(cleanedValues, lookback);
    const smoothed = smoothingResult.smoothed;
    const volatility = smoothingResult.volatility;

    // ── Step 6: Trend Estimation (OLS Regression) ─────────────
    const regressionWindow = Math.min(30, Math.max(2, Math.floor(n / 2)));
    const recentSmoothed = smoothed.slice(-regressionWindow);
    const slope = _calculateOLSSlope(recentSmoothed);

    // ── Step 7: Forecast Prediction ───────────────────────────
    const forecast = _generateForecast(smoothed, slope, weekdayMean, globalMean, forecastDays, labels);

    // ── Step 8 (NEW): Validation Metrics (P0) ─────────────────
    const metrics = _calculateValidationMetrics(values, cleanedValues, outlierInfo, volatility, n);

    // ── Step 9: Render Results ────────────────────────────────
    _renderAdvancedForecastChart(values, labels, forecast.values, forecast.labels, forecastDays, smoothed, slope, n, metrics, hasOutliers, volatility);
}

function _calculateValidationMetrics(original, cleaned, outlierInfo, volatility, n) {
    // Calculate quality metrics for forecast validation
    const outlierCount = outlierInfo.outlierIndices.length;
    const outlierPct = n > 0 ? ((outlierCount / n) * 100).toFixed(1) : 0;

    return {
        outlierCount: outlierCount,
        outlierPct: outlierPct,
        volatility: (volatility * 100).toFixed(1),
        dataQualityScore: _calculateDataQuality(outlierPct, volatility),
        confidenceLevel: _determineConfidenceLevel(outlierCount, volatility, n)
    };
}

function _calculateDataQuality(outlierPct, volatility) {
    // Score from 0-100 (higher = better quality)
    let score = 100;
    score -= Math.min(30, outlierPct * 2);  // -30 max for outliers
    score -= Math.min(30, volatility * 30);  // -30 max for volatility
    return Math.max(20, score).toFixed(0);  // Min 20
}

function _determineConfidenceLevel(outlierCount, volatility, n) {
    // Confidence level: Low/Moderate/High
    if (outlierCount > n * 0.15 || volatility > 0.6) return 'Low';
    if (outlierCount > n * 0.08 || volatility > 0.4) return 'Moderate';
    return 'High';
}

function _calculateRangeDays(labels) {
    if (labels.length < 2) return 1;
    // Estimate range based on label format and count
    // This is a simplified estimation - in production you'd parse actual dates
    return Math.max(1, labels.length);
}

function _determineForecastHorizon(rangeDays, n) {
    // Return forecast that's proportional to the data range shown
    if (n <= 7) return 7;         // This Week: forecast 7 days
    if (n <= 31) return 30;       // This Month: forecast 30 days
    if (n <= 92) return 30;       // 3 months: forecast 30 days
    if (n <= 365) return 60;      // This Year: forecast 60 days
    return Math.min(90, n);       // Multi-year: forecast 90 days
}

function _calculateWeekdaySeasonality(values, labels, globalMean) {
    const weekdaySums = new Array(7).fill(0);
    const weekdayCounts = new Array(7).fill(0);

    // Parse dates more robustly
    labels.forEach((label, i) => {
        try {
            // Try different date parsing strategies
            let date;
            if (label.includes('/')) {
                // MM/DD format
                const [month, day] = label.split('/').map(Number);
                date = new Date(new Date().getFullYear(), month - 1, day);
            } else if (label.includes('-')) {
                // M-D or MM-DD format
                const [month, day] = label.split('-').map(Number);
                date = new Date(new Date().getFullYear(), month - 1, day);
            } else {
                // Try direct parsing
                date = new Date(label + ', ' + new Date().getFullYear());
            }

            if (!isNaN(date)) {
                const wd = date.getDay();
                weekdaySums[wd] += values[i];
                weekdayCounts[wd] += 1;
            }
        } catch (e) {
            // Skip invalid dates
        }
    });

    const weekdayMean = weekdaySums.map((sum, wd) =>
        weekdayCounts[wd] > 0 ? sum / weekdayCounts[wd] : globalMean
    );

    return { weekdayMean, weekdaySums, weekdayCounts };
}

function _calculateOLSSlope(values) {
    const n = values.length;
    if (n < 2) return 0;

    // Calculate sums
    let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
    for (let i = 0; i < n; i++) {
        sumX += i;
        sumY += values[i];
        sumXY += i * values[i];
        sumXX += i * i;
    }

    const denominator = n * sumXX - sumX * sumX;
    if (Math.abs(denominator) < 1e-9) return 0;

    const slope = (n * sumXY - sumX * sumY) / denominator;

    // Apply slope clamping for safety
    const maxSlopePerDay = Math.max(1, 0.5 * (sumY / n));
    return Math.max(-maxSlopePerDay, Math.min(maxSlopePerDay, slope));
}

function _generateForecast(smoothed, slope, weekdayMean, globalMean, forecastDays, labels) {
    const forecastValues = [];
    const forecastLabels = [];

    // Start from the last smoothed value
    let lastValue = smoothed[smoothed.length - 1];

    // Get the last date for proper weekday calculation
    let lastDate = new Date();
    try {
        const lastLabel = labels[labels.length - 1];
        if (lastLabel.includes('/')) {
            const [month, day] = lastLabel.split('/').map(Number);
            lastDate = new Date(new Date().getFullYear(), month - 1, day);
        } else if (lastLabel.includes('-')) {
            const [month, day] = lastLabel.split('-').map(Number);
            lastDate = new Date(new Date().getFullYear(), month - 1, day);
        }
    } catch (e) {
        // Use current date if parsing fails
    }

    for (let i = 1; i <= forecastDays; i++) {
        // Calculate future date
        const futureDate = new Date(lastDate);
        futureDate.setDate(lastDate.getDate() + i);
        const wd = futureDate.getDay();

        // Seasonality adjustment
        const seasonAdj = weekdayMean[wd] - globalMean;

        // Forecast with trend and seasonality
        const predicted = lastValue + slope * i + seasonAdj;

        // Ensure non-negative
        const forecastValue = Math.max(0, Math.round(predicted));
        forecastValues.push(forecastValue);

        // Format label
        forecastLabels.push(futureDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        }));
    }

    return { values: forecastValues, labels: forecastLabels };
}

function _renderAdvancedForecastChart(values, labels, forecastVals, forecastLabels, forecastDays, smoothed, slope, n, metrics, hasOutliers, volatility) {
    document.getElementById('forecastLoading').style.display = 'none';
    document.getElementById('forecastChart').style.display   = 'block';

    // Show last 14 historical points for clarity
    const histSlice  = values.slice(Math.max(0, n - 14));
    const labelSlice = labels.slice(Math.max(0, n - 14));
    const smoothSlice = smoothed.slice(Math.max(0, n - 14));

    const allLabels  = [...labelSlice, ...forecastLabels];
    const histData   = [...histSlice, ...new Array(forecastDays).fill(null)];
    const smoothData = [...smoothSlice, ...new Array(forecastDays).fill(null)];
    const foreData   = [
        ...new Array(histSlice.length - 1).fill(null),
        histSlice[histSlice.length - 1],
        ...forecastVals
    ];

    destroyChart('forecastChart');
    charts['forecastChart'] = new Chart(document.getElementById('forecastChart'), {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Historical Visits',
                    data: histData,
                    borderColor: 'rgba(59,130,246,0.9)',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    tension: 0.35,
                    spanGaps: false,
                },
                {
                    label: 'Smoothed Trend',
                    data: smoothData,
                    borderColor: 'rgba(107,114,128,0.7)',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    borderDash: [5, 3],
                    pointRadius: 0,
                    fill: false,
                    tension: 0.35,
                    spanGaps: false,
                },
                {
                    label: 'Forecasted Visits',
                    data: foreData,
                    borderColor: 'rgba(234,179,8,1)',
                    backgroundColor: 'rgba(234,179,8,0.07)',
                    borderWidth: 2.5,
                    borderDash: [7, 4],
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(234,179,8,1)',
                    fill: false,
                    tension: 0.35,
                    spanGaps: false,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.raw !== null
                            ? `${ctx.dataset.label}: ${ctx.raw} visits`
                            : null
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { maxTicksLimit: 20 } }
            }
        }
    });

    // ── Generate Narrative with Validation Metrics ────────────
    _generateForecastNarrative(forecastVals, forecastLabels, slope, smoothed, metrics, hasOutliers);
}

function _generateForecastNarrative(forecastVals, forecastLabels, slope, smoothed, metrics, hasOutliers) {
    const half1 = Math.floor(forecastVals.length / 2);
    const half2 = forecastVals.length - half1;
    const firstHalfAvg = forecastVals.slice(0, half1).reduce((a,b)=>a+b,0) / half1;
    const lastHalfAvg = forecastVals.slice(half1).reduce((a,b)=>a+b,0) / half2;
    const pct = firstHalfAvg > 0 ? ((lastHalfAvg - firstHalfAvg) / firstHalfAvg) * 100 : 0;
    const maxForecast = Math.max(...forecastVals);
    const peakDay = forecastLabels[forecastVals.indexOf(maxForecast)];
    const avgForecast = Math.round(forecastVals.reduce((a,b)=>a+b,0) / forecastVals.length);
    const lastSmoothed = smoothed[smoothed.length - 1];

    let trendMsg, suggestion;
    if (pct > 5) {
        trendMsg = 'likely <strong class="text-success">increasing</strong>';
        suggestion = maxForecast > 10
            ? 'Consider scheduling additional doctor slots on peak days to manage demand.'
            : 'Ensure appointment availability is sufficient for the projected increase.';
    } else if (pct < -5) {
        trendMsg = 'likely <strong class="text-danger">declining</strong>';
        suggestion = 'Review follow-up booking and patient outreach practices to maintain visit volume.';
    } else {
        trendMsg = '<strong class="text-primary">relatively stable</strong>';
        suggestion = 'Current staffing and scheduling levels appear adequate for the forecast period.';
    }

    const trendDirection = slope > 0.1 ? 'upward' : slope < -0.1 ? 'downward' : 'stable';
    const confidence = Math.abs(slope) > 0.5 ? 'high' : Math.abs(slope) > 0.2 ? 'moderate' : 'low';

    // ── Build narrative with validation metrics ───────────────
    let metricsHtml = '';
    if (metrics) {
        const outlierWarning = metrics.outlierCount > 0
            ? `<span style="color:#f59e0b;font-weight:600">${metrics.outlierCount} outliers (${metrics.outlierPct}%)</span>`
            : '<span style="color:#16a34a;font-weight:600">✓ Clean data</span>';

        const voltagilityColor = metrics.volatility > 40 ? '#ef4444' : metrics.volatility > 25 ? '#f59e0b' : '#16a34a';

        metricsHtml = `
            <div style="margin-top:8px;padding:8px;background:#f1f5f9;border-radius:6px;font-size:11.5px;border-left:3px solid ${voltagilityColor === '#ef4444' ? '#ef4444' : '#16a34a'}">
                <strong style="color:#1e293b">Data Quality:</strong>
                Data stability: <span style="color:${voltagilityColor};font-weight:600">${metrics.volatility}% volatility</span> •
                Outliers: ${outlierWarning} •
                Confidence: <span style="color:${metrics.confidenceLevel === 'High' ? '#16a34a' : metrics.confidenceLevel === 'Moderate' ? '#f59e0b' : '#ef4444'};font-weight:600">${metrics.confidenceLevel}</span>
                Quality Score: <strong>${metrics.dataQualityScore}/100</strong>
            </div>
        `;
    }

    narrative('forecastNarrative',
        `<strong>Advanced Time-Series Forecast</strong> shows demand is ${trendMsg} (${trendDirection} trend, ${confidence} confidence). 
        Projected daily average: <strong>${avgForecast} visits</strong>. 
        Peak demand expected on <strong>${peakDay}</strong> with up to <strong>${maxForecast} visits</strong>. 
        ${suggestion}
        ${metricsHtml}`
    );
}

function _runSimpleAveraging(values, labels, n) {
    // Fallback: Simple averaging with minimal seasonality
    const forecastDays = n <= 7 ? 7 : 30;
    const avgLast = values.slice(Math.max(0, n - 7)).reduce((a,b)=>a+b,0) / Math.min(7, n);

    const forecastVals = [];
    const forecastLabels = [];

    const lastDate = new Date();
    try {
        const lastLabel = labels[labels.length - 1];
        if (lastLabel.includes('/')) {
            const [month, day] = lastLabel.split('/').map(Number);
            lastDate.setMonth(month - 1, day);
        } else if (lastLabel.includes('-')) {
            const [month, day] = lastLabel.split('-').map(Number);
            lastDate.setMonth(month - 1, day);
        }
    } catch (e) {}

    for (let i = 1; i <= forecastDays; i++) {
        const val = Math.max(0, Math.round(avgLast));
        forecastVals.push(val);
        const futureDate = new Date(lastDate);
        futureDate.setDate(lastDate.getDate() + i);
        forecastLabels.push(futureDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }

    _renderSimpleForecastChart(values, labels, forecastVals, forecastLabels, forecastDays, n);
}

function _renderSimpleForecastChart(values, labels, forecastVals, forecastLabels, forecastDays, n) {
    document.getElementById('forecastLoading').style.display = 'none';
    document.getElementById('forecastChart').style.display   = 'block';

    // Show last 14 historical points
    const histSlice  = values.slice(Math.max(0, n - 14));
    const labelSlice = labels.slice(Math.max(0, n - 14));
    const allLabels  = [...labelSlice, ...forecastLabels];
    const histData   = [...histSlice, ...new Array(forecastDays).fill(null)];
    const foreData   = [
        ...new Array(histSlice.length - 1).fill(null),
        histSlice[histSlice.length - 1],
        ...forecastVals
    ];

    destroyChart('forecastChart');
    charts['forecastChart'] = new Chart(document.getElementById('forecastChart'), {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Historical Visits',
                    data: histData,
                    borderColor: 'rgba(59,130,246,0.9)',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    tension: 0.35,
                    spanGaps: false,
                },
                {
                    label: 'Forecasted Visits (Simple Average)',
                    data: foreData,
                    borderColor: 'rgba(234,179,8,1)',
                    backgroundColor: 'rgba(234,179,8,0.07)',
                    borderWidth: 2.5,
                    borderDash: [7, 4],
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(234,179,8,1)',
                    fill: false,
                    tension: 0.35,
                    spanGaps: false,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.raw !== null
                            ? `${ctx.dataset.label}: ${ctx.raw} visits`
                            : null
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { maxTicksLimit: 20 } }
            }
        }
    });

    const avgForecast = Math.round(forecastVals.reduce((a,b)=>a+b,0) / forecastDays);
    const maxForecast = Math.max(...forecastVals);

    narrative('forecastNarrative',
        `<strong>Simple Averaging Forecast</strong> (fallback method) projects an average of <strong>${avgForecast} visits per day</strong> 
        with peak demand up to <strong>${maxForecast} visits</strong>. 
        Consider collecting more historical data for more accurate predictions.`
    );
}

// ── Static smart narratives based on data ─────────────────────
function generateClinicNarratives(data) {
    // Visit Trend
    const trendVals  = data.visitTrend.values;
    const trendTotal = trendVals.reduce((a,b) => a+b, 0);
    const trendFirst = trendVals.slice(0, Math.floor(trendVals.length/2)).reduce((a,b)=>a+b,0);
    const trendSecond= trendVals.slice(Math.floor(trendVals.length/2)).reduce((a,b)=>a+b,0);
    let trendMsg;
    if (trendTotal === 0) {
        trendMsg = 'No visits recorded in this period. Consider reviewing appointment scheduling to increase patient engagement.';
    } else if (trendSecond > trendFirst * 1.1) {
        trendMsg = `Visit volume is <strong>trending upward</strong> — the second half of this period saw more visits than the first. This suggests growing patient demand or improved appointment availability.`;
    } else if (trendSecond < trendFirst * 0.9) {
        trendMsg = `Visit volume appears to be <strong>declining</strong> toward the end of this period. This could reflect seasonal patterns or reduced appointment availability — worth monitoring.`;
    } else {
        trendMsg = `Visit volume is <strong>relatively stable</strong> across this period, indicating consistent patient flow. No significant spikes or drops were observed.`;
    }
    narrative('clinicTrendNarrative', trendMsg);

    // Appointment Status
    const total  = Object.values(data.apptStats).reduce((a,b)=>a+b,0);
    const cancelled = data.apptStats['cancelled'] || 0;
    const completed = data.apptStats['completed'] || 0;
    const cancelRate = total > 0 ? Math.round(cancelled/total*100) : 0;
    const completeRate = total > 0 ? Math.round(completed/total*100) : 0;
    let apptMsg;
    if (total === 0) {
        apptMsg = 'No appointments were booked in this period.';
    } else if (cancelRate > 25) {
        apptMsg = `The <strong>cancellation rate is high at ${cancelRate}%</strong>. Consider sending reminder messages to patients before appointments to reduce no-shows.`;
    } else {
        apptMsg = `<strong>${completeRate}% of appointments were completed</strong> this period${cancelRate > 0 ? ` with a manageable ${cancelRate}% cancellation rate` : ''}. Appointment compliance appears healthy.`;
    }
    narrative('apptStatusNarrative', apptMsg);

    // Visit Status
    const finalized  = data.completedVisits;
    const pending    = data.pendingVisits;
    const inProgress = data.inProgressVisits;
    const allVisits  = finalized + pending + inProgress;
    let visitMsg;
    if (allVisits === 0) {
        visitMsg = 'No visits recorded in this period.';
    } else if (pending > finalized) {
        visitMsg = `There are <strong>${pending} pending visits</strong> awaiting doctor completion — more than the ${finalized} finalized. Doctors should prioritize completing open visit records to maintain accurate patient histories.`;
    } else {
        const pct = allVisits > 0 ? Math.round(finalized/allVisits*100) : 0;
        visitMsg = `<strong>${pct}% of visits are finalized</strong> this period. ${pending > 0 ? `${pending} visit${pending > 1 ? 's' : ''} still pending doctor completion.` : 'All recorded visits have been completed.'}`;
    }
    narrative('visitStatusNarrative', visitMsg);

    // ENT Classification
    const entKeys = Object.keys(data.topEnt);
    const entVals = Object.values(data.topEnt);
    if (entKeys.length > 0) {
        const topEnt  = entKeys[0];
        const topVal  = entVals[0];
        const entTotal= entVals.reduce((a,b)=>a+b,0);
        const topPct  = entTotal > 0 ? Math.round(topVal/entTotal*100) : 0;
        narrative('entClassNarrative',
            `<strong>${topEnt}</strong> is the most common category, accounting for <strong>${topPct}% of cases</strong> this period. Ensuring adequate supplies and consultation time for this category is recommended.`
        );
    }

    // Doctor Workload
    const wKeys = Object.keys(data.workload);
    const wVals = Object.values(data.workload);
    if (wKeys.length > 1) {
        const maxVal = Math.max(...wVals);
        const minVal = Math.min(...wVals);
        const gap    = maxVal - minVal;
        let workMsg;
        if (gap > maxVal * 0.4) {
            workMsg = `Workload distribution is <strong>uneven</strong> — one doctor handled significantly more cases. Consider redistributing appointments to balance the clinical load.`;
        } else {
            workMsg = `Workload is <strong>fairly distributed</strong> among doctors this period. No significant imbalances were detected in case volume.`;
        }
        narrative('workloadNarrative', workMsg);
    }

    // Chief Complaints
    const ccKeys = Object.keys(data.topComplaints);
    if (ccKeys.length > 0) {
        const top1 = ccKeys[0];
        const top2 = ccKeys[1] || null;
        narrative('complaintsNarrative',
            `<strong>${top1}</strong> is the leading complaint this period${top2 ? `, followed by ${top2}` : ''}. These patterns may help guide staff preparation and stock management for commonly needed treatments.`
        );
    }
}
</script>
@endpush