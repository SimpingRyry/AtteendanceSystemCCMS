<p><strong>Student Name:</strong> {{ $student->name }}</p>
<p><strong>Student ID:</strong> {{ $student->student_id }}</p>
<p><strong>Course & Section:</strong> {{ $studentSection }}</p>
<table class="table table-bordered mt-3">
  <thead class="table-light">
    <tr>
      <th>Event</th>
      <th>Date</th>
      <th>Transaction</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
    @php $grandTotal = 0; @endphp

    @foreach ($transactionsGrouped as $acadCode => $transactions)
      <tr>
        <td colspan="4" class="table-primary fw-bold">
          Term: {{ $transactions->first()->acad_term }} | Code: {{ $acadCode }}
        </td>
      </tr>

      @foreach ($transactions as $transaction)
        @php
          $amount = $transaction->fine_amount;
          if ($transaction->transaction_type === 'FINE') {
              $grandTotal += $amount;
              $sign = '+';
              $color = 'text-danger';
          } else {
              $grandTotal -= $amount;
              $sign = '-';
              $color = 'text-success';
          }
        @endphp
        <tr>
          <td>{{ $transaction->event }}</td>
          <td>{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
          <td>{{ $transaction->transaction_type }}</td>
          <td class="{{ $color }}">{{ $sign }}{{ number_format($amount, 2) }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>

<div class="mt-3 fw-bold">
  Total Remaining Balance: ₱{{ number_format($grandTotal, 2) }}
</div>