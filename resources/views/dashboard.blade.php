@extends('layout')

@section('content')
<div style="max-width:900px;margin:0 auto">
  <h2>Dashboard</h2>
  <p>Welcome, <strong>{{ session('user_name') ?? ($user->full_name ?? $user->username) }}</strong></p>

  <div class="card">
    <h4>Quick Links</h4>
    <ul>
      <li><a href="/test-api.html">Open API Tester</a></li>
      <li><a href="/api/patients">View Patients (API)</a></li>
      <li><a href="/api/appointments">View Appointments (API)</a></li>
    </ul>
  </div>
</div>
@endsection
