@extends('layout')

@section('content')
<div class="secretary-patient-profile">
  <div class="page-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 style="margin: 0;">Patient Profile</h2>
      <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-primary" onclick="ModalManager.open('add-appointment-modal')" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Add Appointment</button>
        <button type="button" class="btn btn-success" onclick="ModalManager.open('add-visit-modal')" style="padding: 8px 16px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Add Visit</button>
        <button type="button" class="btn btn-warning" onclick="openEditPatientModal({{ $patient->id }})" style="padding: 8px 16px; background-color: #ffc107; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Edit Profile</button>
      </div>
    </div>
    
    <div class="card settings-card" style="margin-bottom:20px;">
      <h3>Patient Information</h3>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
        <div>
          <strong>First Name:</strong>
          <p>{{ $patient->first_name ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Last Name:</strong>
          <p>{{ $patient->last_name ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Email:</strong>
          <p>{{ $patient->email ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Phone:</strong>
          <p>{{ $patient->phone ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Date of Birth:</strong>
          <p>{{ $patient->date_of_birth ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Gender:</strong>
          <p>{{ $patient->gender ?? 'N/A' }}</p>
        </div>
      </div>
      <p style="color:#999;font-size:12px;margin-top:15px;">
        <strong>Note:</strong> As secretary, you have view-only access to patient profiles.
      </p>
    </div>

    @if($appointments->count() > 0)
      <div class="card settings-card" style="margin-bottom:20px;">
        <h3>Appointments ({{ $appointments->count() }})</h3>
        <div class="table-responsive">
          <table class="settings-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Doctor</th>
              <th>Type</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($appointments as $appointment)
              <tr>
                <td>{{ $appointment->appointment_date ?? 'N/A' }}</td>
                <td>{{ $appointment->doctor?->full_name ?? 'N/A' }}</td>
                <td>{{ $appointment->type ?? 'General' }}</td>
                <td>
                  <span style="padding:5px 10px;border-radius:4px;background-color:{{ $appointment->status === 'completed' ? '#28a745' : ($appointment->status === 'pending' ? '#ffc107' : '#dc3545') }};color:white;font-size:12px;">
                    {{ ucfirst($appointment->status ?? 'pending') }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        </div>
      </div>
    @endif

    <div class="card settings-card" style="margin-bottom:20px;">
      <h3>Visit Timeline ({{ $visits->count() }})</h3>
        <div class="table-responsive">
          <table class="settings-table visit-timeline-table">
            <thead>
              <tr>
                <th style="width:15%;">Visit Date</th>
                <th style="width:15%;">Visit Type</th>
                <th style="width:15%;">Doctor</th>
                <th style="width:15%;">ENT Classification</th>
                <th style="width:15%;">Chief Complaint</th>
                <th style="width:15%;">Diagnosis</th>
                <th style="width:10%;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($visits->sortByDesc('visit_date') as $visit)
                <tr class="visit-row" data-visit-id="{{ $visit->id }}">
                  <td>{{ $visit->visit_date?->format('M d, Y @ H:i') ?? 'N/A' }}</td>
                  <td>
                    <span class="badge" style="background-color:#e7f3ff;color:#0066cc;padding:4px 8px;border-radius:3px;font-size:12px;">
                      {{ $visit->visit_type ?? 'Standard Visit' }}
                    </span>
                  </td>
                  <td>{{ $visit->doctor?->full_name ?? $visit->doctor_name ?? 'N/A' }}</td>
                  <td>
                    <span class="badge" style="background-color:#fff3e0;color:#e65100;padding:4px 8px;border-radius:3px;font-size:12px;">
                      {{ ucfirst(str_replace('_', ' ', $visit->ent_type ?? 'ear')) }}
                    </span>
                  </td>
                  <td>{{ substr($visit->chief_complaint ?? '', 0, 40) }}{{ strlen($visit->chief_complaint ?? '') > 40 ? '...' : '' }}</td>
                  <td>{{ substr($visit->diagnosis ?? '', 0, 40) }}{{ strlen($visit->diagnosis ?? '') > 40 ? '...' : '' }}</td>
                  <td>
                    <button type="button" class="btn-view-details" onclick="toggleVisitDetails(event, {{ $visit->id }})" style="padding:5px 10px;background-color:#17a2b8;color:white;border:none;border-radius:3px;cursor:pointer;font-size:12px;">
                      View
                    </button>
                  </td>
                </tr>
                <tr class="visit-details-row" id="details-{{ $visit->id }}" style="display:none;">
                  <td colspan="7" style="padding:20px;background-color:#f9fafb; width:100rem">
                    <div class="visit-details-container">
                      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:20px;">
                        <!-- Visit Information -->
                        <div>
                          <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">Visit Information</h4>
                          <div class="detail-row">
                            <strong>Visit Date:</strong>
                            <span>{{ $visit->visit_date?->format('M d, Y @ H:i') ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Visit Type:</strong>
                            <span>{{ $visit->visit_type ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Assigned Doctor:</strong>
                            <span>{{ $visit->doctor?->full_name ?? $visit->doctor_name ?? 'N/A' }}</span>
                          </div>
                        </div>

                        <!-- ENT Classification -->
                        <div>
                          <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">ENT Classification</h4>
                          <div class="detail-row">
                            <strong>Primary Area:</strong>
                            <span>{{ ucfirst(str_replace('_', ' ', $visit->ent_type ?? 'ear')) }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Classification:</strong>
                            <span>
                              @php
                                $classifications = [
                                  'ear' => 'Ears',
                                  'nose' => 'Nose',
                                  'throat' => 'Throat',
                                  'head_neck_tumor' => 'Head & Neck',
                                  'lifestyle_medicine' => 'Lifestyle',
                                  'misc' => 'Miscellaneous'
                                ];
                              @endphp
                              {{ $classifications[$visit->ent_type] ?? 'N/A' }}
                            </span>
                          </div>
                        </div>
                      </div>

                      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:20px;">
                        <!-- Clinical Details -->
                        <div>
                          <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">Clinical Details</h4>
                          <div class="detail-row">
                            <strong>Chief Complaint:</strong>
                            <span>{{ $visit->chief_complaint ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>History of Present Illness (HPI):</strong>
                            <span>{{ $visit->history ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Physical Examination Findings:</strong>
                            <span>{{ $visit->physical_exam ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Diagnosis:</strong>
                            <span>{{ $visit->diagnosis ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Treatment / Management Plan:</strong>
                            <span>{{ $visit->treatment_plan ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Prescribed Medications:</strong>
                            <span>{{ $visit->prescription ?? 'N/A' }}</span>
                          </div>
                        </div>

                        <!-- Vitals -->
                        <div>
                          <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">Vitals & Measurements</h4>
                          <div class="detail-row">
                            <strong>Blood Pressure:</strong>
                            <span>{{ $visit->blood_pressure ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Temperature (°C):</strong>
                            <span>{{ $visit->temperature ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Pulse Rate (bpm):</strong>
                            <span>{{ $visit->pulse_rate ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Respiratory Rate:</strong>
                            <span>{{ $visit->respiratory_rate ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Oxygen Saturation (SpO₂):</strong>
                            <span>{{ $visit->oxygen_saturation ? $visit->oxygen_saturation . '%' : 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Height (cm):</strong>
                            <span>{{ $visit->height ?? 'N/A' }}</span>
                          </div>
                          <div class="detail-row">
                            <strong>Weight (kg):</strong>
                            <span>{{ $visit->weight ?? 'N/A' }}</span>
                          </div>
                          @if($visit->height && $visit->weight)
                            <div class="detail-row">
                              <strong>BMI:</strong>
                              <span>
                                @php
                                  $height_m = $visit->height / 100;
                                  $bmi = $visit->weight / ($height_m * $height_m);
                                @endphp
                                {{ number_format($bmi, 2) }}
                              </span>
                            </div>
                          @endif
                        </div>
                      </div>

                      <!-- Doctor's Notes -->
                      <div style="margin-bottom:20px;">
                        <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">Doctor's Notes</h4>
                        <div class="detail-row" style="flex-direction:column;align-items:flex-start;">
                          <span style="white-space:pre-wrap;line-height:1.6;">{{ $visit->notes ?? 'No notes available' }}</span>
                        </div>
                      </div>

                      @if($visit->vitals_notes)
                        <div>
                          <h4 style="margin-top:0;color:#374151;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">Additional Vitals Notes</h4>
                          <div class="detail-row" style="flex-direction:column;align-items:flex-start;">
                            <span style="white-space:pre-wrap;line-height:1.6;">{{ $visit->vitals_notes }}</span>
                          </div>
                        </div>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" style="text-align:center;padding:20px;color:#999;">No visit records found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <a href="{{ route('secretary.patients') }}" style="display:inline-block;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;">Back to Patient List</a>
  </div>
</div>

<style>
.secretary-patient-profile {
  background-color: #f5f5f5;
  min-height: 100vh;
}

/* use admin card/table styling */
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

.card h3 {
  margin-top: 0;
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

/* Visit Timeline Styles */
.visit-timeline-table tbody tr.visit-row:hover {
  background-color: #f0f7ff;
}

.visit-row {
  cursor: pointer;
  transition: all 0.2s ease;
}

.visit-details-row.open {
  display: table-row !important;
}

.visit-details-container {
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.detail-row {
  display: flex;
  justify-content: flex-start;
  padding: 10px 0;
  border-bottom: 1px solid #e5e7eb;
  align-items: baseline;
  gap: 15px;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row strong {
  color: #374151;
  min-width: 180px;
  font-weight: 600;
}

.detail-row span {
  color: #1f2937;
  flex: 1;
}

.btn-view-details {
  transition: all 0.2s ease;
}

.btn-view-details:hover {
  background-color: #138496 !important;
  transform: translateY(-2px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
  display: inline-block;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
  .settings-table {
    font-size: 12px;
  }

  .settings-table thead th,
  .settings-table td {
    padding: 8px 10px;
  }

  .visit-details-container > div {
    grid-template-columns: 1fr !important;
  }

  .detail-row {
    flex-direction: column;
    align-items: flex-start;
  }

  .detail-row strong {
    min-width: auto;
    margin-bottom: 5px;
  }

  .btn-view-details {
    width: 100%;
    padding: 8px 5px !important;
  }
}

@media (max-width: 480px) {
  .settings-table {
    font-size: 11px;
  }

  .settings-table th:nth-child(n+4),
  .settings-table td:nth-child(n+4) {
    display: none;
  }

  .btn-view-details {
    padding: 6px 8px !important;
    font-size: 11px !important;
  }
}
</style>

<script>
function toggleVisitDetails(event, visitId) {
  event.stopPropagation();
  const detailsRow = document.getElementById('details-' + visitId);
  
  if (detailsRow) {
    const isHidden = detailsRow.style.display === 'none';
    
    // Hide all other open details
    document.querySelectorAll('.visit-details-row.open').forEach(row => {
      row.style.display = 'none';
      row.classList.remove('open');
    });
    
    // Toggle current details
    if (isHidden) {
      detailsRow.style.display = 'table-row';
      detailsRow.classList.add('open');
    }
  }
}

// Allow clicking on the row to expand details
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.visit-row').forEach(row => {
    row.addEventListener('click', function(e) {
      // Only toggle if not clicking the button
      if (e.target.closest('.btn-view-details')) {
        return;
      }
      const visitId = this.getAttribute('data-visit-id');
      toggleVisitDetails(e, visitId);
    });
  });
});
</script>

<!-- Include Modals -->
@include('modals.add-appointment')
@include('modals.add-visit')
@include('modals.edit-patient')

<script>
// Pre-populate patient data for secretaries
document.addEventListener('DOMContentLoaded', function() {
  // Check if we need to open the add visit modal (from appointment acceptance)
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('openAddVisitModal') === 'true') {
    // Delay to ensure modal manager is initialized
    setTimeout(() => {
      ModalManager.open('add-visit-modal');
      // Remove the query parameter from URL without page reload
      window.history.replaceState({}, document.title, window.location.pathname);
    }, 500);
  }

  // Fetch available doctors
  fetch('/api/appointments/doctors', {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    credentials: 'same-origin'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const doctorSelect = document.getElementById('appointment-doctor');
        data.data.doctors.forEach(doctor => {
          const option = document.createElement('option');
          option.value = doctor.id;
          option.textContent = doctor.full_name || doctor.name;
          doctorSelect.appendChild(option);
        });
      }
    })
    .catch(error => console.error('Error fetching doctors:', error));
});
</script>

@endsection
