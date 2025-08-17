<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    


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
        <div id="userRole" data-role="{{ strtolower(auth()->user()->role) }}"></div>
                <input type="hidden" id="currentUserRole" value="{{ Auth::user()->role }}">
<input type="hidden" id="currentStudentId" value="{{ Auth::user()->student_id }}">

    <div class="container-fluid mt-5 px-4">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-3 mt-3"> <!-- Add margin-top -->
                <div class="d-flex flex-column gap-3">
                @if(Auth::user()->role !== 'Member')
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <span class="me-2">+</span> Add Event
            </button>
        @endif

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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold text-primary">Create Event</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body position-relative">
                <!-- Step Indicators -->
                <div class="d-flex justify-content-center align-items-center my-4 gap-4 step-indicator">
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-circle active" data-step="1">1</div>
                        <div class="step-label mt-1 text-center">Event Setup</div>
                    </div>
                    <div class="line"></div>
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-circle" data-step="2">2</div>
                        <div class="step-label mt-1 text-center">Evaluation</div>
                    </div>
                </div>

                <form id="eventForm" method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Step 1: Event Details -->
                    <div class="step step-1">
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

                        @if(auth()->user()->role === 'Super Admin')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="organization" class="form-label">Organization</label>
                                <select class="form-select" id="organization" name="organization" required>
                                    <option value="">-- Select Organization --</option>
                                    @foreach($orgs as $org)
                                        <option value="{{ $org->org_name }}">{{ $org->org_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <!-- Combined Participants Section -->
                        <div class="mb-3">
                            <label class="form-label">Participants</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select" id="involvedStudents" name="involved_students" >
                                        <option value="">-- Select Students --</option>
                                        <option value="All">All</option>
                                        <option value="Officers">Officers</option>
                                    </select>
                                </div>
                                <div class="col-md-6 position-relative">
                                    <input type="text" class="form-control" id="guests" name="guests" placeholder="use @ to involved specific members or officers (optional)">
                                    <div id="mentionDropdown" class="list-group position-absolute w-100 z-3" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Pickers -->
                        <div id="timePickers" class="mb-3 d-none">
    <!-- Half Day -->
    <div id="halfDayTimes" class="d-none row">
        <label class="form-label">Half Day Schedule</label>
        <div class="col-md-6 mb-2">
            <label for="half_start" class="form-label">Time In</label>
            <input type="time" class="form-control" name="half_start" id="half_start" value="{{ $timeSettings->morning_in ?? '' }}">
        </div>
        <div class="col-md-6 mb-2">
            <label for="half_end" class="form-label">Time Out</label>
            <input type="time" class="form-control" name="half_end" id="half_end" value="{{ $timeSettings->afternoon_out ?? '' }}">
        </div>
    </div>

    <!-- Whole Day -->
    <div id="wholeDayTimes" class="d-none">
        <label class="form-label">Whole Day Schedule</label>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="morning_start" class="form-label">Morning Time In</label>
                <input type="time" class="form-control" name="morning_start" id="morning_start" value="{{ $timeSettings->morning_in ?? '' }}">
            </div>
            <div class="col-md-6 mb-2">
                <label for="morning_end" class="form-label">Morning Time Out</label>
                <input type="time" class="form-control" name="morning_end" id="morning_end" value="{{ $timeSettings->morning_out ?? '' }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6 mb-2">
                <label for="afternoon_start" class="form-label">Afternoon Time In</label>
                <input type="time" class="form-control" name="afternoon_start" id="afternoon_start" value="{{ $timeSettings->afternoon_in ?? '' }}">
            </div>
            <div class="col-md-6 mb-2">
                <label for="afternoon_end" class="form-label">Afternoon Time Out</label>
                <input type="time" class="form-control" name="afternoon_end" id="afternoon_end" value="{{ $timeSettings->afternoon_out ?? '' }}">
            </div>
        </div>
    </div>
</div>

                        <div class="mb-3">
                            <label for="repeatDates" class="form-label">Repeat On</label>
                            <input type="text" id="repeatDates" class="form-control" placeholder="Select one or more dates">
                            <input type="hidden" id="repeatDatesHidden" name="repeat_dates">
                        </div>

                        <div class="mb-3">
                            <label for="attachedMemo" class="form-label">Attached Memo</label>
                            <input type="file" class="form-control" id="attachedMemo" name="attached_memo" accept="image/*">
                        </div>
                    </div>

                    <!-- Step 2: Evaluation Template -->
                    <div class="step step-2 d-none">
                        <div class="mb-3">
                            <label for="evalTemplate" class="form-label">Evaluation Template</label>
                            <select class="form-select" name="evaluation_template" id="evalTemplate" required>
                                <option value="">-- Select Evaluation Template --</option>
                                @foreach($evaluationTemplates as $template)
                                    <option value="{{ $template->id }}">{{ $template->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Step Navigation -->
                    <div class="modal-footer d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-secondary" id="prevBtn" disabled>Previous</button>
                        <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                        <button type="submit" class="btn btn-success d-none" id="submitBtn">Save Event</button>
                    </div>
                </form>
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

<div class="modal fade" id="viewEventModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold d-flex align-items-center">
          <i class="bi bi-calendar-event me-2"></i> <span id="viewEventName"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body px-4 py-4 bg-light">

        <!-- Venue -->
        <div class="mb-4 p-3 border-start border-4 border-primary bg-white rounded shadow-sm">
          <h6 class="fw-semibold mb-1"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Venue</h6>
          <div id="viewEventVenue" class="text-secondary"></div>
        </div>

        <!-- Date and Time -->
        <div class="mb-4 p-3 border-start border-4 border-info bg-white rounded shadow-sm">
          <h6 class="fw-semibold mb-2"><i class="bi bi-clock-history me-2 text-info"></i>Date & Time</h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Date:</strong>
              <div id="viewEventDate" class="text-secondary"></div>
            </div>
            <div class="col-md-6">
              <strong>Time:</strong>
              <div id="viewEventTimes" class="text-secondary"></div>
            </div>
          </div>
        </div>

       
               <div id="yourAttendanceSection" class="mb-4 p-3 border-start border-4 border-warning bg-white rounded shadow-sm d-none">
            <h6 class="fw-semibold mb-2"><i class="bi bi-person-check-fill me-2 text-warning"></i>Your Attendance</h6>
          <div id="attendanceDetails" class="text-secondary"></div>
            </div>
       
 

        <!-- Description & Guests -->
        <div class="mb-4 p-3 border-start border-4 border-success bg-white rounded shadow-sm">
          <h6 class="fw-semibold mb-2"><i class="bi bi-chat-dots-fill me-2 text-success"></i>Description & Guests</h6>
          <div id="viewEventDescGuests" class="text-secondary"></div>
        </div>
        

        <!-- Attached Memo -->
        <div class="mb-3">
          <h6 class="fw-semibold mb-2"><i class="bi bi-paperclip me-2 text-dark"></i>Attached Memo</h6>
          <div id="viewEventMemo" class="border p-3 rounded bg-white text-center shadow-sm">
            <img id="memoImage" src="" alt="Memo Image" class="img-fluid rounded mb-2" style="max-height: 300px; object-fit: contain;">
            <br>
            <a href="#" id="memoDownloadLink" download class="btn btn-outline-secondary btn-sm mt-2">
              <i class="bi bi-download"></i> Download Memo
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>




@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-success mb-3">
          <i class="bi bi-check-circle-fill" style="font-size: 60px;"></i>
        </div>
        <h5 class="text-success">Success!</h5>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn btn-success mt-2" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endif

@if($errors->has('event_date'))
<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-danger mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </div>
        <h5 class="text-danger">Error!</h5>
        <p>{{ $errors->first('event_date') }}</p>
        <button type="button" class="btn btn-danger mt-2" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
    .step {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .step.d-none {
        opacity: 0;
        transform: translateX(50px);
        position: absolute;
        width: 100%;
    }

    .step-circle {
        width: 35px;
        height: 35px;
        background: #ccc;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 35px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .step-circle.active {
        background-color: #0d6efd;
    }

    .line {
        width: 50px;
        height: 3px;
        background: #ccc;
    }

    .step-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #555;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentStep = 1;

        function showStep(step) {
            document.querySelectorAll(".step").forEach(el => el.classList.add("d-none"));
            document.querySelector(`.step-${step}`).classList.remove("d-none");

            document.querySelectorAll(".step-circle").forEach(c => c.classList.remove("active"));
            document.querySelector(`.step-circle[data-step="${step}"]`).classList.add("active");

            document.getElementById("prevBtn").disabled = step === 1;
            document.getElementById("nextBtn").classList.toggle("d-none", step === 2);
            document.getElementById("submitBtn").classList.toggle("d-none", step !== 2);
        }

        document.getElementById("nextBtn").addEventListener("click", function () {
            currentStep++;
            showStep(currentStep);
        });

        document.getElementById("prevBtn").addEventListener("click", function () {
            currentStep--;
            showStep(currentStep);
        });

        showStep(currentStep);

        // Optional: Time Picker toggle based on dayType
      
    });
</script>

<script>
let adminUsers = [];

document.addEventListener("DOMContentLoaded", () => {
    const guestsInput = document.getElementById("guests");
    const mentionDropdown = document.getElementById("mentionDropdown");

    // Load admin users
    fetch('/officer-users') // Laravel route returns admins
        .then(res => res.json())
        .then(data => adminUsers = data);

    guestsInput.addEventListener("input", (e) => {
        const value = e.target.value;
        const atIndex = value.lastIndexOf("@");

        if (atIndex !== -1) {
            const query = value.substring(atIndex + 1).toLowerCase();
            const matches = adminUsers.filter(user =>
                user.name.toLowerCase().includes(query) || user.email.toLowerCase().includes(query)
            );

            if (matches.length) {
                mentionDropdown.innerHTML = matches.map(user => `
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" data-email="${user.email}">
            <img src="/uploads/${user.picture || 'human.png'}" class="rounded-circle me-2" width="30" height="30">
                        <div>
                            <strong>${user.name}</strong><br>
                            <small>${user.email}</small>
                        </div>
                    </a>
                `).join("");
                mentionDropdown.style.display = "block";
            } else {
                mentionDropdown.style.display = "none";
            }
        } else {
            mentionDropdown.style.display = "none";
        }
    });

    mentionDropdown.addEventListener("click", function (e) {
        e.preventDefault();
        const item = e.target.closest("a");
        if (item) {
            const email = item.dataset.email;
            const value = guestsInput.value;
            const atIndex = value.lastIndexOf("@");
            guestsInput.value = value.substring(0, atIndex) + email + ', ';
            mentionDropdown.style.display = "none";
        }
    });

    document.addEventListener("click", function (e) {
        if (!mentionDropdown.contains(e.target) && e.target !== guestsInput) {
            mentionDropdown.style.display = "none";
        }
    });
});
</script>

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

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
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
const dayTypeSelect = document.getElementById('editEventDayType');
  const timesContainer = document.getElementById('editEventTimesContainer');

  function updateTimeFieldsBasedOnDayType(dayType, existingTimes = []) {
    timesContainer.innerHTML = '';

    if (dayType === 'Half Day') {
      timesContainer.innerHTML = `
        <label>Time In</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[0] || ''}">
        <label>Time Out</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[1] || ''}">
      `;
    } else if (dayType === 'Whole Day') {
      timesContainer.innerHTML = `
        <label>Morning Time In</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[0] || ''}">
        <label>Morning Time Out</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[1] || ''}">
        <label>Afternoon Time In</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[2] || ''}">
        <label>Afternoon Time Out</label>
        <input type="time" name="edit_times[]" class="form-control mb-2" value="${existingTimes[3] || ''}">
      `;
    }
  }

  // Listen for dropdown changes
  dayTypeSelect.addEventListener('change', function () {
    updateTimeFieldsBasedOnDayType(this.value);
  });

  // Example: how to trigger this modal and populate values
  function openEditEventModal(event) {
    document.getElementById('editEventId').value = event.extendedProps.id;
    alert(event.extendedProps.id);
    document.getElementById('editEventTitle').value = event.title;
    document.getElementById('editEventVenue').value = event.extendedProps.venue || '';
    document.getElementById('editEventDate').value = event.start.toISOString().slice(0, 10);
    // document.getElementById('editEventCourse').value = event.extendedProps.course || '';

    const timeout = event.extendedProps.timeout;
    const dayType = (timeout == 2) ? 'Half Day' : 'Whole Day';
    document.getElementById('editEventDayType').value = dayType;

    const times = event.extendedProps.times || [];
    updateTimeFieldsBasedOnDayType(dayType, times);

    // Show modal
    new bootstrap.Modal(document.getElementById('editEventModal')).show();
  }

  const userRole = document.getElementById('userRole').dataset.role;


            // Create icon container (cloud)
            if (userRole !== 'member') {
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
    openEditEventModal(event);
});

            // (Optional) View icon
            const viewIcon = iconContainer.querySelector('.fa-eye');
            viewIcon.addEventListener('click', function (e) {
                e.stopPropagation();

    // Extract data
    const title = event.title;
    const venue = event.extendedProps.venue || 'N/A';
    const date = event.start.toLocaleDateString();
    const times = event.extendedProps.times || [];
    const description = event.extendedProps.description || '';
    const guests = event.extendedProps.guests || [];

    // Format time labels
    let timeDisplay = '';
    if (times.length === 2) {
        timeDisplay = `<strong>Time In:</strong> ${times[0]} <br><strong>Time Out:</strong> ${times[1]}`;
    } else if (times.length === 4) {
        timeDisplay = `
            <strong>Morning Time In:</strong> ${times[0]} <br>
            <strong>Morning Time Out:</strong> ${times[1]} <br>
            <strong>Afternoon Time In:</strong> ${times[2]} <br>
            <strong>Afternoon Time Out:</strong> ${times[3]}
        `;
    } else {
        timeDisplay = times.map((t, i) => `<strong>Time ${i + 1}:</strong> ${t}`).join('<br>');
    }

    // Highlight guests
    const guestBadges = guests.map(guest => `<span class="badge bg-primary me-1">${guest}</span>`).join(' ');

    const memoImage = document.getElementById('memoImage');
const memoDownloadLink = document.getElementById('memoDownloadLink');
const memoUrl = event.extendedProps.attached_memo_url;

if (memoUrl) {
    memoImage.src = memoUrl;
    memoImage.alt = "Memo Image";
    memoDownloadLink.href = memoUrl;
    memoDownloadLink.classList.remove('d-none');
    memoImage.classList.remove('d-none');
} else {
    memoImage.src = "";
    memoImage.alt = "No memo attached.";
    memoImage.classList.add('d-none');
    memoDownloadLink.classList.add('d-none');
}

    // Populate modal
    document.getElementById('viewEventName').innerText = title;
    document.getElementById('viewEventVenue').innerText = venue;
    document.getElementById('viewEventDate').innerText = date;
    document.getElementById('viewEventTimes').innerHTML = timeDisplay;
    document.getElementById('viewEventDescGuests').innerHTML = `
        ${description ? `<p>${description}</p>` : ''}
        ${guests.length ? `<div>${guestBadges}</div>` : '<span class="text-muted">No guests listed.</span>'}
    `;

    // Show modal
            new bootstrap.Modal(document.getElementById('viewEventModal')).show();
                // You can trigger the same logic used in eventClick here if needed
            });

            // (Optional) Delete icon
const deleteIcon = iconContainer.querySelector('.fa-trash');
deleteIcon.addEventListener('click', function (e) {
    e.stopPropagation();

    if (confirm('Are you sure you want to delete this event?')) {
        const eventId = event.id; // <-- use event.id not extendedProps.id

        fetch(`/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to delete');
            return res.json();
        })
        .then(data => {
            alert(data.message || 'Event deleted!');
            event.remove(); // frontend remove
        })
        .catch(err => {
            console.error(err);
            alert('Could not delete event.');
        });
    }
});
            }
           
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();

             const title = info.event.title;
    const venue = info.event.extendedProps.venue || 'N/A';
    const date = info.event.start.toLocaleDateString();
    const times = info.event.extendedProps.times || [];
    const description = info.event.extendedProps.description || '';
    const guests = info.event.extendedProps.guests || [];
    
    

    // Format time labels
    let timeDisplay = '';
    if (times.length === 2) {
        timeDisplay = `<strong>Time In:</strong> ${times[0]} <br><strong>Time Out:</strong> ${times[1]}`;
    } else if (times.length === 4) {
        timeDisplay = `
            <strong>Morning Time In:</strong> ${times[0]} <br>
            <strong>Morning Time Out:</strong> ${times[1]} <br>
            <strong>Afternoon Time In:</strong> ${times[2]} <br>
            <strong>Afternoon Time Out:</strong> ${times[3]}
        `;
    } else {
        timeDisplay = times.map((t, i) => `<strong>Time ${i + 1}:</strong> ${t}`).join('<br>');
    }
    

    // Highlight guests
    const guestBadges = guests.map(guest => `<span class="badge bg-primary me-1">${guest}</span>`).join(' ');
    

    // Populate modal
    
    document.getElementById('viewEventName').innerText = title;
    document.getElementById('viewEventVenue').innerText = venue;
    document.getElementById('viewEventDate').innerText = date;
    document.getElementById('viewEventTimes').innerHTML = timeDisplay;
    document.getElementById('viewEventDescGuests').innerHTML = `
        ${description ? `<p>${description}</p>` : ''}
        ${guests.length ? `<div>${guestBadges}</div>` : '<span class="text-muted">No guests listed.</span>'}
    `;
       const memoImage = document.getElementById('memoImage');
const memoDownloadLink = document.getElementById('memoDownloadLink');
const memoUrl = info.event.extendedProps.attached_memo_url;

if (memoUrl) {
    memoImage.src = memoUrl;
    memoImage.alt = "Memo Image";
    memoDownloadLink.href = memoUrl;
    memoDownloadLink.classList.remove('d-none');
    memoImage.classList.remove('d-none');
} else {
    memoImage.src = "";
    memoImage.alt = "No memo attached.";
    memoImage.classList.add('d-none');
    memoDownloadLink.classList.add('d-none');
}

const userRole = document.getElementById('currentUserRole').value;
const studentId = document.getElementById('currentStudentId').value;
const eventId = info.event.id;


if (userRole === 'Member') {
    fetch(`/api/attendance/${eventId}/${studentId}`)
        .then(res => {
            if (!res.ok) throw new Error('No attendance');
            return res.json();
        })
        .then(data => {
            const section = document.getElementById('yourAttendanceSection');
            const details = document.getElementById('attendanceDetails');
            const timeCount = times.length; // taken from event.extendedProps.times

            let attendanceHTML = '';

            if (timeCount === 2) {
                attendanceHTML += `
                    <strong>Your Time In:</strong> ${data.time_in1 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Your Time Out:</strong> ${data.time_out1 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Status:</strong> ${data.status || '<span class="text-muted">N/A</span>'}
                `;
            } else if (timeCount === 4) {
                attendanceHTML += `
                    <strong>Morning In:</strong> ${data.time_in1 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Morning Out:</strong> ${data.time_out1 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Morning Status:</strong> ${data.morning_status || '<span class="text-muted">N/A</span>'}<br><br>
                    <strong>Afternoon In:</strong> ${data.time_in2 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Afternoon Out:</strong> ${data.time_out2 || '<span class="text-muted">N/A</span>'}<br>
                    <strong>Afternoon Status:</strong> ${data.afternoon_status || '<span class="text-muted">N/A</span>'}
                `;
            } else {
                attendanceHTML = `<span class="text-muted">No attendance information available for this format.</span>`;
            }

            section.classList.remove('d-none');
            details.innerHTML = attendanceHTML;
        })
        .catch(() => {
            document.getElementById('yourAttendanceSection').classList.add('d-none');
        });
}
    // Show modal
    new bootstrap.Modal(document.getElementById('viewEventModal')).show();
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

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif

@if($errors->has('event_date'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    });
</script>
@endif
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
