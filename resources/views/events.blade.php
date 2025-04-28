<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Kalendaryo -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
    <div class="container outer-box mt-5 pt-5 pb-4">
        <div class="container inner-glass shadow p-4" id="main_box">
            <h4 class="mb-4 text-center glow-text">EVENTS</h4>

            <!-- Add Event Button -->
            <div class="row mb-4">
                <div class="col-12 text-right d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Event</button>
                </div>
            </div>

            <!-- FullCalendar Display -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm" method="POST" action="{{ route('events.store') }}">
                    @csrf <!-- CSRF Token for Laravel -->
                    
                    <!-- Event Name -->
                    <div class="mb-3">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="eventName" name="name" required>
                    </div>

                    <!-- Venue -->
                    <div class="mb-3">
                        <label for="venue" class="form-label">Venue</label>
                        <input type="text" class="form-control" id="venue" name="venue" required>
                    </div>

                    <!-- Date Picker -->
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Event Date</label>
                        <input type="date" class="form-control" id="eventDate" name="event_date" required>
                    </div>

                    <!-- Timeouts Dropdown -->
                    <div class="mb-3">
                        <label for="timeouts" class="form-label">Number of Timeouts</label>
                        <select class="form-select" id="timeouts" name="timeouts" onchange="updateTimepickers()" required>
                            <option value="2">2</option>
                            <option value="4">4</option>
                        </select>
                    </div>

                    <!-- Time Pickers for 2 timeouts -->
                    <div id="timepickers2" style="display: none;">
                        <div class="mb-3">
                            <label for="timeout1" class="form-label">Morning</label>
                            <input type="time" class="form-control" id="timeout1" name="times[0]" value="08:00">
                        </div>
                        <div class="mb-3">
                            <label for="timeout2" class="form-label">Afternoon</label>
                            <input type="time" class="form-control" id="timeout2" name="times[1]" value="17:00">
                        </div>
                    </div>

                    <!-- Time Pickers for 4 timeouts -->
                    <div id="timepickers4" style="display: none;">
                        <label class="form-label">Morning</label>
                        <div class="mb-3">
                            <input type="time" class="form-control" id="timeout1" name="times[0]" value="08:00">
                        </div>
                        <div class="mb-3">
                            <input type="time" class="form-control" id="timeout2" name="times[1]" value="12:00">
                        </div>

                        <label class="form-label">Afternoon</label>
                        <div class="mb-3">
                            <input type="time" class="form-control" id="timeout3" name="times[2]" value="13:00">
                        </div>
                        <div class="mb-3">
                            <input type="time" class="form-control" id="timeout4" name="times[3]" value="17:00">
                        </div>
                    </div>

                    <!-- Involved Course -->
                    <div class="mb-3">
                        <label for="course" class="form-label">Involved Course</label>
                        <select class="form-select" id="course" name="course" onchange="appendCourseTag()">
                            <option value="BSIS">BSIS</option>
                            <option value="BSIT">BSIT</option>
                        </select>
                    </div>

                    <!-- Involved Course (Tags) -->
                    <div class="mb-3">
                        <label class="form-label">Selected Courses</label>
                        <div id="courseTags" class="d-flex flex-wrap"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Event Name:</strong> <span id="modalEventName"></span></p>
                <p><strong>Venue:</strong> <span id="modalEventVenue"></span></p>
                <p><strong>Date:</strong> <span id="modalEventDate"></span></p>
                <p><strong>Timeouts:</strong> <span id="modalEventTimeouts"></span></p>
                <p><strong>Times:</strong> <span id="modalEventTimes"></span></p>
                <p><strong>Course:</strong> <span id="modalEventCourse"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="generateAttendanceBtn" class="btn btn-primary" disabled>Generate Attendance Sheet</button>
        
        <!-- Note for availability -->
            <p id="attendanceNote" class="text-muted" style="display: none;"></p>
            </div>
        </div>
    </div>
</div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <script>
    function updateTimepickers() {
        var timeouts = document.getElementById('timeouts').value;
        if (timeouts == '2') {
            document.getElementById('timepickers2').style.display = 'block';
            document.getElementById('timepickers4').style.display = 'none';
        } else if (timeouts == '4') {
            document.getElementById('timepickers2').style.display = 'none';
            document.getElementById('timepickers4').style.display = 'block';
        }
    }

    function appendCourseTag() {
        var course = document.getElementById('course').value;
        var courseTags = document.getElementById('courseTags');

        // Check if the course tag already exists
        var existingTags = document.querySelectorAll('#courseTags .badge');
        for (var i = 0; i < existingTags.length; i++) {
            if (existingTags[i].textContent === course) {
                alert("This course is already added.");
                return;
            }
        }

        // Create a new tag element
        var tag = document.createElement('span');
        tag.classList.add('badge', 'me-2', 'mt-2');

        // Apply violet color for BSIS
        if (course === "BSIS") {
            tag.classList.add('bg-violet');
        } else {
            tag.classList.add('bg-info');
        }

        tag.textContent = course;

        // Append the tag to the tags container
        courseTags.appendChild(tag);
    }
</script>
<style>
    /* Violet color for BSIS tag */
    .bg-violet {
        background-color: #8A2BE2; /* Violet */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: {
            url: "{{ route('events.fetch') }}",  
            method: 'GET',
            failure: function() {
                alert('there was an error while fetching events!');
            },
            eventRender: function(info) {
                if (info.event.extendedProps.course === 'BSIS') {
                    info.el.style.backgroundColor = '#8A2BE2'; // Violet
                    info.el.style.borderColor = '#8A2BE2';
                } else {
                    info.el.style.backgroundColor = '#007bff'; // Blue
                    info.el.style.borderColor = '#007bff';
                }
            }
        },
        eventTimeFormat: { 
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        headerToolbar: {
            left: 'prev,next today', 
            center: 'title', 
            right: 'dayGridMonth,timeGridWeek'
        },
        editable: true,
        droppable: true,
        eventDisplay: 'block',

        // Event click handler
        eventClick: function(info) {
            info.jsEvent.preventDefault();

            // Access event details
            document.getElementById('modalEventName').innerText = info.event.title;  // Event name
            document.getElementById('modalEventVenue').innerText = info.event.extendedProps.venue;  // Venue
            document.getElementById('modalEventDate').innerText = info.event.start.toLocaleDateString();  // Event date
            document.getElementById('modalEventTimeouts').innerText = info.event.extendedProps.timeout;  // Timeout
            document.getElementById('modalEventTimes').innerText = info.event.extendedProps.times.join(' - ');  // Event times
            document.getElementById('modalEventCourse').innerText = info.event.extendedProps.course;  // Course

            // Enable/Disable the button based on the event date
            var currentDate = new Date();
            var eventDate = info.event.start; // Event start date
            var generateAttendanceBtn = document.getElementById('generateAttendanceBtn');
            var attendanceNote = document.getElementById('attendanceNote');

            // Log the current date and event date for debugging
            console.log("Current Date: ", currentDate);
            console.log("Event Date: ", eventDate);

            // Set time of both dates to midnight for comparison (ignores time)
            eventDate.setHours(0, 0, 0, 0); // Set time to midnight for comparison
            currentDate.setHours(0, 0, 0, 0); // Set time to midnight for comparison

            // Enable the button if the event date is today or in the future
            if (eventDate >= currentDate) {
                generateAttendanceBtn.disabled = false;   // Enable button
                attendanceNote.style.display = 'none';     // Hide note
            } else {
                generateAttendanceBtn.disabled = true;    // Disable button
                attendanceNote.style.display = 'block';   // Show note
                attendanceNote.innerText = `Available on ${eventDate.toLocaleDateString()}`;  // Show availability note
            }

            // Show the modal
            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            eventModal.show();
        },
    });

    calendar.render();
});
</script>
<style>
    /* General Calendar Card Look */
    #calendar {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 1rem;
        font-family: 'Poppins', 'Roboto', sans-serif;
    }

    /* Calendar Toolbar */
    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }

    .fc-button {
        background-color: #1976d2; /* MUI blue */
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .fc-button-primary:not(:disabled).fc-button-active,
    .fc-button-primary:not(:disabled):hover {
        background-color: #115293;
    }

    .fc-button-primary {
        background-color: #1976d2;
    }

    /* Today highlight */
    .fc-day-today {
        background: #e3f2fd !important;
    }

    /* Day grid cells */
    .fc-daygrid-day {
        border: 1px solid #f0f0f0;
    }

    /* Events */
    .fc-event {
        background-color: #1976d2;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        padding: 2px 4px;
    }

    /* Event hover */
    .fc-event:hover {
        background-color: #115293;
    }

    /* Hide border around the calendar */
    .fc-scrollgrid {
        border: none !important;
    }
</style>

</body>
</html>
