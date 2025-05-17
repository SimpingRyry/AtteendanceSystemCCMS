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
    <div class="container-fluid mt-5 px-4">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-3 mt-3"> <!-- Add margin-top -->
                <div class="d-flex flex-column gap-3">
                    <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <span class="me-2">+</span> Add Event
                    </button>

                    <div>
                        <label for="monthFilter" class="form-label small">Filter by Month</label>
                        <select class="form-select" id="monthFilter">
                            <option value="all">All Months</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <!-- Add more months -->
                        </select>
                    </div>

                    <input type="text" class="form-control" id="eventSearch" placeholder="Search events...">

                    <div class="border rounded-3 p-3 bg-white">
                        <h6 class="text-center fw-semibold mb-2">Upcoming Events</h6>
                        <ul id="upcomingEventsList" class="list-group list-group-flush small">
                            <!-- Dynamically filled -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-md-8 col-lg-9 mt-3"> <!-- Add margin-top -->
                <div class="bg-white border rounded-3 p-3" id="calendarWrapper">
                    <div id="calendar"></div>
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
                            <option value="">-- Select Course --</option>

                                <option value="BSIS">BSIS</option>
                                <option value="BSIT">BSIT</option>
                                <option value="All">All</option>

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

<div class="modal fade" id="editEventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editEventForm" method="POST" action="{{ route('events.update') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Edit Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editEventId" name="event_id">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" id="editEventTitle" name="title" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Venue</label>
            <input type="text" id="editEventVenue" name="venue" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" id="editEventDate" name="date" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Day Type</label>
            <select id="editEventDayType" name="dayType" class="form-select">
              <option value="Half Day">Half Day</option>
              <option value="Whole Day">Whole Day</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Course</label>
            <input type="text" id="editEventCourse" name="course" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Times</label>
            <div id="editEventTimesContainer"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
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
        var course = document.getElementById('course').value.trim();
        if (!course) return;

        var courseTags = document.getElementById('courseTags');
        var existingTags = document.querySelectorAll('#courseTags .badge');

        // If 'All' is already selected, do not allow other courses
        for (let tag of existingTags) {
            if (tag.firstChild.textContent === 'All') {
                alert("You cannot add other courses when 'All' is selected.");
                return;
            }
        }

        // If selecting 'All', clear previous tags
        if (course === 'All') {
            courseTags.innerHTML = '';
        } else {
            // Prevent adding 'All' if other courses are already added
            for (let tag of existingTags) {
                if (tag.firstChild.textContent === course) {
                    alert("This course is already added.");
                    return;
                }
            }
        }

        // Create the tag
        var tag = document.createElement('span');
        tag.classList.add('badge', 'me-2', 'mt-2', 'd-inline-flex', 'align-items-center');

        // Style based on course
        if (course === 'BSIS') {
            tag.classList.add('bg-violet');
        } else if (course === 'All') {
            tag.classList.add('bg-secondary');
        } else {
            tag.classList.add('bg-info');
        }

        // Add text
        tag.appendChild(document.createTextNode(course));

        // Add close button
        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.classList.add('btn-close', 'btn-close-white', 'ms-2', 'btn-sm');
        closeBtn.setAttribute('aria-label', 'Remove');
        closeBtn.onclick = function () {
            tag.remove();
        };

        tag.appendChild(closeBtn);

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
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: {
            url: "{{ route('events.fetch') }}",
            method: 'GET',
            failure: function () {
                alert('There was an error while fetching events!');
            }
        },
        eventDidMount: function (info) {
            const event = info.event;

            // Color coding
            if (event.extendedProps.course === 'BSIS') {
    info.el.style.backgroundColor = '#8A2BE2'; // violet
    info.el.style.borderColor = '#8A2BE2';
} else if (event.extendedProps.course === 'All') {
    info.el.style.backgroundColor = '#28a745'; // green
    info.el.style.borderColor = '#28a745';
} else {
    info.el.style.backgroundColor = '#007bff'; // blue
    info.el.style.borderColor = '#007bff';
}

            // Create icon container (cloud)
            const iconContainer = document.createElement('div');
            iconContainer.className = 'event-icons';
            iconContainer.style.position = 'absolute';
            iconContainer.style.top = '0';
            iconContainer.style.right = '0';
            iconContainer.style.background = '#fff';
            iconContainer.style.border = '1px solid #ccc';
            iconContainer.style.borderRadius = '8px';
            iconContainer.style.padding = '5px';
            iconContainer.style.display = 'none';
            iconContainer.style.zIndex = '1000';

            iconContainer.innerHTML = `
                <i class="fas fa-edit me-2 text-primary" title="Edit" style="cursor:pointer;"></i>
                <i class="fas fa-eye me-2 text-success" title="View" style="cursor:pointer;"></i>
                <i class="fas fa-trash text-danger" title="Delete" style="cursor:pointer;"></i>
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

            // Edit icon click
            const editIcon = iconContainer.querySelector('.fa-edit');
            editIcon.addEventListener('click', function (e) {
    e.stopPropagation(); // Prevent calendar click

    // Populate basic fields
    document.getElementById('editEventId').value = event.extendedProps.id;
    document.getElementById('editEventTitle').value = event.title;
    document.getElementById('editEventVenue').value = event.extendedProps.venue || '';
    document.getElementById('editEventDate').value = event.start.toISOString().slice(0, 10);
    document.getElementById('editEventCourse').value = event.extendedProps.course || '';

    // Handle day type
    const timeout = event.extendedProps.timeout;
    const dayType = (timeout == 2) ? 'Half Day' : 'Whole Day';
    document.getElementById('editEventDayType').value = dayType;

    // Clear and populate time fields
    const container = document.getElementById('editEventTimesContainer');
    container.innerHTML = '';

    const times = event.extendedProps.times || [];

    if (times.length === 2) {
        container.innerHTML = `
            <label>Time In</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[0]}">
            <label>Time Out</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[1]}">
        `;
    } else if (times.length === 4) {
        container.innerHTML = `
            <label>Morning Time In</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[0]}">
            <label>Morning Time Out</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[1]}">
            <label>Afternoon Time In</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[2]}">
            <label>Afternoon Time Out</label>
            <input type="time" name="edit_times[]" class="form-control mb-2" value="${times[3]}">
        `;
    } else {
        // fallback
        times.forEach(time => addEditTimeInput(time));
    }

    // Show modal
    new bootstrap.Modal(document.getElementById('editEventModal')).show();
});

            // (Optional) View icon
            const viewIcon = iconContainer.querySelector('.fa-eye');
            viewIcon.addEventListener('click', function (e) {
                e.stopPropagation();
                // You can trigger the same logic used in eventClick here if needed
            });

            // (Optional) Delete icon
            const deleteIcon = iconContainer.querySelector('.fa-trash');
            deleteIcon.addEventListener('click', function (e) {
                e.stopPropagation();
                if (confirm('Are you sure you want to delete this event?')) {
                    // Add delete logic here
                }
            });
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();

            document.getElementById('modalEventName').innerText = info.event.title;
            document.getElementById('modalEventVenue').innerText = info.event.extendedProps.venue;
            document.getElementById('modalEventDate').innerText = info.event.start.toLocaleDateString();
            document.getElementById('modalEventTimeouts').innerText = info.event.extendedProps.timeout;
            document.getElementById('modalEventTimes').innerText = info.event.extendedProps.times.join(' - ');
            document.getElementById('modalEventCourse').innerText = info.event.extendedProps.course;

            const currentDate = new Date();
            const eventDate = new Date(info.event.start);

            currentDate.setHours(0, 0, 0, 0);
            eventDate.setHours(0, 0, 0, 0);

            const generateBtn = document.getElementById('generateAttendanceBtn');
            const attendanceNote = document.getElementById('attendanceNote');

            if (eventDate >= currentDate) {
                generateBtn.disabled = false;
                attendanceNote.style.display = 'none';
            } else {
                generateBtn.disabled = true;
                attendanceNote.style.display = 'block';
                attendanceNote.innerText = `Available on ${eventDate.toLocaleDateString()}`;
            }

            new bootstrap.Modal(document.getElementById('eventModal')).show();
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
        eventDisplay: 'block'
    });

    calendar.render();
    fetch("{{ route('events.fetch') }}")
    .then(response => response.json())
    .then(events => {
        const list = document.getElementById('upcomingEventsList');
        list.innerHTML = ''; // Clear current content

        const today = new Date();
        const upcoming = events.filter(event => {
            const eventDate = new Date(event.start);
            return eventDate >= today;
        }).sort((a, b) => new Date(a.start) - new Date(b.start));

        upcoming.forEach(event => {
            const date = new Date(event.start);
            const formattedDate = date.toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            // Determine badge color
            let badgeClass = 'bg-primary';

if (event.extendedProps.course === 'BSIT') {
    badgeClass = 'bg-info text-dark';
} else if (event.extendedProps.course === 'BSIS') {
    badgeClass = 'bg-purple text-white';
} else if (event.extendedProps.course === 'All') {
    badgeClass = 'bg-success text-white';
}

            // Create list item
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center rounded-3 mb-2 shadow-sm';

            li.innerHTML = `
                ${event.title}
                <span class="badge ${badgeClass} rounded-pill">${formattedDate}</span>
            `;

            list.appendChild(li);
        });
    })
    .catch(error => {
        console.error("Error fetching upcoming events:", error);
    });
});


// Add time input field to edit modal
function addEditTimeInput(value = '') {
    const container = document.getElementById('editEventTimesContainer');
    const inputGroup = document.createElement('div');
    inputGroup.className = 'input-group mb-2';

    inputGroup.innerHTML = `
        <input type="time" name="edit_times[]" class="form-control" value="${value}">
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">Remove</button>
    `;

    container.appendChild(inputGroup);
}
</script>
<style>
    .bg-purple {
    background-color: #8A2BE2 !important;
   
}
/* Event styling with smaller fonts and subtle enhancements */
.fc-event {
    position: relative;
    background-color: #1976d2;
    color: white;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 4px;
    border: none;
    box-shadow: none;
    transition: background-color 0.2s ease;
}

.fc-event:hover {
    background-color: #115293;
}

/* Icon Hover Panel */
.event-icons {
    position: absolute;
    top: -40px;
    right: 0;
    display: none;
    background-color: rgba(0, 0, 0, 0.75);
    padding: 5px 8px;
    border-radius: 8px;
    z-index: 9999;
    white-space: nowrap;
}

.event-icons i {
    color: white;
    font-size: 12px;
    margin: 0 4px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.event-icons i:hover {
    transform: scale(1.2);
    color: #ffeb3b;
}

.fc-event:hover .event-icons {
    display: block;
}

/* Calendar Container */
#calendarWrapper {
    background: white;
    border: 1px solid #e0e0e0;
    box-shadow: none;
}

/* Calendar Appearance */
#calendar {
    font-family: 'Poppins', 'Roboto', sans-serif;
}

.fc-toolbar-title {
    font-size: 1.4rem;
    font-weight: 500;
    color: #333;
}

.fc-button {
    background-color: #1976d2;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.85rem;
    color: white;
    transition: background-color 0.3s ease;
}

.fc-button-primary:not(:disabled).fc-button-active,
.fc-button-primary:not(:disabled):hover {
    background-color: #115293;
}

/* Today Cell */
.fc-day-today {
    background-color: #f1f8ff !important;
}

/* Flat cell borders */
.fc-daygrid-day {
    border: 1px solid #f1f1f1;
}

/* Hide calendar grid border */
.fc-scrollgrid {
    border: none !important;
}

main {
    padding-top: 1.5rem;
    padding-bottom: 2rem;
    background-color: #f9f9f9;
}

/* Ensure sidebar contents are spaced from the top */
.col-md-4 .d-flex.flex-column {
    padding-top: 1rem;
}

/* Add top margin to calendar wrapper */
#calendarWrapper {
    margin-top: 0.5rem;
    padding: 1.1rem;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
}

/* Optionally add box spacing to main row */
.row.g-4 {
    margin-top: 1rem;
}

/* Optional: unify sidebar cards and button spacing */
#monthFilter,
#eventSearch,
#addEventModal {
    margin-top: 0.5rem;
}
</style>

</body>
</html>
