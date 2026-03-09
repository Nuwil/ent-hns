@extends('layout')

@section('content')
<div class="secretary-patients">
  <div class="page-wrapper">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h2>Patient List</h2>
      <button type="button" class="btn-add-patient" data-open-modal="addPatientModal">+ Add New Patient</button>
    </div>
    
    <div class="card" style="margin-bottom:20px;">
      <form method="GET" action="{{ route('secretary.patients') }}" style="display:flex;gap:10px;">
        <input 
          type="text" 
          name="search" 
          placeholder="Search by name or email..." 
          value="{{ $search }}"
          style="padding:10px;border:1px solid #ddd;border-radius:4px;flex:1;min-width:200px;"
        >
        <button type="submit" style="padding:10px 20px;background-color:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;">Search</button>
        @if($search)
          <a href="{{ route('secretary.patients') }}" style="padding:10px 20px;background-color:#6c757d;color:white;border-radius:4px;text-decoration:none;display:flex;align-items:center;">Clear</a>
        @endif
      </form>
    </div>

    @if($patients->count() > 0)
      <div class="table-responsive">
        <table class="settings-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($patients as $patient)
              <tr>
                <td>
                  <a href="{{ route('secretary.patient-profile', $patient->id) }}" style="color:#007bff;text-decoration:none;">{{ $patient->id }}</a>
                </td>
                <td>
                  {{ $patient->first_name ?? 'N/A' }} {{ $patient->last_name ?? '' }}
                </td>
                <td>
                  {{ $patient->email ?? 'N/A' }}
                </td>
                <td>
                  {{ $patient->phone ?? 'N/A' }}
                </td>
                <td>
                  <a href="{{ route('secretary.patient-profile', $patient->id) }}" style="color:#007bff;text-decoration:none;margin-right:10px;">View</a>
                  <button type="button" class="btn-action" onclick="openEditPatientModal({{ $patient->id }})" style="background-color:#28a745;color:white;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;margin-right:5px;">Edit</button>
                  <button type="button" class="btn-action" onclick="openDeleteConfirmModal({{ $patient->id }}, '{{ $patient->first_name }} {{ $patient->last_name }}')" style="background-color:#dc3545;color:white;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;">Delete</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="margin-top:20px;display:flex;justify-content:center;">
        {{ $patients->links() }}
      </div>
    @else
      <div class="card" style="text-align:center;padding:40px;">
        <h3>No Patients Found</h3>
        <p>There are no patients in the system{{ $search ? ' matching your search criteria' : '' }}.</p>
      </div>
    @endif

    <a href="{{ route('secretary.dashboard') }}" style="display:inline-block;margin-top:20px;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;">Back to Dashboard</a>
  </div>
</div>

<!-- Add Patient Modal -->
@include('modals.add-patient')

<!-- Edit Patient Modal -->
@include('modals.edit-patient')

<!-- Delete Patient Confirmation Modal -->
@include('modals.delete-patient-confirm')

<style>
.secretary-patients {
  background-color: #f5f5f5;
  min-height: calc(100vh - 4.5rem);
  /* removed margin-left to allow standard layout positioning */
}

.card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-responsive {
  background: white;
  border-radius: 8px;
  overflow-x: auto;
}

table {
  font-size: 14px;
}

th {
  font-weight: bold;
  color: #333;
}

td {
  color: #555;
}

tr:hover {
  background-color: #f9f9f9;
}


/* helper styles copied from admin settings for consistency */
.settings-card,
.card {
  background: white;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  margin-bottom: 24px;
  overflow: hidden;
  transition: box-shadow 0.2s ease;
  padding: 20px;
  width: 100% !important;
}

.card:hover,
.settings-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Table styles taken from admin settings page */
.table-responsive {
  width: 100%;
  overflow-x: auto;
  background: white;
  border-radius: 8px;
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
</style>

<!-- Add Patient Modal -->
@include('modals.add-patient')
@include('modals.edit-patient')
@include('modals.delete-patient-confirm')

@endsection
