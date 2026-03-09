<!-- Edit Patient Profile Modal -->
<div id="edit-patient-modal-overlay" class="modal-overlay">
  <div id="edit-patient-modal" class="modal">
    <!-- Modal Header -->
    <div class="modal-header">
      <h2>Edit Patient Profile</h2>
      <button type="button" class="modal-close" onclick="closeModal('edit-patient-modal-overlay')">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
      <form id="edit-patient-form">
        @csrf
        <input type="hidden" name="patient_id" id="edit-patient-id" value="{{ isset($patient) ? $patient->id : '' }}">

        <!-- Personal Information Section -->
        <h4 style="color: #333; font-weight: 600; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Personal Information</h4>

        <!-- Row: First Name and Last Name -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-first-name">First Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-first-name" 
              name="first_name" 
              required
              value="{{ isset($patient) ? $patient->first_name : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-last-name">Last Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-last-name" 
              name="last_name" 
              required
              value="{{ isset($patient) ? $patient->last_name : '' }}"
            >
          </div>
        </div>

        <!-- Row: Gender and Date of Birth -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-gender">Gender <span class="required">*</span></label>
            <select id="edit-gender" name="gender" required>
              <option value="">-- Select --</option>
              <option value="Male" {{ isset($patient) && strtolower($patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ isset($patient) && strtolower($patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
              <option value="Other" {{ isset($patient) && strtolower($patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="edit-dob">Date of Birth <span class="required">*</span></label>
            <input 
              type="date" 
              id="edit-dob" 
              name="date_of_birth" 
              required
              value="{{ isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '' }}"
            >
          </div>
        </div>

        <!-- Contact Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Contact Information</h4>

        <!-- Row: Email and Phone -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-email">Email Address <span class="required">*</span></label>
            <input 
              type="email" 
              id="edit-email" 
              name="email" 
              required
              value="{{ isset($patient) ? $patient->email : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-phone">Phone Number <span class="required">*</span></label>
            <input 
              type="tel" 
              id="edit-phone" 
              name="phone" 
              required
              value="{{ isset($patient) ? $patient->phone : '' }}"
            >
          </div>
        </div>

        <!-- Occupation -->
        <div class="form-group">
          <label for="edit-occupation">Occupation</label>
          <input 
            type="text" 
            id="edit-occupation" 
            name="occupation"
            value="{{ isset($patient) ? $patient->occupation : '' }}"
          >
        </div>

        <!-- Address Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Address</h4>

        <!-- Street Address -->
        <div class="form-group">
          <label for="edit-address">Street Address <span class="required">*</span></label>
          <input 
            type="text" 
            id="edit-address" 
            name="address" 
            required
            value="{{ isset($patient) ? $patient->address : '' }}"
          >
        </div>

        <!-- Row: City, State, Country -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-city">City <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-city" 
              name="city" 
              required
              value="{{ isset($patient) ? $patient->city : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-state">State/Province <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-state" 
              name="state" 
              required
              value="{{ isset($patient) ? $patient->state : '' }}"
            >
          </div>
        </div>

        <!-- Row: Country and Postal Code -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-country">Country <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-country" 
              name="country" 
              required
              value="{{ isset($patient) ? $patient->country : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-postal">Postal Code</label>
            <input 
              type="text" 
              id="edit-postal" 
              name="postal_code"
              value="{{ isset($patient) ? $patient->postal_code : '' }}"
            >
          </div>
        </div>

        <!-- Health Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Health Information</h4>

        <!-- Row: Height and Weight -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-height">Height (cm) <span class="required">*</span></label>
            <input 
              type="number" 
              id="edit-height" 
              name="height" 
              step="0.1"
              required
              value="{{ isset($patient) ? $patient->height : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-weight">Weight (kg) <span class="required">*</span></label>
            <input 
              type="number" 
              id="edit-weight" 
              name="weight" 
              step="0.1"
              required
              value="{{ isset($patient) ? $patient->weight : '' }}"
            >
          </div>
        </div>

        <!-- BMI Display -->
        <div style="padding: 10px 12px; background-color: #e7f3ff; border-radius: 4px; margin-bottom: 20px; color: #0066cc; font-weight: 600;">
          BMI: <span id="edit-bmi-value">--</span>
        </div>

        <!-- Allergies -->
        <div class="form-group">
          <label for="edit-allergies">Allergies</label>
          <textarea 
            id="edit-allergies" 
            name="allergies"
            placeholder="List any known allergies..."
          >{{ isset($patient) ? $patient->allergies : '' }}</textarea>
        </div>

        <!-- Vaccine History -->
        <div class="form-group">
          <label for="edit-vaccines">Vaccine History</label>
          <textarea 
            id="edit-vaccines" 
            name="vaccine_history"
            placeholder="Enter vaccine history..."
          >{{ isset($patient) ? $patient->vaccine_history : '' }}</textarea>
        </div>

        <!-- Emergency Contact Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Emergency Contact Information</h4>

        <div class="form-group">
          <label for="edit-emergency-name">Emergency Contact Name <span class="required">*</span></label>
          <input 
            type="text" 
            id="edit-emergency-name" 
            name="emergency_contact_name" 
            required
            value="{{ isset($patient) ? $patient->emergency_contact_name : '' }}"
          >
        </div>

        <!-- Row: Relationship and Phone -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-emergency-relationship">Relationship <span class="required">*</span></label>
            <input 
              type="text" 
              id="edit-emergency-relationship" 
              name="emergency_contact_relationship" 
              placeholder="e.g., Spouse, Parent, Sibling"
              required
              value="{{ isset($patient) ? $patient->emergency_contact_relationship : '' }}"
            >
          </div>

          <div class="form-group">
            <label for="edit-emergency-phone">Emergency Contact Phone <span class="required">*</span></label>
            <input 
              type="tel" 
              id="edit-emergency-phone" 
              name="emergency_contact_phone" 
              required
              value="{{ isset($patient) ? $patient->emergency_contact_phone : '' }}"
            >
          </div>
        </div>
      </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="ModalManager.close('edit-patient-modal')">
        Cancel
      </button>
      <button type="button" class="btn btn-warning" onclick="submitEditPatientForm()">
        Save Changes
      </button>
    </div>
  </div>
</div>

<script>
// Calculate BMI helper function
function calculateEditBMI(height, weight) {
  if (!height || !weight) return null;
  const heightInMeters = height / 100;
  return (weight / (heightInMeters * heightInMeters)).toFixed(2);
}

// Calculate BMI on height/weight change
document.addEventListener('DOMContentLoaded', function() {
  const heightInput = document.getElementById('edit-height');
  const weightInput = document.getElementById('edit-weight');
  const bmiDisplay = document.getElementById('edit-bmi-value');

  const calculateAndDisplay = () => {
    const bmi = calculateEditBMI(heightInput.value, weightInput.value);
    bmiDisplay.textContent = bmi ? bmi : '--';
  };

  // Initial calculation
  calculateAndDisplay();

  heightInput.addEventListener('change', calculateAndDisplay);
  heightInput.addEventListener('input', calculateAndDisplay);
  weightInput.addEventListener('change', calculateAndDisplay);
  weightInput.addEventListener('input', calculateAndDisplay);
});

function submitEditPatientForm() {
  const form = document.getElementById('edit-patient-form');
  const patientId = document.getElementById('edit-patient-id').value;

  // Validate form
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Clear previous errors
  FormValidator.clearFormErrors(form);

  // Get form data
  const formData = new FormData(form);
  const data = {
    first_name: formData.get('first_name'),
    last_name: formData.get('last_name'),
    gender: formData.get('gender'),
    date_of_birth: formData.get('date_of_birth'),
    email: formData.get('email'),
    phone: formData.get('phone'),
    occupation: formData.get('occupation'),
    address: formData.get('address'),
    city: formData.get('city'),
    state: formData.get('state'),
    country: formData.get('country'),
    postal_code: formData.get('postal_code'),
    height: formData.get('height'),
    weight: formData.get('weight'),
    allergies: formData.get('allergies'),
    vaccine_history: formData.get('vaccine_history'),
    emergency_contact_name: formData.get('emergency_contact_name'),
    emergency_contact_relationship: formData.get('emergency_contact_relationship'),
    emergency_contact_phone: formData.get('emergency_contact_phone')
  };

  // Set loading state
  setFormLoading(form, true);

  // Submit via API
  ApiManager.request('PUT', `/api/patients/${patientId}`, data)
    .then(response => {
        if (response.success) {
        Notification.success('Patient profile updated successfully!');
        ModalManager.close('edit-patient-modal');
        form.reset();
        // TODO: update patient display area dynamically instead of reloading
      }
    })
    .catch(error => {
      setFormLoading(form, false);
      
      if (error.status === 422 && error.data.errors) {
        displayServerValidationErrors(error.data.errors, form);
        scrollToFirstError(form);
      } else {
        const errorMessage = error.data?.error || error.data?.message || 'Failed to update patient profile';
        Notification.error(errorMessage);
      }
    });
}
</script>
