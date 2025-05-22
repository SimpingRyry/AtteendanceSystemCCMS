<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/clearance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
        <div class="container outer-box mt-5 pt-5 pb-4">
            <div class="container inner-glass shadow p-4" id="main_box">
                <div class="mb-4">
                    <h2 class="fw-bold" style="color: #232946;">Clearance</h2>
                    <small style="color: #989797;">Get /</small>
                    <small style="color: #444444;">Clearance</small>
                </div>
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="org" class="form-label">Filter by Organization</label>
                        <select name="org" id="org" class="form-select" onchange="this.form.submit()">
                            <option value="">All Organizations</option>
                            @foreach($orgs as $org)
                            <option value="{{ $org }}" {{ $selectedOrg == $org ? 'selected' : '' }}>
                                {{ $org }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="program" class="form-label">Filter by Program</label>
                        <select name="program" id="program" class="form-select" onchange="this.form.submit()">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                            <option value="{{ $program }}" {{ $selectedProgram == $program ? 'selected' : '' }}>
                                {{ $program }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Student ID</th>
                <th>Organization</th>
                <th>Program</th>
                <th>Fine Amount (₱)</th>
                <th>Status</th>
                <th>Action</th> <!-- NEW: Action Column -->
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
                        <span class="badge bg-danger">Not Eligible</span>
                    @else
                        <span class="badge bg-success">Eligible</span>
                    @endif
                </td>
                <td>
                    <a href="" class="btn btn-sm btn-primary">
                        <i class="fas fa-file-pdf"></i> Generate Clearance
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
                <!-- <div class="mb-3">
    <a href="{{ route('clearance.pdf', ['org' => request('org'), 'program' => request('program')]) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-file-pdf"></i> Generate PDF
    </a>
</div> -->
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>