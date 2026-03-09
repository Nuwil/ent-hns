@extends('layout')

@section('content')
  <div class="doctor-patients">
    <div class="page-wrapper">
      <div class="flex-between" style="margin-bottom: 30px;">
        <h1>My Patients</h1>
        <button type="button" class="btn btn-primary" data-open-modal="addPatientModal">+ Add New Patient</button>
      </div>

      <x-card>
        <form method="GET" action="{{ route('doctor.patients') }}" style="display: flex; gap: 15px; margin-bottom: 20px;">
          <input type="text" name="search" placeholder="Search by name or email..." value="{{ $search ?? '' }}"
            class="form-control" style="flex: 1;">
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
      </x-card>

      @if($patients->count() > 0)
        <div class="card" style="overflow-x: auto;">
          <x-table :headers="['ID', 'Name', 'Email', 'Phone', 'Actions']" striped="true">
            @foreach($patients as $patient)
              <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->first_name ?? 'N/A' }} {{ $patient->last_name ?? '' }}</td>
                <td>{{ $patient->email ?? 'N/A' }}</td>
                <td>{{ $patient->phone ?? 'N/A' }}</td>
                <td style="white-space: nowrap;">
                  <a href="{{ route('doctor.patient-profile', $patient->id) }}" class="btn btn-sm btn-primary" style="margin-right: 5px;">View</a>
                  <button type="button" class="btn btn-sm btn-success" onclick="openEditPatientModal({{ $patient->id }})" style="margin-right: 5px;">Edit</button>
                  <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteConfirmModal({{ $patient->id }}, '{{ $patient->first_name }} {{ $patient->last_name }}')">Delete</button>
                </td>
              </tr>
            @endforeach
          </x-table>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: center;">
          {{ $patients->links() }}
        </div>
      @else
        <x-card>
          <div style="text-align: center; padding: 40px 0;">
            <h3>No Patients Found</h3>
            <p style="color: var(--color-text-muted);">You have no patients in the system yet.</p>
          </div>
        </x-card>
      @endif

      <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary" style="margin-top: 30px;">← Back to
        Dashboard</a>
    </div>
  </div>

  <!-- Add Patient Modal -->
  @include('modals.add-patient')

  <!-- Edit Patient Modal -->
  @include('modals.edit-patient')

  <!-- Delete Patient Confirmation Modal -->
  @include('modals.delete-patient-confirm')

@endsection