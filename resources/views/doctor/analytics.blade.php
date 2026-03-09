@extends('layout')

@section('content')
<div class="doctor-analytics">
  <div class="page-wrapper">
    <!-- Page Header -->
    <div class="flex-between" style="margin-bottom: 30px;">
      <div>
        <h1>Clinical Analytics Dashboard</h1>
        <p style="color: var(--color-text-muted); margin: 0;">Advanced forecasting and insights for your practice</p>
      </div>
    </div>

    <!-- Time Range Filter -->
    <div class="card" style="margin-bottom: 30px;">
      <form method="GET" action="{{ route('doctor.analytics') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0;">
          <label for="timeRange" class="form-label">Time Range:</label>
          <select name="timeRange" id="timeRange" class="form-control">
            <option value="today" {{ $timeRange === 'today' ? 'selected' : '' }}>Today</option>
            <option value="this_week" {{ $timeRange === 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month" {{ $timeRange === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="this_year" {{ $timeRange === 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="custom" {{ $timeRange === 'custom' ? 'selected' : '' }}>Custom Range</option>
          </select>
        </div>

        @if($timeRange === 'custom')
        <div class="form-group" style="margin: 0;">
          <label for="fromDate" class="form-label">From:</label>
          <input type="date" name="fromDate" id="fromDate" class="form-control" value="{{ $fromDate }}">
        </div>

        <div class="form-group" style="margin: 0;">
          <label for="toDate" class="form-label">To:</label>
          <input type="date" name="toDate" id="toDate" class="form-control" value="{{ $toDate }}">
        </div>
        @endif

        <button type="submit" class="btn btn-primary">Apply Filter</button>
      </form>
    </div>


    <!-- Tab Navigation -->
    <div class="grid grid-3" style="gap: 10px; margin-bottom: 30px;">
      <button class="analytics-tab-btn btn btn-primary active" onclick="switchTab('descriptive')">📊 Descriptive Analytics</button>
      <button class="analytics-tab-btn btn btn-secondary" onclick="switchTab('predictive')">🔮 Predictive Analytics</button>
      <button class="analytics-tab-btn btn btn-secondary" onclick="switchTab('prescriptive')">💡 Prescriptive Analytics</button>
    </div>

    <!-- DESCRIPTIVE ANALYTICS TAB -->
    <div id="descriptive-tab" class="analytics-tab" style="display: block;">
      <div style="margin-bottom: 30px;">
        <h2>Descriptive Analytics - Current Performance</h2>
        <p style="color: var(--color-text-muted);">Historical data and actual performance metrics for the selected period</p>
      </div>

      <!-- Key Metrics -->
      <div class="grid grid-responsive" style="margin-bottom: 30px;">
        <x-stat-card title="Total Appointments" :value="$descriptiveAnalytics['total_appointments'] ?? 0" description="In selected period" color="var(--color-primary)" />
        <x-stat-card title="Completed" :value="$descriptiveAnalytics['completed_appointments'] ?? 0" description="Successfully completed" color="var(--color-success)" />
        <x-stat-card title="Pending" :value="$descriptiveAnalytics['pending_appointments'] ?? 0" description="Awaiting confirmation" color="var(--color-warning)" />
        <x-stat-card title="Confirmed" :value="$descriptiveAnalytics['confirmed_appointments'] ?? 0" description="Scheduled appointments" color="var(--color-info)" />
        <x-stat-card title="Cancelled" :value="$descriptiveAnalytics['cancelled_appointments'] ?? 0" description="Cancelled by patient" color="var(--color-danger)" />
        <x-stat-card title="Total Patients" :value="$descriptiveAnalytics['total_patients'] ?? 0" description="In the database" color="var(--color-primary)" />
      </div>

      <!-- Appointment Status Bar Chart -->
      <div class="grid grid-2" style="gap: 30px; margin-bottom: 30px;">
        <x-card header="Appointment Status Distribution - Bar Chart">
          <div class="chart-container" style="height: 300px; position: relative;">
            <canvas id="statusBarChart"></canvas>
          </div>
        </x-card>

        <!-- Status Breakdown Pie Chart -->
        <x-card header="Status Distribution - Pie Chart">
          <div class="chart-container" style="height: 300px; position: relative;">
            <canvas id="statusPieChart"></canvas>
          </div>
        </x-card>
      </div>
    </div>


    <!-- PREDICTIVE ANALYTICS TAB -->
    <div id="predictive-tab" class="analytics-tab" style="display: none;">
      <div style="margin-bottom: 30px;">
        <h2>Predictive Analytics - Forecasting</h2>
        <p style="color: var(--color-text-muted);">AI-powered predictions and forecasts for the selected period</p>
      </div>

      <!-- Prediction Metrics -->
      <div class="grid grid-responsive" style="margin-bottom: 30px;">
        <x-stat-card title="Predicted Appointments" :value="$predictiveAnalytics['predicted_appointments'] ?? 0" description="Expected bookings" color="var(--color-primary)" />
        <x-stat-card title="Predicted Completed" :value="$predictiveAnalytics['predicted_completed_appointments'] ?? 0" description="Expected completions" color="var(--color-success)" />
        <x-stat-card title="Completion Rate" :value="($predictiveAnalytics['predicted_completion_rate'] ?? 0) . '%'" description="Historical trend" color="#6f42c1" />
        <x-stat-card title="Avg per Day" :value="$predictiveAnalytics['avg_appointments_per_day'] ?? 0" description="Last 3 months" color="#fd7e14" />
        <x-stat-card title="Patient Growth" :value="($predictiveAnalytics['patient_growth_rate'] ?? 0) . '%'" description="Month-over-month" color="{{ ($predictiveAnalytics['patient_growth_rate'] ?? 0) >= 0 ? 'var(--color-success)' : 'var(--color-danger)' }}" />
      </div>

      <!-- Appointment Trend Chart -->
      <x-card header="Appointment Trend (Last 6 Months)">
        <div class="chart-container" style="height: 300px; position: relative;">
          <canvas id="trendChart"></canvas>
        </div>
      </x-card>
    </div>

    <!-- PRESCRIPTIVE ANALYTICS TAB -->
    <div id="prescriptive-tab" class="analytics-tab" style="display: none;">
      <div style="margin-bottom: 30px;">
        <h2>Prescriptive Analytics - Recommendations</h2>
        <p style="color: var(--color-text-muted);">Actionable insights and recommendations to optimize your practice</p>
      </div>

      <!-- Recommendations -->
      <div class="grid" style="gap: 15px;">
        @forelse($prescriptiveAnalytics ?? [] as $recommendation)
        <div class="alert alert-{{ $recommendation['type'] ?? 'info' }}">
          <h4 style="margin: 0 0 8px 0;">{{ $recommendation['title'] }}</h4>
          <p style="margin: 0;">{{ $recommendation['message'] }}</p>
        </div>
        @empty
        <div class="card">
          <p style="text-align: center; color: var(--color-text-muted); margin: 0;">No recommendations at this time.</p>
        </div>
        @endforelse
      </div>
    </div>

    <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary" style="margin-top: 30px;">← Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// Chart color palette
const colors = {
  pending: '#ffc107',
  confirmed: '#17a2b8',
  completed: '#28a745',
  cancelled: '#dc3545',
  primary: '#007bff',
  secondary: '#6c757d',
  success: '#28a745',
  danger: '#dc3545'
};

// Initialize charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  initializeCharts();
});

function initializeCharts() {
  // Status Bar Chart
  const statusCtx = document.getElementById('statusBarChart');
  if (statusCtx) {
    try {
      new Chart(statusCtx, {
        type: 'bar',
        data: {
          labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
          datasets: [{
            label: 'Appointments',
            data: [
              {{ $descriptiveAnalytics['appointment_status_distribution']['pending'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['confirmed'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['completed'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['cancelled'] ?? 0 }}
            ],
            backgroundColor: [
              colors.pending,
              colors.confirmed,
              colors.completed,
              colors.cancelled
            ],
            borderColor: [
              colors.pending,
              colors.confirmed,
              colors.completed,
              colors.cancelled
            ],
            borderWidth: 1,
            borderRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#e9ecef'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
      console.log('Status bar chart initialized successfully');
    } catch (e) {
      console.error('Error initializing status bar chart:', e);
    }
  }

  // Status Pie Chart
  const pieCtx = document.getElementById('statusPieChart');
  if (pieCtx) {
    try {
      new Chart(pieCtx, {
        type: 'doughnut',
        data: {
          labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
          datasets: [{
            label: 'Appointments by Status',
            data: [
              {{ $descriptiveAnalytics['appointment_status_distribution']['pending'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['confirmed'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['completed'] ?? 0 }},
              {{ $descriptiveAnalytics['appointment_status_distribution']['cancelled'] ?? 0 }}
            ],
            backgroundColor: [
              colors.pending,
              colors.confirmed,
              colors.completed,
              colors.cancelled
            ],
            borderColor: '#ffffff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 15,
                font: {
                  size: 13
                }
              }
            }
          }
        }
      });
      console.log('Pie chart initialized successfully');
    } catch (e) {
      console.error('Error initializing pie chart:', e);
    }
  }

  // Appointment Trend Chart
  const trendCtx = document.getElementById('trendChart');
  if (trendCtx) {
    try {
      const trendData = {!! json_encode($predictiveAnalytics['appointment_trend'] ?? []) !!};
      const trendLabels = Object.keys(trendData);
      const trendValues = Object.values(trendData);

      new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: trendLabels,
          datasets: [{
            label: 'Appointments',
            data: trendValues,
            borderColor: colors.primary,
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: colors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#e9ecef'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
      console.log('Trend chart initialized successfully');
    } catch (e) {
      console.error('Error initializing trend chart:', e);
    }
  }
}

function switchTab(tabName) {
  // Hide all tabs
  document.querySelectorAll('.analytics-tab').forEach(tab => {
    tab.style.display = 'none';
  });

  // Deactivate all buttons
  document.querySelectorAll('.analytics-tab-btn').forEach(btn => {
    btn.classList.remove('btn-primary', 'active');
    btn.classList.add('btn-secondary');
  });

  // Show selected tab
  document.getElementById(tabName + '-tab').style.display = 'block';

  // Activate selected button
  event.target.classList.remove('btn-secondary');
  event.target.classList.add('btn-primary', 'active');
}

// Handle custom date range visibility
document.getElementById('timeRange').addEventListener('change', function() {
  if (this.value !== 'custom') {
    document.querySelector('form').submit();
  }
});
</script>

<style>
.doctor-analytics {
  background-color: #f5f7fb;
  min-height: 100vh;
}

.analytics-tab {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.analytics-tab-btn {
  transition: all 0.3s ease;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.analytics-tab-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.analytics-tab-btn.active {
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.chart-container {
  position: relative;
  background: white;
  border-radius: 8px;
  padding: 2px;
}

/* Improved card styling */
.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  transition: box-shadow 0.3s ease;
}

.card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Grid improvements */
.grid {
  display: grid;
  gap: 20px;
}

.grid-2 {
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
}

.grid-responsive {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.grid-3 {
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .grid-3 {
    grid-template-columns: 1fr;
  }
  
  .grid-responsive {
    grid-template-columns: 1fr;
  }
  
  .analytics-tab-btn {
    font-size: 14px;
    padding: 10px 12px;
  }
}
</style>
  transition: all 0.3s ease;
}

.analytics-tab-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

@media (max-width: 768px) {
  .grid-2 {
    grid-template-columns: 1fr !important;
  }
  
  .analytics-tab-btn {
    font-size: 12px;
    padding: 10px 12px !important;
  }
}
</style>
@endsection
