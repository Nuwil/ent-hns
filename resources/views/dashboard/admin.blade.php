@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
	<div class="page-content">
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

		<div class="row g-4 mt-1">
			<div class="col-lg-7">
				@include('partials.activity-log', ['activityLogs' => $activityLogs])
			</div>

			<div class="col-lg-5 d-flex flex-column gap-4">
				<div class="card-panel flex-1">
					<div class="card-panel-header">
						<div class="card-panel-title"><i class="bi bi-calendar2-day me-2"></i>Recent Appointments</div>
					</div>

					<div class="card-panel-body p-0">
						@forelse($recentAppointments as $appt)
							<div class="dash-appt-item">
								<div class="dash-appt-time">
									{{ $appt->scheduled_at->format('M j') }}<br><small>{{ $appt->scheduled_at->format('H:i') }}</small>
								</div>
								<div class="dash-appt-info">
									<div class="fw-semibold small">{{ $appt->patient->full_name ?? '—' }}</div>
									<div class="text-muted" style="font-size:11px">{{ $appt->doctor->name ?? '—' }} ·
										{{ Str::limit($appt->reason, 30) }}
									</div>
								</div>
								<span
									class="badge{{ $appt->statusBadgeClass() }}">{{ $appt->status === 'accepted' ? 'Confirmed' : ucfirst($appt->status) }}</span>
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

		{{-- ── Predictive Analytics ───────────────────────────────── --}}
		<div class="row g-4 mt-1">
			<div class="col-12">
				<div class="card-panel">
					<div class="card-panel-header">
						<div class="card-panel-title">
							<i class="bi bi-cpu me-2 text-primary"></i>Predictive Analytics
							<span class="badge bg-primary ms-2"
								style="font-size:10px;font-weight:600;padding:3px 8px;">{{ $predictive['month'] }}</span>
						</div>
						<a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary btn-sm">
							<i class="bi bi-bar-chart-fill me-1"></i>Full Analytics
						</a>
					</div>
					<div class="card-panel-body">
						@if($predictive['complaints']->isEmpty())
							<div class="empty-state-sm py-4">
								<i class="bi bi-clipboard2-x"></i>
								<span>No consultation data for {{ $predictive['month'] }} yet.</span>
							</div>
						@else
							<div class="row g-4 align-items-start">
								<div class="col-lg-7">
									<div
										style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
										Top 5 Chief Complaints — {{ $predictive['month'] }}
									</div>
									<table style="width:100%;border-collapse:collapse;font-size:13px;">
										<thead>
											<tr style="border-bottom:2px solid #e2e8f0;">
												<th
													style="padding:6px 10px;font-size:11.5px;color:#64748b;font-weight:700;text-align:left;">
													#</th>
												<th
													style="padding:6px 10px;font-size:11.5px;color:#64748b;font-weight:700;text-align:left;">
													Complaint</th>
												<th
													style="padding:6px 10px;font-size:11.5px;color:#64748b;font-weight:700;text-align:center;">
													Cases</th>
												<th
													style="padding:6px 10px;font-size:11.5px;color:#64748b;font-weight:700;text-align:center;">
													% Total</th>
												<th
													style="padding:6px 10px;font-size:11.5px;color:#64748b;font-weight:700;text-align:center;">
													Trend</th>
											</tr>
										</thead>
										<tbody>
											@foreach($predictive['complaints'] as $i => $c)
												<tr style="border-bottom:1px solid #f1f5f9;">
													<td style="padding:8px 10px;color:#94a3b8;font-size:12px;">{{ $i + 1 }}</td>
													<td style="padding:8px 10px;font-weight:600;">{{ $c['complaint'] }}</td>
													<td style="padding:8px 10px;text-align:center;font-weight:800;color:#1e293b;">
														{{ $c['count'] }}
													</td>
													<td style="padding:8px 10px;text-align:center;">
														<div
															style="display:flex;align-items:center;gap:6px;justify-content:center;">
															<div
																style="height:6px;background:#dbeafe;border-radius:3px;width:50px;overflow:hidden;">
																<div
																	style="height:100%;width:{{ $c['pct'] }}%;background:#2563eb;border-radius:3px;">
																</div>
															</div>
															<span
																style="font-size:12px;font-weight:600;color:#2563eb;">{{ $c['pct'] }}%</span>
														</div>
													</td>
													<td style="padding:8px 10px;text-align:center;">
														@if($c['trend'] === 'up')
															<span style="color:#16a34a;font-size:12px;font-weight:700;"><i
																	class="bi bi-arrow-up-short"></i>Up</span>
														@elseif($c['trend'] === 'down')
															<span style="color:#dc2626;font-size:12px;font-weight:700;"><i
																	class="bi bi-arrow-down-short"></i>Down</span>
														@else
															<span style="color:#94a3b8;font-size:12px;">— Same</span>
														@endif
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="col-lg-5">
									<div
										style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
										Case Distribution
									</div>
									<canvas id="predictiveChart" height="180"></canvas>
									<div class="mt-3 p-3"
										style="background:#f8fafc;border-radius:8px;border-left:3px solid #2563eb;">
										<div style="font-size:12px;color:#475569;line-height:1.5;">
											<strong>{{ $predictive['totalThisMonth'] }}</strong> consultations this month
											@if($predictive['totalPrevMonth'] > 0)
												vs <strong>{{ $predictive['totalPrevMonth'] }}</strong> last month
												@php
													$diff = $predictive['totalThisMonth'] - $predictive['totalPrevMonth'];
													$pct = round(abs($diff) / $predictive['totalPrevMonth'] * 100, 1);
												@endphp
												<span class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
													({{ $diff >= 0 ? '+' : '' }}{{ $diff }},
													{{ $diff >= 0 ? '+' : '-' }}{{ $pct }}%)
												</span>
											@endif
										</div>
									</div>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
		<script>
			@if(!$predictive['complaints']->isEmpty())
				(function () {
					const labels = @json($predictive['complaints']->pluck('complaint'));
					const values = @json($predictive['complaints']->pluck('count'));
					new Chart(document.getElementById('predictiveChart'), {
						type: 'line',
						data: {
							labels: labels,
							datasets: [{
								label: 'Cases',
								data: values,
								borderColor: 'rgba(37,99,235,1)',
								backgroundColor: 'rgba(37,99,235,0.08)',
								borderWidth: 2.5,
								pointBackgroundColor: [
									'rgba(37,99,235,1)', 'rgba(124,58,237,1)', 'rgba(16,163,74,1)',
									'rgba(217,119,6,1)', 'rgba(220,38,38,1)'
								],
								pointBorderColor: '#fff',
								pointBorderWidth: 2,
								pointRadius: 6,
								pointHoverRadius: 8,
								fill: true,
								tension: 0.35,
							}]
						},
						options: {
							responsive: true,
							plugins: { legend: { display: false } },
							scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { font: { size: 10 }, maxRotation: 30 } } }
						}
					});
				})();
			@endif
		</script>
	@endpush

@endsection