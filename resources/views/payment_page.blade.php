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
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
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
        <h2 class="fw-bold" style="color: #232946;">Payment</h2>
        <small style="color: #989797;">Manage /</small>
        <small style="color: #444444;">Payment</small>
      </div>

      <!-- MUI Payment Section -->
      <div style="margin-top: 2rem;">
        <div class="mui-container" style="max-width: 800px; margin: auto;">
          <div style="background: rgba(255,255,255,0.8); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <!-- Payment Summary -->
            <h4 style="font-weight: 700; color: #232946;">Attendance Fine Summary</h4>
            <div style="margin-top: 1.5rem;">
              <div class="row mb-4">
                <div class="col-md-4">
                  <p class="text-muted mb-1">Total Fine Amount</p>
                  <h5 class="text-success fw-bold">₱ 1,200.00</h5>
                </div>
                <div class="col-md-4">
                  <p class="text-muted mb-1">Total Offenses</p>
                  <h5 class="fw-bold">3</h5>
                </div>
                <div class="col-md-4">
                  <p class="text-muted mb-1">Last Offense Date</p>
                  <h5 class="fw-bold">April 20, 2025</h5>
                </div>
              </div>

              <!-- Fine Details Table -->
              <div style="overflow-x: auto;">
                <table class="table table-hover" style="border-radius: 8px; overflow: hidden;">
                  <thead style="background-color: #f5f5f5;">
                    <tr>
                      <th>Date</th>
                      <th>Reason</th>
                      <th>Fine Amount</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>March 15, 2025</td>
                      <td>Late Arrival</td>
                      <td>₱ 400</td>
                      <td><span class="badge bg-danger">Unpaid</span></td>
                    </tr>
                    <tr>
                      <td>April 10, 2025</td>
                      <td>Missed Attendance</td>
                      <td>₱ 500</td>
                      <td><span class="badge bg-danger">Unpaid</span></td>
                    </tr>
                    <tr>
                      <td>April 20, 2025</td>
                      <td>Unauthorized Absence</td>
                      <td>₱ 300</td>
                      <td><span class="badge bg-danger">Unpaid</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Payment Form -->
            <div style="margin-top: 3rem;">
              <h5 class="fw-bold" style="color: #232946;">Proceed with Payment</h5>

              <div class="row mt-3">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Amount to Pay</label>
                  <input type="text" class="form-control" value="₱ 1,200.00" readonly />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Payment Method</label>
                  <select class="form-select">
                    <option selected>Choose...</option>
                    <option value="gcash">GCash</option>
                  </select>
                </div>

                <div class="col-12 mb-3">
                  <label class="form-label">Reference Number (if applicable)</label>
                  <input type="text" class="form-control" placeholder="Enter your payment reference number" />
                </div>

                <div class="col-12 text-end mt-4">
                  <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius: 8px;">
                    Pay Now
                  </button>
                </div>
              </div>
            </div>

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