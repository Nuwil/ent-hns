<style>
  /** Calendar Component CSS **/
  .appointments-calendar-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
  }

  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .calendar-nav {
    display: flex;
    gap: 10px;
    align-items: center;
  }

  .calendar-month-year {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    min-width: 200px;
    text-align: center;
  }

  .calendar-nav button {
    padding: 8px 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s ease;
  }

  .calendar-nav button:hover {
    background: #0056b3;
  }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-bottom: 20px;
  }

  .calendar-weekday {
    text-align: center;
    font-weight: 600;
    color: #666;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
  }

  .calendar-day {
    min-height: 140px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    background: #fff;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
  }

  .calendar-day:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .calendar-day.other-month {
    background: #f9f9f9;
    color: #ccc;
  }

  .calendar-day-number {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
  }

  .calendar-day.other-month .calendar-day-number {
    color: #ccc;
  }

  .calendar-day.today {
    background: #e8f5ff;
    border-color: #007bff;
  }

  .calendar-day.today .calendar-day-number {
    color: #007bff;
    font-weight: 700;
  }

  .calendar-appointments {
    flex: 1;
    overflow-y: auto;
    font-size: 12px;
  }

  .appointment-item {
    background: #f0f8ff;
    padding: 6px;
    margin-bottom: 4px;
    border-radius: 3px;
    border-left: 3px solid #007bff;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .appointment-item:hover {
    background: #e1f0ff;
    transform: translateX(2px);
  }

  .appointment-patient {
    color: #333;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .appointment-type {
    color: #666;
    font-size: 11px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .calendar-day.has-appointments {
    background: #fff9e6;
    border-color: #ffc107;
  }

  .calendar-day.has-appointments::after {
    content: '●';
    position: absolute;
    top: 8px;
    right: 8px;
    color: #ffc107;
    font-size: 12px;
  }

  .calendar-legend {
    display: flex;
    gap: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    font-size: 13px;
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .legend-indicator {
    width: 16px;
    height: 16px;
    border-radius: 3px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .calendar-grid {
      grid-template-columns: repeat(7, 1fr);
      gap: 4px;
    }

    .calendar-day {
      min-height: 100px;
      padding: 6px;
      font-size: 12px;
    }

    .calendar-day-number {
      font-size: 13px;
      margin-bottom: 4px;
      padding-bottom: 4px;
    }

    .appointment-item {
      padding: 4px;
      margin-bottom: 2px;
      font-size: 10px;
    }

    .calendar-appointments {
      font-size: 10px;
    }
  }

  /* Appointment detail modal */
  .appointment-detail-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .appointment-detail-modal.active {
    display: flex;
  }

  .appointment-detail-content {
    background: white;
    border-radius: 8px;
    padding: 20px;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  }

  .appointment-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
  }

  .appointment-detail-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
  }

  .appointment-detail-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
  }

  .appointment-detail-close:hover {
    color: #333;
  }

  .detail-row {
    margin-bottom: 15px;
  }

  .detail-label {
    font-weight: 600;
    color: #666;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .detail-value {
    color: #333;
    font-size: 14px;
  }

  .status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .status-pending {
    background: #fff3cd;
    color: #856404;
  }

  .status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
  }

  .status-completed {
    background: #d4edda;
    color: #155724;
  }

  .status-cancelled {
    background: #f8d7da;
    color: #721c24;
  }
</style>

<div class="appointments-calendar-container">
  <div class="calendar-header">
    <h3 style="margin: 0; font-size: 20px; color: #333;">Appointments Calendar</h3>
    <div class="calendar-nav">
      <button type="button" id="calendarPrevBtn" title="Previous month">← Prev</button>
      <div class="calendar-month-year" id="calendarMonthYear"></div>
      <button type="button" id="calendarNextBtn" title="Next month">Next →</button>
      <button type="button" id="calendarTodayBtn" style="background: #28a745;">Today</button>
    </div>
  </div>

  <div class="calendar-grid" id="calendarGrid"></div>

  <div class="calendar-legend">
    <div class="legend-item">
      <div class="legend-indicator" style="background: #e8f5ff; border: 1px solid #007bff;"></div>
      <span>Today</span>
    </div>
    <div class="legend-item">
      <div class="legend-indicator" style="background: #fff9e6; border: 1px solid #ffc107;"></div>
      <span>Has Appointments</span>
    </div>
    <div class="legend-item">
      <div class="legend-indicator" style="background: #f9f9f9;"></div>
      <span>Other Month</span>
    </div>
  </div>
</div>

<!-- Appointment Detail Modal -->
<div class="appointment-detail-modal" id="appointmentDetailModal">
  <div class="appointment-detail-content">
    <div class="appointment-detail-header">
      <h2 class="appointment-detail-title">Appointment Details</h2>
      <button type="button" class="appointment-detail-close" id="appointmentDetailClose">×</button>
    </div>
    <div id="appointmentDetailBody"></div>
  </div>
</div>

<script>
  // Calendar initialization
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof AppointmentsCalendar !== 'undefined' && window.appointmentsData) {
      AppointmentsCalendar.init(window.appointmentsData);
    }
  });
</script>
