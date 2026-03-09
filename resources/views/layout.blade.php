<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ENT Clinic Online</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #f5f7fb;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* override mini.css default card width so cards expand to fill parent */
    .card,
    .settings-card {
      max-width: none !important;
    }

    .card a:visited {
      color: white;
    }

    /* wrapper class pages should use; confines content within main-content while allowing full width use */
    .page-wrapper {
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
      padding: 32px 24px;
    }

    .app-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 12%;
      min-width: 200px;
      height: 100vh;
      background: #fff;
      color: #333;
      overflow-y: auto;
      overflow-x: hidden;
      transition: transform 0.3s ease, width 0.3s ease;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    /* Sidebar Header */
    .sidebar-header {
      padding: 16px 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 60px;
    }

    .sidebar-header h2 {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar-logo {
      font-size: 25px;
      font-weight: bold;
      color: var(--a-visited-color) !important;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      margin: 0;
    }

    /* User Info */
    .user-info {
      display: none;
    }

    .user-info .user-name {
      font-weight: 600;
      margin-bottom: 5px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .user-info .user-role {
      font-size: 11px;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Navigation Menu */
    .nav-menu {
      list-style: none;
      padding: 8px 8px;
      margin: 0;
      flex: 1;
      overflow-y: auto;
      padding-bottom: 90px;
    }

    .nav-item {
      margin-bottom: 3px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: all 0.2s ease;
      font-size: 13px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.25);
      border-left: none;
      box-shadow: inset 3px 0 0 rgba(255, 255, 255, 0.5);
    }

    .nav-link i {
      font-size: 14px;
      min-width: 18px;
      text-align: center;
    }

    nav a,
    nav a:visited {
      color: #555;
      padding: 0;
    }

    a.nav-link {
      padding: 0;
    }

    nav.nav-menu {
      padding: 0;
    }

    .nav-section-title {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: rgba(255, 255, 255, 0.5);
      padding: 12px 12px 8px 12px;
      margin-top: 8px;
    }

    /* Main Content Area */
    .main-content {
      margin-left: 12%;
      width: 88%;
      transition: all 0.3s ease;
      /* prevent children from pushing the container wider than the calculated width */
      overflow-x: hidden;
      position: relative;
      /* leave space at top for fixed navbar */
      padding-top: 4.5rem;
    }

    .main-content.expanded {
      margin-left: 0;
      width: 100%;
    }

    /* Top Header/Navbar */
    .top-navbar {
      background: white;
      padding: 12px 24px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      /* fixed at viewport top so it never scrolls under content */
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      z-index: 200;
      height: 4.5rem;
    }

    .navbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .toggle-sidebar-btn {
      background: none;
      border: none;
      font-size: 20px;
      color: #6b7280;
      cursor: pointer;
      padding: 6px 8px;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .toggle-sidebar-btn:hover {
      background: #f3f4f6;
      color: #374151;
    }

    .navbar-title {
      font-size: 16px;
      font-weight: 600;
      color: #111827;
      margin: 0;
    }

    /* Sidebar User Menu (Bottom) */
    .sidebar-user-menu {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 12px;
    }

    .sidebar-user-info {
      flex: 1;
      min-width: 0;
    }

    .sidebar-user-name {
      font-size: 13px;
      font-weight: 600;
      color: #333;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .sidebar-user-role {
      font-size: 11px;
      color: #777;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .navbar-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .user-menu {
      display: none;
    }

    .user-menu-avatar {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 14px;
    }

    .logout-btn {
      background: transparent;
      color: #333;
      border: 1px solid #d1d5db;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 6px;
      border: none;
    }

    .logout-btn:hover {
      background: #f3f4f6;
      color: red;
      border-color: #9ca3af;
    }

    .navbar-right form {
      background: none;
      border: none;
    }

    /* Main Content */
    main {
      padding: 24px;
      min-height: calc(100vh - 60px);
      background: #f5f7fb;
    }

    /* Global button & table styles (used application-wide) */
    .btn-primary {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .btn-primary:hover {
      background: #0056b3;
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .btn-secondary:hover {
      background: #5a6268;
    }

    .btn-add-patient {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .btn-add-patient:hover {
      background-color: #218838;
    }

    .settings-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    .settings-table thead th {
      background: #f9fafb;
      border-bottom: 2px solid #e5e7eb;
      padding: 14px 16px;
      text-align: left;
      font-weight: 700;
      color: #374151;
      position: sticky;
      top: 0;
    }

    .settings-table tbody tr {
      border-bottom: 1px solid #f3f4f6;
      transition: background-color 0.2s ease;
    }

    .settings-table tbody tr:hover {
      background-color: #f9fafb;
    }

    .settings-table td {
      padding: 14px 16px;
      color: #1f2937;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .sidebar {
        min-width: 180px;
      }

      .nav-link {
        font-size: 13px;
        padding: 10px 12px;
      }

      main {
        padding: 20px;
      }
    }

    @media (max-width: 768px) {
      .app-container {
        flex-direction: column;
      }

      .sidebar {
        position: fixed;
        left: 0;
        top: 60px;
        width: 100%;
        min-width: unset;
        height: calc(100vh - 60px);
      }

      .sidebar.collapsed {
        transform: translateX(-100%);
      }

      .main-content {
        margin-left: 0;
        width: 100%;
      }

      .main-content.expanded {
        margin-left: 0;
        width: 100%;
      }

      main {
        padding: 15px;
        min-height: calc(100vh - 60px);
      }

      .navbar-title {
        font-size: 16px;
      }
    }

    @media (max-width: 480px) {
      .top-navbar {
        padding: 12px 15px;
      }

      .navbar-title {
        font-size: 14px;
      }

      .toggle-sidebar-btn {
        font-size: 18px;
      }

      main {
        padding: 10px;
      }

      .user-menu {
        flex-direction: column;
        gap: 5px;
        padding: 5px 10px;
      }
    }

    /* Scrollbar styling */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    /* Page transitions */
    main>div,
    main>section {
      animation: fadeIn 0.3s ease;
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

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      gap: 8px;
      margin-bottom: 20px;
      font-size: 13px;
    }

    .breadcrumb a {
      color: #667eea;
      text-decoration: none;
    }

    .breadcrumb a:hover {
      text-decoration: underline;
    }

    .breadcrumb-separator {
      color: #999;
    }

    .breadcrumb-current {
      color: #666;
      font-weight: 500;
    }
  </style>
  
  <!-- Modal Styles -->
  <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
  
  <!-- Notification Styles -->
  <style>
    #notification-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      max-width: 400px;
    }

    .notification {
      background: white;
      padding: 16px;
      margin-bottom: 10px;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      display: flex;
      justify-content: space-between;
      align-items: center;
      animation: slideInRight 0.3s ease;
    }

    .notification-success {
      border-left: 4px solid #28a745;
      background: #d4edda;
      color: #155724;
    }

    .notification-error {
      border-left: 4px solid #dc3545;
      background: #f8d7da;
      color: #721c24;
    }

    .notification-warning {
      border-left: 4px solid #ffc107;
      background: #fff3cd;
      color: #856404;
    }

    .notification-close {
      background: none;
      border: none;
      font-size: 20px;
      color: inherit;
      cursor: pointer;
      padding: 0;
      margin-left: 10px;
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(100px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @media (max-width: 768px) {
      #notification-container {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
      }

      .notification {
        flex-direction: column;
        align-items: flex-start;
      }

      .notification-close {
        margin-left: 0;
        margin-top: 8px;
      }
    }
  </style>
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Vite Assets -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <!-- jQuery (required for Select2) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Select2 for Searchable Dropdowns -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <style>
    .select2-container--default.select2-container--focus .select2-selection--single {
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    .select2-container--default .select2-selection--single {
      border: 1px solid var(--color-border);
      border-radius: var(--radius-md);
      height: 42px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 40px;
      padding-left: 12px;
    }
    .select2-dropdown {
      border-color: var(--color-border) !important;
    }
  </style>
</head>

<body>
  <div class="app-container">
    <!-- Sidebar Navigation -->
    @if(session('user_id'))
      <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
          <div class="sidebar-logo">
            <i class="fas fa-hospital"></i>
            ENT Clinic
          </div>
        </div>

        <div class="user-info">
          <div class="user-name">{{ session('user_name') }}</div>
          <div class="user-role">{{ session('user_role') }}</div>
        </div>

        <nav class="nav-menu">
          {{-- Admin Navigation --}}
          @if(session('user_role') === 'admin')
            <li class="nav-item">
              <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Administrator Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.settings') }}"
                class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
              </a>
            </li>

            {{-- Doctor Navigation --}}
          @elseif(session('user_role') === 'doctor')
            <li class="nav-item">
              <a href="{{ route('doctor.dashboard') }}"
                class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
              </a>
            </li>

            <!-- <div class="nav-section-title">Clinical</div> -->

            <li class="nav-item">
              <a href="{{ route('doctor.patients') }}"
                class="nav-link {{ request()->routeIs('doctor.patients') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>My Patients</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('doctor.appointments') }}"
                class="nav-link {{ request()->routeIs('doctor.appointments') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Appointments</span>
              </a>
            </li>

            <!-- <div class="nav-section-title">Analytics</div> -->

            <li class="nav-item">
              <a href="{{ route('doctor.analytics') }}"
                class="nav-link {{ request()->routeIs('doctor.analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
              </a>
            </li>

            {{-- Secretary Navigation --}}
          @elseif(strtolower(session('user_role')) === 'secretary')
            <li class="nav-item">
              <a href="{{ route('secretary.dashboard') }}"
                class="nav-link {{ request()->routeIs('secretary.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('secretary.patients') }}"
                class="nav-link {{ request()->routeIs('secretary.patients') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Patients</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('secretary.appointments') }}"
                class="nav-link {{ request()->routeIs('secretary.appointments') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Appointments</span>
              </a>
            </li>

            {{-- Default Staff Navigation --}}
          @else
            <li class="nav-item">
              <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
              </a>
            </li>
          @endif
        </nav>

        <!-- User Menu at Sidebar Bottom -->
        <div class="sidebar-user-menu">
          <div class="user-menu-avatar">
            {{ strtoupper(substr(session('user_name'), 0, 1)) }}
          </div>
          <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ session('user_name') }}</div>
            <div class="sidebar-user-role">{{ ucfirst(strtolower(session('user_role'))) }}</div>
          </div>
        </div>
      </aside>
    @endif

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
      <!-- Top Navigation Bar -->
      @if(session('user_id'))
        <div class="top-navbar">
          <div class="navbar-left">
            <button class="toggle-sidebar-btn" id="toggleSidebarBtn" title="Toggle Sidebar">
              <i class="fas fa-bars"></i>
            </button>
            <h1 class="navbar-title" id="pageTitle">{{ ucfirst(session('user_role')) }}</h1>
          </div>

          <div class="navbar-right">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
              @csrf
              <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <!-- <span>Logout</span> -->
              </button>
            </form>
          </div>
        </div>
      @endif

      <!-- Page Content -->
      <main>
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    // Sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function () {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      const toggleBtn = document.getElementById('toggleSidebarBtn');
      const pageTitle = document.getElementById('pageTitle');

      // Check localStorage for sidebar state
      const sidebarState = localStorage.getItem('sidebarCollapsed') === 'true';
      if (sidebarState) {
        sidebar?.classList.add('collapsed');
        mainContent?.classList.add('expanded');
      }

      // Handle top-navbar positioning relative to sidebar state
      const topNavbar = document.querySelector('.top-navbar');

      function updateTopNavbarPosition(isCollapsed) {
        if (!topNavbar) return;
        if (isCollapsed) {
          topNavbar.style.left = '0';
          topNavbar.style.width = '100%';
        } else {
          // keep the navbar aligned to the main content when sidebar is visible
          topNavbar.style.left = '12%';
          topNavbar.style.width = 'calc(100% - 12%)';
        }
      }

      // Initialize navbar position based on saved sidebar state
      updateTopNavbarPosition(sidebarState);

      // Toggle sidebar on button click and update navbar
      toggleBtn?.addEventListener('click', function () {
        const isCollapsed = sidebar?.classList.toggle('collapsed');
        mainContent?.classList.toggle('expanded');
        updateTopNavbarPosition(Boolean(isCollapsed));
        localStorage.setItem('sidebarCollapsed', isCollapsed);
      });

      // Update page title based on current page
      function updatePageTitle() {
        const titles = {
          'admin.dashboard': 'Admin Dashboard',
          'admin.settings': 'System Settings',
          'doctor.dashboard': 'Doctor Dashboard',
          'doctor.patients': 'My Patients',
          'doctor.appointments': 'My Appointments',
          'doctor.analytics': 'Clinical Analytics',
          'doctor.patient-profile': 'Patient Profile',
          'secretary.dashboard': 'Secretary Dashboard',
          'secretary.patients': 'Patient List',
          'secretary.appointments': 'Appointment Management',
          'secretary.patient-profile': 'Patient Profile',
        };

        // Get current route from page (you can also use data attributes)
        const currentPath = window.location.pathname;
        let title = 'Dashboard';

        Object.entries(titles).forEach(([route, titleText]) => {
          if (currentPath.includes('/' + route.split('.')[1])) {
            title = titleText;
          }
        });

        if (pageTitle) {
          pageTitle.textContent = title;
        }
      }

      updatePageTitle();

      // Close sidebar on mobile when navigating
      if (window.innerWidth < 768) {
        document.querySelectorAll('.nav-link').forEach(link => {
          link.addEventListener('click', function () {
            if (sidebar && !sidebar.classList.contains('collapsed')) {
              sidebar.classList.add('collapsed');
              mainContent?.classList.add('expanded');
            }
          });
        });
      }
    });

    // Handle window resize
    window.addEventListener('resize', function () {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');

      if (window.innerWidth >= 768) {
        // Reset on desktop
        sidebar?.classList.remove('collapsed');
        mainContent?.classList.remove('expanded');
      }
    });
  </script>

  <!-- Notification Container -->
  <div id="notification-container"></div>

  <!-- Modal Management Scripts -->
  @vite(['resources/js/modals.js', 'resources/js/patient-form.js', 'resources/js/appointments-calendar.js'])

  <!-- Patient Modal Functions -->
  <script>
    /**
     * Patient Modal Functions
     * Handles opening edit patient modal with data population
     */

    function openEditPatientModal(patientId) {
        // Fetch patient data
        fetch(`/api/patients/${patientId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const patient = data.data;
                populateEditPatientForm(patient);
                openModal('edit-patient-modal-overlay');
            } else {
                Notification.error('Failed to load patient data');
            }
        })
        .catch(error => {
            console.error('Error loading patient:', error);
            Notification.error('An error occurred while loading patient data');
        });
    }

    function populateEditPatientForm(patient) {
        // Helper function to safely set element value
        function setValueIfExists(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.value = value || '';
            }
        }

        // Helper function to safely set element text
        function setTextIfExists(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text || '';
            }
        }

        // Personal Information
        setValueIfExists('edit-patient-id', patient.id);
        setValueIfExists('edit-first-name', patient.first_name);
        setValueIfExists('edit-last-name', patient.last_name);
        setValueIfExists('edit-gender', patient.gender);
        
        // Format date_of_birth
        if (patient.date_of_birth) {
            const dateObj = new Date(patient.date_of_birth);
            const formattedDate = dateObj.toISOString().split('T')[0];
            setValueIfExists('edit-dob', formattedDate);
        }

        // Contact Information
        setValueIfExists('edit-email', patient.email);
        setValueIfExists('edit-phone', patient.phone);
        setValueIfExists('edit-occupation', patient.occupation);

        // Address Information
        setValueIfExists('edit-address', patient.address);
        setValueIfExists('edit-city', patient.city);
        setValueIfExists('edit-state', patient.state);
        setValueIfExists('edit-country', patient.country);
        setValueIfExists('edit-postal', patient.postal_code);

        // Health Information
        setValueIfExists('edit-height', patient.height);
        setValueIfExists('edit-weight', patient.weight);
        setValueIfExists('edit-allergies', patient.allergies);
        setValueIfExists('edit-vaccines', patient.vaccine_history);

        // Emergency Contact Information (if fields exist)
        setValueIfExists('edit-emergency-contact-name', patient.emergency_contact_name);
        setValueIfExists('edit-emergency-contact-relationship', patient.emergency_contact_relationship);
        setValueIfExists('edit-emergency-contact-phone', patient.emergency_contact_phone);

        // Medical information (if fields exist)
        setValueIfExists('edit-medical-history', patient.medical_history);
        setValueIfExists('edit-current-medications', patient.current_medications);

        // Update BMI display if function exists
        const heightField = document.getElementById('edit-height');
        const weightField = document.getElementById('edit-weight');
        if (heightField && weightField && typeof calculateBMI === 'function') {
            calculateBMI();
        }
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Check if it's an overlay
            if (modalId.includes('overlay')) {
                modal.style.display = 'flex';
                modal.style.justifyContent = 'center';
                modal.style.alignItems = 'center';
            } else {
                // It's a regular modal
                if (modal.classList) {
                    modal.classList.add('active');
                } else {
                    modal.style.display = 'flex';
                }
            }
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Check if it's an overlay
            if (modalId.includes('overlay')) {
                modal.style.display = 'none';
            } else {
                // It's a regular modal
                if (modal.classList) {
                    modal.classList.remove('active');
                } else {
                    modal.style.display = 'none';
                }
            }
        }
    }

    // Delete Patient Modal Functions
    let deletePatientId = null;

    function openDeleteConfirmModal(patientId, patientName) {
        deletePatientId = patientId;
        document.getElementById('delete-patient-name').textContent = patientName;
        const modal = document.getElementById('delete-patient-confirm-overlay');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';
        }
    }

    function closeDeleteConfirmModal() {
        deletePatientId = null;
        const modal = document.getElementById('delete-patient-confirm-overlay');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function confirmDeletePatient() {
        if (!deletePatientId) return;

        ApiManager.request('DELETE', `/api/patients/${deletePatientId}`)
            .then(response => {
                if (response.success) {
                    Notification.success('Patient deleted successfully');
                    closeDeleteConfirmModal();
                    // Reload the page to refresh the patient list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Notification.error(response.error || 'Failed to delete patient');
                }
            })
            .catch(error => {
                console.error('Error deleting patient:', error);
                Notification.error('An error occurred while deleting the patient');
            });
    }
    

    // Close modals when clicking on overlay background
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachModalListeners);
    } else {
        attachModalListeners();
    }

    function attachModalListeners() {
        // Close add patient modal on background click
        const addPatientOverlay = document.getElementById('addPatientModal-overlay');
        if (addPatientOverlay) {
            addPatientOverlay.addEventListener('click', function(e) {
                if (e.target === addPatientOverlay) {
                    closeModal('addPatientModal-overlay');
                }
            });
        }

        // Close edit patient modal on background click
        const editPatientOverlay = document.getElementById('edit-patient-modal-overlay');
        if (editPatientOverlay) {
            editPatientOverlay.addEventListener('click', function(e) {
                if (e.target === editPatientOverlay) {
                    closeModal('edit-patient-modal-overlay');
                }
            });
        }

        // Close delete patient modal on background click
        const deletePatientOverlay = document.getElementById('delete-patient-confirm-overlay');
        if (deletePatientOverlay) {
            deletePatientOverlay.addEventListener('click', function(e) {
                if (e.target === deletePatientOverlay) {
                    closeModal('delete-patient-confirm-overlay');
                }
            });
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const addModal = document.getElementById('addPatientModal-overlay');
                const editModal = document.getElementById('edit-patient-modal-overlay');
                const deleteModal = document.getElementById('delete-patient-confirm-overlay');
                if (addModal && addModal.style.display === 'flex') {
                    closeModal('addPatientModal-overlay');
                }
                if (editModal && editModal.style.display === 'flex') {
                    closeModal('edit-patient-modal-overlay');
                }
                if (deleteModal && deleteModal.style.display === 'flex') {
                    closeModal('delete-patient-confirm-overlay');
                }
            }
        });
    }
  </script>
</body>

</html>