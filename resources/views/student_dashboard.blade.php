<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/dashboard_page.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

  <title>CCMS Attendance System</title>

  <!-- Inline styling for charts -->
  <style>
    .chart-container {
      height: 300px;
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }
  </style>
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
          <h2 class="fw-bold" style="color: #232946;">Student Dashboard</h2>
          <small style="color: #989797;">Home /</small>
          <small style="color: #444444;">Dashboard</small>
        </div>

        <!-- Overview Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 rounded-4">
              <h6 class="text-muted">Completed Events</h6>
              <h2><span id="completedEvents">0</span></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 rounded-4">
              <h6 class="text-muted">My Events</h6>
              <h2><span id="myEvents">0</span></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 rounded-4">
              <h6 class="text-muted">My Attendance</h6>
              <h2><span id="myAttendance">0%</span></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 rounded-4">
              <h6 class="text-muted">Total Fines</h6>
              <h2>₱<span id="totalFines">0</span></h2>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
          <!-- My Attendance Chart -->
          <div class="col-lg-6">
            <div class="card shadow-sm p-3 rounded-4">
              <h6 class="mb-3 text-center">My Attendance Overview</h6>
              <canvas id="myAttendanceChart" style="height: 300px;"></canvas>
            </div>
          </div>

          <!-- Event Participation -->
          <div class="col-lg-6">
            <div class="card shadow-sm p-3 rounded-4">
              <h6 class="mb-3 text-center">Event Participation</h6>
              <div class="chart-container">
                <canvas id="eventParticipationChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Table & Events Row -->
        <div class="row g-4">
          <!-- Recent Payments -->
          <div class="col-lg-6">
            <div class="card shadow-sm p-3 rounded-4">
              <h6 class="mb-3 text-center">Recent Payments</h6>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Event/Org</th>
                      <th>Amount</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody id="recentPaymentsBody">
                    <!-- Populated via JS -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Upcoming Events -->
          <div class="col-lg-6">
            <div class="card shadow-sm p-3 rounded-4">
              <h6 class="mb-3 text-center">Upcoming Events</h6>
              <ul class="list-group list-group-flush" id="upcomingEventsList">
                <!-- Populated via JS -->
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <script>
    // Dummy data
    const dashboardData = {
      completedEvents: 4,
      myEvents: 5,
      attendance: 92,
      totalFines: 250,
      recentPayments: [
        { id: 1, title: "Praxis Gen Assembly", amount: 50, date: "2025-04-20" },
        { id: 2, title: "ITS Hackathon", amount: 100, date: "2025-04-19" }
      ],
      upcomingEvents: [
        { title: "Praxis General Assembly", date: "April 25, 2025" },
        { title: "ITS Hackathon", date: "April 30, 2025" },
        { title: "Leadership Seminar", date: "May 5, 2025" }
      ]
    };

    // Populate values
    document.getElementById('completedEvents').textContent = dashboardData.completedEvents;
    document.getElementById('myEvents').textContent = dashboardData.myEvents;
    document.getElementById('myAttendance').textContent = dashboardData.attendance + '%';
    document.getElementById('totalFines').textContent = dashboardData.totalFines;

    // Populate recent payments
    const tableBody = document.getElementById('recentPaymentsBody');
    tableBody.innerHTML = "";
    dashboardData.recentPayments.forEach(payment => {
      const row = `<tr>
        <td>${payment.id}</td>
        <td>${payment.title}</td>
        <td>₱${payment.amount}</td>
        <td>${payment.date}</td>
      </tr>`;
      tableBody.innerHTML += row;
    });

    // Populate upcoming events
    const eventsList = document.getElementById('upcomingEventsList');
    eventsList.innerHTML = "";
    dashboardData.upcomingEvents.forEach(event => {
      const badgeColor = event.title.includes("Hackathon") ? "success" :
        event.title.includes("Leadership") ? "warning text-dark" :
        "primary";
      const listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
        ${event.title}
        <span class="badge bg-${badgeColor} rounded-pill">${event.date}</span>
      </li>`;
      eventsList.innerHTML += listItem;
    });

    // Attendance Chart (Months)
    const ctx1 = document.getElementById('myAttendanceChart').getContext('2d');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr'],
        datasets: [{
          label: 'Attendance (%)',
          data: [88, 95, 90, 92],
          backgroundColor: '#007bff'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: 100
          }
        }
      }
    });

    // Event Participation Chart (Pie)
    const ctx2 = document.getElementById('eventParticipationChart').getContext('2d');
    new Chart(ctx2, {
      type: 'pie',
      data: {
        labels: ['Attended', 'Missed'],
        datasets: [{
          data: [4, 1],
          backgroundColor: ['#28a745', '#dc3545']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>
</body>
</html>
