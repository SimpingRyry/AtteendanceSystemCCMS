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
        <h2 class="fw-bold" style="color: #232946;">Dashboard</h2>
        <small style="color: #989797;">Home /</small>
        <small style="color: #444444;">Dash</small>
      </div>

      <!-- Fines Filter -->
      <form class="row g-2 mb-3">
        <div class="col-auto">
          <select class="form-select" id="filterYear">
            <option selected disabled>Year</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
          </select>
        </div>
        <div class="col-auto">
          <select class="form-select" id="filterMonth">
            <option selected disabled>Month</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-primary" onclick="filterFines()">Filter</button>
        </div>
      </form>

      <!-- Overview Cards -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card text-center shadow-sm p-3 rounded-4">
            <h6 class="text-muted">Total Students</h6>
            <h2>1,024</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm p-3 rounded-4">
            <h6 class="text-muted">Registered</h6>
            <h2>867</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm p-3 rounded-4">
            <h6 class="text-muted">Unregistered</h6>
            <h2>157</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm p-3 rounded-4">
            <h6 class="text-muted">Total Fines</h6>
            <h2 id="totalFines">₱0</h2>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row g-4 mb-4">
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Absentees Report</h6>
            <canvas id="absenteesChart" style="height: 300px;"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Registration Status</h6>
            <div style="height: 300px; width: 220px; margin: auto;">
              <canvas id="registrationChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Table & Events Row -->
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Recent Payments</h6>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Org</th>
                    <th>Position</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>Jane Doe</td>
                    <td>2023001</td>
                    <td>Praxis</td>
                    <td>President</td>
                    <td>2025-04-20</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>John Smith</td>
                    <td>2023012</td>
                    <td>ITS</td>
                    <td>Member</td>
                    <td>2025-04-19</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Upcoming Events</h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Praxis General Assembly
                <span class="badge bg-primary rounded-pill">April 25, 2025</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                ITS Hackathon
                <span class="badge bg-success rounded-pill">April 30, 2025</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Leadership Seminar
                <span class="badge bg-warning rounded-pill text-dark">May 5, 2025</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>
<script>
  const finesData = {
    "2025-04": 12450,
    "2025-03": 8650,
    "2024-12": 10800,
    "2024-11": 9400
  };

  function filterFines() {
    const year = document.getElementById('filterYear').value;
    const month = document.getElementById('filterMonth').value;
    const key = `${year}-${month}`;
    const amount = finesData[key] || 0;
    document.getElementById('totalFines').innerText = `₱${amount.toLocaleString()}`;
  }

  // Set current month/year on load
  document.addEventListener("DOMContentLoaded", () => {
    const today = new Date();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const year = today.getFullYear();
    const key = `${year}-${month}`;
    const amount = finesData[key] || 0;
    document.getElementById('totalFines').innerText = `₱${amount.toLocaleString()}`;
  });
</script>
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

        const ctx = document.getElementById('registrationChart').getContext('2d');
    const registrationChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Registered', 'Unregistered'],
            datasets: [{
                data: [120, 30],
                backgroundColor: ['#4CAF50', '#F44336'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
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
