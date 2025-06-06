<p><strong>Student Name:</strong> {{ $student->name }}</p>
<p><strong>Student ID:</strong> {{ $student->student_id }}</p>
<p><strong>Course & Section:</strong> {{ $studentSection }}</p>

<table class="table table-bordered mt-3">
  <thead class="table-light">
    <tr>
      <th>Date</th>
      <th>Transaction</th>
      <th>Debit</th>
      <th>Credit</th>
      <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    @php $grandTotal = 0; @endphp
    @foreach ($transactionsGrouped as $acadCode => $transactions)
      <tr>
        <td colspan="5" class="table-primary fw-bold">
          Term: {{ $transactions->first()->acad_term }} | Code: {{ $acadCode }}
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
          <td>{{ $transaction->transaction_type }}</td>
          <td>{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
          <td>{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
          <td>{{ number_format($balance, 2) }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>

<div class="mt-3 fw-bold">
  Total Remaining Balance: ₱{{ number_format($grandTotal, 2) }}
</div>