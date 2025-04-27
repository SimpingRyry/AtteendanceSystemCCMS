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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    @include('layout.navbar')
    @include('layout.sidebar')

    <main>
<div class="container outer-box mt-5 pt-5 pb-4 shadow">
    <div class="container-fluid" id="mainContainer">
        <!-- Heading -->
        <div class="mb-3">
            <h2 class="fw-bold" style="color: #5CE1E6;">Students</h2>
            <small style="color: #989797;">Manage /</small>
            <small style="color: #444444;">Student</small>
        </div>

        <!-- Main Card -->
        <div class="card shadow-sm p-4">
            <div class="row g-3">
                @foreach (['Course', 'Block', 'Year Level', 'Status'] as $label)
                    <div class="col-md">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">{{ $label }}</h6>
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
                                            <option>Enrolled</option>
                                            <option>Unenrolled</option>
                                        @endif
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Search Right Only -->
            <div class="row mb-4 mt-4 justify-content-end">
                <div class="col-md-6 d-flex justify-content-md-end">
                    <div class="d-flex" style="gap: 8px; max-width: 350px; margin-top: 15px;">
                        <input type="text" class="form-control" placeholder="Enter search...">
                        <button class="btn btn-success">Search</button>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="card shadow-sm p-4 mt-2">
                <h5 class="mb-3 fw-bold" style="color: #5CE1E6;">Student List</h5>

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
                                <th>Section</th>
                                <th>Contact Number</th>
                                <th>Birth Date</th>
                                <th>Address</th>
                                <th>Action</th>
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
                                    <td>{{ $student->section }}</td>
                                    <td>{{ $student->contact_no }}</td>
                                    <td>{{ $student->birth_date }}</td>
                                    <td>{{ $student->address }}</td>
                                    <td><button type="button" class="btn btn-primary register-btn" data-bs-toggle="modal" data-bs-target="#registerStudentModal" onclick="fillModalData(this)">Register</button></td>
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

            <!-- Generate Schedule Button -->
           

        </div>
    </div>

    <!-- Generate Schedule Modal -->
    

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
          <div class="modal-body">
            <div class="mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="green" class="bi bi-check-circle-fill animate__animated animate__bounceIn" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.07 0l4-4a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.53 7.47a.75.75 0 0 0-1.06 1.06l2.5 2.5z"/>
              </svg>
            </div>
            <h5 class="modal-title mb-2" id="successModalLabel">Schedule Generated Successfully!</h5>
            <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="registerStudentModal" tabindex="-1" aria-labelledby="registerStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
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
              <label>Fingerprint ID</label>
              <input type="text" name="fingerprint" class="form-control" id="fingerprintId" placeholder="Fingerprint ID">
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
              <label>Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            </div>
          </div>

          <!-- Role/Position -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Role / Position</h4>
          <div class="mb-4">
            <label for="roleSelect">Select Role</label>
            <select name="role" id="roleSelect" class="form-select" required>
              @if(auth()->user()->org == 'ITS')
                <option value="">-- Select Role --</option>
                <option value="IT Support">IT Support</option>
                <option value="Network Admin">Network Admin</option>
                <option value="System Analyst">System Analyst</option>
              @elseif(auth()->user()->org == 'PRAXIS')
                <option value="">-- Select Role --</option>
                <option value="Research Assistant">Research Assistant</option>
                <option value="Project Coordinator">Project Coordinator</option>
                <option value="Field Agent">Field Agent</option>
              @elseif(auth()->user()->org == 'SG')
                <option value="">-- Select Role --</option>
                <option value="Security Guard">Security Guard</option>
                <option value="Supervisor">Supervisor</option>
                <option value="Admin Officer">Admin Officer</option>
              @else
                <option value="">No roles available for this organization</option>
              @endif
            </select>
          </div>

          <!-- Profile Picture -->
          <h4 class="fw-bold mb-3" style="color: #5CE1E6;">Profile Picture</h4>
          <div class="mb-4">
            <div class="d-flex gap-2 mb-3">
              <button type="button" class="btn btn-outline-primary" onclick="showUpload()">Upload Image</button>
              <button type="button" class="btn btn-outline-secondary" onclick="showCamera()">Capture Image</button>
            </div>

            <!-- Upload Input -->
            <input type="file" name="uploaded_picture" accept="image/*" id="uploadInput" class="form-control mb-3" style="display: none;" onchange="previewUploadImage(event)">

            <!-- Camera Stream -->
            <div id="cameraContainer" style="display: none; text-align: center;">
              <video id="cameraStream" width="100%" height="300" autoplay playsinline style="border-radius: 8px; border: 1px solid #ccc;"></video>
              <button type="button" class="btn btn-success mt-2" onclick="capturePhoto()">Take Picture</button>
            </div>

            <!-- Captured Image Preview -->
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


</div>
</main>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>
    
    
</body>

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
    document.getElementById('modalSection').value = cells[6].innerText.trim();
    document.getElementById('modalContactNumber').value = cells[7].innerText.trim();
    document.getElementById('modalBirthDate').value = cells[8].innerText.trim();
    document.getElementById('modalAddress').value = cells[9].innerText.trim();
}
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
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('registering') === 'true') {
        openRegisterFormFromStorage();
    }
});

function openRegisterForm(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');

    const data = {
        no: cells[0].innerText,
        id_number: cells[1].innerText,
        name: cells[2].innerText,
        gender: cells[3].innerText,
        course: cells[4].innerText,
        year: cells[5].innerText,
        section: cells[6].innerText,
        contact_no: cells[7].innerText,
        birth_date: cells[8].innerText,
        address: cells[9].innerText,
        email: '' // Adjust if needed
    };

    localStorage.setItem('registering', 'true');
    localStorage.setItem('studentData', JSON.stringify(data));

    renderRegisterForm(data);
}




let videoStream;

function cancelRegistration() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
    }
    localStorage.removeItem('registering');
    localStorage.removeItem('studentData');
    location.reload();
}

function startCamera() {
    const video = document.getElementById('cameraStream');
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            videoStream = stream;
            video.srcObject = stream;
        })
        .catch((error) => {
            console.error('Camera error:', error);
            alert('Could not access the camera. Please allow permissions.');
        });
}

function capturePhoto() {
    const video = document.getElementById('cameraStream');
    const canvas = document.getElementById('capturedCanvas');
    const img = document.getElementById('capturedImage');
    const capturedInput = document.getElementById('capturedImageInput');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageDataURL = canvas.toDataURL('image/png');
    img.src = imageDataURL;
    img.style.display = 'block';

    capturedInput.value = imageDataURL; // save image to hidden input
}

function handleFormSubmit(event) {
    event.preventDefault(); // prevent default submit

    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
    }

    const form = event.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: 'Registration Submitted!',
                text: 'The student registration was submitted successfully.',
                confirmButtonColor: '#5CE1E6'
            }).then(() => {
                localStorage.removeItem('registering');
                localStorage.removeItem('studentData');
                window.location.reload();
            });
        } else {
            return response.text().then(text => { throw new Error(text); });
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something went wrong during registration!',
            confirmButtonColor: '#5CE1E6'
        });
    });
}
</script>



</html>
