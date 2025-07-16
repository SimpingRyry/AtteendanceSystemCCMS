<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@foreach ($pagedChunks as $index => $chunk)
    <h2>{{ $title }}</h2>
    <p>Date: <strong>{{ $chunk['date'] }}</strong></p>
    <p>Page {{ $index + 1 }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Section</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chunk['students'] as $i => $student)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->section }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
