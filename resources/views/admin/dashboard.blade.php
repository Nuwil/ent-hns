@extends('layout')

@section('content')
  <div class="admin-dashboard">
    <div class="page-wrapper">
      <h1>Admin Dashboard</h1>
      <p style="color: var(--color-text-muted);">Welcome,
        <strong>{{ session('user_name') ?? ($user->full_name ?? $user->username) }}</strong></p>

      <!-- Summary Tiles -->
      <div class="grid grid-3" style="margin: 30px 0;">
        <!-- Total Patients Tile -->
        <div class="summary-tile tile-gradient-blue">
          <div class="tile-content">
            <div>
              <div class="tile-label">Total Patients</div>
              <div class="tile-number">{{ $totalPatients ?? 0 }}</div>
            </div>
            <div class="tile-icon"><i class="fas fa-users"></i></div>
          </div>
        </div>

        <!-- System Users Tile -->
        <div class="summary-tile tile-gradient-pink">
          <div class="tile-content">
            <div>
              <div class="tile-label">System Users</div>
              <div class="tile-number">{{ $systemUsers ?? 0 }}</div>
            </div>
            <div class="tile-icon"><i class="fas fa-user-friends"></i></div>
          </div>
        </div>

        <!-- Your Role Tile -->
        <div class="summary-tile tile-gradient-cyan">
          <div class="tile-content">
            <div>
              <div class="tile-label">Your Role</div>
              <div class="tile-number">Administrator</div>
              <div class="tile-sub">Full System Access</div>
            </div>
            <div class="tile-icon"><i class="fas fa-crown"></i></div>
          </div>
        </div>
      </div>

      <!-- Module Cards -->
      <div class="grid grid-3" style="margin-top: 30px;">
        <!-- User Management Card -->
        <div class="module-card">
          <div class="module-icon module-icon-blue"><i class="fas fa-users-cog"></i></div>
          <h3 class="module-title">User Management</h3>
          <p class="module-desc">Create, update, and manage user accounts for doctors, secretaries, and other
            administrators.</p>
          <a href="{{ route('admin.settings') }}#users" class="btn btn-primary" style="align-self: flex-start;">Go to
            Settings</a>
        </div>

        <!-- Patient Management Card -->
        <div class="module-card">
          <div class="module-icon module-icon-pink"><i class="fas fa-user-injured"></i></div>
          <h3 class="module-title">Patient Management</h3>
          <p class="module-desc">Access and manage all patient records, medical history, and visit information.</p>
          <a href="#patients" class="btn btn-primary" style="align-self: flex-start;">View Patients</a>
        </div>

        <!-- System Administration Card -->
        <div class="module-card">
          <div class="module-icon module-icon-purple"><i class="fas fa-tools"></i></div>
          <h3 class="module-title">System Administration</h3>
          <p class="module-desc">Export/import data, manage system configuration, and perform administrative tasks.</p>
          <a href="{{ route('admin.settings') }}" class="btn btn-primary" style="align-self: flex-start;">System
            Settings</a>
        </div>
      </div>
    </div>
  </div>

  <style>
    .admin-dashboard {
      background-color: #f5f5f5;
      min-height: 100vh;
    }

    .grid.grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    /* Summary Tiles */
    .summary-tile {
      border-radius: 10px;
      padding: 20px;
      color: white;
      min-height: 100px;
      display: flex;
      align-items: center;
      box-shadow: var(--shadow-lg);
      transition: all 0.3s ease;
    }

    .summary-tile:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .tile-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .tile-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      opacity: 0.85;
      letter-spacing: 0.5px;
    }

    .tile-number {
      font-size: 32px;
      font-weight: 800;
      margin-top: 6px;
      line-height: 1;
    }

    .tile-sub {
      font-size: 11px;
      opacity: 0.8;
      margin-top: 4px;
    }

    .tile-icon {
      font-size: 40px;
      opacity: 0.2;
    }

    .tile-gradient-blue {
      background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
    }

    .tile-gradient-pink {
      background: linear-gradient(135deg, #f5365c 0%, #ec250d 100%);
    }

    .tile-gradient-cyan {
      background: linear-gradient(135deg, #11cdef 0%, #00bcd4 100%);
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
      font-size: 13px;
      color: #6b7280;
      line-height: 1.5;
      flex: 1;
    }

    @media (max-width: 768px) {
      .grid-3 {
        grid-template-columns: 1fr 1fr 1fr;
      }
    }

    .tile-number {
      font-size: 24px;
    }

    .tile-icon {
      font-size: 28px;
    }
  </style>
@endsection