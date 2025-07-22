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
        <h2 class="fw-bold" style="color: #232946;">Statement of Account</h2>
        <small style="color: #989797;">Student /</small>
        <small style="color: #444444;">Statement of Account</small>
      </div>

      <!-- Filter Form -->
      <div class="mb-4">
        <form class="row g-3" method="GET" action="{{ route('student.statement') }}">
          <!-- Semester Input -->
          <div class="col-md-3">
            <label for="inputSemester" class="form-label fw-semibold">Semester</label>
            <input type="text" class="form-control" name="semester" id="inputSemester" placeholder="e.g., 1st Semester"
                   value="{{ request('semester') }}">
          </div>

          <!-- Organization Filter -->
          <div class="col-md-3">
            <label for="inputOrganization" class="form-label fw-semibold">Organization</label>
            <select id="inputOrganization" name="organization" class="form-select" onchange="this.form.submit()">
              <option disabled {{ is_null($selectedOrg) ? 'selected' : '' }}>Select organization</option>
              <option value="All" {{ $selectedOrg == 'All' ? 'selected' : '' }}>All</option>
              <option value="CCMS Student Government" {{ $selectedOrg == 'CCMS Student Government' ? 'selected' : '' }}>CCMS Student Government</option>
              <option value="{{ auth()->user()->org }}" {{ $selectedOrg == auth()->user()->org ? 'selected' : '' }}>
                {{ auth()->user()->org }}
              </option>
            </select>
          </div>
        </form>
      </div>

      <!-- Student Info -->
      <p><strong>Student Name:</strong> <span id="studentName">{{ $student->name }}</span></p>
      <p><strong>Student ID:</strong> <span id="studentID">{{ $student->student_id }}</span></p>
      <p><strong>Course & Section:</strong> <span id="studentCourseSection">{{ $studentSection }}</span></p>

      <!-- Statement of Account Table -->
      <div class="table-responsive">
        <table class="table table-bordered mt-3">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Event</th>
              <th>Transaction</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Balance</th>
              <th>OR/Reference No.</th>
            </tr>
          </thead>
          <tbody id="soaTableBody">
            @php $grandTotal = 0; @endphp

            @forelse ($transactionsGrouped as $acadCode => $transactions)
              <tr>
                <td colspan="7" class="table-primary fw-bold">
                  Academic Code: {{ $acadCode }}
                </td>
              </tr>

              @php $balance = 0; @endphp
              @foreach ($transactions as $transaction)
                @php
                  $debit = $transaction->transaction_type === 'FINE' ? $transaction->fine_amount : 0;
                  $credit = $transaction->transaction_type === 'PAYMENT' ? $transaction->fine_amount : 0;
                  $balance += ($debit - $credit);
                  $grandTotal += ($debit - $credit);
                @endphp
                <tr>
                  <td>{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                  <td>{{ $transaction->event }}</td>
                  <td>{{ $transaction->transaction_type }}</td>
                  <td>{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                  <td>{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                  <td>{{ number_format($balance, 2) }}</td>
                  <td>{{ $transaction->or_num ?? '-' }}</td>
                </tr>
              @endforeach
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No transactions found for the selected filter.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 fw-bold">
        Total Remaining Balance: <span id="totalBalance">{{ number_format($grandTotal, 2) }}</span>
      </div>

      <!-- Pay Online Button -->
      <div class="mt-4" id="payOnlineWrapper" style="display: none;">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#onlinePaymentModal">
          <i class="fas fa-wallet me-1"></i> Pay Online
        </button>
      </div>
    </div>
  </div>
</main>
<div class="modal fade" id="onlinePaymentModal" tabindex="-1" aria-labelledby="onlinePaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="onlinePaymentModalLabel">Online Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="onlinePaymentForm" method="POST" action="{{ route('gcash.pay') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="paymentAmount" class="form-label">Enter Amount to Pay</label>
            <input type="number" class="form-control" id="paymentAmount" name="amount" placeholder="₱0.00" required min="20">
            <small class="form-text text-danger mt-1">
              Please pay either the exact amount or any amount <strong>not less than ₱20.00</strong>.
            </small>
          </div>
          <div class="text-muted small">
            You will be redirected to GCash to complete your payment.
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-1"></i> Proceed Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(session('error'))
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-danger mb-3">
          <i class="bi bi-x-circle-fill" style="font-size: 60px;"></i>
        </div>
        <h5 class="text-danger">Error!</h5>
        <p>{{ session('error') }}</p>
        <button type="button" class="btn btn-danger mt-2" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const orgSelect = document.getElementById('inputOrganization');
    const payBtnWrapper = document.getElementById('payOnlineWrapper');

    function togglePayButton(value) {
      if (
        value &&
        value !== 'All' &&
        value !== 'Select organization'
      ) {
        payBtnWrapper.style.display = 'block';
      } else {
        payBtnWrapper.style.display = 'none';
      }
    }

    // Check on page load
    togglePayButton(orgSelect.value);

    // Also update on change
    orgSelect.addEventListener('change', function () {
      togglePayButton(this.value);
    });
  });
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
@if(session('error'))
<script>
  const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
  errorModal.show();
</script>
@endif
<script>
  const soaTableBody = document.getElementById("soaTableBody");
  const totalBalanceDisplay = document.getElementById("totalBalance");

  // let totalBalance = 5000.00; // Example starting balance

  // function updateBalanceDisplay() {
  //   totalBalanceDisplay.textContent = `₱${totalBalance.toFixed(2)}`;
  // }

  

  // Initial display
  // updateBalanceDisplay();
</script>

<script>
  // Sample student ID for demonstration; in real app, get this dynamically
  // const currentStudentID = "2023-001";

  // const studentData = {
  //   "2023-001": {
  //     name: "Jane Doe",
  //     course: "BSIT",
  //     section: "A",
  //     transactions: {
  //       "1st Semester SY 2024-2025 (Jan 2025)": [
  //         { date: "1/1/2025", transaction: "Assessment", debit: 5000, credit: 0, balance: 5000}
          
  //       ]
  //     }
  //   },
  //   "2023-002": {
  //     name: "John Smith",
  //     course: "BSCS",
  //     section: "B",
  //     transactions: {
  //       "1st Semester SY 2024-2025 (Jan 2025)": [
  //         { date: "1/5/2025", transaction: "Assessment", debit: 8000, credit: 0, balance: 8000, processedBy: "Registrar" },
  //         { date: "1/20/2025", transaction: "Payment", debit: 0, credit: 3000, balance: 5000, processedBy: "Cashier" }
  //       ]
  //     }
  //   }
  // };

  // function formatCurrency(value) {
  //   return "₱" + value.toFixed(2);
  // }

  // function renderSOATable(studentID) {
  //   const student = studentData[studentID];
  //   if (!student) return;

  //   document.getElementById("studentName").textContent = student.name;
  //   document.getElementById("studentID").textContent = studentID;
  //   document.getElementById("studentCourseSection").textContent = `${student.course} - ${student.section}`;

  //   const tbody = document.getElementById("soaTableBody");
  //   tbody.innerHTML = "";

  //   let lastBalance = 0;
  //   for (const [semester, transactions] of Object.entries(student.transactions)) {
  //     // Semester row as a header inside tbody
  //     const semesterRow = document.createElement("tr");
  //     semesterRow.classList.add("table-secondary", "fw-bold");
  //     semesterRow.innerHTML = `<td colspan="6">${semester}</td>`;
  //     tbody.appendChild(semesterRow);

  //     transactions.forEach(txn => {
  //       const tr = document.createElement("tr");
  //       tr.innerHTML = `
  //         <td>${txn.date}</td>
  //         <td>${txn.transaction}</td>
  //         <td>${txn.debit ? formatCurrency(txn.debit) : ""}</td>
  //         <td>${txn.credit ? formatCurrency(txn.credit) : ""}</td>
  //         <td>${formatCurrency(txn.balance)}</td>
  //         <td>${txn.processedBy || ""}</td>
  //       `;
  //       tbody.appendChild(tr);
  //     });

  //     lastBalance = transactions[transactions.length - 1].balance;
  //   }

  //   document.getElementById("totalBalance").textContent = formatCurrency(lastBalance);
  // }

  // // On page load, render SOA for the current student
  // document.addEventListener("DOMContentLoaded", () => {
  //   renderSOATable(currentStudentID);
  // });
</script>
</body>

</html>
