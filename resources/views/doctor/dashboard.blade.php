@extends('layout')

@section('content')
  <div class="doctor-dashboard">
    <div class="page-wrapper">
      <h1>Doctor Dashboard</h1>
      <p style="color: var(--color-text-muted);">Welcome,
        <strong>{{ session('user_name') ?? ($user->full_name ?? $user->username) }}</strong></p>

      <div class="grid grid-2" style="margin: 30px 0;">
        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #4e73df, #1cc88a);">
          <h4 class="stat-title">Total Appointments</h4>
          <p class="stat-number">{{ $totalAppointments }}</p>
          <i class="fas fa-calendar-check stat-icon"></i>
        </div>

        <div class="stat-card-gradient" style="background: linear-gradient(135deg, #36b9cc, #f6c23e);">
          <h4 class="stat-title">Pending Appointments</h4>
          <p class="stat-number">{{ $pendingAppointments }}</p>
          <i class="fas fa-hourglass-half stat-icon"></i>
        </div>
      </div>

      <div class="grid grid-3" style="margin-top: 30px;">
        <!-- Patient List Card -->
        <div class="module-card">
          <div class="module-icon module-icon-blue"><i class="fas fa-users"></i></div>
          <h3 class="module-title">Patient List</h3>
          <p class="module-desc">Manage your clinic patients and their records.</p>
          <a href="{{ route('doctor.patients') }}" class="btn btn-primary" style="align-self: flex-start;">Go to Patients</a>
        </div>

        <!-- Appointments Card -->
        <div class="module-card">
          <div class="module-icon module-icon-pink"><i class="fas fa-calendar-check"></i></div>
          <h3 class="module-title">Appointments</h3>
          <p class="module-desc">View and manage your appointment schedule.</p>
          <a href="{{ route('doctor.appointments') }}" class="btn btn-primary" style="align-self: flex-start;">Go to Appointments</a>
        </div>

        <!-- Analytics Card -->
        <div class="module-card">
          <div class="module-icon module-icon-purple"><i class="fas fa-chart-bar"></i></div>
          <h3 class="module-title">Analytics</h3>
          <p class="module-desc">Access analytics and insights for your practice.</p>
          <a href="{{ route('doctor.analytics') }}" class="btn btn-primary" style="align-self: flex-start;">View Analytics</a>
        </div>
      </div>
    </div>
  </div>

  <style>
    .doctor-dashboard {
      background-color: #f5f5f5;
      min-height: 100vh;
    }

    .grid.grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .grid.grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .stat-card-gradient {
      padding: 24px;
      border-radius: 8px;
      color: white;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: all 0.3s ease;
    }

    .stat-card-gradient:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .stat-card-gradient .stat-title {
      margin: 0 0 12px 0;
      font-size: 14px;
      font-weight: 600;
      opacity: 0.9;
    }

    .stat-card-gradient .stat-number {
      font-size: 36px;
      font-weight: bold;
      margin: 0;
    }

    .stat-card-gradient .stat-icon {
      position: absolute;
      right: 24px;
      top: 24px;
      font-size: 32px;
      opacity: 0.2;
    }

    /* Module Cards */
    .module-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: var(--shadow-md);
      display: flex;
      flex-direction: column;
      border-top: 3px solid #e5e7eb;
      transition: all 0.3s ease;
    }

    .module-card:hover {
      box-shadow: var(--shadow-lg);
      border-top-color: var(--color-primary);
    }

    .module-icon {
      width: 48px;
      height: 48px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: white;
      margin-bottom: 12px;
    }

    .module-icon-blue {
      background-color: #5e72e4;
    }

    .module-icon-pink {
      background-color: #f5365c;
    }

    .module-icon-purple {
      background-color: #825ee4;
    }

    .module-title {
      margin: 0 0 8px 0;
      font-size: 16px;
      font-weight: 700;
      color: #111827;
    }

    .module-desc {
      margin: 0 0 12px 0;
      font-size: 14px;
      color: var(--color-text-muted);
      line-height: 1.5;
      flex-grow: 1;
    }

    @media (max-width: 768px) {
      .grid-2 {
        grid-template-columns: 1fr;
      }

      .grid-3 {
        grid-template-columns: 1fr;
      }
    }
  </style>
@endsection