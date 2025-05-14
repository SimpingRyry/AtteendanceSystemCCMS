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
    <link rel="stylesheet" href="{{ asset('css/dashboard_page.css') }}">
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
            <!-- Heading -->
            <div class="mb-3">
                <h2 class="fw-bold" style="color: #232946;">Super Admin Dashboard</h2>
                <small style="color: #989797;">Home /</small>
                <small style="color: #444444;">Dashboard</small>
            </div>

            <!-- Overview Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4">
                        <h6 class="text-muted">Total Organizations</h6>
                        <h2>35</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4">
                        <h6 class="text-muted">Events Created</h6>
                        <h2>128</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4">
                        <h6 class="text-muted">Active Users</h6>
                        <h2>2,415</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4">
                        <h6 class="text-muted">Total Fines</h6>
                        <h2>₱25,730</h2>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Organization Activity Chart -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">Organization Activity Overview</h6>
                        <canvas id="absenteesChart" style="height: 300px;"></canvas>
                    </div>
                </div>

                <!-- User Registration Trends -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">User Registration Trends</h6>
                        <canvas id="registrationChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Table & Events Row -->
            <div class="row g-4">
                <!-- Activity Table -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">Recent Activities</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>SuperUser1</td>
                                        <td>Moderator</td>
                                        <td>Created Event</td>
                                        <td>2025-05-10</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Admin2</td>
                                        <td>Org Admin</td>
                                        <td>Updated Organization Info</td>
                                        <td>2025-05-09</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">Upcoming Events</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tech Conference 2025
                                <span class="badge bg-primary rounded-pill">May 20, 2025</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Admin Training Workshop
                                <span class="badge bg-success rounded-pill">May 28, 2025</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Mid-Year Assembly
                                <span class="badge bg-warning rounded-pill text-dark">June 15, 2025</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const absenteeCtx = document.getElementById('absenteesChart').getContext('2d');
        new Chart(absenteeCtx, {
            type: 'bar',
            data: {
                labels: ['Praxis', 'ITS'],
                datasets: [{
                    label: 'Total Absentees',
                    data: [14, 23],
                    backgroundColor: ['#1976d2', '#2e7d32'], 
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const registrationCtx = document.getElementById('registrationChart').getContext('2d');
        new Chart(registrationCtx, {
            type: 'line', // Changed to line chart
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'], // Example months for trend
                datasets: [{
                    label: 'BSIT Registered',
                    data: [200, 300, 350, 400, 450],
                    borderColor: '#42a5f5', // Blue
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'BSIT Unregistered',
                    data: [50, 60, 75, 50, 45],
                    borderColor: '#ef5350', // Red
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'BSIS Registered',
                    data: [300, 330, 350, 400, 450],
                    borderColor: '#66bb6a', // Green
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'BSIS Unregistered',
                    data: [100, 120, 100, 80, 75],
                    borderColor: '#ffca28', // Yellow
                    fill: false,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>
