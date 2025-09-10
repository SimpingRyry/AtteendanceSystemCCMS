<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <title>Ticktax Attendance System</title>

  <style>

  #filterCloud {
    box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
    border-radius: 16px;
    background: #fff;
}


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

      <!-- Filter Toggle Button -->

      <!-- Filter Cloud -->
      <div class="row align-items-center mb-3">
  <div class="col-md-12 d-flex justify-content-end align-items-end gap-2">

    <!-- Filter Button + Cloud -->
    <div style="position: relative;">
      <button class="btn btn-outline-secondary" onclick="toggleFilterCloud()" style="border-radius: 6px;">
        <i class="bi bi-funnel-fill"></i>
      </button>

      <!-- Filter Cloud -->
    <div id="filterCloud" class="shadow p-3 rounded" style="position: absolute; top: 120%; right: 0; background-color: white; border: 1px solid #ddd; border-radius: 12px; display: none; min-width: 270px; z-index: 10;">
  
  <!-- Year Level -->
  <div class="mb-2">
    <label for="filterYear" class="form-label">Filter by Year Level</label>
    <select id="filterYear" class="form-select form-select-sm">
      <option value="">All</option>
      @foreach($years as $year)
        <option>{{ $year }}</option>
      @endforeach
    </select>
  </div>

  <!-- Section -->
  <div class="mb-2">
    <label for="filterCourseSection" class="form-label">Filter by Section</label>
    <select id="filterCourseSection" class="form-select form-select-sm">
      <option value="">All</option>
      @foreach($sections as $section)
        <option>{{ $section }}</option>
      @endforeach
    </select>
  </div>

  <!-- Organization (Only if Super Admin) -->
  @if(auth()->user()->role === 'Super Admin')
  <div>
    <label for="filterOrg" class="form-label">Filter by Organization</label>
    <select id="filterOrg" class="form-select form-select-sm">
      <option value="">All</option>
      @foreach($orgs as $org)
        <option>{{ $org }}</option>
      @endforeach
    </select>
  </div>
  @endif
</div>
    </div>

    <!-- Search Input -->
    <div style="min-width: 250px;">
      <label class="form-label mb-1">Search</label>
<input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Enter name or ID..." oninput="applySearchFilter()">
    </div>
  </div>
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
              <th>Balance</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
              <tr>
                <td>{{ $student->student_id }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->studentList->course ?? 'N/A' }}</td>
                <td>{{ $student->studentList->section ?? 'N/A' }}</td>
                <td>{{ $student->org }}</td>
                <td>₱{{ number_format($balances[$student->student_id] ?? 0, 2) }}</td>
                <td>
                <button class="btn btn-success btn-sm pay-btn"
        data-student-id="{{ $student->student_id }}"
        data-org="{{ $student->org }}"
        data-bs-toggle="modal"
        data-bs-target="#paymentModal">
  <i class="fas fa-money-bill-wave"></i> Pay
</button>
                  <button class="btn btn-primary btn-sm view-btn"
                          data-student-id="{{ $student->student_id }}"
                          data-bs-toggle="modal"
                          data-bs-target="#soaModal">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Statement of Account Modal (if applicable) -->

    </div>
  </div>
</main>
  <div class="modal fade" id="soaModal" tabindex="-1" aria-labelledby="soaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="soaModalLabel">Statement of Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="soaModalBody">
        <div class="text-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form method="POST" action="{{ route('transactions.pay') }}" class="w-100">
      @csrf
      <input type="hidden" name="student_id" id="paymentStudentId">
      <input type="hidden" name="org" id="paymentOrg">
      
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body px-4">

          <!-- Unpaid Events Dropdown -->
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="event_id" class="form-label">Event <span class="text-danger">*</span></label>
               <select name="event_id" id="event_id" class="form-select" required>
      <option value="">-- Select Event with Unpaid Fine --</option>
    </select>
            </div>
          </div>

          <!-- Amount and OR Number -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" readonly required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="or_number" class="form-label">Receipt Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="or_number" name="or_number" placeholder="Enter official receipt number" required>
            </div>
          </div>

          <!-- Payment Date -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
        </div>
        
        <div class="modal-footer px-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Submit Payment</button>
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

<!-- Auto-fill script -->

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
  const eventSelect = document.getElementById('event_id');
  const amountInput = document.getElementById('amount');

  // When clicking Pay button
  document.querySelectorAll('.pay-btn').forEach(button => {
    button.addEventListener('click', function () {
      const studentId = this.getAttribute('data-student-id');
      const org = this.getAttribute('data-org');

      // Fill hidden inputs
      document.getElementById('paymentStudentId').value = studentId;
      document.getElementById('paymentOrg').value = org;

      // Clear dropdown
      eventSelect.innerHTML = '<option value="">-- Select Event with Unpaid Fine --</option>';
      amountInput.value = '';

      // Fetch unpaid events
      fetch(`/students/${studentId}/unpaid-events`)
        .then(response => response.json())
        .then(events => {
          events.forEach(event => {
            const option = document.createElement('option');
            option.value = event.id;
            option.setAttribute('data-amount', event.fine_amount);
            option.textContent = `${event.name} (${event.event_date}) - ₱${parseFloat(event.fine_amount).toFixed(2)}`;
            eventSelect.appendChild(option);
          });
        });
    });
  });

  // Auto-fill amount when event selected
  eventSelect.addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const fineAmount = selectedOption.getAttribute('data-amount');
    amountInput.value = fineAmount ? fineAmount : '';
  });
});
</script>
<!-- Bootstrap JS -->




<script>

    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-success').forEach(button => {
      button.addEventListener('click', function () {
        const row = this.closest('tr');
        const studentId = row.querySelector('td:nth-child(1)').innerText.trim();
        const org = row.querySelector('td:nth-child(5)').innerText.trim();

        document.getElementById('paymentStudentId').value = studentId;
        document.getElementById('paymentOrg').value = org;

        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
      });
    });
  });
  
  document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', () => {
      const studentId = button.getAttribute('data-student-id');
      const modalBody = document.getElementById('soaModalBody');
      
      modalBody.innerHTML = `
        <div class="text-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      `;

      fetch(`/admin/soa/${studentId}`)
        .then(response => response.text())
        .then(html => {
          modalBody.innerHTML = html;
        })
        .catch(error => {
          modalBody.innerHTML = `<p class="text-danger text-center">Error loading data.</p>`;
          console.error('Error:', error);
        });
    });
  });
});
</script>

<script>
function applySearchFilter() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

// If URL has a ?search=STUDENT_ID param, auto-fill and filter
document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const searchValue = params.get("search");

    if (searchValue) {
        document.getElementById("searchInput").value = searchValue;
        applySearchFilter();
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const viewButtons = document.querySelectorAll(".view-btn");
  const payButtons = document.querySelectorAll(".btn-success");
  const modalBody = document.getElementById("modalTableBody");
  const totalBalanceDisplay = document.getElementById("modalTotalBalance");

  const studentData = {
    "2023-001": {
      name: "Jane Doe",
      course: "BSIT",
      section: "A",
      transactions: {
        "1st Semester SY 2024-2025 (Jan 2025)": [
          { date: "1/1/2025", transaction: "Assessment", debit: 6515, credit: 0, balance: 6515, processedBy: "Admin" },
          { date: "1/1/2025", transaction: "Discount", debit: 0, credit: 6515, balance: 0, processedBy: "Admin" }
        ]
      }
    },
    "2023-002": {
      name: "John Smith",
      course: "BSCS",
      section: "B",
      transactions: {
        "1st Semester SY 2024-2025 (Jan 2025)": [
          { date: "1/5/2025", transaction: "Assessment", debit: 8000, credit: 0, balance: 8000, processedBy: "Registrar" },
          { date: "1/20/2025", transaction: "Payment", debit: 0, credit: 3000, balance: 5000, processedBy: "Cashier" }
        ]
      }
    }
  };

  function formatCurrency(value) {
    return "₱" + value.toFixed(2);
  }

  function getLatestBalance(transactions) {
    if (transactions.length === 0) return 0;
    return transactions[transactions.length - 1].balance;
  }

  function renderModal(studentID) {
    const student = studentData[studentID];
    if (!student) return;

    document.getElementById("modalStudentName").textContent = student.name;
    document.getElementById("modalStudentID").textContent = studentID;
    document.getElementById("modalCourseSection").textContent = `${student.course} - ${student.section}`;

    modalBody.innerHTML = "";
    let lastBalance = 0;

    for (const [semester, transactions] of Object.entries(student.transactions)) {
      const semesterRow = document.createElement("tr");
      semesterRow.classList.add("table-secondary", "fw-bold");
      semesterRow.innerHTML = `<td colspan="6">${semester}</td>`;
      modalBody.appendChild(semesterRow);

      transactions.forEach(item => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${item.date}</td>
          <td>${item.transaction}</td>
          <td>${item.debit ? formatCurrency(item.debit) : ""}</td>
          <td>${item.credit ? formatCurrency(item.credit) : ""}</td>
          <td>${formatCurrency(item.balance)}</td>
          <td>${item.processedBy || ""}</td>
        `;
        modalBody.appendChild(tr);
      });

      lastBalance = transactions[transactions.length - 1].balance;
    }

    totalBalanceDisplay.textContent = formatCurrency(lastBalance);
  }

  function updateFinesInTable(studentID) {
    const student = studentData[studentID];
    const semester = Object.keys(student.transactions)[0];
    const transactions = student.transactions[semester];
    const latestBalance = getLatestBalance(transactions);

    const rows = document.querySelectorAll("table tbody tr");
    rows.forEach(row => {
      if (row.children[0].textContent.trim() === studentID) {
        row.children[5].textContent = formatCurrency(latestBalance);
      }
    });
  }

  viewButtons.forEach(button => {
    button.addEventListener("click", function () {
      const row = button.closest("tr");
      const studentID = row.children[0].textContent.trim();
      renderModal(studentID);
    });
  });

  payButtons.forEach(button => {
    button.addEventListener("click", function () {
      const row = button.closest("tr");
      const studentID = row.children[0].textContent.trim();
      const student = studentData[studentID];
      const semester = Object.keys(student.transactions)[0];
      const txns = student.transactions[semester];

      const lastBalance = getLatestBalance(txns);
      if (lastBalance <= 0) {
        alert("No outstanding balance.");
        return;
      }

      let input = prompt(`Enter payment amount (max: ₱${lastBalance.toFixed(2)}):`);
      if (!input) return;

      const amount = parseFloat(input);
      if (isNaN(amount) || amount <= 0 || amount > lastBalance) {
        alert("Invalid amount entered.");
        return;
      }

      const processor = prompt("Enter name of person processing this payment:");
      if (!processor || processor.trim() === "") {
        alert("Payment must be processed by someone.");
        return;
      }

      const newBalance = lastBalance - amount;
      const today = new Date().toLocaleDateString("en-PH");

      txns.push({
        date: today,
        transaction: "Payment",
        debit: 0,
        credit: amount,
        balance: newBalance,
        processedBy: processor
      });

      updateFinesInTable(studentID);

      const modalEl = document.getElementById("soaModal");
      const isModalVisible = modalEl.classList.contains("show");
      if (isModalVisible) {
        renderModal(studentID);
      }

      alert(`Payment of ₱${amount.toFixed(2)} applied to ${student.name} by ${processor}`);
    });
  });
});
</script>

<script>
  function toggleFilterCloud() {
    const cloud = document.getElementById('filterCloud');
    cloud.style.display = cloud.style.display === 'none' ? 'block' : 'none';
  }
</script>
</body>
<style>

</style>
</html>
