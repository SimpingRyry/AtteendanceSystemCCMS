<!DOCTYPE html>
<html>
<head>
    <title>Finance Clearance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Finance Clearance Report</h2>
    <div class="date">Date: {{ $date }}</div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Organization</th>
                <th>Program</th>
                <th>Fine Amount (₱)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financeSummary as $record)
            <tr>
                <td>{{ $record->student_id }}</td>
                <td>{{ $record->org }}</td>
                <td>{{ $record->program }}</td>
                <td>₱{{ number_format($record->total_fines, 2) }}</td>
                <td>
                    @if($record->total_fines > 0)
                        Not Cleared
                    @else
                        Cleared
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
