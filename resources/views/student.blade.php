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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet" />


  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/content.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

  <title>CCMS Attendance System</title>
</head>

<body>

  @include('layout.navbar')
  @include('layout.sidebar')

  <!------------------------------------------------------ MAIN_BOX -------------------------------------------------------->
  <main>
    <div class="container outer-box mt-5 pt-5 pb-4 shadow">
      <div class="container-fluid">
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

          <!-- Upload Left + Search Right -->
          <div class="row mb-4 mt-4 align-items-center justify-content-between">
            <!-- CSV Upload Left -->
            <div class="col-md-6 d-flex">
              <form id="csvForm" action="{{ route('import') }}" method="post" enctype="multipart/form-data"
                class="d-flex align-items-center flex-wrap" style="gap: 8px; max-width: 400px;" onsubmit="return validateCSV();">
                @csrf
                <div class="form-text text-muted me-2">
                  Upload a .csv file
                </div>
                <div class="input-group input-group-sm">
                  <input type="file" class="form-control" name="importFile" accept=".csv" required>
                  <button class="btn btn-sm btn-primary" type="submit">Upload</button>
                </div>
              </form>
            </div>

            <!-- Search Right -->
            <div class="col-md-6 d-flex justify-content-md-end mt-6 mt-md-0">
              <div class="d-flex" style="gap: 8px; max-width: 350px; margin-top: 15px;">
                <input type="text" class="form-control" placeholder="Enter search...">
                <button class="btn btn-success">Search</button>
              </div>
            </div>
          </div>

          <!-- Student List -->
          <div class="card shadow-sm p-4 mt-2">
            <h5 class="mb-3 fw-bold" style="color: #5CE1E6;">Student List</h5>

            <!-- Displaying students list -->
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

                  <!-- Add other columns as needed -->
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

                  <!-- Add other columns as needed -->
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

            <!-- Generate Schedule Button -->

          </div>
          <div class="text-center mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateScheduleModal">
              Generate Schedule
            </button>
          </div>
        </div>
      </div>

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

                <!-- Stretch Submit Button -->
                <div class="d-grid">
                  <button type="submit" class="btn btn-success">Generate Schedule</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>


      <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content text-center p-4">
            <div class="modal-body">
              <div class="mb-3">
                <!-- Check Animation -->
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="green" class="bi bi-check-circle-fill animate__animated animate__bounceIn" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.07 0l4-4a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.53 7.47a.75.75 0 0 0-1.06 1.06l2.5 2.5z" />
                </svg>
              </div>
              <h5 class="modal-title mb-2" id="successModalLabel">Schedule Generated Successfully!</h5>
              <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">OK</button>
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

</html>