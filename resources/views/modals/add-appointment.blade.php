<!-- Add Appointment Modal -->
<div id="add-appointment-modal-overlay" class="modal-overlay">
    <div id="add-appointment-modal" class="modal">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2>Schedule New Appointment</h2>
            <button type="button" class="modal-close">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <form id="add-appointment-form">
                @csrf

                <!-- Hidden Patient ID -->
                <input type="hidden" id="appointment-patient-id" name="patient_id"
                    value="{{ isset($patient) ? $patient->id : '' }}">

                <!-- Row: Patient Selection and Date -->
                <div class="form-grid-two">
                    <!-- Patient Selection -->
                    @if(isset($patient))
                    <div class="form-group">
                        <label for="appointment-patient-name">Patient Name <span class="required">*</span></label>
                        <input type="text" id="appointment-patient-name" name="patient_name" readonly
                            class="field-readonly"
                            value="{{ isset($patient) ? $patient->first_name . ' ' . $patient->last_name : '' }}">
                    </div>
                    @else
                    <div class="form-group">
                        <label for="appointment-patient-select">Select Patient <span class="required">*</span></label>
                        <select id="appointment-patient-select" name="patient_select" class="form-control select2-patient" required>
                            <option value="">-- Search and Select Patient --</option>
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="appointment-date">Appointment Date & Time <span class="required">*</span></label>
                        <input type="datetime-local" id="appointment-date" name="appointment_date" class="form-control" required>
                    </div>
                </div>

                <!-- Appointment Type -->
                <div class="form-grid-two">
                    <div class="form-group">
                        <label for="appointment-type">Appointment Type <span class="required">*</span></label>
                        <select id="appointment-type" name="appointment_type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="Consultation">Consultation</option>
                            <option value="Procedure">Procedure</option>
                            <option value="Follow-up">Follow-up</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Initial Assessment">Initial Assessment</option>
                        </select>
                    </div>
                    <!-- Doctor Assignment -->
                    <div class="form-group">
                        <label for="appointment-doctor">Assigned Doctor <span class="required">*</span></label>
                        <select id="appointment-doctor" name="doctor_id" class="form-control select2-doctor" required>
                            <option value="">-- Search and Select Doctor --</option>
                        </select>
                    </div>
                </div>

                <!-- Chief Complaint -->
                <div class="form-grid-two">
                    <div class="form-group">
                        <label for="appointment-complaint">Chief Complaint <span class="required">*</span></label>
                        <textarea id="appointment-complaint" name="chief_complaint" class="form-control"
                            placeholder="Describe the reason for this appointment..." required></textarea>
                    </div>
                    <!-- Additional Notes -->
                    <div class="form-group">
                        <label for="appointment-notes">Additional Notes</label>
                        <textarea id="appointment-notes" name="notes" class="form-control"
                            placeholder="Any additional information..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="ModalManager.close('add-appointment-modal')">
                Cancel
            </button>
            <button type="button" class="btn btn-primary" onclick="submitAddAppointmentForm()">
                Schedule Appointment
            </button>
        </div>
    </div>
</div>

<script>
// Initialize Select2 when modal opens
function initializeAppointmentModal() {
    const patientSelect = document.getElementById('appointment-patient-select');
    const patientIdInput = document.getElementById('appointment-patient-id');
    const doctorSelect = document.getElementById('appointment-doctor');
    
    // Initialize Patient Select2
    if (patientSelect && !patientSelect.classList.contains('select2-hidden-accessible')) {
        $(patientSelect).select2({
            placeholder: 'Type patient name to search...',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: '/api/patients',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    if (data.success && data.data.patients) {
                        return {
                            results: data.data.patients.map(patient => ({
                                id: patient.id,
                                text: (patient.first_name || '') + ' ' + (patient.last_name || '')
                            })),
                            pagination: {
                                more: (params.page || 1) < (data.data.pages || 1)
                            }
                        };
                    }
                    return { results: [] };
                }
            }
        });
        
        patientSelect.addEventListener('change', function() {
            patientIdInput.value = this.value;
        });
    }
    
    // Initialize Doctor Select2
    if (doctorSelect && !doctorSelect.classList.contains('select2-hidden-accessible')) {
        $(doctorSelect).select2({
            placeholder: 'Click to view available doctors...',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: '/api/appointments/doctors',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                data: function(params) {
                    return {
                        search: params.term || ''
                    };
                },
                processResults: function(data) {
                    if (data.success && data.data && Array.isArray(data.data.doctors)) {
                        return {
                            results: data.data.doctors.map(doctor => ({
                                id: doctor.id,
                                text: doctor.full_name || 'Unknown'
                            }))
                        };
                    }
                    return { results: [] };
                },
                error: function(error) {
                    console.error('Error loading doctors:', error);
                }
            }
        });
    }
}

// Override ModalManager.open to initialize Select2
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ModalManager !== 'undefined') {
        const originalModalOpen = ModalManager.open;
        ModalManager.open = function(modalId) {
            const result = originalModalOpen.call(this, modalId);
            
            if (modalId === 'add-appointment-modal') {
                // Use setTimeout to ensure DOM is ready
                setTimeout(initializeAppointmentModal, 100);
            }
            
            return result;
        };
    }
});

function submitAddAppointmentForm() {
    const form = document.getElementById('add-appointment-form');
    const patientIdInput = document.getElementById('appointment-patient-id');
    const patientSelectInput = document.getElementById('appointment-patient-select');
    
    // Determine patient ID from either hidden input or select
    let patientId = patientIdInput.value;
    if (!patientId && patientSelectInput) {
        patientId = patientSelectInput.value;
    }

    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validate patient selection
    if (!patientId) {
        alert('Please select a patient');
        return;
    }
    
    // Validate doctor selection
    if (!form.doctor_id.value) {
        alert('Please select a doctor');
        return;
    }

    // Clear previous errors
    FormValidator.clearFormErrors(form);

    // Get form data
    const formData = {
        patient_id: patientId,
        appointment_date: form.appointment_date.value.replace('T', ' '),
        appointment_type: form.appointment_type.value,
        doctor_id: form.doctor_id.value,
        chief_complaint: form.chief_complaint.value,
        notes: form.notes.value
    };

    // Set loading state
    setFormLoading(form, true);

    // Submit via API
    ApiManager.request('POST', '/api/appointments', formData)
        .then(response => {
            if (response.success) {
                Notification.success('Appointment scheduled successfully');
                ModalManager.close('add-appointment-modal');
                form.reset();
                // Reset Select2
                $(document.getElementById('appointment-patient-select')).val(null).trigger('change');
                $(document.getElementById('appointment-doctor')).val(null).trigger('change');
                setFormLoading(form, false);
                // Refresh page after short delay
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(error => {
            setFormLoading(form, false);

            if (error.status === 422 && error.data.errors) {
                displayServerValidationErrors(error.data.errors, form);
                scrollToFirstError(form);
            } else {
                const errorMessage = error.data?.error || error.data?.message || 'Failed to schedule appointment';
                Notification.error(errorMessage);
            }
        });
}
</script>