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

    <title>Ticktax Attendance System</title>

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
                <h2 class="fw-bold" style="color: #232946;">OSSD Dashboard</h2>
                <small style="color: #989797;">Home /</small>
                <small style="color: #444444;">Dashboard</small>
            </div>

            <!-- Overview Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4 h-100">
                        <h6 class="text-muted">Total Organizations</h6>
                     <h2 class="text-center">{{ $totalOrganizations }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4 h-100">
                        <h6 class="text-muted">Events Created</h6>
                        <h2 class="text-center">{{ $totalEvents }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4 h-100">
                        <h6 class="text-muted">Registered</h6>
                      <h2 class="text-center"></h2>  <h2>{{ $registeredStudents }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm p-3 rounded-4 h-100">
                        <h6 class="text-muted">Total Students</h6>
                        <h2 class="text-center">{{ $totalStudents }}</h2>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Events per Org Chart -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">Events Created per Organization</h6>
                        <div id="eventsChartData"
                             data-labels='@json(array_keys($eventsPerOrg))'
                             data-values='@json(array_values($eventsPerOrg))'>
                        </div>
                      <div class="chart-wrapper">
    <canvas id="absenteesChart"></canvas>
</div>
                    </div>
                </div>

                <!-- Upcoming Events Expanded -->
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3 rounded-4 h-100">
                        <h6 class="mb-3 text-center">Upcoming Events</h6>
    <ul class="list-group list-group-flush">
      @forelse($upcomingEvents as $event)
        @php
          $times = json_decode($event->times, true);
          $startTime = (!empty($times) && isset($times[0]))
    ? \Carbon\Carbon::createFromFormat('H:i:s', trim($times[0]))->format('g:i A')
              : 'N/A';
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row">
          <div>
            <strong>{{ $event->name }}</strong><br>
            <small class="text-muted">
              {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}
            </small>
          </div>
          <span class="badge 
              @if($loop->index == 0) bg-primary
              @elseif($loop->index == 1) bg-success
              @elseif($loop->index == 2) bg-warning text-dark
              @else bg-secondary
              @endif
              rounded-pill align-self-md-center mt-2 mt-md-0">
            {{ $startTime }}
          </span>
        </li>
      @empty
        <li class="list-group-item text-center">No upcoming events.</li>
      @endforelse
    </ul>
                    </div>
                </div>
            </div>

            <!-- Recently Added Organizations Table -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm p-3 rounded-4">
                        <h6 class="mb-3 text-center">Recently Added Organizations</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Organization Name</th>
                                        <th>Organization Description</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
    @if ($latestOrg)
    <tr>
        <td>1</td>
        <td>{{ $latestOrg->org_name }}</td>
        <td>{{ $latestOrg->description }}</td>

    </tr>
    @else
    <tr>
        <td colspan="2">No organizations found.</td>
    </tr>
    @endif
</tbody>
                            </table>
                        </div>

                        <div class="text-center mt-3" >
                            <a href="/manage_orgs_page" class="btn btn-outline-primary">
                                See More <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>



    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
.chart-wrapper {
    position: relative;
    height: 300px; /* Default desktop height */
}

/* On small screens, allow more height so labels fit */
@media (max-width: 768px) {
    .chart-wrapper {
        height: 400px;
    }
}
</style>
    <script>
    const eventsChartData = document.getElementById('eventsChartData');
const orgLabels = JSON.parse(eventsChartData.dataset.labels);
const orgEvents = JSON.parse(eventsChartData.dataset.values);
const ctx = document.getElementById('absenteesChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: orgLabels,
        datasets: [{
            label: 'Events Created',
            data: orgEvents,
            backgroundColor: orgLabels.map((_, i) => `hsl(${i * 40 % 360}, 70%, 60%)`),
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                ticks: {
                    autoSkip: false,
                    maxRotation: window.innerWidth < 768 ? 45 : 0,
                    minRotation: 0,
                    callback: function(value) {
                        let label = this.getLabelForValue(value);

                        // On small screens, shorten labels to avoid overlap
                        if (window.innerWidth < 768) {
                            return label.length > 8 ? label.slice(0, 8) + '…' : label;
                        }
                        return label; // full label on desktop
                    }
                }
            }
        }
    }
});
const programChartData = document.getElementById('studentProgramData');
const programLabels = JSON.parse(programChartData.dataset.labels);
const programValues = JSON.parse(programChartData.dataset.values);

new Chart(document.getElementById('studentProgramPieChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: programLabels,
        datasets: [{
            data: programValues,
            backgroundColor: programLabels.map((_, i) => `hsl(${i * 60 % 360}, 70%, 60%)`),
            hoverOffset: 30
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 20,
                    padding: 15
                }
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
