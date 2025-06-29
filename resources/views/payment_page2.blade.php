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
    <td>  ₱{{ number_format($balances[$student->student_id] ?? 0, 2) }}
</td>
    <td>
      <button class="btn btn-success btn-sm"><i class="fas fa-money-bill-wave"></i> Pay</button>
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

        <!-- Statement of Account Modal 1 -->
      

        <!-- Modal 2 (Optional, repeat for others as needed) -->
       

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
  <div class="modal-dialog">
    <form method="POST" action="{{ route('transactions.pay') }}">
      @csrf
      <input type="hidden" name="student_id" id="paymentStudentId">
      <input type="hidden" name="org" id="paymentOrg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Enter Payment Amount</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit Payment</button>
        </div>
      </div>
    </form>
  </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

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
</body>

</html>
