@extends('layouts.app')
@section('title', 'Analytics & Insights')
@section('page-title', 'Analytics & Insights')

@push('styles')
<style>
.insight-card {
    border-left: 4px solid;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 12px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.insight-card.insight-success { border-color: #22c55e; background: #f0fdf4; }
.insight-card.insight-warning { border-color: #f59e0b; background: #fffbeb; }
.insight-card.insight-danger  { border-color: #ef4444; background: #fef2f2; }
.insight-card.insight-info    { border-color: #3b82f6; background: #eff6ff; }
.insight-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.insight-success .insight-icon { background: #dcfce7; color: #16a34a; }
.insight-warning .insight-icon { background: #fef3c7; color: #d97706; }
.insight-danger  .insight-icon { background: #fee2e2; color: #dc2626; }
.insight-info    .insight-icon { background: #dbeafe; color: #2563eb; }
.insight-title { font-weight: 700; font-size: 13.5px; margin-bottom: 2px; }
.insight-text  { font-size: 12.5px; color: #475569; line-height: 1.5; }

.kpi-mini {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
}
.kpi-mini-icon {
    width: 40px; height: 40px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.kpi-mini-val  { font-size: 22px; font-weight: 800; line-height: 1; }
.kpi-mini-label { font-size: 11.5px; color: #64748b; margin-top: 2px; }
.kpi-mini-trend { font-size: 11px; font-weight: 600; }
.trend-up   { color: #16a34a; }
.trend-down { color: #dc2626; }
.trend-flat { color: #64748b; }

.forecast-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    border-radius: 20px; padding: 4px 12px;
    font-size: 12px; font-weight: 600; color: #334155;
}
.forecast-badge .val { color: #2563eb; font-size: 15px; font-weight: 800; }

.section-title {
    font-size: 11px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.6px;
    margin-bottom: 14px;
}
</style>
@endpush

@section('content')
<div class="page-content">

    <div class="page-header-row mb-4">
        <div>
            <h1 class="page-heading">Analytics & Insights</h1>
            <div class="text-muted small">Data as of {{ now()->format('F j, Y') }}</div>
        </div>
    </div>

    {{-- ── KPI ROW ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#dbeafe;color:#2563eb">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $totalPatients }}</div>
                    <div class="kpi-mini-label">Total Patients</div>
                    <div class="kpi-mini-trend trend-flat">avg {{ $avgVisitsPerPatient }} visits each</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#dcfce7;color:#16a34a">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $visitsThisMonth }}</div>
                    <div class="kpi-mini-label">Visits This Month</div>
                    @if($visitGrowth !== null)
                        <div class="kpi-mini-trend {{ $visitGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="bi bi-arrow-{{ $visitGrowth >= 0 ? 'up' : 'down' }}-short"></i>
                            {{ abs($visitGrowth) }}% vs last month
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#fef3c7;color:#d97706">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $returnRate }}%</div>
                    <div class="kpi-mini-label">Patient Return Rate</div>
                    <div class="kpi-mini-trend {{ $returnRate >= 50 ? 'trend-up' : 'trend-flat' }}">
                        {{ $returnRate >= 60 ? 'Excellent' : ($returnRate >= 40 ? 'Good' : 'Needs attention') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#fee2e2;color:#dc2626">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <div>
                    <div class="kpi-mini-val">{{ $cancelRate }}%</div>
                    <div class="kpi-mini-label">Cancellation Rate</div>
                    <div class="kpi-mini-trend {{ $cancelRate <= 20 ? 'trend-up' : 'trend-down' }}">
                        {{ $cancelRate <= 20 ? 'Within target' : 'Above threshold' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ─────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Visit Trend + Forecast Chart --}}
            <div class="card-panel mb-4">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-bar-chart-line me-2"></i>Visit Trend & Forecast
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(array_map(null, $forecastLabels, $forecastValues) as [$label, $val])
                            <span class="forecast-badge">
                                <span class="val">{{ $val }}</span> {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="card-panel-body">
                    <canvas id="visitsChart" height="90"></canvas>
                    <div class="d-flex gap-3 mt-2" style="font-size:11.5px;color:#64748b">
                        <span><span style="display:inline-block;width:12px;height:12px;background:rgba(59,130,246,0.8);border-radius:2px;margin-right:4px"></span>Actual visits</span>
                        <span><span style="display:inline-block;width:12px;height:12px;background:rgba(139,92,246,0.7);border-radius:2px;margin-right:4px"></span>Forecast (linear regression)</span>
                    </div>
                </div>
            </div>

            {{-- ENT Classification Trend --}}
            <div class="card-panel mb-4">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-bar-chart-steps me-2"></i>ENT Case Distribution (Last 6 Months)</div>
                </div>
                <div class="card-panel-body">
                    <canvas id="entChart" height="80"></canvas>
                </div>
            </div>

            {{-- Busiest Days & Hours side by side --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card-panel h-100">
                        <div class="card-panel-header">
                            <div class="card-panel-title"><i class="bi bi-calendar-week me-2"></i>Busiest Days</div>
                        </div>
                        <div class="card-panel-body">
                            <canvas id="daysChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-panel h-100">
                        <div class="card-panel-header">
                            <div class="card-panel-title"><i class="bi bi-clock me-2"></i>Busiest Hours</div>
                        </div>
                        <div class="card-panel-body">
                            <canvas id="hoursChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Diagnoses --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-list-ol me-2"></i>Top Diagnoses</div>
                </div>
                <div class="card-panel-body">
                    @forelse($topDiagnoses as $diagnosis => $count)
                        <div class="diagnosis-bar-row">
                            <div class="diagnosis-label">{{ Str::limit($diagnosis, 45) }}</div>
                            <div class="diagnosis-bar-wrap">
                                <div class="diagnosis-bar"
                                     style="width: {{ ($count / max($topDiagnoses->max(), 1)) * 100 }}%">
                                </div>
                            </div>
                            <div class="diagnosis-count">{{ $count }}</div>
                        </div>
                    @empty
                        <div class="empty-state-sm py-4">
                            <i class="bi bi-clipboard2-x fs-3 d-block mb-2 text-muted"></i>
                            <span class="text-muted">No finalized visit diagnoses yet</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Predictive Insights --}}
            <div class="card-panel mb-4">
                <div class="card-panel-header">
                    <div class="card-panel-title">
                        <i class="bi bi-stars me-2 text-warning"></i>Predictive Insights
                    </div>
                </div>
                <div class="card-panel-body">
                    @forelse($insights as $insight)
                        <div class="insight-card insight-{{ $insight['type'] }} d-flex gap-3">
                            <div class="insight-icon">
                                <i class="bi {{ $insight['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="insight-title">{{ $insight['title'] }}</div>
                                <div class="insight-text">{{ $insight['text'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-sm py-3 text-muted text-center">
                            <i class="bi bi-bar-chart-line fs-3 d-block mb-2"></i>
                            More visit data needed to generate insights.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Gender + Age --}}
            <div class="card-panel mb-4">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-people me-2"></i>Patient Demographics</div>
                </div>
                <div class="card-panel-body">
                    <div class="section-title">Gender</div>
                    <canvas id="genderChart" height="140" class="mb-4"></canvas>
                    <div class="section-title">Age Groups</div>
                    @forelse($ageGroups as $group => $count)
                        <div class="diagnosis-bar-row">
                            <div class="diagnosis-label" style="font-size:12px">{{ $group }}</div>
                            <div class="diagnosis-bar-wrap">
                                <div class="diagnosis-bar" style="background:#8b5cf6;width:{{ ($count / max($ageGroups->max(), 1)) * 100 }}%"></div>
                            </div>
                            <div class="diagnosis-count">{{ $count }}</div>
                        </div>
                    @empty
                        <div class="text-muted small">No data yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Appointment Stats + Follow-up rate --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div class="card-panel-title"><i class="bi bi-calendar2-check me-2"></i>Appointment Health</div>
                </div>
                <div class="card-panel-body">
                    @foreach(['pending' => ['warning','Pending'], 'accepted' => ['info','Confirmed'], 'completed' => ['success','Completed'], 'cancelled' => ['danger','Cancelled']] as $status => [$color, $label])
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span style="font-size:13px">{{ $label }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress" style="width:90px;height:7px;border-radius:10px">
                                    <div class="progress-bar bg-{{ $color }}"
                                         style="width: {{ $appointmentStats->sum() > 0 ? ($appointmentStats->get($status, 0) / $appointmentStats->sum()) * 100 : 0 }}%;border-radius:10px">
                                    </div>
                                </div>
                                <span class="badge bg-{{ $color }}">{{ $appointmentStats->get($status, 0) }}</span>
                            </div>
                        </div>
                    @endforeach
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:13px"><i class="bi bi-arrow-repeat me-1 text-primary"></i>Follow-up Rate</span>
                        <span class="fw-bold" style="font-size:14px;color:#2563eb">{{ $followUpRate }}%</span>
                    </div>
                    <div class="progress mt-1" style="height:6px;border-radius:10px">
                        <div class="progress-bar bg-primary" style="width:{{ $followUpRate }}%;border-radius:10px"></div>
                    </div>
                    <div class="text-muted small mt-1">of finalized visits have a follow-up scheduled</div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── DATA FROM PHP ──────────────────────────────────────────────
const visitsActual   = @json($visitsPerMonth);
const forecastLabels = @json($forecastLabels);
const forecastValues = @json($forecastValues);
const entData        = @json($entTrend);
const genderData     = @json($genderBreakdown);
const dayData        = @json($dayData);
const hourData       = @json($hourData);

// ── VISIT TREND + FORECAST (combined chart) ───────────────────
const actualLabels = Object.keys(visitsActual);
const actualValues = Object.values(visitsActual);
const allLabels    = [...actualLabels, ...forecastLabels];
const allActual    = [...actualValues, ...Array(forecastLabels.length).fill(null)];
const allForecast  = [...Array(actualLabels.length - 1).fill(null),
                      actualValues[actualValues.length - 1],  // connect line
                      ...forecastValues];

new Chart(document.getElementById('visitsChart'), {
    type: 'bar',
    data: {
        labels: allLabels,
        datasets: [
            {
                label: 'Actual Visits',
                data: allActual,
                backgroundColor: 'rgba(59,130,246,0.75)',
                borderColor: 'rgb(59,130,246)',
                borderWidth: 1,
                borderRadius: 6,
                order: 2,
            },
            {
                label: 'Forecast',
                data: allForecast,
                type: 'line',
                borderColor: 'rgba(139,92,246,0.9)',
                backgroundColor: 'rgba(139,92,246,0.1)',
                borderWidth: 2,
                borderDash: [6, 4],
                pointBackgroundColor: 'rgba(139,92,246,0.9)',
                pointRadius: 5,
                tension: 0.3,
                fill: false,
                order: 1,
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        if (ctx.raw === null) return null;
                        return `${ctx.dataset.label}: ${ctx.raw} visits`;
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: {
                ticks: {
                    color: ctx => forecastLabels.includes(allLabels[ctx.index])
                        ? 'rgba(139,92,246,0.9)' : '#64748b'
                }
            }
        }
    }
});

// ── ENT CLASSIFICATION HORIZONTAL BAR ────────────────────────
const entColors = ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)','rgba(239,68,68,0.8)'];
new Chart(document.getElementById('entChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(entData),
        datasets: [{
            data: Object.values(entData),
            backgroundColor: entColors,
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── GENDER DOUGHNUT ───────────────────────────────────────────
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(genderData).map(l => l.charAt(0).toUpperCase() + l.slice(1)),
        datasets: [{
            data: Object.values(genderData),
            backgroundColor: ['rgba(59,130,246,0.8)','rgba(236,72,153,0.8)','rgba(168,85,247,0.8)'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

// ── BUSIEST DAYS BAR ──────────────────────────────────────────
const maxDay = Math.max(...Object.values(dayData), 1);
new Chart(document.getElementById('daysChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(dayData),
        datasets: [{
            data: Object.values(dayData),
            backgroundColor: Object.values(dayData).map(v =>
                v === maxDay ? 'rgba(239,68,68,0.8)' : 'rgba(59,130,246,0.6)'
            ),
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── BUSIEST HOURS LINE ────────────────────────────────────────
new Chart(document.getElementById('hoursChart'), {
    type: 'line',
    data: {
        labels: Object.keys(hourData),
        datasets: [{
            data: Object.values(hourData),
            borderColor: 'rgba(16,185,129,0.9)',
            backgroundColor: 'rgba(16,185,129,0.1)',
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(16,185,129,0.9)',
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush
@endsection