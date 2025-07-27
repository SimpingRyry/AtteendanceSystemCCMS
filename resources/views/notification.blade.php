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
@php
use App\Models\Notification;
use Carbon\Carbon;

$user = Auth::user();
$filter = request('filter');

$notificationsQuery = Notification::where('user_id', $user->id);

switch ($filter) {
    case 'today':
        $notificationsQuery->whereDate('created_at', Carbon::today());
        break;
    case 'yesterday':
        $notificationsQuery->whereDate('created_at', Carbon::yesterday());
        break;
    case 'this_week':
        $notificationsQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        break;
    case 'last_week':
        $notificationsQuery->whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ]);
        break;
    case 'this_month':
        $notificationsQuery->whereMonth('created_at', Carbon::now()->month);
        break;
}

$notifications = $notificationsQuery->latest()->paginate(8); // Pagination

$unreadCount = Notification::where('user_id', $user->id)
    ->whereNull('read_at')
    ->count();
@endphp

<main>
  <div class="container outer-box mt-5 pt-5 pb-4">
    <div class="container inner-glass shadow p-4" id="main_box">
      <div class="mb-4">
        <h2 class="fw-bold" style="color: #232946;">Notifications</h2>
        <small style="color: #989797;">User Panel /</small>
        <small style="color: #444444;">My Notifications</small>

        @if($unreadCount > 0)
        <div class="alert alert-info mt-3">
          You have <strong>{{ $unreadCount }}</strong> unread notification{{ $unreadCount > 1 ? 's' : '' }}.
        </div>
        @else
        <div class="alert alert-secondary mt-3">
          You are all caught up!
        </div>
        @endif
      </div>

      <!-- Filter -->
      <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <label for="filter" class="form-label">Filter by Date</label>
            <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
              <option value="">All</option>
              <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
              <option value="yesterday" {{ request('filter') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
              <option value="this_week" {{ request('filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
              <option value="last_week" {{ request('filter') == 'last_week' ? 'selected' : '' }}>Last Week</option>
              <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
            </select>
          </div>
        </div>
      </form>

      <!-- Notification List (stacked like Gmail) -->
      <div class="list-group">
        @forelse($notifications as $note)
        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ $note->read_at ? '' : 'bg-light' }}">
          <div class="ms-2 me-auto">
            <div class="fw-bold">{{ $note->title ?? 'Notification' }}</div>
            <div class="small text-muted">
              {{ \Illuminate\Support\Str::limit($note->message ?? 'No content', 100) }}
            </div>
          </div>
          <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
        </a>
        @empty
        <div class="text-center text-muted py-3">No notifications found{{ $filter ? ' for this filter.' : '.' }}</div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-4">
  <nav>
    {{ $notifications->links('pagination::bootstrap-5') }}
  </nav>
</div>

      <!-- Mark All as Read -->
      <form action="" method="POST" class="mt-3">
        @csrf
        <button class="btn btn-sm btn-secondary" type="submit">
          <i class="fas fa-check-circle"></i> Mark All as Read
        </button>
      </form>
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