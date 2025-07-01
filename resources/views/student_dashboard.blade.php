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

      <!-- Hidden Data for JS -->
      <div id="dashboard-data" hidden>
        <div id="completedEventsValue">{{ $completedEvents }}</div>
        <div id="myEventsValue">{{ $upcomingEventsCount }}</div>
        <div id="myAttendanceValue">{{ $attendancePercentage }}</div>
        <div id="totalFinesValue">{{ $totalFines }}</div>
        <div id="attendedValue">{{ $attended }}</div>
        <div id="absentValue">{{ $absent }}</div>
        <div id="monthlyFines">{{ $monthlyFines }}</div>

        <div id="recentPaymentsJson">{!! json_encode($recentPayments->map(function($t){
          return [
            'title' => $t->title,
            'amount' => $t->fine_amount,
            'date' => \Carbon\Carbon::parse($t->date)->format('F d, Y')
          ];
        })) !!}</div>

        <div id="upcomingEventsJson">{!! json_encode($upcomingEvents->map(function($e){
          return [
            'title' => $e->name,
            'date' => \Carbon\Carbon::parse($e->date)->format('F d, Y')
          ];
        })) !!}</div>
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
            <h6 class="text-muted">Upcoming Events</h6>
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
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Fines Overview</h6>
            <canvas id="myAttendanceChart" style="height: 300px;"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Event Participation</h6>
            <div class="chart-container">
              <canvas id="eventParticipationChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Tables -->
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4"style="min-height: 200px">
            <h6 class="mb-3 text-center">Recent Payments</h6>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                  
                    <th>Amount</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="recentPaymentsBody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4"style="min-height: 200px">
            <h6 class="mb-3 text-center">Upcoming Events</h6>
            <ul class="list-group list-group-flush" id="upcomingEventsList"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const completedEvents = parseInt(document.getElementById('completedEventsValue').textContent);
  const myEvents = parseInt(document.getElementById('myEventsValue').textContent);
  const attendance = parseInt(document.getElementById('myAttendanceValue').textContent);
  const totalFines = parseFloat(document.getElementById('totalFinesValue').textContent);
  const attendedCount = parseInt(document.getElementById('attendedValue').textContent);
  const absentCount = parseInt(document.getElementById('absentValue').textContent);
  const recentPayments = JSON.parse(document.getElementById('recentPaymentsJson').textContent);
  const upcomingEvents = JSON.parse(document.getElementById('upcomingEventsJson').textContent);
  const finesData = JSON.parse(document.getElementById('monthlyFines').textContent);

  const finesLabels = Object.keys(finesData).map(date => {
  const [year, month] = date.split("-");
  return new Date(year, month - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
});
const finesValues = Object.values(finesData);

  document.getElementById('completedEvents').textContent = completedEvents;
  document.getElementById('myEvents').textContent = myEvents;
  document.getElementById('myAttendance').textContent = attendance + '%';
  document.getElementById('totalFines').textContent = totalFines;

  const tableBody = document.getElementById('recentPaymentsBody');
  tableBody.innerHTML = "";
  recentPayments.forEach((payment, index) => {
    const row = `<tr>
      <td>${index + 1}</td>
     
      <td>₱${payment.amount}</td>
      <td>${payment.date}</td>
    </tr>`;
    tableBody.innerHTML += row;
  });

const eventsList = document.getElementById('upcomingEventsList');
eventsList.innerHTML = "";

if (upcomingEvents.length === 0) {
  eventsList.innerHTML = `<li class="list-group-item text-center text-muted">No upcoming events.</li>`;
} else {
  upcomingEvents.forEach(event => {
    const badgeColor = "primary"; // default color for all events

    const listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
      ${event.title}
      <span class="badge bg-${badgeColor} rounded-pill">${event.date}</span>
    </li>`;
    eventsList.innerHTML += listItem;
  });
}

 new Chart(document.getElementById('myAttendanceChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: finesLabels,
    datasets: [{
      label: 'Monthly Fines (₱)',
      data: finesValues,
      backgroundColor: '#f87171'
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => '₱' + value
        }
      }
    }
  }
});

  new Chart(document.getElementById('eventParticipationChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Attended', 'Missed'],
      datasets: [{
        data: [attendedCount, absentCount],
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
