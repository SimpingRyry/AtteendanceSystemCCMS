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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 600,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                }
            });
            calendar.render();

            // Disable past dates
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("eventDate").setAttribute('min', today);
        });

        document.getElementById('eventDate').setAttribute('min', new Date().toISOString().split('T')[0]);
    </script>

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
            url: "{{ route('events.fetch') }}",  // Your route to fetch events
            method: 'GET',
            failure: function() {
                alert('there was an error while fetching events!');
            },
            eventRender: function(info) {
                // Customizing the event color based on course
                if (info.event.extendedProps.course === 'BSIS') {
                    info.el.style.backgroundColor = '#8A2BE2'; // Violet for BSIS
                    info.el.style.borderColor = '#8A2BE2';
                } else {
                    info.el.style.backgroundColor = '#007bff'; // Blue for others
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
        editable: true, // Allow events to be editable
        droppable: true, // Enable dragging and dropping events
        eventDisplay: 'block', // Ensures events are shown properly (like Google Calendar)
    });

    calendar.render();
});
</script>


</body>
</html>
