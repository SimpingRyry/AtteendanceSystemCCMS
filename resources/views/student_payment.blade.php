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

      <!-- Filter Form (optional, can be used to filter semesters or years) -->
      <div class="mb-4">
  <form class="row g-3" onsubmit="return false;">
    <div class="col-md-3">
      <label for="inputSemester" class="form-label fw-semibold">Semester</label>
      <input type="text" class="form-control" id="inputSemester" placeholder="e.g., 1st Semester">
    </div>
  </form>
</div>

      <!-- Student Info -->
      <p><strong>Student Name:</strong> <<span id="studentName">{{ $student->name }}</span></p>
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
            </tr>
          </thead>
          <tbody id="soaTableBody">
          @php $grandTotal = 0; @endphp

    @foreach ($transactionsGrouped as $acadCode => $transactions)
        <tr>
            <td colspan="6" class="table-primary fw-bold">
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
            </tr>
        @endforeach
    @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3 fw-bold">
        Total Remaining Balance: <span id="totalBalance">{{ number_format($grandTotal, 2) }}</span>
      </div>
      <div class="mt-4">
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
      <form id="onlinePaymentForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="paymentAmount" class="form-label">Enter Amount to Pay</label>
            <input type="number" class="form-control" id="paymentAmount" placeholder="₱0.00" required min="1">
          </div>
          <div class="text-muted small">Note: This simulates an online payment transaction. In future, it can be linked to a payment gateway.</div>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const soaTableBody = document.getElementById("soaTableBody");
  const totalBalanceDisplay = document.getElementById("totalBalance");

  // let totalBalance = 5000.00; // Example starting balance

  // function updateBalanceDisplay() {
  //   totalBalanceDisplay.textContent = `₱${totalBalance.toFixed(2)}`;
  // }

  document.getElementById("onlinePaymentForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const amount = parseFloat(document.getElementById("paymentAmount").value);
    if (isNaN(amount) || amount <= 0 || amount > totalBalance) {
      alert("Invalid amount. Please enter a valid payment.");
      return;
    }

    // Simulate transaction
    const today = new Date().toLocaleDateString();
    totalBalance -= amount;

    const newRow = document.createElement("tr");
    newRow.innerHTML = `
      <td>${today}</td>
      <td>Online Payment</td>
      <td></td>
      <td>₱${amount.toFixed(2)}</td>
      <td>₱${totalBalance.toFixed(2)}</td>
      <td>Online System</td>
    `;
    soaTableBody.appendChild(newRow);
    updateBalanceDisplay();

    // Reset and close modal
    document.getElementById("paymentAmount").value = '';
    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('onlinePaymentModal'));
    paymentModal.hide();
  });

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
