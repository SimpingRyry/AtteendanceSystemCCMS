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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    


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
            <div class="row">
                <!-- Sidebar for Filters and Event List -->
                <div class="col-md-4 col-lg-3">
                    <div class="d-flex flex-column">
                        <!-- Add Event Button (Left aligned with "+" icon) -->
                        <div class="mb-3 text-left">
                            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <span class="me-2">+</span> Add Event
                            </button>
                        </div>

                        <!-- Month Filter -->
                        <div class="mb-3">
                            <label for="monthFilter" class="form-label">Filter by Month</label>
                            <select class="form-select" id="monthFilter">
                                <option value="all">All Months</option>
                                <option value="January">January</option>
                                <option value="February">February</option>
                                <!-- Add other months here -->
                            </select>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" class="form-control" id="eventSearch" placeholder="Search events...">
                        </div>

                        <!-- Upcoming Events List -->
                        <div class="mb-3">
                            <h6>Upcoming Events</h6>
                            <ul id="upcomingEventsList" class="list-unstyled">
                                <!-- List upcoming events dynamically -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Main Calendar Section -->
                <div class="col-md-8 col-lg-9">
                    <div class="card-body">
                        <h4 class="text-center glow-text mb-4">EVENTS</h4>
                        <!-- FullCalendar Display -->
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold text-primary">Create Event</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="eventForm" method="POST" action="{{ route('events.store') }}">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="eventName" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="eventName" name="name" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="venue" class="form-label">Location</label>
                            <input type="text" class="form-control" id="venue" name="venue">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="eventDate" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="dayType" class="form-label">Day Type</label>
                            <select class="form-select" id="dayType" name="timeouts" required>
                                <option value="">Select Day Type</option>
                                <option value="2">Half Day</option>
                                <option value="4">Whole Day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Time Pickers -->
                    <div id="timePickers" class="mb-3 d-none">
                        <div id="halfDayTimes" class="d-none row">
                            <label class="form-label">Half Day</label>
                            <div class="col-md-6 mb-2">
                                <input type="time" class="form-control" name="half_start" placeholder="Start Time">
                            </div>
                            <div class="col-md-6">
                                <input type="time" class="form-control" name="half_end" placeholder="End Time">
                            </div>
                        </div>

                        <div id="wholeDayTimes" class="d-none">
                            <label class="form-label">Morning</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="time" class="form-control" name="morning_start" placeholder="Morning Start">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="time" class="form-control" name="morning_end" placeholder="Morning End">
                                </div>
                            </div>
                            <label class="form-label mt-3">Afternoon</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="time" class="form-control" name="afternoon_start" placeholder="Afternoon Start">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="time" class="form-control" name="afternoon_end" placeholder="Afternoon End">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="repeatDates" class="form-label">Repeat On</label>
                        <input type="text" id="repeatDates" class="form-control" placeholder="Select one or more dates">
                        <input type="hidden" id="repeatDatesHidden" name="repeat_dates">
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="course" class="form-label">Tag Course</label>
                            <select class="form-select" id="course" name="course" onchange="appendCourseTag()">
                                <option value="BSIS">BSIS</option>
                                <option value="BSIT">BSIT</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="guests" class="form-label">Add Guests (Optional)</label>
                            <input type="text" class="form-control" id="guests" name="guests" placeholder="Separate emails by commas">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tagged Courses</label>
                        <div id="courseTags" class="d-flex flex-wrap"></div>
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#repeatDates", {
        mode: "multiple",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            document.getElementById('repeatDatesHidden').value = dateStr;
        }
    });
</script>

<script>
    document.getElementById('dayType').addEventListener('change', function () {
        const type = this.value;
        const timePickers = document.getElementById('timePickers');
        const half = document.getElementById('halfDayTimes');
        const whole = document.getElementById('wholeDayTimes');

        timePickers.classList.remove('d-none');
        half.classList.add('d-none');
        whole.classList.add('d-none');

        if (type === '2') {
            half.classList.remove('d-none');
        } else if (type === '4') {
            whole.classList.remove('d-none');
        } else {
            timePickers.classList.add('d-none');
        }
    });
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
            url: "{{ route('events.fetch') }}",  
            method: 'GET',
            failure: function() {
                alert('there was an error while fetching events!');
            }
        },
        eventDidMount: function(info) {
            // Color coding by course
            if (info.event.extendedProps.course === 'BSIS') {
                info.el.style.backgroundColor = '#8A2BE2';
                info.el.style.borderColor = '#8A2BE2';
            } else {
                info.el.style.backgroundColor = '#007bff';
                info.el.style.borderColor = '#007bff';
            }

            // Create the icon cloud
            const iconContainer = document.createElement('div');
            iconContainer.className = 'event-icons';
            iconContainer.innerHTML = `
                <i class="fas fa-edit" title="Edit"></i>
                <i class="fas fa-eye" title="View"></i>
                <i class="fas fa-trash" title="Delete"></i>
            `;

            info.el.appendChild(iconContainer);

            // Show/hide logic
            let hideTimeout;

            function showIcons() {
                clearTimeout(hideTimeout);
                iconContainer.style.display = 'block';
            }

            function hideIcons() {
                hideTimeout = setTimeout(() => {
                    iconContainer.style.display = 'none';
                }, 200);
            }

            info.el.addEventListener('mouseenter', showIcons);
            info.el.addEventListener('mouseleave', hideIcons);
            iconContainer.addEventListener('mouseenter', showIcons);
            iconContainer.addEventListener('mouseleave', hideIcons);
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

            document.getElementById('modalEventName').innerText = info.event.title;
            document.getElementById('modalEventVenue').innerText = info.event.extendedProps.venue;
            document.getElementById('modalEventDate').innerText = info.event.start.toLocaleDateString();
            document.getElementById('modalEventTimeouts').innerText = info.event.extendedProps.timeout;
            document.getElementById('modalEventTimes').innerText = info.event.extendedProps.times.join(' - ');
            document.getElementById('modalEventCourse').innerText = info.event.extendedProps.course;

            var currentDate = new Date();
            var eventDate = new Date(info.event.start);

            var generateAttendanceBtn = document.getElementById('generateAttendanceBtn');
            var attendanceNote = document.getElementById('attendanceNote');

            currentDate.setHours(0, 0, 0, 0);
            eventDate.setHours(0, 0, 0, 0);

            if (eventDate >= currentDate) {
                generateAttendanceBtn.disabled = false;
                attendanceNote.style.display = 'none';
            } else {
                generateAttendanceBtn.disabled = true;
                attendanceNote.style.display = 'block';
                attendanceNote.innerText = `Available on ${eventDate.toLocaleDateString()}`;
            }

            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            eventModal.show();
        },
    });

    calendar.render();
});
</script>
<style>

.fc-event {
    position: relative;
}

.event-icons {
    position: absolute;
    top: -45px;
    right: 0;
    display: none;
    background-color: rgba(0, 0, 0, 0.75);
    padding: 6px 10px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    white-space: nowrap;
    transition: opacity 0.2s ease-in-out;
}

.event-icons i {
    color: white;
    margin: 0 6px;
    cursor: pointer;
    font-size: 14px;
    transition: transform 0.2s;
}

.event-icons i:hover {
    transform: scale(1.2);
}

.fc-event:hover .event-icons {
    display: block;
}
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
