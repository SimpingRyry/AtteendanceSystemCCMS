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
    <link rel="stylesheet" href="{{ asset('css/logs.css') }}">
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
      <!-- Page Title and Tabs -->
      <div class="mb-4">
        <div class="mb-4">
          <h2 class="fw-bold" style="color: #232946;">Logs</h2>
          <small style="color: #989797;">Manage /</small>
          <small style="color: #444444;">Logs</small>
        </div>
        <ul class="nav nav-tabs" id="logsTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#userLogs" type="button" role="tab">User</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#eventLogs" type="button" role="tab">Events</button>
          </li>

           <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#paymentLogs" type="button" role="tab">Payments</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="other-tab" data-bs-toggle="tab" data-bs-target="#otherLogs" type="button" role="tab">Others</button>
          </li>
        </ul>
      </div>

      <!-- Tab Content -->
      <div class="tab-content" id="logsTabContent">
        <!-- 🟦 User Logs Tab -->
        <div class="tab-pane fade show active" id="userLogs" role="tabpanel">
          <h4 class="fw-bold mt-4" style="color:#232946;">User Logs</h4>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Description</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                  @foreach ($userLogs as $log)
    <tr>
      <td>{{ $log->user ?? 'N/A' }}</td>
      <td>{{ $log->action }}</td>
      <td>{{ $log->description }}</td>
      <td>{{ \Carbon\Carbon::parse($log->date_time)->format('Y-m-d H:i:s') }}</td>
    </tr>
  @endforeach
              
              </tbody>
            </table>
          </div>
        </div>

        <!-- 🟩 Event Logs Tab -->
        <div class="tab-pane fade" id="eventLogs" role="tabpanel">
          <h4 class="fw-bold mt-4" style="color:#232946;">Event Logs</h4>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Description</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>

                @foreach ($eventLogs as $log)
                  <tr>
                    <td>{{ $log->user }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                  </tr>
                @endforeach


          
              </tbody>
            </table>
          </div>
        </div>

         <div class="tab-pane fade" id="paymentLogs" role="tabpanel">
          <h4 class="fw-bold mt-4" style="color:#232946;">Event Logs</h4>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
                 <thead class="table-dark">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Description</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($paymentLogs as $log)
                  <tr>
                    <td>{{ $log->user ?? 'N/A' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->date_time)->format('Y-m-d H:i:s') }}</td>
                  </tr>
                @endforeach
          
              </tbody>
            </table>
          </div>
        </div>

        <!-- 🟨 Other Logs Tab -->
        <div class="tab-pane fade" id="otherLogs" role="tabpanel">
          <h4 class="fw-bold mt-4" style="color:#232946;">Other Logs</h4>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                <tr>
                  <th>User</th>
                  <th>Action</th>
                  <th>Description</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($otherLogs as $log)
                  <tr>
                    <td>{{ $log->user ?? 'N/A' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->date_time)->format('Y-m-d H:i:s') }}</td>
                  </tr>
                @endforeach
             
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>