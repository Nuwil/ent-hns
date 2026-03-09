<!-- Add Visit Modal -->
<div id="add-visit-modal-overlay" class="modal-overlay">
  <div id="add-visit-modal" class="modal">
    <!-- Modal Header -->
    <div class="modal-header">
      <h2>Record New Patient Visit</h2>
      <button type="button" class="modal-close">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
      <form id="add-visit-form">
        @csrf

        <!-- Hidden Patient ID -->
        <input type="hidden" id="visit-patient-id" name="patient_id" value="{{ isset($patient) ? $patient->id : '' }}">
        <!-- Hidden User Role -->
        <input type="hidden" id="visit-user-role" value="{{ isset($user) ? $user->role : 'viewer' }}">

        <!-- Row: Visit Date and Visit Type -->
        <div class="form-grid">
          <div class="form-group">
            <label for="visit-date">Visit Date <span class="required">*</span></label>
            <input 
              type="date" 
              id="visit-date" 
              name="visit_date" 
              required
              value="{{ date('Y-m-d') }}"
            >
          </div>

          <div class="form-group">
            <label for="visit-type">Visit Type <span class="required">*</span></label>
            <select id="visit-type" name="visit_type" required>
              <option value="">-- Select Type --</option>
              <option value="Consultation">Consultation</option>
              <option value="Follow-up">Follow-up</option>
              <option value="Emergency">Emergency</option>
              <option value="Routine">Routine Checkup</option>
              <option value="Procedure">Procedure Follow-up</option>
            </select>
          </div>
        </div>

        <!-- ENT Classification -->
        <div class="form-group">
          <label for="visit-ent-type">ENT Classification <span class="required">*</span></label>
          <select id="visit-ent-type" name="ent_type" required>
            <option value="">-- Select Area --</option>
            <option value="ear">Ears</option>
            <option value="nose">Nose</option>
            <option value="throat">Throat</option>
            <option value="head_neck_tumor">Head & Neck</option>
            <option value="lifestyle_medicine">Lifestyle</option>
            <option value="misc">Others / Miscellaneous</option>
          </select>
        </div>

        <!-- Chief Complaint -->
        <div class="form-group">
          <label for="visit-complaint">Chief Complaint <span class="required">*</span></label>
          <textarea 
            id="visit-complaint" 
            name="chief_complaint" 
            placeholder="Describe the patient's main complaint..."
            required
          ></textarea>
        </div>

        <!-- History of Present Illness (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-history-group">
          <label for="visit-history">History of Present Illness</label>
          <textarea 
            id="visit-history" 
            name="history" 
            placeholder="Relevant patient history..."
          ></textarea>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
        </div>

        <!-- Physical Examination (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-exam-group">
          <label for="visit-exam">Physical Examination Findings</label>
          <textarea 
            id="visit-exam" 
            name="physical_exam" 
            placeholder="Examination findings..."
          ></textarea>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
        </div>

        <!-- Diagnosis (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-diagnosis-group">
          <label for="visit-diagnosis">Diagnosis <span class="required">*</span></label>
          <textarea 
            id="visit-diagnosis" 
            name="diagnosis" 
            placeholder="Clinical diagnosis..."
            required
          ></textarea>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
        </div>

        <!-- Treatment Plan (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-treatment-group">
          <label for="visit-treatment">Treatment / Management Plan</label>
          <textarea 
            id="visit-treatment" 
            name="treatment_plan" 
            placeholder="Recommended treatment plan..."
          ></textarea>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
        </div>

        <!-- Prescription List (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-prescriptions-group">
          <label>Prescription List</label>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
          
          <table id="prescriptions-table" style="width: 100%; margin-top: 10px; border-collapse: collapse;">
            <thead>
              <tr style="background-color: #f0f0f0; border-bottom: 2px solid #ccc;">
                <th style="padding: 10px; text-align: left; font-weight: 600; width: 30%;">Medicine Name</th>
                <th style="padding: 10px; text-align: left; font-weight: 600; width: 40%;">Instruction</th>
                <th style="padding: 10px; text-align: center; font-weight: 600; width: 10%;">Action</th>
              </tr>
            </thead>
            <tbody id="prescriptions-tbody">
              <!-- Prescription rows will be added here dynamically -->
            </tbody>
          </table>
          
          <button 
            type="button" 
            class="btn btn-sm btn-primary" 
            onclick="addPrescriptionRow()" 
            style="margin-top: 10px;"
          >
            + Add Medicine
          </button>
        </div>

        <!-- Vitals Section -->
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
          <h4 style="margin-top: 0; color: #333; font-size: 14px; font-weight: 600; margin-bottom: 15px;">Vital Signs</h4>

          <div class="form-grid">
            <div class="form-group">
              <label for="visit-bp">Blood Pressure (e.g., 120/80)</label>
              <input 
                type="text" 
                id="visit-bp" 
                name="blood_pressure" 
                placeholder="120/80"
              >
            </div>

            <div class="form-group">
              <label for="visit-temp">Temperature (°C)</label>
              <input 
                type="number" 
                id="visit-temp" 
                name="temperature" 
                step="0.1"
                placeholder="37.0"
              >
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="visit-pulse">Pulse Rate (bpm)</label>
              <input 
                type="number" 
                id="visit-pulse" 
                name="pulse_rate" 
                placeholder="72"
              >
            </div>

            <div class="form-group">
              <label for="visit-respiratory">Respiratory Rate</label>
              <input 
                type="number" 
                id="visit-respiratory" 
                name="respiratory_rate" 
                placeholder="16"
              >
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="visit-oxygen">Oxygen Saturation (SpO₂ %)</label>
              <input 
                type="number" 
                id="visit-oxygen" 
                name="oxygen_saturation" 
                min="0" 
                max="100"
                placeholder="98"
              >
            </div>

            <div class="form-group">
              <label for="visit-height">Height (cm)</label>
              <input 
                type="number" 
                id="visit-height" 
                name="height" 
                step="0.1"
                placeholder="170"
              >
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="visit-weight">Weight (kg)</label>
              <input 
                type="number" 
                id="visit-weight" 
                name="weight" 
                step="0.1"
                placeholder="70"
              >
            </div>

            <div style="margin-top: 30px;">
              <span id="visit-bmi-display" style="font-size: 12px; color: #666;"></span>
            </div>
          </div>
        </div>

        <!-- Doctor's Notes (Doctor Only) -->
        <div class="form-group visit-medical-field" id="visit-notes-group">
          <label for="visit-notes">Doctor's Notes</label>
          <textarea 
            id="visit-notes" 
            name="notes" 
            placeholder="Additional clinical notes..."
          ></textarea>
          <small style="color: #999; margin-top: 4px; display: block;">Doctors only</small>
        </div>
      </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="ModalManager.close('add-visit-modal')">
        Cancel
      </button>
      <button type="button" class="btn btn-success" onclick="submitAddVisitForm()">
        Save Visit Record
      </button>
    </div>
  </div>
</div>

<script>
// Role-based field visibility and requirements
function initializeVisitFormRoles() {
  const userRole = document.getElementById('visit-user-role').value;
  const medicalFields = document.querySelectorAll('.visit-medical-field');
  const diagnosisInput = document.getElementById('visit-diagnosis');

  if (userRole === 'secretary') {
    // Hide medical fields for secretaries
    medicalFields.forEach(field => {
      field.style.display = 'none';
    });
    // Remove required attribute
    diagnosisInput.removeAttribute('required');
  } else if (userRole === 'doctor') {
    // Show all fields for doctors
    medicalFields.forEach(field => {
      field.style.display = 'block';
    });
    // Set required attribute
    diagnosisInput.setAttribute('required', 'required');
  }
}

// Calculate BMI helper function
function calculateVisitBMI(height, weight) {
  if (!height || !weight) return null;
  const heightInMeters = height / 100;
  return (weight / (heightInMeters * heightInMeters)).toFixed(2);
}

// Calculate BMI on height/weight change
document.addEventListener('DOMContentLoaded', function() {
  // Initialize role-based visibility
  initializeVisitFormRoles();

  const heightInput = document.getElementById('visit-height');
  const weightInput = document.getElementById('visit-weight');
  const bmiDisplay = document.getElementById('visit-bmi-display');

  const calculateAndDisplay = () => {
    const bmi = calculateVisitBMI(heightInput.value, weightInput.value);
    if (bmi) {
      bmiDisplay.innerHTML = `<strong>BMI: ${bmi}</strong>`;
    } else {
      bmiDisplay.innerHTML = '';
    }
  };

  heightInput.addEventListener('change', calculateAndDisplay);
  heightInput.addEventListener('input', calculateAndDisplay);
  weightInput.addEventListener('change', calculateAndDisplay);
  weightInput.addEventListener('input', calculateAndDisplay);
});

// Prescription management functions
let medicinesList = []; // Will be populated from server
let prescriptionRowCounter = 0;

// Initialize medicines list from server
async function initializeMedicinesList() {
  try {
    const response = await ApiManager.request('GET', '/api/medicines?limit=1000');
    if (response.success && response.data && response.data.medicines) {
      medicinesList = response.data.medicines;
    }
  } catch (error) {
    console.error('Failed to load medicines:', error);
  }
}

function addPrescriptionRow() {
  const tbody = document.getElementById('prescriptions-tbody');
  const rowId = 'prescription-row-' + (++prescriptionRowCounter);
  
  const row = document.createElement('tr');
  row.id = rowId;
  row.style.borderBottom = '1px solid #eee';
  
  const medicineOptions = medicinesList
    .map(m => `<option value="${m.id}" data-name="${m.name}">${m.name} (${m.dosage} ${m.unit})</option>`)
    .join('');
  
  row.innerHTML = `
    <td style="padding: 10px;">
      <select 
        class="prescription-medicine-select" 
        data-row-id="${rowId}"
        style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
      >
        <option value="">-- Select Medicine --</option>
        ${medicineOptions}
      </select>
    </td>
    <td style="padding: 10px;">
      <input 
        type="text" 
        class="prescription-instruction" 
        placeholder="e.g., 1 tablet twice daily for 5 days"
        style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;"
      />
    </td>
    <td style="padding: 10px; text-align: center;">
      <button 
        type="button" 
        class="btn btn-sm btn-danger"
        onclick="removePrescriptionRow('${rowId}')"
        style="padding: 4px 8px; font-size: 12px;"
      >
        Remove
      </button>
    </td>
  `;
  
  tbody.appendChild(row);
}

function removePrescriptionRow(rowId) {
  const row = document.getElementById(rowId);
  if (row) {
    row.remove();
  }
}

function getPrescriptionData() {
  const tbody = document.getElementById('prescriptions-tbody');
  const rows = tbody.querySelectorAll('tr');
  const prescriptions = [];
  
  rows.forEach((row, index) => {
    const medicineSelect = row.querySelector('.prescription-medicine-select');
    const instructionInput = row.querySelector('.prescription-instruction');
    
    const medicineId = medicineSelect.value;
    const instruction = instructionInput.value.trim();
    
    if (medicineId && instruction) {
      const selectedOption = medicineSelect.selectedOptions[0];
      const medicineName = selectedOption.getAttribute('data-name');
      
      prescriptions.push({
        medicine_id: medicineId,
        medicine_name: medicineName,
        instruction: instruction
      });
    }
  });
  
  return prescriptions;
}

function submitAddVisitForm() {
  const form = document.getElementById('add-visit-form');
  const patientId = document.getElementById('visit-patient-id').value;
  const userRole = document.getElementById('visit-user-role').value;

  // Validate required fields
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Role-based validation: Secretaries cannot submit medical fields
  if (userRole === 'secretary') {
    // Clear all doctor-only field values for secretaries before submission
    document.getElementById('visit-history').value = '';
    document.getElementById('visit-exam').value = '';
    document.getElementById('visit-diagnosis').value = '';
    document.getElementById('visit-treatment').value = '';
    document.getElementById('visit-prescription').value = '';
    document.getElementById('visit-notes').value = '';
  }

  // Clear previous errors
  FormValidator.clearFormErrors(form);

  // Get form data
  const formData = new FormData(form);
  
  // Get prescription items
  const prescriptions = getPrescriptionData();
  
  const data = {
    patient_id: patientId,
    user_role: userRole,
    visit_date: formData.get('visit_date'),
    visit_type: formData.get('visit_type'),
    ent_type: formData.get('ent_type'),
    chief_complaint: formData.get('chief_complaint'),
    history: formData.get('history'),
    physical_exam: formData.get('physical_exam'),
    diagnosis: formData.get('diagnosis'),
    treatment_plan: formData.get('treatment_plan'),
    prescription: formData.get('prescription'),
    blood_pressure: formData.get('blood_pressure'),
    temperature: formData.get('temperature'),
    pulse_rate: formData.get('pulse_rate'),
    respiratory_rate: formData.get('respiratory_rate'),
    oxygen_saturation: formData.get('oxygen_saturation'),
    height: formData.get('height'),
    weight: formData.get('weight'),
    notes: formData.get('notes'),
    prescriptions: prescriptions
  };

  // Set loading state
  setFormLoading(form, true);

  // Submit via API
  ApiManager.request('POST', '/api/visits', data)
    .then(response => {
        if (response.success) {
        Notification.success('Visit record saved successfully!');
        ModalManager.close('add-visit-modal');
        form.reset();
        // TODO: replace full page reload with targeted UI refresh when needed
        // e.g. refresh visits list via API or emit an event. Keeping page stable now.
      }
    })
    .catch(error => {
      setFormLoading(form, false);
      
      if (error.status === 422 && error.data.errors) {
        displayServerValidationErrors(error.data.errors, form);
        scrollToFirstError(form);
      } else {
        const errorMessage = error.data?.error || error.data?.message || 'Failed to save visit record';
        Notification.error(errorMessage);
      }
    });
}

// Initialize the modal when needed
document.addEventListener('DOMContentLoaded', function() {
  // Load medicines list
  initializeMedicinesList();
});
</script>
