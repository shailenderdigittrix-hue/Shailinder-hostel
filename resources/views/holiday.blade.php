<div id='calendar'></div>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: [
        {
          title: "New Year's Day (US)",
          start: '2025-10-04',
          color: '#378006'
        },
        {
          title: "Republic Day (India)",
          start: '2025-10-05',
          color: '#ff5733'
        },
        {
          title: "Independence Day (India)",
          start: '2025-10-07',
          color: '#138808'
        },
        {
          title: "Christmas Day (US)",
          start: '2025-10-10',
          color: '#c41e3a'
        }
      ]
    });
    calendar.render();
  });
</script>
