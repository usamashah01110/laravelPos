<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar</title>
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <!-- Include jQuery (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id='calendar'></div>

    <script>
          document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: {
                    url: 'api/v1/calender/bookings',
                    method: 'GET',
                    headers: {
                        'Authorization': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwODIvYXBpL3YxL2xvZ2luIiwiaWF0IjoxNzIyMjQ0MjczLCJleHAiOjE3MjIyNDc4NzMsIm5iZiI6MTcyMjI0NDI3MywianRpIjoiWjRwVUJ2ZXI4T0lyWFNCYiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.UsEN5m_og86jsGleA0aXX7ctq_gkyGRT_v04pqYK8lk' // Replace with your actual token
                    },
                    success: function(data) {
                        console.log('Events data:', data); // Debugging: Check the data structure
                    },
                    failure: function() {
                        console.error('Failed to load events');
                    }
                },
                eventColor: '#378006',
                eventDidMount: function(info) {
                    // Optionally add tooltips or other customizations
                    info.el.setAttribute('title', info.event.extendedProps.description);
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
