/**
 * Patient Modal Functions
 * Handles opening edit patient modal with data population
 */

function openEditPatientModal(patientId) {
    // Fetch patient data
    fetch(`/api/patients/${patientId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const patient = data.data;
            populateEditPatientForm(patient);
            openModal('edit-patient-modal');
        } else {
            Notification.error('Failed to load patient data');
        }
    })
    .catch(error => {
        console.error('Error loading patient:', error);
        Notification.error('An error occurred while loading patient data');
    });
}

function populateEditPatientForm(patient) {
    // Personal Information
    document.getElementById('edit-patient-id').value = patient.id || '';
    document.getElementById('edit-first-name').value = patient.first_name || '';
    document.getElementById('edit-last-name').value = patient.last_name || '';
    document.getElementById('edit-gender').value = patient.gender || '';
    
    // Format date_of_birth
    if (patient.date_of_birth) {
        const dateObj = new Date(patient.date_of_birth);
        const formattedDate = dateObj.toISOString().split('T')[0];
        document.getElementById('edit-dob').value = formattedDate;
    }

    // Contact Information
    document.getElementById('edit-email').value = patient.email || '';
    document.getElementById('edit-phone').value = patient.phone || '';
    document.getElementById('edit-occupation').value = patient.occupation || '';

    // Address Information
    document.getElementById('edit-address').value = patient.address || '';
    document.getElementById('edit-city').value = patient.city || '';
    document.getElementById('edit-state').value = patient.state || '';
    document.getElementById('edit-country').value = patient.country || '';
    document.getElementById('edit-postal').value = patient.postal_code || '';

    // Health Information
    document.getElementById('edit-height').value = patient.height || '';
    document.getElementById('edit-weight').value = patient.weight || '';
    document.getElementById('edit-allergies').value = patient.allergies || '';
    document.getElementById('edit-vaccine-history').value = patient.vaccine_history || '';

    // Emergency Contact Information (if fields exist)
    const emergencyNameField = document.getElementById('edit-emergency-contact-name');
    const emergencyRelationField = document.getElementById('edit-emergency-contact-relationship');
    const emergencyPhoneField = document.getElementById('edit-emergency-contact-phone');

    if (emergencyNameField) {
        emergencyNameField.value = patient.emergency_contact_name || '';
    }
    if (emergencyRelationField) {
        emergencyRelationField.value = patient.emergency_contact_relationship || '';
    }
    if (emergencyPhoneField) {
        emergencyPhoneField.value = patient.emergency_contact_phone || '';
    }

    // Medical information (if fields exist)
    const medicalHistoryField = document.getElementById('edit-medical-history');
    const currentMedicationsField = document.getElementById('edit-current-medications');

    if (medicalHistoryField) {
        medicalHistoryField.value = patient.medical_history || '';
    }
    if (currentMedicationsField) {
        currentMedicationsField.value = patient.current_medications || '';
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        if (modal.classList) {
            modal.classList.add('active');
        } else {
            modal.style.display = 'flex';
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        if (modal.classList) {
            modal.classList.remove('active');
        } else {
            modal.style.display = 'none';
        }
    }
}

// Make functions available globally
if (typeof window !== 'undefined') {
    window.openEditPatientModal = openEditPatientModal;
    window.populateEditPatientForm = populateEditPatientForm;
    window.openModal = openModal;
    window.closeModal = closeModal;
}

export { openEditPatientModal, populateEditPatientForm, openModal, closeModal };
