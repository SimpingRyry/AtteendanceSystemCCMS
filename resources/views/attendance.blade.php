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
        <div class="container inner-glass shadow p-4" id="main_box">
            <div class="mb-4">
                <h2 class="fw-bold" style="color: #232946;">Attendance</h2>
                <small style="color: #989797;">Monitor /</small>
                <small style="color: #444444;">Attendance Records</small>

                @if($currentEvent)
                    <div class="alert alert-info mt-3">
                        <strong>Today's Event:</strong> {{ $currentEvent->name }} at {{ $currentEvent->venue }}
                    </div>
                @else
                    <div class="alert alert-secondary mt-3">
                        No event scheduled for today.
                    </div>
                @endif
            </div>

            @if($currentEvent)
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
                                    @else
                                        <th rowspan="1">Time-In</th>
                                        <th rowspan="1">Time-Out</th>
                                    @endif

                                    <th rowspan="{{ $currentEvent->timeouts == 4 ? 2 : 1 }}">Status</th>
                                </tr>

                                @if($currentEvent->timeouts == 4)
                                <tr>
                                    <th>Time-In</th>
                                    <th>Time-Out</th>
                                    <th>Time-In</th>
                                    <th>Time-Out</th>
                                </tr>
                                @endif
                        </thead>
                        <tbody>
                            <!-- Sample Row (replace with dynamic content) -->
                            @if($currentEvent->timeouts == 4)
                                <tr>
                                    <td>22-0788</td>
                                    <td>Borje, Ver Andre A.</td>
                                    <td>MAIN-BSIT</td>
                                    <td>B</td>
                                    <td>Orientation</td>
                                    <td>2025-05-22</td>
                                    <td>08:05 AM</td> <!-- Time-In AM -->
                                    <td>12:00 PM</td> <!-- Time-Out AM -->
                                    <td>01:10 PM</td> <!-- Time-In PM -->
                                    <td>04:55 PM</td> <!-- Time-Out PM -->
                                    <td><span class="badge bg-warning text-dark">Late</span></td>
                                </tr>
                            @else
                                <tr>
                                    <td>22-0788</td>
                                    <td>Borje, Ver Andre A.</td>
                                    <td>MAIN-BSIT</td>
                                    <td>B</td>
                                    <td>Orientation</td>
                                    <td>2025-05-22</td>
                                    <td>08:05 AM</td>
                                    <td>12:00 PM</td>
                                    <td><span class="badge bg-warning text-dark">Late</span></td>
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

        const rows = document.querySelectorAll('#attendanceTable tbody tr');
        const attendance = [];

        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            attendance.push({
                student_id: cols[0].innerText.trim(),
                name: cols[1].innerText.trim(),
                program: cols[2].innerText.trim(),
                block: cols[3].innerText.trim(),
                event: cols[4].innerText.trim(),
                date: cols[5].innerText.trim(),
                time_in: cols[6].innerText.trim(),
                time_out: cols[7].innerText.trim(),
                status: cols[8].innerText.trim().replace(/^\s+|\s+$/g, '') // Remove spaces
            });
        });

        document.getElementById('attendance_data').value = JSON.stringify(attendance);
        this.submit();
    });
</script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>