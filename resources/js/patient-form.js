// Handle patient form AJAX submission
document.addEventListener('DOMContentLoaded', function() {
  const patientForm = document.getElementById('patientForm');
  if (!patientForm) return;

  patientForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear existing errors
    FormValidator.clearFormErrors(patientForm);

    // Gather form data
    const data = {};
    new FormData(patientForm).forEach((value, key) => {
      data[key] = value;
    });

    // Determine API endpoint based on current URL
    let apiEndpoint = '/api/patients';
    
    setFormLoading(patientForm, true);

    ApiManager.request('POST', apiEndpoint, data)
      .then(function(res) {
        setFormLoading(patientForm, false);
        if (res.success && res.data) {
          Notification.success('Patient created successfully!');
          
          // Close modal
          const modal = patientForm.closest('.modal');
          if (modal) {
            modal.setAttribute('hidden', '');
            document.body.style.overflow = 'auto';
            patientForm.reset();
          }

          // Show notification with link to profile
          const viewUrl = window.location.pathname.startsWith('/doctor')
            ? '/doctor/patients/' + res.data.id + '/profile'
            : '/secretary/patients/' + res.data.id + '/profile';
          
          Notification.show(`<a href="${viewUrl}" style="color:#fff;text-decoration:underline;">View Profile</a>`, 'success', 5000);
        }
      })
      .catch(function(err) {
        setFormLoading(patientForm, false);
        if (err.status === 422 && err.data.errors) {
          displayServerValidationErrors(err.data.errors, patientForm);
          scrollToFirstError(patientForm);
        } else {
          Notification.error(err.data?.error || 'Error creating patient');
        }
      });
  });

  // Initialize locations manager
  if (typeof LocationsManager !== 'undefined') {
    LocationsManager.init();
  }
});
