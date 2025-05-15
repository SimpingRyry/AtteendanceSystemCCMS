<!DOCTYPE html>
<html>
<head>
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #eeeeee;
        }
        h2 {
            text-align: center;
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
            @foreach($financeReports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->student_id }}</td>
                    <td>{{ $report->student_name }}</td>
                    <td>{{ $report->org }}</td>
                    <td>{{ $report->program }}</td>
                    <td>₱{{ number_format($report->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->created_at)->toFormattedDateString() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
