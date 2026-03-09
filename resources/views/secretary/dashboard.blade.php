@extends('layout')

@section('content')
  <div class="secretary-dashboard">
    <div class="page-wrapper">
      <h1>Secretary Dashboard</h1>
      <p style="color: var(--color-text-muted);">Welcome,
        <strong>{{ session('user_name') ?? ($user->full_name ?? $user->username) }}</strong></p>

      <!-- Statistic Panels -->
      <div class="grid grid-3" style="margin: 30px 0;">
        <div class="stat-card-gradient stat-card-purple">
          <div class="stat-icon"><i class="fas fa-users"></i></div>
          <div class="stat-content">
            <div class="stat-number">{{ $totalPatients }}</div>
            <div class="stat-label">Total Patients</div>
          </div>
        </div>

        <div class="stat-card-gradient stat-card-pink">
          <div class="stat-icon"><i class="fas fa-id-badge"></i></div>
          <div class="stat-content">
            <div class="stat-number">Secretary</div>
            <div class="stat-label">Your Role</div>
          </div>
        </div>

        <div class="stat-card-gradient stat-card-blue">
          <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
          <div class="stat-content">
            <div class="stat-number">Active</div>
            <div class="stat-label">System Status</div>
          </div>
        </div>
      </div>

      <!-- Action Cards -->
      <div class="grid grid-4" style="margin: 30px 0;">
        <!-- Patient Management Card -->
        <div class="module-card">
          <div class="module-icon module-icon-blue"><i class="fas fa-users"></i></div>
          <h3 class="module-title">Patient Management</h3>
          <p class="module-desc">View and manage patient records, contact information, and basic patient data.</p>
          <a href="{{ route('secretary.patients') }}" class="btn btn-primary" style="align-self: flex-start;">View Patients</a>
        </div>

        <!-- Patient Profiles Card -->
        <div class="module-card">
          <div class="module-icon module-icon-pink"><i class="fas fa-id-card"></i></div>
          <h3 class="module-title">Patient Profiles</h3>
          <p class="module-desc">Access patient profiles to view detailed information and contact details.</p>
          <a href="{{ route('secretary.patients') }}" class="btn btn-primary" style="align-self: flex-start;">Browse Patients</a>
        </div>

        <!-- Add New Patient Card -->
        <div class="module-card">
          <div class="module-icon module-icon-purple"><i class="fas fa-user-plus"></i></div>
          <h3 class="module-title">Add New Patient</h3>
          <p class="module-desc">Register new patients and enter their basic information into the system.</p>
          <a href="{{ url('/test-api.html') }}" class="btn btn-primary" style="align-self: flex-start;">Add Patient</a>
        </div>

        <!-- Search Patients Card -->
        <div class="module-card">
          <div class="module-icon module-icon-cyan"><i class="fas fa-search"></i></div>
          <h3 class="module-title">Search Patients</h3>
          <p class="module-desc">Quickly find patients by name, phone number, or other identifying information.</p>
          <a href="{{ route('secretary.patients') }}" class="btn btn-primary" style="align-self: flex-start;">Search Patients</a>
        </div>
      </div>
    </div>
  </div>

  <style>
    .secretary-dashboard {
      background-color: #f5f5f5;
      min-height: 100vh;
    }

    .grid.grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .grid.grid-4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    /* Gradient Stat Cards */
    .stat-card-gradient {
      color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: var(--shadow-md);
      display: flex;
      align-items: center;
      gap: 15px;
      transition: all 0.3s ease;
    }

    .stat-card-gradient:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .stat-card-gradient .stat-icon {
      font-size: 36px;
      opacity: 0.3;
      flex-shrink: 0;
    }

    .stat-card-gradient .stat-number {
      font-size: 28px;
      font-weight: bold;
    }

    .stat-card-gradient .stat-label {
      font-size: 13px;
      opacity: 0.9;
      margin-top: 4px;
    }

    .stat-card-purple {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }

    .stat-card-pink {
      background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    }

    .stat-card-blue {
      background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
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

    .module-icon-cyan {
      background-color: #11cdef;
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

    @media (max-width: 1024px) {
      .grid-4 {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .grid-3 {
        grid-template-columns: 1fr;
      }

      .grid-4 {
        grid-template-columns: 1fr;
      }
    }
  </style>
@endsection