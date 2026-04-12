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
                                    Predictive Forecast
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
    generateForecast(data.forecastTrend || data.visitTrend, currentRange);
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

function generateForecast(visitTrend, range) {
    const values = visitTrend.values || [];
    const labels = visitTrend.labels || [];
    const n      = values.length;

    // Determine forecast horizon based on the selected UI range:
    //   week  →  7 days  (short-term, high detail)
    //   month → days remaining in month + next month ≈ 30 days
    //   year  → 90 days  (quarterly outlook — daily beyond that is noise)
    //   custom/fallback → 30 days
    const forecastDays = _horizonForRange(range);

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
            _runAdvancedForecast(values, labels, n, forecastDays, range);
        } catch(e) {
            console.warn('Advanced forecast failed, falling back to simple averaging:', e);
            _runSimpleAveraging(values, labels, n, forecastDays);
        }
    }, 50);
}

function _horizonForRange(range) {
    switch (range) {
        case 'today': return 3;   // show next 3 days when viewing today
        case 'week':  return 7;   // predict the next 7 days
        case 'month': return 30;  // predict the next ~month
        case 'year':  return 90;  // quarterly outlook
        default:      return 30;
    }
}

function _runAdvancedForecast(values, labels, n, forecastDays, range) {
    // ── Step 1: forecastDays comes from _horizonForRange(range)
    //    which is set by the active UI range button (week/month/year).

    // ── Step 2: Lookback Window Selection ─────────────────────
    const lookback = Math.min(28, Math.max(1, n));

    // ── Step 3: Global Mean Calculation ───────────────────────
    const globalMean = values.reduce((a, b) => a + b, 0) / n;

    // ── Step 4: Weekday Seasonality Model ─────────────────────
    const { weekdayMean } = _calculateWeekdaySeasonality(values, labels, globalMean);

    // ── Step 5: Data Smoothing (Moving Average) ───────────────
    const windowSize = Math.min(7, Math.max(1, Math.floor(lookback / 4)));
    const smoothed = _applyMovingAverage(values, windowSize);

    // ── Step 6: Trend Estimation (OLS Regression) ─────────────
    const regressionWindow = Math.min(30, Math.max(2, Math.floor(n / 2)));
    const recentSmoothed = smoothed.slice(-regressionWindow);
    const slope = _calculateOLSSlope(recentSmoothed);

    // ── Step 7: Forecast Prediction ───────────────────────────
    const forecast = _generateForecast(smoothed, slope, weekdayMean, globalMean, forecastDays, labels);

    // ── Step 8: Render Results ────────────────────────────────
    _renderAdvancedForecastChart(values, labels, forecast.values, forecast.labels, forecastDays, smoothed, slope, n, range);
}

function _calculateRangeDays(labels) {
    // forecastTrend is always daily, so label count == day count
    return Math.max(1, labels.length);
}

function _determineForecastHorizon(rangeDays, n) {
    if (n <= 7) return 7;
    if (rangeDays <= 30) return 30;
    return 30; // Default to 30 days
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

function _applyMovingAverage(values, windowSize) {
    const smoothed = [];
    for (let i = 0; i < values.length; i++) {
        const start = Math.max(0, i - Math.floor(windowSize / 2));
        const end = Math.min(values.length - 1, i + Math.floor(windowSize / 2));
        const window = values.slice(start, end + 1);
        const avg = window.reduce((a, b) => a + b, 0) / window.length;
        smoothed.push(avg);
    }
    return smoothed;
}

function _calculateOLSSlope(values) {
    const n = values.length;
    if (n < 2) return 0;

    // Trim trailing zeros — today might have 0 visits simply because
    // the day isn't over yet, which would otherwise produce a false
    // steep downward slope.
    let trimmed = [...values];
    while (trimmed.length > 2 && trimmed[trimmed.length - 1] === 0) {
        trimmed.pop();
    }
    const tn = trimmed.length;

    let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
    for (let i = 0; i < tn; i++) {
        sumX  += i;
        sumY  += trimmed[i];
        sumXY += i * trimmed[i];
        sumXX += i * i;
    }

    const denominator = tn * sumXX - sumX * sumX;
    if (Math.abs(denominator) < 1e-9) return 0;

    const slope = (tn * sumXY - sumX * sumY) / denominator;

    // Clamp slope to ±2 visits/day — prevents wild swings on sparse data
    // while still capturing real trends on busy clinics.
    return Math.max(-2, Math.min(2, slope));
}

function _generateForecast(smoothed, slope, weekdayMean, globalMean, forecastDays, labels) {
    const forecastValues = [];
    const forecastLabels = [];

    // Anchor: use the mean of the last 14 non-zero days as the baseline.
    // This prevents a single zero-day (e.g. today not finished yet) from
    // dragging the entire forecast to zero.
    const recent = smoothed.slice(-14).filter(v => v > 0);
    const anchor = recent.length > 0
        ? recent.reduce((a, b) => a + b, 0) / recent.length
        : smoothed[smoothed.length - 1];

    // If there's genuinely no data at all, return a flat zero forecast
    // rather than nonsense numbers.
    const effectiveMean = globalMean > 0 ? globalMean : 1;

    // Parse the last label as today's date anchor for weekday calculation.
    let lastDate = new Date();
    try {
        const lastLabel = labels[labels.length - 1];
        // Labels come as "Apr 12" format from PHP's 'M j'
        const parsed = new Date(lastLabel + ' ' + new Date().getFullYear());
        if (!isNaN(parsed)) lastDate = parsed;
    } catch (e) { /* use today */ }

    for (let i = 1; i <= forecastDays; i++) {
        const futureDate = new Date(lastDate);
        futureDate.setDate(lastDate.getDate() + i);
        const wd = futureDate.getDay();

        // Seasonality ratio (multiplicative) — safer than additive when
        // globalMean is near zero, avoids negative forecasts.
        const wdMean = weekdayMean[wd] > 0 ? weekdayMean[wd] : effectiveMean;
        const seasonRatio = wdMean / effectiveMean;

        // Trend projection from anchor, dampened over time so it doesn't
        // run away to infinity on long ranges.
        const dampening = Math.exp(-0.02 * i); // decay trend influence
        const trended   = anchor + slope * i * dampening;

        const predicted     = Math.max(0, trended) * seasonRatio;
        const forecastValue = Math.max(0, Math.round(predicted));
        forecastValues.push(forecastValue);

        forecastLabels.push(futureDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        }));
    }

    return { values: forecastValues, labels: forecastLabels };
}

function _renderAdvancedForecastChart(values, labels, forecastVals, forecastLabels, forecastDays, smoothed, slope, n, range) {
    document.getElementById('forecastLoading').style.display = 'none';
    document.getElementById('forecastChart').style.display   = 'block';

    // Show a range-appropriate history window so the chart isn't crowded.
    const histWindow = { today: 7, week: 14, month: 30, year: 60 }[range] || 14;
    const histSlice   = values.slice(Math.max(0, n - histWindow));
    const labelSlice  = labels.slice(Math.max(0, n - histWindow));
    const smoothSlice = smoothed.slice(Math.max(0, n - histWindow));

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
                    label: `Forecasted Visits (Next ${forecastDays}d)`,
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

    // ── Generate Narrative ────────────────────────────────────
    _generateForecastNarrative(forecastVals, forecastLabels, slope, smoothed, range);
}

function _generateForecastNarrative(forecastVals, forecastLabels, slope, smoothed, range) {
    const half1 = Math.floor(forecastVals.length / 2);
    const half2 = forecastVals.length - half1;
    const firstHalfAvg = forecastVals.slice(0, half1).reduce((a,b)=>a+b,0) / half1;
    const lastHalfAvg = forecastVals.slice(half1).reduce((a,b)=>a+b,0) / half2;
    const pct = firstHalfAvg > 0 ? ((lastHalfAvg - firstHalfAvg) / firstHalfAvg) * 100 : 0;
    const maxForecast = Math.max(...forecastVals);
    const peakDay = forecastLabels[forecastVals.indexOf(maxForecast)];
    const avgForecast = Math.round(forecastVals.reduce((a,b)=>a+b,0) / forecastVals.length);
    const lastSmoothed = smoothed[smoothed.length - 1];

    const horizonLabel = { today: '3 days', week: '7 days', month: '30 days', year: '90 days' };

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

    const rangeLabel = horizonLabel[range] || '30 days';
    narrative('forecastNarrative',
        `<strong>Advanced Time-Series Forecast (Next ${rangeLabel})</strong> shows demand is ${trendMsg} (${trendDirection} trend, ${confidence} confidence). 
        Projected daily average: <strong>${avgForecast} visits</strong>. 
        Peak demand expected on <strong>${peakDay}</strong> with up to <strong>${maxForecast} visits</strong>. 
        ${suggestion}`
    );
}

function _runSimpleAveraging(values, labels, n, forecastDays) {
    // Fallback: Simple averaging with minimal seasonality
    forecastDays = forecastDays || (n <= 7 ? 7 : 30);
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