/* ===== MODAL MANAGEMENT ===== */

// Modal Manager Object
const ModalManager = {
  // Open a modal by ID
  open(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(`${modalId}-overlay`);
    
    if (modal && overlay) {
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      // Prevent sidebar/main content from disappearing
      document.body.style.visibility = 'visible';
      document.body.style.display = 'block';
    }
  },

  // Close a modal by ID
  close(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(`${modalId}-overlay`);
    
    if (modal && overlay) {
      overlay.classList.remove('active');
      document.body.style.overflow = 'auto';
      document.body.style.visibility = 'visible';
      document.body.style.display = 'block';
    }
  },

  // Close all modals
  closeAll() {
    document.querySelectorAll('.modal-overlay.active').forEach(overlay => {
      overlay.classList.remove('active');
    });
    document.body.style.overflow = 'auto';
    document.body.style.visibility = 'visible';
    document.body.style.display = 'block';
  }
};

// Event Listeners for Modal Interactions
document.addEventListener('DOMContentLoaded', function() {
  // Handle data-open-modal button clicks
  document.querySelectorAll('[data-open-modal]').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const modalId = this.getAttribute('data-open-modal');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        document.body.style.visibility = 'visible';
      }
    });
  });

  // Handle data-close-modal button clicks
  document.querySelectorAll('[data-close-modal]').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const modal = this.closest('.modal');
      if (modal) {
        modal.setAttribute('hidden', '');
        document.body.style.overflow = 'auto';
        document.body.style.visibility = 'visible';
        const form = modal.querySelector('form');
        if (form) form.reset();
      }
    });
  });

  // Handle modal backdrop clicks (close modal)
  document.querySelectorAll('[data-close-modal-backdrop]').forEach(backdrop => {
    backdrop.addEventListener('click', function(e) {
      const modal = this.closest('.modal');
      if (modal) {
        modal.setAttribute('hidden', '');
        document.body.style.overflow = 'auto';
        document.body.style.visibility = 'visible';
        const form = modal.querySelector('form');
        if (form) form.reset();
      }
    });
  });

  // Close modal when clicking close button
  document.querySelectorAll('.modal-close').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const modal = this.closest('.modal');
      if (modal) {
        // Try the overlay method first for modals with overlays
        const overlay = modal.closest('.modal-overlay');
        if (overlay) {
          const modalId = overlay.id.replace('-overlay', '');
          ModalManager.close(modalId);
        } else {
          // Fallback for simple modal structure with hidden attribute
          modal.setAttribute('hidden', '');
          document.body.style.overflow = 'auto';
          document.body.style.visibility = 'visible';
          const form = modal.querySelector('form');
          if (form) form.reset();
        }
      }
    });
  });

  // Close modal when clicking overlay background
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === this) {
        const modalId = this.id.replace('-overlay', '');
        ModalManager.close(modalId);
      }
    });
  });

  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      ModalManager.closeAll();
      // Also close any modals with hidden attribute
      document.querySelectorAll('.modal:not([hidden])').forEach(modal => {
        modal.setAttribute('hidden', '');
        document.body.style.overflow = 'auto';
      });
    }
  });
});

// ===== FORM VALIDATION =====

const FormValidator = {
  // Validate required fields
  validateRequired(value, fieldName) {
    if (!value || value.trim() === '') {
      return `${fieldName} is required`;
    }
    return null;
  },

  // Validate email format
  validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return 'Please enter a valid email address';
    }
    return null;
  },

  // Validate phone format
  validatePhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
    if (!phoneRegex.test(phone)) {
      return 'Please enter a valid phone number';
    }
    return null;
  },

  // Validate date
  validateDate(date) {
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) {
      return 'Please enter a valid date';
    }
    return null;
  },

  // Validate future date
  validateFutureDate(date, fieldName = 'Date') {
    const error = this.validateDate(date);
    if (error) return error;
    
    const dateObj = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (dateObj < today) {
      return `${fieldName} must be in the future`;
    }
    return null;
  },

  // Validate numeric value
  validateNumeric(value, fieldName, min = 0, max = null) {
    const num = parseFloat(value);
    if (isNaN(num)) {
      return `${fieldName} must be a number`;
    }
    if (num < min) {
      return `${fieldName} must be at least ${min}`;
    }
    if (max && num > max) {
      return `${fieldName} must not exceed ${max}`;
    }
    return null;
  },

  // Display error on form group
  showError(input, message) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    // Remove existing error
    const existingError = formGroup.querySelector('.form-error');
    if (existingError) {
      existingError.remove();
    }

    // Add error state
    formGroup.classList.add('error');

    // Display error message
    if (message) {
      const errorEl = document.createElement('div');
      errorEl.className = 'form-error';
      errorEl.textContent = message;
      formGroup.appendChild(errorEl);
    }
  },

  // Clear error on form group
  clearError(input) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    formGroup.classList.remove('error');
    const errorEl = formGroup.querySelector('.form-error');
    if (errorEl) {
      errorEl.remove();
    }
  },

  // Clear all errors on a form
  clearFormErrors(formElement) {
    formElement.querySelectorAll('.form-group.error').forEach(group => {
      group.classList.remove('error');
      const errorEl = group.querySelector('.form-error');
      if (errorEl) errorEl.remove();
    });
  }
};

// ===== API COMMUNICATION =====

const ApiManager = {
  // Make API request
  async request(method, url, data = null) {
    const options = {
      method: method,
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    };

    if (data) {
      options.body = JSON.stringify(data);
    }

    try {
      const response = await fetch(url, options);
      const responseData = await response.json();

      if (!response.ok) {
        throw {
          status: response.status,
          data: responseData
        };
      }

      return responseData;
    } catch (error) {
      if (error.status) {
        throw error;
      }
      throw {
        status: 500,
        data: { error: error.message || 'Network error' }
      };
    }
  }
};

// ===== NOTIFICATION SYSTEM =====

const Notification = {
  show(message, type = 'success', duration = 3000) {
    const container = document.getElementById('notification-container');
    if (!container) {
      console.warn('Notification container not found');
      return;
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
      <div class="notification-content">
        <span>${message}</span>
        <button class="notification-close">&times;</button>
      </div>
    `;

    container.appendChild(notification);

    // Auto close
    const timer = setTimeout(() => {
      notification.remove();
    }, duration);

    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
      clearTimeout(timer);
      notification.remove();
    });
  },

  success(message, duration = 3000) {
    this.show(message, 'success', duration);
  },

  error(message, duration = 4000) {
    this.show(message, 'error', duration);
  },

  warning(message, duration = 3500) {
    this.show(message, 'warning', duration);
  }
};

// ===== HELPER FUNCTIONS =====

// Calculate BMI
function calculateBMI(height, weight) {
  if (!height || !weight) return null;
  const heightInMeters = height / 100;
  return (weight / (heightInMeters * heightInMeters)).toFixed(2);
}

// Format date to YYYY-MM-DD
function formatDateForInput(date) {
  if (!date) return '';
  const d = new Date(date);
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${d.getFullYear()}-${month}-${day}`;
}

// Get today's date
function getTodayDate() {
  return formatDateForInput(new Date());
}

// Set form loading state
function setFormLoading(form, isLoading = true) {
  const submitButton = form.querySelector('button[type="submit"]');
  if (!submitButton) return;

  if (isLoading) {
    submitButton.disabled = true;
    submitButton.classList.add('loading');
    submitButton.dataset.originalText = submitButton.textContent;
    submitButton.textContent = 'Saving...';
  } else {
    submitButton.disabled = false;
    submitButton.classList.remove('loading');
    submitButton.textContent = submitButton.dataset.originalText || 'Save';
  }
}

// Display server validation errors
function displayServerValidationErrors(errors, form) {
  Object.keys(errors).forEach(fieldName => {
    const input = form.querySelector(`[name="${fieldName}"]`);
    if (input) {
      const message = Array.isArray(errors[fieldName]) 
        ? errors[fieldName][0] 
        : errors[fieldName];
      FormValidator.showError(input, message);
    }
  });
}

// Scroll to first error
function scrollToFirstError(form) {
  const firstError = form.querySelector('.form-group.error');
  if (firstError) {
    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}
