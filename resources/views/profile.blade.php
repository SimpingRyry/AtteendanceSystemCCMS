

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>

</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <!------------------------------------------------------ MAIN_BOX -------------------------------------------------------->
<main>
  <div class="container mt-5 pt-5 pb-4">
    <div class="row mb-4 g-4 align-items-stretch">
      <!-- Profile Card -->
      <div class="col-md-4 d-flex">
        <div class="card text-center shadow-sm w-100 h-100 border-0 rounded-4">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="mb-3" style="width: 150px; height: 150px;">
              <img src="{{ asset('uploads/' . $profile->photo) }}" class="rounded-circle w-100 h-100" style="object-fit: cover;" alt="Profile Picture">
            </div>
            <h5 class="card-title mb-1">{{ $profile->name }}</h5>
            <p class="text-muted">{{ $profile->position }}</p>
          </div>
        </div>
      </div>

      <!-- Profile Details -->
      <div class="col-md-8">
        <div class="card shadow-sm h-100 border-0 rounded-4">
          <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
  @csrf

  <!-- Name (readonly) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Name:</label>
    <div class="col-sm-9">
      <input type="text" name="name" class="form-control-plaintext border rounded px-3 bg-light" value="{{ $profile->name }}" readonly>
    </div>
  </div>
  <hr>

  <!-- Program (readonly) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Program:</label>
    <div class="col-sm-9">
      <input type="text" name="program" class="form-control-plaintext border rounded px-3 bg-light" value="{{ $profile->program }}" readonly>
    </div>
  </div>
  <hr>

  <!-- Position (readonly) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Position:</label>
    <div class="col-sm-9">
      <input type="text" name="position" class="form-control-plaintext border rounded px-3 bg-light" value="{{ $profile->position }}" readonly>
    </div>
  </div>
  <hr>

  <!-- Email (readonly) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Email:</label>
    <div class="col-sm-9">
      <input type="email" name="email" class="form-control-plaintext border rounded px-3 bg-light" value="{{ $profile->email }}" readonly>
    </div>
  </div>
  <hr>

  <!-- Mobile No. (editable) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Mobile No.:</label>
    <div class="col-sm-9">
      <input type="text" name="mobile" class="form-control border rounded px-3" value="{{ $profile->mobile }}">
    </div>
  </div>
  <hr>

  <!-- Address (readonly) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Address:</label>
    <div class="col-sm-9">
      <input type="text" name="address" class="form-control-plaintext border rounded px-3 bg-light" value="{{ $profile->address }}" readonly>
    </div>
  </div>
  <hr>

  <!-- Password (editable) -->
  <div class="row mb-3">
    <label class="col-sm-3 col-form-label fw-semibold">Password:</label>
    <div class="col-sm-9">
      <input type="password" name="password" class="form-control border rounded px-3" placeholder="••••••••">
      <small class="text-muted">Leave blank to keep current password.</small>
    </div>
  </div>

  <!-- Save Button -->
  <div class="text-end">
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </div>
</form>
          </div>
        </div>
      </div>
    </div>

    <!-- Hidden Attendance Stats -->
    <div id="attendance-data" hidden>
      <div id="attended">{{ $attended }}</div>
      <div id="late">{{ $late }}</div>
      <div id="absent">{{ $absent }}</div>
    </div>

    <!-- Charts Row -->
   <div class="row g-4">
  <!-- Attendance Breakdown (Full Width) -->
  <div class="col-md-12">
    <div class="card shadow-sm h-100 border-0 rounded-4">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Attendance Breakdown</h6>
        <canvas id="barChart" height="100" style="max-height: 200px;"></canvas>
      </div>
    </div>
  </div>
</div>
  </div>
</main>

<!-- Chart Initialization Script -->
<script>
  const attended = parseInt(document.getElementById('attended').textContent);
  const late = parseInt(document.getElementById('late').textContent);
  const absent = parseInt(document.getElementById('absent').textContent);

  const ctxBar = document.getElementById('barChart');
  const barChart = new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: ['Events Attended', 'Late Attendance', 'Did not attend'],
    datasets: [{
      label: 'Count',
      data: [attended, late, absent],
      backgroundColor: ['#4ade80', '#facc15', '#f87171']
    }]
  },
  options: {
    indexAxis: 'y',
    layout: {
      padding: 10
    },
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      x: {
        beginAtZero: true,
        ticks: {
          stepSize: 1
        }
      }
    }
  }
});
</script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>

</html>
