<!-- Add Patient Modal -->
<div id="addPatientModal-overlay" class="modal-overlay">
  <div id="addPatientModal" class="modal">
    <!-- Modal Header -->
    <div class="modal-header">
      <h2>Add New Patient</h2>
      <button type="button" class="modal-close" onclick="closeModal('addPatientModal-overlay')">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
      <form id="patientForm">
        @csrf

        <!-- Personal Information Section -->
        <h4 style="color: #333; font-weight: 600; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Personal Information</h4>

        <!-- Row: First Name and Last Name -->
        <div class="form-grid">
          <div class="form-group">
            <label for="first-name">First Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="first-name" 
              name="first_name" 
              required
              placeholder="Enter first name"
            >
          </div>

          <div class="form-group">
            <label for="last-name">Last Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="last-name" 
              name="last_name" 
              required
              placeholder="Enter last name"
            >
          </div>
        </div>

        <!-- Row: Gender and Date of Birth -->
        <div class="form-grid">
          <div class="form-group">
            <label for="gender">Gender <span class="required">*</span></label>
            <select id="gender" name="gender" required>
              <option value="">-- Select --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="dob">Date of Birth <span class="required">*</span></label>
            <input 
              type="date" 
              id="dob" 
              name="date_of_birth" 
              required
            >
          </div>
        </div>

        <!-- Contact Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Contact Information</h4>

        <!-- Row: Email and Phone -->
        <div class="form-grid">
          <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              required
              placeholder="Enter email address"
            >
          </div>

          <div class="form-group">
            <label for="phone">Phone Number <span class="required">*</span></label>
            <input 
              type="tel" 
              id="phone" 
              name="phone" 
              required
              placeholder="Enter phone number"
            >
          </div>
        </div>

        <!-- Occupation -->
        <div class="form-group">
          <label for="occupation">Occupation</label>
          <input 
            type="text" 
            id="occupation" 
            name="occupation"
            placeholder="Enter occupation"
          >
        </div>

        <!-- Address Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Address</h4>

        <!-- Street Address -->
        <div class="form-group">
          <label for="address">Street Address <span class="required">*</span></label>
          <input 
            type="text" 
            id="address" 
            name="address" 
            required
            placeholder="Enter street address"
          >
        </div>

        <!-- Row: City, State, Country -->
        <div class="form-grid">
          <div class="form-group">
            <label for="city">City <span class="required">*</span></label>
            <input 
              type="text" 
              id="city" 
              name="city" 
              required
              placeholder="Enter city"
            >
          </div>

          <div class="form-group">
            <label for="state">State/Province <span class="required">*</span></label>
            <input 
              type="text" 
              id="state" 
              name="state" 
              required
              placeholder="Enter state or province"
            >
          </div>
        </div>

        <!-- Row: Country and Postal Code -->
        <div class="form-grid">
          <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <input 
              type="text" 
              id="country" 
              name="country" 
              required
              placeholder="Enter country"
            >
          </div>

          <div class="form-group">
            <label for="postal">Postal Code</label>
            <input 
              type="text" 
              id="postal" 
              name="postal_code"
              placeholder="Enter postal code"
            >
          </div>
        </div>

        <!-- Health Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Health Information</h4>

        <!-- Row: Height and Weight -->
        <div class="form-grid">
          <div class="form-group">
            <label for="height">Height (cm) <span class="required">*</span></label>
            <input 
              type="number" 
              id="height" 
              name="height" 
              step="0.1"
              required
              placeholder="Enter height in cm"
            >
          </div>

          <div class="form-group">
            <label for="weight">Weight (kg) <span class="required">*</span></label>
            <input 
              type="number" 
              id="weight" 
              name="weight" 
              step="0.1"
              required
              placeholder="Enter weight in kg"
            >
          </div>
        </div>

        <!-- BMI Display -->
        <div style="padding: 10px 12px; background-color: #e7f3ff; border-radius: 4px; margin-bottom: 20px; color: #0066cc; font-weight: 600;">
          BMI: <span id="bmi-value">--</span>
        </div>

        <!-- Allergies -->
        <div class="form-group">
          <label for="allergies">Allergies</label>
          <textarea 
            id="allergies" 
            name="allergies"
            placeholder="List any known allergies..."
            rows="3"
          ></textarea>
        </div>

        <!-- Vaccine History -->
        <div class="form-group">
          <label for="vaccines">Vaccine History</label>
          <textarea 
            id="vaccines" 
            name="vaccine_history"
            placeholder="Enter vaccine history..."
            rows="3"
          ></textarea>
        </div>

        <!-- Emergency Contact Information -->
        <h4 style="color: #333; font-weight: 600; margin-top: 20px; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Emergency Contact Information</h4>

        <div class="form-group">
          <label for="emergency-name">Emergency Contact Name <span class="required">*</span></label>
          <input 
            type="text" 
            id="emergency-name" 
            name="emergency_contact_name" 
            required
            placeholder="Enter emergency contact name"
          >
        </div>

        <!-- Row: Relationship and Phone -->
        <div class="form-grid">
          <div class="form-group">
            <label for="emergency-relationship">Relationship <span class="required">*</span></label>
            <input 
              type="text" 
              id="emergency-relationship" 
              name="emergency_contact_relationship" 
              placeholder="e.g., Spouse, Parent, Sibling"
              required
            >
          </div>

          <div class="form-group">
            <label for="emergency-phone">Emergency Contact Phone <span class="required">*</span></label>
            <input 
              type="tel" 
              id="emergency-phone" 
              name="emergency_contact_phone" 
              required
              placeholder="Enter emergency contact phone"
            >
          </div>
        </div>
      </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="ModalManager.close('addPatientModal')">
        Cancel
      </button>
      <button type="button" class="btn btn-primary" onclick="submitPatientForm()">
        Create Patient
      </button>
    </div>
  </div>
</div>

<script>
// Calculate BMI on height/weight change
document.addEventListener('DOMContentLoaded', function() {
  const heightInput = document.getElementById('height');
  const weightInput = document.getElementById('weight');
  const bmiDisplay = document.getElementById('bmi-value');

  const calculateAndDisplay = () => {
    const bmi = calculateBMI(heightInput.value, weightInput.value);
    bmiDisplay.textContent = bmi ? bmi : '--';
  };

  // Initial calculation
  calculateAndDisplay();

  heightInput.addEventListener('change', calculateAndDisplay);
  heightInput.addEventListener('input', calculateAndDisplay);
  weightInput.addEventListener('change', calculateAndDisplay);
  weightInput.addEventListener('input', calculateAndDisplay);
});

function submitPatientForm() {
  const form = document.getElementById('patientForm');

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
  ApiManager.request('POST', '/api/patients', data)
    .then(response => {
      setFormLoading(form, false);
      if (response.success && response.data) {
        Notification.success('Patient created successfully!');
        ModalManager.close('addPatientModal');
        form.reset();
        
        // Reload the patients page
        window.location.reload();
      }
    })
    .catch(error => {
      setFormLoading(form, false);
      
      if (error.status === 422 && error.data.errors) {
        displayServerValidationErrors(error.data.errors, form);
        scrollToFirstError(form);
      } else {
        const errorMessage = error.data?.error || error.data?.message || 'Failed to create patient';
        Notification.error(errorMessage);
      }
    });
}

// Helper function to calculate BMI
function calculateBMI(height, weight) {
  if (!height || !weight) return null;
  const h = parseFloat(height) / 100; // Convert cm to meters
  const w = parseFloat(weight);
  if (h <= 0 || w <= 0) return null;
  const bmi = (w / (h * h)).toFixed(1);
  return bmi;
}
</script>
