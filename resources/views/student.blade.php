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
  <main>
  <div class="container outer-box mt-5 pt-5 pb-4 shadow">
    <div class="container-fluid">
      <!-- Heading -->
      <div class="mb-3">
        <h2 class="fw-bold" style="color: #232946;">Students</h2>
        <small style="color: #989797;">Manage /</small>
        <small style="color: #444444;">Student</small>
      </div>

      <!-- Button Area (Upper Right) -->
      <div class="d-flex justify-content-end mb-3 gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCSVModal">
          <i class="bi bi-upload me-2"></i> Import CSV
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateScheduleModal">
          <i class="bi bi-calendar-check me-2"></i> Generate Schedule
        </button>
      </div>

      <!-- Main Card -->
      <div class="card shadow-sm p-4">
        <div class="row g-3">
          @foreach (['Organization', 'Block', 'Year Level', 'Status'] as $label)
          <div class="col-md">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h6 class="card-title">{{ $label }}</h6>
                @if ($label == 'Organization')
                <select class="form-select">
                  <option selected disabled>Select Organization</option>
                  <option>ITS</option>
                  <option>Praxis</option>
                </select>
                @else
                @if ($label == 'Course')
                <input type="text" class="form-control" placeholder="Enter Course">
                @else
                <select class="form-select">
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
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="row mb-4 mt-4 align-items-center justify-content-between">
          <!-- Search Right -->
          <div class="col-md-6 d-flex justify-content-md-end mt-6 mt-md-0 ms-auto">
            <div class="d-flex" style="gap: 8px; max-width: 350px; margin-top: 15px;">
              <input type="text" class="form-control" placeholder="Enter search...">
              <button class="btn btn-success">Search</button>
            </div>
          </div>
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadCSVModal" tabindex="-1" aria-labelledby="uploadCSVModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form id="csvForm" action="{{ route('import') }}" method="post" enctype="multipart/form-data" onsubmit="return validateCSV();">
                @csrf
                <div class="modal-header">
                  <h5 class="modal-title" id="uploadCSVModalLabel">Upload CSV File</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="form-text text-muted mb-2">
                    Upload a .csv file
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="file" class="form-control" name="importFile" accept=".csv" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-primary" type="submit">Upload</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Student List -->
        <div class="card shadow-sm p-4 mt-2">
          <h5 class="mb-3 fw-bold" style="color: #232946;">Student List</h5>

          @if($students->isEmpty())
          <p>No students found.</p>
          @else
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Course</th>
                <th>Year</th>
                <th>Units</th>
                <th>Section</th>
                <th>Contact Number</th>
                <th>Birth Date</th>
                <th>Address</th>
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
                  <button type="button" class="btn btn-primary register-btn" data-bs-toggle="modal" data-bs-target="#registerStudentModal" onclick="fillModalData(this)">Register</button>
                  @else
                  <span class="badge bg-success">Registered</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @endif

          <!-- Pagination -->
          <div class="d-flex justify-content-end mt-3">
            <nav>
              <ul class="pagination">
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <!-- Generate Schedule Modal -->
      <div class="modal fade" id="generateScheduleModal" tabindex="-1" aria-labelledby="generateScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="generateScheduleModalLabel">Generate Schedule</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <form id="generateScheduleForm" method="POST" action="{{ route('generate.memo.pdf') }}">
                @csrf
                <div class="mb-3">
                  <label for="course" class="form-label">Course</label>
                  <select class="form-select" name="course" id="course" required>
                    <option selected disabled>Select Course</option>
                    <option>BSIT</option>
                    <option>BSIS</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="block" class="form-label">Block</label>
                  <select class="form-select" name="block" id="block" required>
                    <option selected disabled>Select Block</option>
                    <option>A</option>
                    <option>B</option>
                    <option>C</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="year" class="form-label">Year</label>
                  <select class="form-select" name="year" id="year" required>
                    <option selected disabled>Select Year</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="venue" class="form-label">Venue</label>
                  <input type="text" class="form-control" name="venue" id="venue" placeholder="Enter venue" required>
                </div>

                <div class="mb-3">
                  <label for="date" class="form-label">Date</label>
                  <input type="date" class="form-control" name="date" id="date" required>
                </div>

                <div class="mb-3">
                  <label for="startTime" class="form-label">Start Time</label>
                  <input type="time" class="form-control" name="startTime" id="startTime" required>
                </div>

                <div class="mb-3">
                  <label for="endTime" class="form-label">End Time</label>
                  <input type="time" class="form-control" name="endTime" id="endTime" required>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-success">Generate Schedule</button>
                </div>
              </form>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


</body>

<script>
  let fingerprintPolling = null;

  function updateFingerprintImage() {
    console.log('updateFingerprintImage function is called');
    axios.get('/api/fingerprint/latest')
      .then(response => {
        if (response.data && response.data.url) {
          const url = response.data.url;
           console.log(url);
          const modal = document.getElementById("registerStudentModal");

          if (modal && modal.classList.contains("show")) {
            const imgEl = document.getElementById("fingerprintImage");
            imgEl.src = url; // Add timestamp to prevent caching
            imgEl.style.display = 'block';

            // Optionally store the URL or related data in a hidden field
            document.getElementById('fingerprintData').value = url;
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





</html>