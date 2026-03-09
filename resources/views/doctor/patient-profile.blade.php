@extends('layout')

@section('content')
<div class="doctor-patient-profile">
  <div class="page-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 style="margin: 0;">Patient Profile</h2>
      <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-primary" onclick="ModalManager.open('add-appointment-modal')" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Add Appointment</button>
        <button type="button" class="btn btn-success" onclick="ModalManager.open('add-visit-modal')" style="padding: 8px 16px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Add Visit</button>
        <button type="button" class="btn btn-warning" onclick="openEditPatientModal({{ $patient->id }})" style="padding: 8px 16px; background-color: #ffc107; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Edit Profile</button>
      </div>
    </div>
    
    <div class="card" style="margin-bottom:20px;">
      <h3>Patient Information</h3>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:20px;">
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
        <div>
          <strong>Address:</strong>
          <p>{{ $patient->address ?? 'N/A' }}</p>
        </div>
        <div>
          <strong>Medical Record Number:</strong>
          <p>{{ $patient->id ?? 'N/A' }}</p>
        </div>
      </div>
      <p style="color:#28a745;font-size:12px;">
        <strong>Note:</strong> As doctor, you have full access to this patient's records including edit permissions.
      </p>
    </div>

    @if($appointments->count() > 0)
      <div class="card" style="margin-bottom:20px;">
        <h3>Your Appointments with Patient ({{ $appointments->count() }})</h3>
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background-color:#f0f0f0;border-bottom:2px solid #ddd;">
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Date</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Type</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Notes</th>
              <th style="padding:10px;text-align:left;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($appointments as $appointment)
              <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:10px;border-right:1px solid #ddd;">{{ $appointment->appointment_date ?? 'N/A' }}</td>
                <td style="padding:10px;border-right:1px solid #ddd;">{{ $appointment->type ?? 'General' }}</td>
                <td style="padding:10px;border-right:1px solid #ddd;">{{ substr($appointment->notes ?? '', 0, 30) }}{{ strlen($appointment->notes ?? '') > 30 ? '...' : '' }}</td>
                <td style="padding:10px;">
                  <span style="padding:5px 10px;border-radius:4px;background-color:{{ $appointment->status === 'completed' ? '#28a745' : ($appointment->status === 'pending' ? '#ffc107' : '#dc3545') }};color:white;font-size:12px;">
                    {{ ucfirst($appointment->status ?? 'pending') }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <div class="card" style="margin-bottom:20px;">
      <h3>Visit Timeline ({{ $visits->count() }})</h3>
        <table style="width:100%;border-collapse:collapse;" class="visit-timeline-table">
          <thead>
            <tr style="background-color:#f0f0f0;border-bottom:2px solid #ddd;">
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Visit Date</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Visit Type</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">ENT Area</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Chief Complaint</th>
              <th style="padding:10px;text-align:left;border-right:1px solid #ddd;">Diagnosis</th>
              <th style="padding:10px;text-align:left;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($visits->sortByDesc('visit_date') as $visit)
              <tr style="border-bottom:1px solid #ddd;" class="visit-row" data-visit-id="{{ $visit->id }}">
                <td style="padding:10px;border-right:1px solid #ddd;">{{ $visit->visit_date?->format('M d, Y @ H:i') ?? 'N/A' }}</td>
                <td style="padding:10px;border-right:1px solid #ddd;">
                  <span style="background-color:#e7f3ff;color:#0066cc;padding:4px 8px;border-radius:3px;font-size:12px;">
                    {{ $visit->visit_type ?? 'Standard' }}
                  </span>
                </td>
                <td style="padding:10px;border-right:1px solid #ddd;">
                  <span style="background-color:#fff3e0;color:#e65100;padding:4px 8px;border-radius:3px;font-size:12px;">
                    {{ ucfirst(str_replace('_', ' ', $visit->ent_type ?? 'ear')) }}
                  </span>
                </td>
                <td style="padding:10px;border-right:1px solid #ddd;">{{ substr($visit->chief_complaint ?? '', 0, 35) }}{{ strlen($visit->chief_complaint ?? '') > 35 ? '...' : '' }}</td>
                <td style="padding:10px;border-right:1px solid #ddd;">{{ substr($visit->diagnosis ?? '', 0, 35) }}{{ strlen($visit->diagnosis ?? '') > 35 ? '...' : '' }}</td>
                <td style="padding:10px;">
                  <button type="button" class="btn-view-details" onclick="toggleVisitDetails(event, {{ $visit->id }})" style="padding:5px 12px;background-color:#17a2b8;color:white;border:none;border-radius:3px;cursor:pointer;font-size:12px;">
                    View Details
                  </button>
                </td>
              </tr>
              <tr class="visit-details-row" id="details-{{ $visit->id }}" style="display:none;border-bottom:1px solid #ddd;">
                <td colspan="6" style="padding:30px;background-color:#f9f9f9;width:100rem;">
                  <div class="visit-details-container">
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:30px;margin-bottom:30px;">
                      <!-- Visit Information -->
                      <div>
                        <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">Visit Information</h4>
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
                        <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">ENT Classification</h4>
                        <div class="detail-row">
                          <strong>Primary Area:</strong>
                          <span>{{ ucfirst(str_replace('_', ' ', $visit->ent_type ?? 'ear')) }}</span>
                        </div>
                        <div class="detail-row">
                          <strong>Classification Type:</strong>
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

                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:30px;margin-bottom:30px;">
                      <!-- Clinical Details -->
                      <div>
                        <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">Clinical Details</h4>
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

                      <!-- Vitals & Measurements -->
                      <div>
                        <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">Vitals & Measurements</h4>
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
                      <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">Doctor's Notes</h4>
                      <div style="background-color:#fff;padding:12px;border-radius:4px;border-left:4px solid #17a2b8;white-space:pre-wrap;line-height:1.6;color:#555;">
                        {{ $visit->notes ?? 'No notes available' }}
                      </div>
                    </div>

                    @if($visit->vitals_notes)
                      <div>
                        <h4 style="margin-top:0;color:#333;border-bottom:2px solid #ddd;padding-bottom:10px;font-size:14px;">Additional Vitals Notes</h4>
                        <div style="background-color:#fff;padding:12px;border-radius:4px;border-left:4px solid #ffc107;white-space:pre-wrap;line-height:1.6;color:#555;">
                          {{ $visit->vitals_notes }}
                        </div>
                      </div>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center;padding:30px;color:#999;">No visit records found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- <div class="card" style="margin-bottom:20px;">
      <h3>Actions</h3>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button style="padding:10px 15px;background-color:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;">Schedule Appointment</button>
        <button style="padding:10px 15px;background-color:#17a2b8;color:white;border:none;border-radius:4px;cursor:pointer;">Record Visit</button>
        <button style="padding:10px 15px;background-color:#ffc107;color:black;border:none;border-radius:4px;cursor:pointer;">Add Prescription</button>
        <button style="padding:10px 15px;background-color:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">Edit Notes</button>
      </div>
    </div> -->

    <a href="{{ route('doctor.patients') }}" style="display:inline-block;padding:10px 15px;background-color:#6c757d;color:white;text-decoration:none;border-radius:4px;">Back to My Patients</a>
  </div>
</div>

<style>
.doctor-patient-profile {
  background-color: #f5f5f5;
  min-height: 100vh;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card h3 {
  margin-top: 0;
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

/* Visit Timeline Styles */
.visit-timeline-table tbody tr.visit-row:hover {
  background-color: #f0f7ff !important;
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
  border-bottom: 1px solid #ddd;
  align-items: baseline;
  gap: 15px;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row strong {
  color: #333;
  min-width: 200px;
  font-weight: 600;
}

.detail-row span {
  color: #555;
  flex: 1;
}

.btn-view-details {
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-view-details:hover {
  background-color: #138496 !important;
  transform: translateY(-2px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
  .visit-timeline-table {
    font-size: 12px;
  }

  .visit-timeline-table th,
  .visit-timeline-table td {
    padding: 8px 6px !important;
  }

  .visit-timeline-table th {
    border-right: none !important;
  }

  .visit-timeline-table td {
    border-right: none !important;
  }

  .visit-details-container > div {
    grid-template-columns: 1fr !important;
    gap: 15px !important;
  }

  .detail-row {
    flex-direction: column;
    align-items: flex-start;
    padding: 8px 0;
  }

  .detail-row strong {
    min-width: auto;
  }

  .btn-view-details {
    width: 100%;
    padding: 8px 5px !important;
  }
}

@media (max-width: 480px) {
  .visit-timeline-table {
    font-size: 11px;
  }

  .visit-timeline-table th:nth-child(3),
  .visit-timeline-table th:nth-child(4),
  .visit-timeline-table th:nth-child(5),
  .visit-timeline-table td:nth-child(3),
  .visit-timeline-table td:nth-child(4),
  .visit-timeline-table td:nth-child(5) {
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
// Pre-populate patient data for doctors
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
