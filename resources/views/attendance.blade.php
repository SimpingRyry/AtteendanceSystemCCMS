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

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
    <div class="container outer-box mt-5 pt-5 pb-4">
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
                        <label for="program" class="form-label">Filter by Program</label>
                        <select name="program" id="program" class="form-select">
                            <option value="">All Programs</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSCS">BSCS</option>
                            <!-- Add more as needed -->
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
                                        <th rowspan="1">Time-In</th>
                                        <th rowspan="1">Time-Out</th>
                                        <th rowspan="1">Status</th>
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
                            <!-- Sample Row (replace with dynamic content) -->
                            @if($currentEvent->timeouts == 4)
                                <tr>
                                    
                                </tr>
                            @else
                                <tr>
                                   
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Record Attendance Form -->
                <form method="POST" action="{{ route('record.attendance') }}" id="recordAttendanceForm">
                    @csrf
                    <input type="hidden" name="attendance_data" id="attendance_data">
                    <button type="submit" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-file-pdf"></i> Record Attendance
                    </button>
                </form>
            @else
                <!-- Disabled Message -->
                <div class="alert alert-warning mt-4">
                    <strong>Notice:</strong> Attendance sheet is disabled because there's no event scheduled for today.
                </div>
            @endif
        </div>
    </div>
</main>

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
    fetch("/attendance/live-data")
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#attendanceTable tbody');
            tbody.innerHTML = '';

            const timeoutCount = parseInt(document.getElementById('main_box').dataset.timeouts);
            const hasFourTimeouts = timeoutCount === 4;

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>