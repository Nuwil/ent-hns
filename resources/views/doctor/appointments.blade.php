@extends('layout')

@section('content')
<div class="doctor-appointments">
  <div class="page-wrapper">
    <div class="flex-between" style="margin-bottom: 30px;">
      <h1>My Appointments</h1>
      <button type="button" class="btn btn-primary" onclick="ModalManager.open('add-appointment-modal')">Book Appointment</button>
    </div>

    {{-- Calendar Component --}}
    @include('partials.appointments_calendar')

    {{-- Pass calendar data to JavaScript --}}
    <script>
      window.appointmentsData = @json($calendarAppointments ?? []);
    </script>
    
    <x-card style="margin-bottom: 30px;">
      <form method="GET" action="{{ route('doctor.appointments') }}" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0;">
          <label for="status" class="form-label">Filter by Status:</label>
          <select name="status" id="status" class="form-control" style="width: 200px;">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
      </form>
    </x-card>

    @if($appointments->count() > 0)
      <div class="card" style="overflow-x: auto;">
        <x-table :headers="['ID', 'Patient', 'Date & Time', 'Type', 'Duration', 'Status', 'Actions']" striped="true">
          @foreach($appointments as $appointment)
            <tr>
              <td>{{ $appointment->id }}</td>
              <td>
                <a href="{{ route('doctor.patient-profile', $appointment->patient_id) }}" style="color: var(--color-primary); text-decoration: none;">
                  {{ $appointment->patient?->first_name ?? 'N/A' }} {{ $appointment->patient?->last_name ?? '' }}
                </a>
              </td>
              <td>{{ $appointment->appointment_date ?? 'N/A' }}</td>
              <td>{{ $appointment->type ?? 'General' }}</td>
              <td>{{ $appointment->duration ?? '30 min' }}</td>
              <td>
                <x-badge :type="$appointment->status === 'completed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : 'danger'))">
                  {{ ucfirst($appointment->status ?? 'pending') }}
                </x-badge>
                @if(strtolower($appointment->status) === 'pending')
                  <button 
                    type="button" 
                    class="btn btn-sm btn-success"
                    onclick="acceptAppointment({{ $appointment->id }}, {{ $appointment->patient_id }})"
                    style="margin-top: 5px; padding: 4px 8px; font-size: 12px;"
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
                    title="Edit appointment"
                  >
                    Edit
                  </button>
                  <button 
                    type="button" 
                    class="btn btn-sm btn-danger"
                    onclick="confirmDeleteAppointment({{ $appointment->id }})"
                    title="Delete appointment"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </x-table>
      </div>

      <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $appointments->links() }}
      </div>
    @else
      <x-card>
        <div style="text-align: center; padding: 40px 0;">
          <h3>No Appointments Found</h3>
          <p style="color: var(--color-text-muted);">You have no appointments in your schedule.</p>
        </div>
      </x-card>
    @endif

    <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary" style="margin-top: 30px;">← Back to Dashboard</a>
  </div>
</div>

<style>
.doctor-appointments {
  background-color: #f5f5f5;
  min-height: 100vh;
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

    // Fetch appointments for doctor
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
          notes: apt.notes || ''
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
          window.location.href = `/doctor/patients/${patientId}/profile?openAddVisitModal=true`;
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
