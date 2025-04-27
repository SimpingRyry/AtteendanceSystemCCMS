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
      <h4 class="mb-4 text-center glow-text">D A S H B O A R D</h4>

      <!-- First Row: Overview Cards -->
      <div class="row g-4 px-2">
        <div class="col-12">
          <div class="p-4 glassy-header rounded-4 shadow">
            <h4 class="mb-4">Overview</h4>
            <div class="row g-3">

              <!-- Total Students -->
              <div class="col-md-6 col-xl-3">
                <div class="card glassy-card p-3 shadow-sm border-0">
                  <div class="d-flex justify-content-between w-100 align-items-start mb-3">
                    <div class="icon-box d-flex align-items-center justify-content-center">
                      <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div class="growth-badge bg-success text-white px-2 py-1 rounded-pill small fw-semibold">+4%</div>
                  </div>
                  <div class="d-flex justify-content-between w-100 align-items-end">
                    <span class="label-text">Total Students</span>
                    <span class="number fs-5 fw-semibold">1,024</span>
                  </div>
                </div>
              </div>

              <!-- Registered -->
              <div class="col-md-6 col-xl-3">
                <div class="card glassy-card p-3 shadow-sm border-0">
                  <div class="d-flex justify-content-between w-100 align-items-start mb-3">
                    <div class="icon-box d-flex align-items-center justify-content-center">
                      <i class="fas fa-user-check fa-lg"></i>
                    </div>
                    <div class="growth-badge bg-success text-white px-2 py-1 rounded-pill small fw-semibold">+6%</div>
                  </div>
                  <div class="d-flex justify-content-between w-100 align-items-end">
                    <span class="label-text">Registered</span>
                    <span class="number fs-5 fw-semibold">867</span>
                  </div>
                </div>
              </div>

              <!-- Unregistered -->
              <div class="col-md-6 col-xl-3">
                <div class="card glassy-card p-3 shadow-sm border-0">
                  <div class="d-flex justify-content-between w-100 align-items-start mb-3">
                    <div class="icon-box d-flex align-items-center justify-content-center">
                      <i class="fas fa-user-times fa-lg"></i>
                    </div>
                    <div class="growth-badge bg-danger text-white px-2 py-1 rounded-pill small fw-semibold">-2%</div>
                  </div>
                  <div class="d-flex justify-content-between w-100 align-items-end">
                    <span class="label-text">Unregistered</span>
                    <span class="number fs-5 fw-semibold">157</span>
                  </div>
                </div>
              </div>

              <!-- Total Fines -->
              <div class="col-md-6 col-xl-3">
                <div class="card glassy-card p-3 shadow-sm border-0">
                  <div class="d-flex justify-content-between w-100 align-items-start mb-3">
                    <div class="icon-box d-flex align-items-center justify-content-center">
                      <i class="fas fa-coins fa-lg"></i>
                    </div>
                    <div class="growth-badge bg-success text-white px-2 py-1 rounded-pill small fw-semibold">+8%</div>
                  </div>
                  <div class="d-flex justify-content-between w-100 align-items-end">
                    <span class="label-text">Total Fines</span>
                    <span class="number fs-5 fw-semibold">₱12,450</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Second Row: Absentees Chart and Registration Chart -->
      <div class="row g-4 px-2 mt-2">

        <!-- Absentees Report -->
        <div class="col-lg-6 col-xl-6">
          <div class="p-4 glassy-header rounded-4 shadow h-100">
            <h4 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Absentees Report</h4>
            <canvas id="absenteesChart" style="height: 280px;"></canvas>
          </div>
        </div>

        <!-- BSIT and BSIS Registration Status Chart -->
        <div class="col-lg-6 col-xl-6">
          <div class="p-4 glassy-header rounded-4 shadow h-100">
            <h4 class="mb-4"><i class="fas fa-chart-pie me-2"></i>BSIT & BSIS Registration Status</h4>
            <canvas id="registrationChart" style="height: 280px;"></canvas>
          </div>
        </div>

      </div>

      <!-- Third Row: Payments Table -->
      <div class="row g-4 px-2 mt-2">
        <div class="col-lg-6 col-xl-6">
          <div class="p-4 glassy-header rounded-4 shadow h-100">
            <h4 class="mb-4"><i class="fas fa-money-check-alt me-2"></i>Payments</h4>
            <div class="table-responsive">
              <table class="table table-striped table-hover table-bordered rounded-3 overflow-hidden">
                <thead class="table-primary">
                  <tr>
                    <th>No.</th>
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

        <!-- Upcoming Events -->
        <div class="col-lg-6 col-xl-6">
          <div class="p-4 glassy-header rounded-4 shadow h-100">
            <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Upcoming Events</h4>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bullhorn me-2 text-primary"></i> Praxis General Assembly</span>
                <span class="badge bg-primary rounded-pill">April 25, 2025</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-2 text-success"></i> ITS Hackathon</span>
                <span class="badge bg-success rounded-pill">April 30, 2025</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-graduation-cap me-2 text-warning"></i> Leadership Seminar</span>
                <span class="badge bg-warning rounded-pill text-dark">May 5, 2025</span>
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

  
  const ctx = document.getElementById('absenteesChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Praxis', 'ITS'],
      datasets: [{
        label: 'Total Absentees',
        data: [14, 23],
        backgroundColor: ['#42a5f5', '#66bb6a'],
        borderRadius: 8
      }]
    },
    options: {
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 5
          }
        }
      }
    }
  });
</script>
<script>
   const ctz = document.getElementById('registrationChart').getContext('2d');
  const registrationChart = new Chart(ctz, {
    type: 'bar', // you can change to 'pie' or 'doughnut' if you want
    data: {
      labels: ['BSIT Registered', 'BSIT Unregistered', 'BSIS Registered', 'BSIS Unregistered'],
      datasets: [{
        label: 'Number of Students',
        data: [400, 50, 467, 107], // <--- You can adjust numbers based on your real data
        backgroundColor: [
          'rgba(75, 192, 192, 0.7)',
          'rgba(255, 99, 132, 0.7)',
          'rgba(153, 102, 255, 0.7)',
          'rgba(255, 159, 64, 0.7)'
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
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