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

      thead.table-dark th {
    color: #fff !important;
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
            'date' => \Carbon\Carbon::parse($e->event_date)->format('F d, Y'),
            'times' => json_decode($e->times, true)
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

      <!-- Tables Row -->
      <div class="row g-4">
        <!-- Recent Payments -->
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4" style="min-height: 245px">
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

        <!-- Upcoming Events -->
        <div class="col-lg-6">
          <div class="card shadow-sm p-3 rounded-4" style="min-height: 200px">
            <h6 class="mb-3 text-center">Upcoming Events</h6>
            <ul class="list-group list-group-flush" id="upcomingEventsList"></ul>
          </div>
        </div>
      </div>

      <!-- Evaluation Section -->
      <div class="row g-4 mt-1">
        <div class="col-12">
          <div class="card shadow-sm p-3 rounded-4">
            <h6 class="mb-3 text-center">Evaluation</h6>
            @if(session('success'))
              <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
              <table class="table table-bordered mt-2">
                <thead class="table-dark text-white">
                  <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Event Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($assignments as $assign)
                  <tr>
                    <td>{{ $assign->event->name ?? '—' }}</td>
                    <td>{{ $assign->evaluation->title }}</td>
                    <td>{{ Str::limit($assign->evaluation->description, 80) }}</td>
                    <td>
                      @if($assign->attendance && $assign->attendance->is_answered)
                        <button class="btn btn-sm btn-success show-evaluation-btn" 
                          data-id="{{ $assign->evaluation->id }}" 
                          data-event-id="{{ $assign->event->id }}">
                          Show
                        </button>
                      @else
                        <button class="btn btn-sm btn-primary evaluation-card" 
                          data-id="{{ $assign->evaluation->id }}" 
                          data-event-id="{{ $assign->event->id }}">
                          Answer
                        </button>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">No evaluations available.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>


<div class="modal fade" id="evalModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="evalForm" class="modal-content">
      @csrf
      <input type="hidden" name="evaluation_id" id="evaluation_id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p id="modalDesc" class="text-muted"></p>
        <div id="modalQuestions"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewEvaluationModal" tabindex="-1" aria-labelledby="viewEvaluationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewEvaluationModalLabel">Evaluation Answers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="evaluationAnswersContent">
        <!-- Answers will be injected via JS -->
      </div>
    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.show-evaluation-btn').forEach(button => {
    button.addEventListener('click', function () {
      const evalId = this.getAttribute('data-id');
      const eventId = this.getAttribute('data-event-id');

      fetch(`/evaluation-answers/${evalId}/${eventId}`)
        .then(res => res.json())
        .then(data => {
          let html = '';
          data.forEach(item => {
            html += `
              <div class="mb-3">
                <strong>Q: ${item.question}</strong>
                <p class="ms-3">Answer: ${item.answer}</p>
              </div>
            `;
          });

          document.getElementById('evaluationAnswersContent').innerHTML = html;
          new bootstrap.Modal(document.getElementById('viewEvaluationModal')).show();
        });
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const cards = document.querySelectorAll('.evaluation-card');
  const modal = new bootstrap.Modal(document.getElementById('evalModal'));

  cards.forEach(card => {
    card.addEventListener('click', async () => {
      const id = card.dataset.id;
      const res = await fetch(`/student/evaluation/${id}/json`);
      if (!res.ok) return alert('Failed to load evaluation.');

      const data = await res.json();
      document.getElementById('modalTitle').textContent = data.title;
      document.getElementById('modalDesc').textContent = data.description || '—';
      document.getElementById('evaluation_id').value = data.id;

      const container = document.getElementById('modalQuestions');
      container.innerHTML = '';

      data.questions.forEach((q, i) => {
        const base = `responses[${i}]`;
        let html = `
          <div class="mb-4">
            <input type="hidden" name="${base}[question_id]" value="${q.id}">
            <label class="form-label fw-semibold">${q.order}. ${q.question}</label>
        `;

        switch (q.type) {
          case 'short':
            html += `<input type="text" class="form-control" name="${base}[answer]" required>`;
            break;
          case 'textarea':
            html += `<textarea class="form-control" name="${base}[answer]" rows="3" required></textarea>`;
            break;
          case 'mcq':
            q.options.forEach(opt => {
              html += `
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="${base}[answer]" value="${opt}" required>
                  <label class="form-check-label">${opt}</label>
                </div>`;
            });
            break;
          case 'checkbox':
            q.options.forEach(opt => {
              html += `
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="${base}[answer][]" value="${opt}">
                  <label class="form-check-label">${opt}</label>
                </div>`;
            });
            break;
        }

        container.insertAdjacentHTML('beforeend', html + '</div>');
      });

      modal.show();
    });
  });

  // Form submission via AJAX
  document.getElementById('evalForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  try {
    const res = await fetch('/student/evaluation/submit', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
      },
      body: formData
    });

    if (res.ok) {
      alert('Your responses were submitted!');
      bootstrap.Modal.getInstance(document.getElementById('evalModal')).hide();
      form.reset();
    } else {
      const errorText = await res.text();
      alert('Error submitting answers: ' + errorText);
      console.error('Submit error:', errorText);
    }
  } catch (error) {
    alert('Network or server error: ' + error.message);
    console.error(error);
  }
});
});
</script>
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
  upcomingEvents.forEach((event, index) => {
    const eventDate = new Date(event.date);
    const formattedDate = eventDate.toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });

    let startTime = 'N/A';
    try {
      const times = event.times;
      if (times.length > 0) {
        const [hours, minutes] = times[0].split(':');
        const timeObj = new Date();
        timeObj.setHours(parseInt(hours));
        timeObj.setMinutes(parseInt(minutes));
        startTime = timeObj.toLocaleTimeString('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        });
      }
    } catch (e) {
      console.error("Invalid time format:", event.times);
    }

    let badgeColor = 'secondary';
    if (index === 0) badgeColor = 'primary';
    else if (index === 1) badgeColor = 'success';
    else if (index === 2) badgeColor = 'warning text-dark';

    const listItem = `
      <li class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row">
        <div>
          <strong>${event.title}</strong><br>
          <small class="text-muted">${formattedDate}</small>
        </div>
        <span class="badge bg-${badgeColor} rounded-pill align-self-md-center mt-2 mt-md-0">${startTime}</span>
      </li>
    `;

    eventsList.innerHTML += listItem;
  });

  // Add the See More button
  const seeMoreItem = `
    <li class="list-group-item text-center">
      <a href="/events" class="btn btn-outline-primary btn-sm ">
        See More <i class="bi bi-arrow-right-circle"></i>
      </a>
    </li>
  `;
  eventsList.innerHTML += seeMoreItem;
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
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
