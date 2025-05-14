@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm rounded">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Financial Report</h4>
        </div>

        <div class="card-body">
            @if($financeReports->isEmpty())
                <div class="alert alert-warning mb-0">
                    No financial data found for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Student ID</th>
                                <th>Organization</th>
                                <th>Program</th>
                                <th class="text-end">Amount (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalAmount = 0; @endphp
                            @foreach($financeReports as $report)
                                @php $totalAmount += $report->amount; @endphp
                                <tr>
                                    <td>{{ $report->student_id }}</td>
                                    <td>{{ $report->org }}</td>
                                    <td>{{ $report->program }}</td>
                                    <td class="text-end">{{ number_format($report->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-semibold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
