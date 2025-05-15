<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        thead tr {
            background-color: #f2f2f2;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        tfoot tr {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Financial Report</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Organization</th>
                <th>Program</th>
                <th>Amount</th>
                <th>Date Issued</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($financeReports as $index => $report)
                @php $total += $report->amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->student_id }}</td>
                    <td>{{ $report->student_name ?? 'N/A' }}</td>
                    <td>{{ $report->org }}</td>
                    <td>{{ $report->program }}</td>
                    <td class="right">Php {{ number_format($report->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->date_issued)->toFormattedDateString() }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="right">Total Amount:</td>
                <td class="right">Php {{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>