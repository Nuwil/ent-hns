@extends('layout')

@section('content')
<div class="secretary-appointments">
  <div class="page-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 style="margin: 0;">Appointments Management</h2>
      <button type="button" class="btn btn-primary" onclick="ModalManager.open('add-appointment-modal')" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Book Appointment</button>
    </div>
    
    {{-- Calendar Component --}}
    @include('partials.appointments_calendar')
    
    {{-- Pass calendar data to JavaScript --}}
    <script>
      window.appointmentsData = @json($calendarAppointments ?? []);
    </script>
    
    <div class="card" style="margin-bottom:20px;">
      <form method="GET" action="{{ route('secretary.appointments') }}" style="display:flex;gap:10px;align-items:center;">
        <label for="status" style="font-weight:bold;">Filter by Status:</label>
        <select name="status" id="status" style="padding:10px;border:1px solid #ddd;border-radius:4px;">
          <option value="">All Statuses</option>
          <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="confirmed" {{ $filterStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
          <option value="completed" {{ $filterStatus === 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="cancelled" {{ $filterStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" style="padding:10px 20px;background-color:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;">Filter</button>
        @if($filterStatus)
          <a href="{{ route('secretary.appointments') }}" style="padding:10px 20px;background-color:#6c757d;color:white;border-radius:4px;text-decoration:none;">Clear</a>
        @endif
      </form>
    </div>

    @if($appointments->count() > 0)
      <div class="table-responsive">
        <table class="settings-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Patient</th>
              <th>Doctor</th>
              <th>Date & Time</th>
              <th>Type</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($appointments as $appointment)
              <tr>
                <td>{{ $appointment->id }}</td>
                <td>
                  <a href="{{ route('secretary.patient-profile', $appointment->patient_id) }}" style="color:#007bff;text-decoration:none;">
                    {{ $appointment->patient?->first_name ?? 'N/A' }} {{ $appointment->patient?->last_name ?? '' }}
                  </a>
                </td>
                <td>{{ $appointment->doctor?->full_name ?? 'N/A' }}</td>
                <td>{{ $appointment->appointment_date ?? 'N/A' }}</td>
                <td>{{ $appointment->type ?? 'General' }}</td>
                <td>
                  <span style="padding:5px 10px;border-radius:4px;background-color:{{ $appointment->status === 'completed' ? '#28a745' : ($appointment->status === 'pending' ? '#ffc107' : ($appointment->status === 'confirmed' ? '#17a2b8' : '#dc3545')) }};color:white;font-size:12px;">
                    {{ ucfirst($appointment->status ?? 'pending') }}
                  </span>
                  @if(strtolower($appointment->status) === 'pending')
                    <button 
                      type="button"
                      onclick="acceptAppointment({{ $appointment->id }}, {{ $appointment->patient_id }})"
                      style="margin-top: 5px; padding: 5px 10px; font-size: 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;"
                      title="Accept appointment"
                    >
                      Accept
                    </button>
                  @endif
                </td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button 
                      type="button" 
                      class="btn btn-sm btn-info"
                      onclick="openEditAppointmentModal({{ $appointment->id }}, '{{ $appointment->appointment_date }}', '{{ $appointment->type }}', '{{ $appointment->duration }}')"
                      style="padding: 5px 10px; font-size: 12px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;"
                      title="Edit appointment"
                    >
                      Edit
                    </button>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-danger"
                      onclick="confirmDeleteAppointment({{ $appointment->id }})"
                      style="padding: 5px 10px; font-size: 12px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;"
                      title="Delete appointment"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="margin-top:20px;display:flex;justify-content:center;">
        {{ $appointments->links() }}
      </div>
    @else
      <div class="card" style="text-align:center;padding:40px;">
        <h3>No Appointments Found</h3>
        <p>There are no appointments{{ $filterStatus ? ' with status: ' . $filterStatus : '' }} in the system.</p>
      </div>
    @endif

    <a href="{{ route('secretary.dashboard') }}" style="display:inline-block;margin-top:20px;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;">Back to Dashboard</a>
  </div>
</div>

<style>
.secretary-appointments {
  background-color: #f5f5f5;
  min-height: 100vh;
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


/* replicate admin settings card/table styles */
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

<script>
  // Fetch appointments data and initialize calendar
  document.addEventListener('DOMContentLoaded', function() {
    // Check if calendar component is loaded
    if (typeof AppointmentsCalendar === 'undefined') {
      console.warn('AppointmentsCalendar is not loaded');
      return;
    }

    // Fetch appointments from the current month
    const today = new Date();
    const startDate = new Date(today.getFullYear(), today.getMonth(), 1);
    const endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const start = startDate.toISOString().split('T')[0];
    const end = endDate.toISOString().split('T')[0];

    // Fetch all appointments for secretary (not filtered by doctor)
    fetch(`/api/appointments?start=${start}&end=${end}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.data.appointments) {
        const appointments = data.data.appointments.map(apt => ({
          id: apt.id,
          appointment_date: apt.appointment_date,
          patient_name: apt.patient?.first_name + ' ' + apt.patient?.last_name,
          appointment_type: apt.type || apt.appointment_type || 'General',
          duration: apt.duration,
          status: apt.status,
          notes: apt.notes || '',
          doctor_name: apt.doctor?.full_name || 'N/A'
        }));

        window.appointmentsData = appointments;
        AppointmentsCalendar.init(appointments);
      }
    })
    .catch(error => {
      console.error('Error fetching appointments:', error);
    });
  });

  // Edit appointment function
  function openEditAppointmentModal(appointmentId, appointmentDate, appointmentType, duration) {
    // Parse the date to get separate date and time
    const dateObj = new Date(appointmentDate);
    const date = dateObj.toISOString().split('T')[0];
    const time = appointmentDate.split(' ')[1] || '09:00'; // Extract time
    
    // Store appointment ID for later use
    window.editingAppointmentId = appointmentId;
    
    // Open edit modal and populate fields
    ModalManager.open('edit-appointment-modal');
    
    // Set form values
    document.getElementById('edit-appointment-id').value = appointmentId;
    document.getElementById('edit-appointment-date').value = date;
    document.getElementById('edit-appointment-time').value = time;
    document.getElementById('edit-appointment-type').value = appointmentType;
    document.getElementById('edit-appointment-duration').value = duration;
  }

  // Confirm delete appointment
  function confirmDeleteAppointment(appointmentId) {
    const message = 'Are you sure you want to delete this appointment? This action cannot be undone.';
    
    if (confirm(message)) {
      deleteAppointment(appointmentId);
    }
  }

  // Delete appointment function
  function deleteAppointment(appointmentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/api/appointments/${appointmentId}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Notification.success('Appointment deleted successfully!');
        // Refresh the page to show updated list
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        Notification.error(data.error || 'Failed to delete appointment');
      }
    })
    .catch(error => {
      console.error('Error deleting appointment:', error);
      Notification.error('Failed to delete appointment');
    });
  }

  // Submit edit appointment form
  function submitEditAppointmentForm() {
    const appointmentId = document.getElementById('edit-appointment-id').value;
    const date = document.getElementById('edit-appointment-date').value;
    const time = document.getElementById('edit-appointment-time').value;
    const type = document.getElementById('edit-appointment-type').value;
    const duration = document.getElementById('edit-appointment-duration').value;
    const status = document.getElementById('edit-appointment-status').value;
    
    const appointmentDateTime = `${date} ${time}`;
    
    const data = {
      appointment_date: appointmentDateTime,
      appointment_type: type,
      duration: duration,
      status: status
    };
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/api/appointments/${appointmentId}`, {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin',
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Notification.success('Appointment updated successfully!');
        ModalManager.close('edit-appointment-modal');
        // Refresh the page
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        if (data.errors) {
          const errors = Object.values(data.errors).flat();
          Notification.error(errors.join(', '));
        } else {
          Notification.error(data.error || 'Failed to update appointment');
        }
      }
    })
    .catch(error => {
      console.error('Error updating appointment:', error);
      Notification.error('Failed to update appointment');
    });
  }

  // Accept appointment function
  function acceptAppointment(appointmentId, patientId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const data = {
      status: 'Accepted'
    };
    
    fetch(`/api/appointments/${appointmentId}`, {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin',
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Notification.success('Appointment accepted! Redirecting to patient profile...');
        // Redirect to patient profile with modal open indicator
        setTimeout(() => {
          window.location.href = `/secretary/patients/${patientId}/profile?openAddVisitModal=true`;
        }, 1500);
      } else {
        Notification.error(data.error || 'Failed to accept appointment');
      }
    })
    .catch(error => {
      console.error('Error accepting appointment:', error);
      Notification.error('Failed to accept appointment');
    });
  }
</script>

<!-- Include Modals -->
@include('modals.add-appointment')
@include('modals.edit-appointment')

@endsection
