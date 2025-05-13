<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

  <title>CCMS Attendance System</title>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }

    @media screen and (max-width: 768px) {
      .modal-body p,
      .modal-body td {
        font-size: 14px;
      }
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
        <div class="mb-4">
          <h2 class="fw-bold" style="color: #232946;">Payment</h2>
          <small style="color: #989797;">Manage /</small>
          <small style="color: #444444;">Payment</small>
        </div>

        <!-- Filter Form -->
        <div class="mb-4">
          <form class="row g-3">
            <div class="col-md-3">
              <label for="filterYear" class="form-label fw-semibold">Year Level</label>
              <select class="form-select" id="filterYear">
                <option selected>All</option>
                <option>1st Year</option>
                <option>2nd Year</option>
                <option>3rd Year</option>
                <option>4th Year</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="filterCourseSection" class="form-label fw-semibold">Course & Section</label>
              <select class="form-select" id="filterCourseSection">
                <option selected>All</option>
                <option>BSIT - A</option>
                <option>BSCS - B</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="filterOrg" class="form-label fw-semibold">Organization</label>
              <select class="form-select" id="filterOrg">
                <option selected>All</option>
                <option>Science Club</option>
                <option>Math Society</option>
                <option>Debate Team</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i>Apply Filters</button>
            </div>
          </form>
        </div>

        <!-- Student Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Section</th>
                <th>Organization</th>
                <th>Fines</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2023-001</td>
                <td>Jane Doe</td>
                <td>BSIT</td>
                <td>A</td>
                <td>Science Club</td>
                <td>₱150.00</td>
                <td>
                  <button class="btn btn-success btn-sm"><i class="fas fa-money-bill-wave"></i> Pay</button>
                  <button class="btn btn-primary btn-sm view-btn" data-bs-toggle="modal" data-bs-target="#soaModal">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td>2023-002</td>
                <td>John Smith</td>
                <td>BSCS</td>
                <td>B</td>
                <td>Math Society</td>
                <td>₱200.00</td>
                <td>
                  <button class="btn btn-success btn-sm"><i class="fas fa-money-bill-wave"></i> Pay</button>
                  <button class="btn btn-primary btn-sm view-btn"data-bs-toggle="modal" data-bs-target="#soaModal">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Statement of Account Modal 1 -->
      

        <!-- Modal 2 (Optional, repeat for others as needed) -->
       

      </div>
    </div>
  </main>
  <div class="modal fade" id="soaModal" tabindex="-1" aria-labelledby="soaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="soaModalLabel">Statement of Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Student Name:</strong> <span id="modalStudentName"></span></p>
        <p><strong>Student ID:</strong> <span id="modalStudentID"></span></p>
        <p><strong>Course & Section:</strong> <span id="modalCourseSection"></span></p>

        <div class="table-responsive">
          <table class="table table-bordered mt-3">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount Paid</th>
                <th>Remaining Balance</th>
              </tr>
            </thead>
            <tbody id="modalTableBody">
              <!-- Dynamic content here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <span class="fw-bold">Total Remaining Balance: <span id="modalTotalBalance"></span></span>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success">Proceed to Pay</button>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const viewButtons = document.querySelectorAll(".view-btn");

    viewButtons.forEach(button => {
      button.addEventListener("click", function () {
        const row = button.closest("tr");
        const studentID = row.children[0].textContent;
        const studentName = row.children[1].textContent;
        const course = row.children[2].textContent;
        const section = row.children[3].textContent;

        // Set modal content
        document.getElementById("modalStudentName").textContent = studentName;
        document.getElementById("modalStudentID").textContent = studentID;
        document.getElementById("modalCourseSection").textContent = `${course} - ${section}`;

        // Replace with actual data or fetch from database
        let tableBody = document.getElementById("modalTableBody");
        tableBody.innerHTML = ""; // Clear previous

        let exampleData = [];

        if (studentID === "2023-001") {
          exampleData = [
            { date: "2025-01-10", desc: "Science Club Fee", paid: "₱100.00", balance: "₱50.00" }
          ];
          document.getElementById("modalTotalBalance").textContent = "₱50.00";
        } else if (studentID === "2023-002") {
          exampleData = [
            { date: "2025-01-20", desc: "Math Contest Fee", paid: "₱150.00", balance: "₱50.00" }
          ];
          document.getElementById("modalTotalBalance").textContent = "₱50.00";
        }

        // Add rows to table
        exampleData.forEach(item => {
          let rowHTML = `
            <tr>
              <td>${item.date}</td>
              <td>${item.desc}</td>
              <td>${item.paid}</td>
              <td>${item.balance}</td>
            </tr>
          `;
          tableBody.insertAdjacentHTML('beforeend', rowHTML);
        });
      });
    });
  });
</script>
</body>

</html>
