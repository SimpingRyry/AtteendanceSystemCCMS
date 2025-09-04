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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/clearance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>Ticktax Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

<main>
    <div class="container outer-box mt-5 pt-5 pb-4">
        <ul class="nav nav-tabs mb-4" id="attendanceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today" type="button" role="tab">Today</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="archive-tab" data-bs-toggle="tab" data-bs-target="#archive" type="button" role="tab">Attendance Archives</button>
            </li>
        </ul>

        <div class="tab-content" id="attendanceTabContent">
            <!-- Today's Attendance Tab -->
            <div class="tab-pane fade show active" id="today" role="tabpanel">
                @if($currentEvent && auth()->user()->org === $currentEvent->org)
                <div class="container inner-glass shadow p-4" id="main_box" data-timeouts="{{ $currentEvent->timeouts }}">
                @endif

                    <div class="mb-4">
                        <h2 class="fw-bold" style="color: #232946;">Attendance</h2>
                        <small style="color: #989797;">Monitor /</small>
                        <small style="color: #444444;">Attendance Records</small>

                        @if($currentEvent && auth()->user()->org === $currentEvent->org)
                            <div class="alert alert-info mt-3">
                                <strong>Today's Event:</strong> {{ $currentEvent->name }} at {{ $currentEvent->venue }}
                            </div>
                        @else
                            <div class="alert alert-secondary mt-3">
                                No event scheduled for today.
                            </div>
                        @endif
                    </div>

                    @if($currentEvent && auth()->user()->org === $currentEvent->org)
                        <!-- Filter Form -->
                       <form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="status" class="form-label">Filter by Status</label>
        <select name="status" id="status" class="form-select">
            <option value="">All Status</option>
            <option value="On Time">On Time</option>
            <option value="Late">Late</option>
            <option value="Absent">Absent</option>
        </select>
    </div>
</form>
                        <!-- Attendance Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="attendanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Student ID</th>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Name</th>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Program</th>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Block</th>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Event</th>
                                        <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Date</th>

                                        @if($currentEvent->timeouts == 4)
                                            <th colspan="2" class="text-center">Morning</th>
                                            <th colspan="2" class="text-center">Afternoon</th>
                                            <th colspan="2" class="text-center">Status</th>
                                        @else
                                            <th>Time-In</th>
                                            <th>Time-Out</th>
                                            <th>Status</th>
                                        @endif
                                    </tr>

                                    @if($currentEvent->timeouts == 4)
                                    <tr>
                                        <th>Time-In</th>
                                        <th>Time-Out</th>
                                        <th>Time-In</th>
                                        <th>Time-Out</th>
                                        <th>Morning</th>
                                        <th>Afternoon</th>
                                    </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    <!-- Your dynamic data -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Record Attendance Form -->
                        
                    @else
                        <div class="alert alert-warning mt-4">
                            <strong>Notice:</strong> Attendance sheet is disabled because there's no event scheduled for today.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attendance Archives Tab -->
         <div class="tab-pane fade" id="archive" role="tabpanel">
    <div class="container inner-glass shadow p-4">
        <h4 class="fw-bold mb-3">Attendance Archives</h4>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="archiveEvent" class="form-label">Select Past Event</label>
                <select name="archive_event_id" id="archiveEvent" class="form-select">
                    <option value="">-- Select an Event --</option>
                    @foreach($pastEvents as $event)
                        <option value="{{ $event->id }}">
                            {{ $event->name }} ({{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Archive results injected here -->
        <div id="archiveResults"></div>
    </div>
</div>
        </div>
    </div>
</main>



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



<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#archiveEvent').on('change', function () {
        const eventId = $(this).val();
        if (eventId) {
            $.ajax({
                url: '/attendance/archive/ajax/' + eventId,
                type: 'GET',
                success: function (response) {
                    $('#archiveResults').html(response);
                },
                error: function () {
                    $('#archiveResults').html('<div class="alert alert-danger">Failed to load archive data.</div>');
                }
            });
        } else {
            $('#archiveResults').empty();
        }
    });
});
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif
<script>
document.getElementById('recordAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const timeoutCount = parseInt(document.getElementById('main_box').dataset.timeouts);
    const hasFourTimeouts = timeoutCount === 4;

    const rows = document.querySelectorAll('#attendanceTable tbody tr');
    const attendance = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td');

        if (hasFourTimeouts) {
            attendance.push({
                student_id: cols[0].innerText.trim(),
                name: cols[1].innerText.trim(),
                program: cols[2].innerText.trim(),
                block: cols[3].innerText.trim(),
                event: cols[4].innerText.trim(),
                date: cols[5].innerText.trim(),
                time_in1: cols[6].innerText.trim(),
                time_out1: cols[7].innerText.trim(),
                time_in2: cols[8].innerText.trim(),
                time_out2: cols[9].innerText.trim(),
                status_morning: cols[10].innerText.trim(),
                status_afternoon: cols[11].innerText.trim()
            });
        } else {
            attendance.push({
                student_id: cols[0].innerText.trim(),
                name: cols[1].innerText.trim(),
                program: cols[2].innerText.trim(),
                block: cols[3].innerText.trim(),
                event: cols[4].innerText.trim(),
                date: cols[5].innerText.trim(),
                time_in1: cols[6].innerText.trim(),
                time_out1: cols[7].innerText.trim(),
                status: cols[8].innerText.trim()
            });
        }
    });

    document.getElementById('attendance_data').value = JSON.stringify(attendance);
    this.submit();
});
</script>

<script>
setInterval(() => {
    // Get selected status from dropdown
    let status = document.getElementById('status')?.value || '';

    fetch(`/attendance/live-data?status=${encodeURIComponent(status)}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#attendanceTable tbody');
            tbody.innerHTML = '';

            const timeoutCount = parseInt(document.getElementById('main_box').dataset.timeouts);
            const hasFourTimeouts = timeoutCount === 4;

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="12" class="text-center">No records found</td></tr>`;
                return;
            }

            data.forEach(row => {
                if (hasFourTimeouts) {
                    tbody.innerHTML += `
                        <tr>
                            <td>${row.student_id}</td>
                            <td>${row.name}</td>
                            <td>${row.program}</td>
                            <td>${row.block}</td>
                            <td>${row.event}</td>
                            <td>${row.date}</td>
                            <td>${row.time_in1 ?? ''}</td>
                            <td>${row.time_out1 ?? ''}</td>
                            <td>${row.time_in2 ?? ''}</td>
                            <td>${row.time_out2 ?? ''}</td>
                            <td>${row.status_morning ?? ''}</td>
                            <td>${row.status_afternoon ?? ''}</td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML += `
                        <tr>
                            <td>${row.student_id}</td>
                            <td>${row.name}</td>
                            <td>${row.program}</td>
                            <td>${row.block}</td>
                            <td>${row.event}</td>
                            <td>${row.date}</td>
                            <td>${row.time_in1 ?? ''}</td>
                            <td>${row.time_out1 ?? ''}</td>
                            <td>${row.status ?? ''}</td>
                        </tr>
                    `;
                }
            });
        });
}, 1000);
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@include('partials.excuse_modal')




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>