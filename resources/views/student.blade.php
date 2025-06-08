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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />


  <!-- Google Font -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/content.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

  <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

  @include('layout.navbar')
  @include('layout.sidebar')

  <!------------------------------------------------------ MAIN_BOX -------------------------------------------------------->
  <main class="pt-5">
  <div class="container bg-white rounded-4 p-4 shadow-sm">

    <!-- Heading -->
    <div class="mb-4">
      <h2 class="fw-bold text-dark">Students</h2>
      <small class="text-muted">Manage / </small>
      <small class="text-body-secondary">Student</small>
    </div>

    <!-- Button Row -->
    <div class="d-flex justify-content-end gap-2 mb-4">
      <button class="btn btn-outline-secondary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="false" aria-controls="filterPanel">
        <i class="bi bi-funnel-fill me-1"></i> Filter
      </button>
      <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#uploadCSVModal">
        <i class="bi bi-upload me-1"></i> Import CSV
      </button>
      <button class="btn btn-outline-success rounded-pill" data-bs-toggle="modal" data-bs-target="#scheduleModal">
        <i class="bi bi-calendar-check me-1"></i> Generate Schedule
      </button>
    </div>

    <!-- Collapsible Filter Panel -->
    <div class="collapse mb-4" id="filterPanel">
      <div class="p-4 rounded-4 shadow-sm bg-light border">
        <div class="row g-3">
          @foreach (['Organization', 'Block', 'Year Level', 'Status'] as $label)
          <div class="col-md">
            <div class="border rounded-3 p-3 bg-white">
              <label class="form-label small text-muted">{{ $label }}</label>
              @if ($label == 'Organization')
              <select class="form-select form-select-sm">
                <option selected disabled>Select Organization</option>
                <option>ITS</option>
                <option>Praxis</option>
              </select>
              @else
              <select class="form-select form-select-sm">
                <option selected disabled>Select {{ $label }}</option>
                @if ($label == 'Block')
                  @foreach (['A', 'B', 'C', 'D'] as $option)
                  <option>{{ $option }}</option>
                  @endforeach
                @elseif ($label == 'Year Level')
                  @foreach (range(1, 4) as $year)
                  <option>{{ $year }}</option>
                  @endforeach
                @else
                  <option>Registered</option>
                  <option>Unregistered</option>
                @endif
              </select>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="d-flex justify-content-end mb-4">
      <div class="input-group" style="max-width: 350px;">
        <input type="text" class="form-control form-control-sm rounded-start" placeholder="Search...">
        <button class="btn btn-success btn-sm rounded-end">Search</button>
      </div>
    </div>

    <!-- Student List -->
    <div class="bg-white border rounded-4 p-4 shadow-sm">
      <h5 class="fw-bold text-dark mb-3">Student List</h5>
      @if($students->isEmpty())
      <p class="text-muted">No students found.</p>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Student ID</th>
              <th>Name</th>
              <th>Gender</th>
              <th>Course</th>
              <th>Year</th>
              <th>Units</th>
              <th>Section</th>
              <th>Contact</th>
              <th>Birth Date</th>
              <th>Address</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
            <tr>
              <td>{{ $student->no }}</td>
              <td>{{ $student->id_number }}</td>
              <td>{{ $student->name }}</td>
              <td>{{ $student->gender }}</td>
              <td>{{ $student->course }}</td>
              <td>{{ $student->year }}</td>
              <td>{{ $student->units }}</td>
              <td>{{ $student->section }}</td>
              <td>{{ $student->contact_no }}</td>
              <td>{{ $student->birth_date }}</td>
              <td>{{ $student->address }}</td>
              <td>
              @if($student->status === 'Unregistered')
  @if(!(auth()->user()->role === 'Super Admin' || auth()->user()->organization === 'CCMS Student Government'))
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerStudentModal" onclick="fillModalData(this)">Register</button>
  @else
    <span class="badge bg-warning">Unregistered</span>
  @endif
@else
  <span class="badge bg-success">Registered</span>
@endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

      <!-- Pagination -->
      <div class="d-flex justify-content-end mt-3">
        <nav>
          <ul class="pagination pagination-sm">
            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Upload Modal -->
  <div class="modal fade" id="uploadCSVModal" tabindex="-1" aria-labelledby="uploadCSVModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="csvForm" action="{{ route('import') }}" method="post" enctype="multipart/form-data" onsubmit="return validateCSV();">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Upload CSV File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <small class="text-muted d-block mb-2">Only .csv files are supported</small>
          <input type="file" class="form-control form-control-sm" name="importFile" accept=".csv" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>

</main>


<div class="modal fade" id="registerStudentModal" tabindex="-1" aria-labelledby="registerStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" style="max-width: 70%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="registerStudentModalLabel" style="color: #5CE1E6;">Student Registration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="registerStudentForm" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <!-- Student Details -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Student Details</h4>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label>Student ID</label>
              <input type="text" name="student_id" id="modalStudentId" class="form-control" value="${data.id_number}" readonly>
            </div>
            <div class="col-md-6">
              <label>Name</label>
              <input type="text" name="sname" id="modalStudentName" class="form-control" value="${data.name}" readonly>
            </div>
            <div class="col-md-6">
              <label>Gender</label>
              <input type="text" class="form-control" id="modalGender" value="${data.gender}" readonly>
            </div>
            <div class="col-md-6">
              <label>Course</label>
              <input type="text" class="form-control" id="modalCourse" name="course" value="${data.course}" readonly>
            </div>
            <div class="col-md-6">
              <label>Year</label>
              <input type="text" class="form-control" id="modalYear" value="${data.year}" readonly>
            </div>
            <div class="col-md-6">
              <label>Section</label>
              <input type="text" class="form-control" id="modalSection" value="${data.section}" readonly>
            </div>
            <div class="col-md-6">
              <label>Birth Date</label>
              <input type="text" class="form-control" id="modalBirthDate" value="${data.birth_date}" readonly>
            </div>
            <div class="col-md-6">
              <label>Fingerprint Scan</label>
              <div style="border: 1px solid #ccc; border-radius: 8px; width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f9f9f9;">
                <img id="fingerprintImage" src="" alt="Fingerprint will appear here" style="max-height: 100%; max-width: 100%; display: none;">
              </div>
              <input type="hidden" name="fingerprint_data" id="fingerprintData">
              <input type="hidden" name="fingerprint_user_id" id="fingerprintUserId">

            </div>
          </div>

          <!-- Contact Details -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Contact Details</h4>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label>Contact Number</label>
              <input type="text" class="form-control" id="modalContactNumber" value="${data.contact_no}" readonly>
            </div>
            <div class="col-12">
              <label>Address</label>
              <input type="text" class="form-control" id="modalAddress" value="${data.address}" readonly>
            </div>
            <div class="col-12">
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            </div>
          </div>

          <!-- Role/Position -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Role / Position</h4>
          <div class="mb-4">
            <label for="roleSelect">Select Role <span class="text-danger">*</span></label>
            <select name="role" id="roleSelect" class="form-select" required>
              @if(auth()->user()->org == 'ITS')
              <option value="">-- Select Role --</option>
                <option value="member">Member</option>
                <option value="officer">Officer</option>
              @elseif(auth()->user()->org == 'PRAXIS')
                <option value="">-- Select Role --</option>
                <option value="member">Member</option>
                <option value="officer">Officer</option>
              @elseif(auth()->user()->org == 'SG')
              <option value="">-- Select Role --</option>
                <option value="member">Member</option>
                <option value="officer">Officer</option>
              @else
                <option value="">-- Select Role --</option>
                <option value="Member">Member</option>
                <option value="officer">Officer</option>
              @endif
            </select>
          </div>

          <!-- Organization Dropdown for Super Admin -->
          @if(auth()->user()->role == 'Super Admin')
            <div class="mb-4">
              <label for="organizationSelect">Select Organization <span class="text-danger">*</span></label>
              <select name="organization" id="organizationSelect" class="form-select" required>
                <option value="">-- Select Organization --</option>
                @foreach ($org_list as $org)
                  <option value="{{ $org->org_name }}">{{ $org->org_name }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <!-- Profile Picture -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Profile Picture</h4>
          <div class="mb-4">
            <div class="d-flex gap-2 mb-3">
              <button type="button" class="btn btn-outline-primary" onclick="showUpload()">Upload Image</button>
              <button type="button" class="btn btn-outline-secondary" onclick="showCamera()">Capture Image</button>
            </div>

            <input type="file" name="uploaded_picture" accept="image/*" id="uploadInput" class="form-control mb-3" style="display: none;" onchange="previewUploadImage(event)">

            <div id="cameraContainer" style="display: none; text-align: center;">
              <video id="cameraStream" width="100%" height="300" autoplay playsinline style="border-radius: 8px; border: 1px solid #ccc;"></video>
              <button type="button" class="btn btn-success mt-2" onclick="capturePhoto()">Take Picture</button>
            </div>

            <img id="capturedImage" class="img-fluid mt-3" style="display: none; max-height: 300px;" />
            <input type="hidden" name="captured_image" id="capturedImageInput">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="registerStudentForm" class="btn btn-success">Submit Registration</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ url('/generate-biometrics-schedule') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Set Schedule Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="scheduleDate" class="form-label">Schedule Date</label>
                    <input type="date" class="form-control" id="scheduleDate" name="date" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Generate PDF</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>
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



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


</body>
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif

<script>
  let fingerprintPolling = null;

  function updateFingerprintImage() {
  console.log('updateFingerprintImage function is called');
  axios.get('/api/fingerprint/latest')
    .then(response => {
      if (response.data && response.data.url && response.data.user_id) {
        const url = response.data.url;
        const userId = response.data.user_id;
        console.log("URL:", url);
        console.log("User ID:", userId);

        const modal = document.getElementById("registerStudentModal");

        if (modal && modal.classList.contains("show")) {
          const imgEl = document.getElementById("fingerprintImage");
          imgEl.src = url + '?t=' + new Date().getTime(); // avoid browser cache
          imgEl.style.display = 'block';

          // Store values in hidden inputs
          document.getElementById('fingerprintData').value = url;
          document.getElementById('fingerprintUserId').value = userId;
        }
      }
    })
    .catch(error => {
      console.error("Error fetching fingerprint image:", error);
    });
}

  function startFingerprintPolling() {
    updateFingerprintImage(); // Run once immediately
    fingerprintPolling = setInterval(updateFingerprintImage, 2000); // Continue every 2 seconds
  }

  function stopFingerprintPolling() {
    clearInterval(fingerprintPolling);
    fingerprintPolling = null;
  }

  // jQuery version to match the Bootstrap modal event usage in the first script
  $('#registerStudentModal').on('shown.bs.modal', function () {
    console.log('Modal is shown');
    startFingerprintPolling();
  });

  $('#registerStudentModal').on('hidden.bs.modal', function () {
    stopFingerprintPolling();
  });
</script>


<script>
  document.getElementById('generateScheduleForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submit (remove this if you want to still submit server-side)

    // Here you can actually trigger PDF generation if needed, then show success modal

    // Hide the first modal
    var generateModal = bootstrap.Modal.getInstance(document.getElementById('generateScheduleModal'));
    generateModal.hide();

    // Show success modal
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();

    // Optional: Submit the form after delay (if you still want to download PDF)
    setTimeout(() => {
      this.submit();
    }, 1500); // Delay submit so user sees the success modal first
  });


  function validateCSV() {
    const fileInput = document.querySelector('input[type="file"]');
    const file = fileInput.files[0];

    if (!file) {
      redirectWithError("CSV file is required");
      return false;
    }

    const fileName = file.name.toLowerCase();
    if (!fileName.endsWith('.csv')) {
      redirectWithError("Only CSV files are allowed");
      return false;
    }

    return true;
  }
</script>

<script>
  function showUpload() {
    document.getElementById('uploadInput').style.display = 'block';
    document.getElementById('cameraContainer').style.display = 'none';
    document.getElementById('capturedImage').style.display = 'none';
    stopCamera();
  }

  function showCamera() {
    document.getElementById('uploadInput').style.display = 'none';
    document.getElementById('cameraContainer').style.display = 'block';
    document.getElementById('capturedImage').style.display = 'none';
    startCamera();
  }

  function previewUploadImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
      const img = document.getElementById('capturedImage');
      img.src = reader.result;
      img.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
  }

  function startCamera() {
    const video = document.getElementById('cameraStream');
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => {
        video.srcObject = stream;
      })
      .catch(err => {
        alert("Camera access denied: " + err);
      });
  }

  function stopCamera() {
    const video = document.getElementById('cameraStream');
    const stream = video.srcObject;
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    video.srcObject = null;
  }

  function capturePhoto() {
    const canvas = document.getElementById('canvas');
    const video = document.getElementById('cameraStream');
    const image = document.getElementById('capturedImage');
    const input = document.getElementById('capturedImageInput');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    image.src = imageData;
    image.style.display = 'block';
    input.value = imageData;
  }
</script>

<script>
function fillModalData(button) {
    // Find the row (`<tr>`) that contains the clicked button
    var row = button.closest('tr');

    // Get all cells (`<td>`) in that row
    var cells = row.getElementsByTagName('td');

    // Now fill the modal inputs
    document.getElementById('modalStudentId').value = cells[1].innerText.trim();
    document.getElementById('modalStudentName').value = cells[2].innerText.trim();
    document.getElementById('modalGender').value = cells[3].innerText.trim();
    document.getElementById('modalCourse').value = cells[4].innerText.trim();
    document.getElementById('modalYear').value = cells[5].innerText.trim();
    document.getElementById('modalSection').value = cells[7].innerText.trim();
    document.getElementById('modalContactNumber').value = cells[8].innerText.trim();
    document.getElementById('modalBirthDate').value = cells[9].innerText.trim();
    document.getElementById('modalAddress').value = cells[10].innerText.trim();
}
</script>


<style>
  body {
  background-color: #f9fafb;
  font-family: 'Inter', 'Segoe UI', sans-serif;
}

h2, h5 {
  color: #1e293b;
}

.btn {
  transition: all 0.2s ease;
}
.btn:hover {
  opacity: 0.9;
}

input::placeholder {
  color: #cbd5e1;
}
</style>


</html>