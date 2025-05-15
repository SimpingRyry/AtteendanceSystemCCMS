<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Roster Report</title>
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
            margin-left: auto;
            margin-right: auto;
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
    <h2>Student Roster Report</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Program</th>
                <th>Year</th>
                <th>Units</th>
                <th>Section</th>
                <th>Contact No.</th>
                <th>Birth Date</th>
                <th>Address</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->id_number }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->course }}</td>
                <td>{{ $student->year }}</td>
                <td>{{ $student->units }}</td>
                <td>{{ $student->section }}</td>
                <td>{{ $student->contact_no }}</td>
                <td>{{ \Carbon\Carbon::parse($student->birth_date)->format('M d, Y') }}</td>
                <td>{{ $student->address }}</td>
                <td>{{ $student->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
