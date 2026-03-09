<!-- Edit Appointment Modal -->
<div id="edit-appointment-modal-overlay" class="modal-overlay">
  <div id="edit-appointment-modal" class="modal">
    <!-- Modal Header -->
    <div class="modal-header">
      <h2>Edit Appointment</h2>
      <button type="button" class="modal-close">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
      <form id="edit-appointment-form">
        @csrf

        <!-- Hidden Appointment ID -->
        <input type="hidden" id="edit-appointment-id" name="appointment_id">

        <!-- Row: Date and Time -->
        <div class="form-grid">
          <div class="form-group">
            <label for="edit-appointment-date">Appointment Date <span class="required">*</span></label>
            <input 
              type="date" 
              id="edit-appointment-date" 
              name="appointment_date" 
              required
            >
          </div>

          <div class="form-group">
            <label for="edit-appointment-time">Time <span class="required">*</span></label>
            <input 
              type="time" 
              id="edit-appointment-time" 
              name="appointment_time" 
              required
            >
          </div>
        </div>

        <!-- Appointment Type -->
        <div class="form-group">
          <label for="edit-appointment-type">Appointment Type <span class="required">*</span></label>
          <input 
            type="text" 
            id="edit-appointment-type" 
            name="appointment_type" 
            placeholder="e.g., Consultation, Follow-up"
            required
          >
        </div>

        <!-- Duration -->
        <div class="form-group">
          <label for="edit-appointment-duration">Duration (minutes)</label>
          <input 
            type="number" 
            id="edit-appointment-duration" 
            name="duration" 
            placeholder="30"
            min="15"
            step="15"
          >
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="edit-appointment-status">Status <span class="required">*</span></label>
          <select id="edit-appointment-status" name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Pending">Pending</option>
            <option value="Accepted">Accepted</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
            <option value="No-Show">No-Show</option>
          </select>
        </div>
      </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="ModalManager.close('edit-appointment-modal')">
        Cancel
      </button>
      <button type="button" class="btn btn-success" onclick="submitEditAppointmentForm()">
        Save Changes
      </button>
    </div>
  </div>
</div>
