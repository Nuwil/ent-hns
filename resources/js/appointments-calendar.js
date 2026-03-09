/**
 * Appointments Calendar Component
 * Handles calendar view, month navigation, and appointment display
 */
const AppointmentsCalendar = (function() {
  let currentDate = new Date();
  let appointments = [];
  let appointmentsByDate = {};

  /**
   * Initialize calendar with appointments data
   */
  function init(data) {
    appointments = data || [];
    buildAppointmentIndex();
    render();
    attachEventListeners();
  }

  /**
   * Build index of appointments by date for quick lookup
   */
  function buildAppointmentIndex() {
    appointmentsByDate = {};
    appointments.forEach(apt => {
      const dateKey = formatDateKey(apt.appointment_date);
      if (!appointmentsByDate[dateKey]) {
        appointmentsByDate[dateKey] = [];
      }
      appointmentsByDate[dateKey].push(apt);
    });
  }

  /**
   * Format date to YYYY-MM-DD key
   */
  function formatDateKey(dateString) {
    const date = new Date(dateString);
    return date.getFullYear() + '-' +
           String(date.getMonth() + 1).padStart(2, '0') + '-' +
           String(date.getDate()).padStart(2, '0');
  }

  /**
   * Get formatted month-year string
   */
  function getMonthYearString(date) {
    const options = { year: 'numeric', month: 'long' };
    return date.toLocaleDateString('en-US', options);
  }

  /**
   * Get first day of month and total days
   */
  function getMonthInfo(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    return { firstDay, daysInMonth, daysInPrevMonth, year, month };
  }

  /**
   * Render calendar grid
   */
  function render() {
    const calendarGrid = document.getElementById('calendarGrid');
    if (!calendarGrid) return;

    calendarGrid.innerHTML = '';
    
    // Update month-year display
    document.getElementById('calendarMonthYear').textContent = getMonthYearString(currentDate);

    // Render weekday headers
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(day => {
      const weekdayEl = document.createElement('div');
      weekdayEl.className = 'calendar-weekday';
      weekdayEl.textContent = day;
      calendarGrid.appendChild(weekdayEl);
    });

    const monthInfo = getMonthInfo(currentDate);
    const today = new Date();
    const todayKey = formatDateKey(today);

    // Render days from previous month
    for (let i = monthInfo.firstDay - 1; i >= 0; i--) {
      const dayNum = monthInfo.daysInPrevMonth - i;
      const dayEl = createDayElement(dayNum, true, null, null);
      calendarGrid.appendChild(dayEl);
    }

    // Render days of current month
    for (let day = 1; day <= monthInfo.daysInMonth; day++) {
      const date = new Date(monthInfo.year, monthInfo.month, day);
      const dateKey = formatDateKey(date);
      const isToday = dateKey === todayKey;
      const appts = appointmentsByDate[dateKey] || [];
      const dayEl = createDayElement(day, false, appts, isToday);
      calendarGrid.appendChild(dayEl);
    }

    // Render days from next month
    const totalCells = calendarGrid.children.length - 7; // Subtract weekday headers
    const remainingCells = 42 - totalCells; // 6 rows × 7 days
    for (let day = 1; day <= remainingCells; day++) {
      const dayEl = createDayElement(day, true, null, null);
      calendarGrid.appendChild(dayEl);
    }
  }

  /**
   * Create a single day element
   */
  function createDayElement(dayNum, isOtherMonth, appointments, isToday) {
    const dayEl = document.createElement('div');
    dayEl.className = 'calendar-day';
    
    if (isOtherMonth) {
      dayEl.classList.add('other-month');
    }
    
    if (isToday) {
      dayEl.classList.add('today');
    }

    // Day number
    const dayNumberEl = document.createElement('div');
    dayNumberEl.className = 'calendar-day-number';
    dayNumberEl.textContent = dayNum;
    dayEl.appendChild(dayNumberEl);

    // Appointments list
    if (appointments && appointments.length > 0) {
      dayEl.classList.add('has-appointments');
      
      const appointmentsEl = document.createElement('div');
      appointmentsEl.className = 'calendar-appointments';
      
      appointments.forEach(apt => {
        const aptEl = document.createElement('div');
        aptEl.className = 'appointment-item';
        aptEl.style.cursor = 'pointer';
        
        const patientName = apt.patient_name || 'Unknown Patient';
        const appointmentType = apt.appointment_type || 'General';
        
        aptEl.innerHTML = `
          <span class="appointment-patient">${patientName}</span>
          <span class="appointment-type">${appointmentType}</span>
        `;
        
        aptEl.addEventListener('click', (e) => {
          e.stopPropagation();
          showAppointmentDetail(apt);
        });
        
        appointmentsEl.appendChild(aptEl);
      });
      
      dayEl.appendChild(appointmentsEl);
    }

    return dayEl;
  }



  /**
   * Show appointment detail modal
   */
  function showAppointmentDetail(appointment) {
    const modal = document.getElementById('appointmentDetailModal');
    const body = document.getElementById('appointmentDetailBody');
    
    if (!modal || !body) return;

    const statusClass = `status-${appointment.status.toLowerCase().replace(' ', '-')}`;
    const formattedDate = new Date(appointment.appointment_date).toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    body.innerHTML = `
      <div class="detail-row">
        <div class="detail-label">Patient</div>
        <div class="detail-value">${appointment.patient_name || 'Unknown'}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Date</div>
        <div class="detail-value">${formattedDate}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Appointment Type</div>
        <div class="detail-value">${appointment.appointment_type || 'General'}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Duration</div>
        <div class="detail-value">${appointment.duration || 'N/A'} minutes</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
          <span class="status-badge ${statusClass}">${appointment.status}</span>
        </div>
      </div>
      ${appointment.notes ? `
      <div class="detail-row">
        <div class="detail-label">Notes</div>
        <div class="detail-value" style="white-space: pre-wrap;">${escapeHtml(appointment.notes)}</div>
      </div>
      ` : ''}
      <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
        ${appointment.status.toLowerCase() === 'pending' ? `
          <button type="button" class="btn btn-primary" onclick="updateAppointmentStatus(${appointment.id}, 'accepted')" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            Accept Appointment
          </button>
          <button type="button" class="btn btn-danger" onclick="updateAppointmentStatus(${appointment.id}, 'declined')" style="width: 100%; padding: 10px;">
            Decline Appointment
          </button>
        ` : `
          <button type="button" class="btn btn-secondary" style="width: 100%; padding: 10px;" disabled>
            ${appointment.status} (Unable to modify)
          </button>
        `}
      </div>
    `;

    modal.classList.add('active');
  }

  /**
   * Hide appointment detail modal
   */
  function hideAppointmentDetail() {
    const modal = document.getElementById('appointmentDetailModal');
    if (modal) {
      modal.classList.remove('active');
    }
  }

  /**
   * Update appointment status
   */
  function updateAppointmentStatus(appointmentId, newStatus) {
    if (!confirm(`Are you sure you want to ${newStatus} this appointment?`)) {
      return;
    }

    fetch(`/api/appointments/${appointmentId}`, {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Notification.success(`Appointment ${newStatus}`);
        hideAppointmentDetail();
        // Refresh appointments list
        fetchAppointmentsForMonth();
      } else {
        Notification.error(data.error || 'Failed to update appointment');
      }
    })
    .catch(error => {
      console.error('Error updating appointment status:', error);
      Notification.error('An error occurred while updating the appointment');
    });
  }

  /**
   * Escape HTML special characters
   */
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Attach event listeners
   */
  function attachEventListeners() {
    // Previous month button
    const prevBtn = document.getElementById('calendarPrevBtn');
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        render();
        fetchAppointmentsForMonth();
      });
    }

    // Next month button
    const nextBtn = document.getElementById('calendarNextBtn');
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        render();
        fetchAppointmentsForMonth();
      });
    }

    // Today button
    const todayBtn = document.getElementById('calendarTodayBtn');
    if (todayBtn) {
      todayBtn.addEventListener('click', () => {
        currentDate = new Date();
        render();
        fetchAppointmentsForMonth();
      });
    }

    // Detail modal close button
    const closeBtn = document.getElementById('appointmentDetailClose');
    if (closeBtn) {
      closeBtn.addEventListener('click', hideAppointmentDetail);
    }

    // Close modal when clicking outside
    const modal = document.getElementById('appointmentDetailModal');
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          hideAppointmentDetail();
        }
      });
    }

    // Close modal with ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        hideAppointmentDetail();
      }
    });
  }

  /**
   * Update appointments and refresh calendar
   */
  function updateAppointments(newAppointments) {
    appointments = newAppointments || [];
    buildAppointmentIndex();
    render();
  }

  /**
   * Fetch appointments for the current month
   */
  function fetchAppointmentsForMonth() {
    const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    const start = startDate.toISOString().split('T')[0];
    const end = endDate.toISOString().split('T')[0];

    fetch(`/api/appointments?start=${start}&end=${end}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin'
    })
    .then(response => {
      // Log response status and headers for debugging
      console.log('API Response Status:', response.status, response.statusText);
      
      // Check if response is 401 or other error
      if (!response.ok) {
        throw new Error(`API Error: ${response.status} ${response.statusText}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success && data.data.appointments) {
        const appointments = data.data.appointments.map(apt => ({
          id: apt.id,
          appointment_date: apt.appointment_date.split(' ')[0], // Extract date part
          patient_name: apt.patient?.first_name + ' ' + apt.patient?.last_name,
          appointment_type: apt.type || apt.appointment_type || 'General',
          duration: apt.duration,
          status: apt.status,
          notes: apt.notes || '',
          doctor_name: apt.doctor?.full_name || 'N/A'
        }));

        updateAppointments(appointments);
      } else {
        console.error('API response missing expected data:', data);
      }
    })
    .catch(error => {
      console.error('Error fetching appointments for month:', error);
    });
  }

  // API
  return {
    init: init,
    updateAppointments: updateAppointments,
    updateAppointmentStatus: updateAppointmentStatus,
    getCurrentDate: () => currentDate,
    getAppointments: () => appointments
  };
})();

// Expose to global scope for Blade templates
if (typeof window !== 'undefined') {
  window.AppointmentsCalendar = AppointmentsCalendar;
  window.updateAppointmentStatus = AppointmentsCalendar.updateAppointmentStatus;
}

export default AppointmentsCalendar;
