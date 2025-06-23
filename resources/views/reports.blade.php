<!-- resources/views/reports.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCMS Attendance System</title>

    <!-- External Resources -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Local Styles -->
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">
</head>

<body style="background-color: #fffffe;">
    @include('layout.navbar')
    @include('layout.sidebar')

<main>
    <div class="container outer-box mt-5 pt-5 pb-4">
        <div class="container inner-glass shadow p-4" id="main_box">
            <div class="mb-4">
                <h2 class="fw-bold" style="color: #232946;">Reports</h2>
                <small style="color: #989797;">Manage /</small>
                <small style="color: #444444;">Reports</small>
            </div>

            {{-- Financial Report Section --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white fw-semibold">
                    Generate Financial Report
                </div>
                <div class="card-body">
                    <form action="{{ route('report.financial') }}" method="GET" target="_blank">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    <option value="">All</option>
                                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year</label>
                                <input type="number" name="year" class="form-control" placeholder="e.g., 2025" min="2000" max="{{ now()->year }}">
                            </div>
                            @if(auth()->user()->role === 'Super Admin')
                            <div class="col-md-4">
                                <label class="form-label">Organization</label>
                                <select name="organization" class="form-select">
                                    <option value="">All</option>
                                    <option value="Information Technology Society">Information Technology Society</option>
                                    <option value="PRAXIS">PRAXIS</option>
                                </select>
                            </div>
                            @endif
                        </div>

                        <input type="hidden" name="export" value="pdf">

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-file-export"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Student Roster Section --}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white fw-semibold">
                    Generate Student Roster
                </div>
                <div class="card-body">
                    <form action="{{ route('report.studentRoster') }}" method="GET">
                       
                        <div class="row g-3">
                            @if(auth()->user()->role === 'Super Admin')
                            <div class="col-md-4">
                                <label class="form-label">Organization</label>
                                <select class="form-select" name="organization">
                                    <option value="">All</option>
                                    <option value="ITS">ITS</option>
                                    <option value="PRAXIS">PRAXIS</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Program</label>
                                <select class="form-select" name="program">
                                    <option value="">All</option>
                                    <option value="IT">Information Technology</option>
                                    <option value="IS">Information Systems</option>
                                </select>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <label class="form-label">Year Level</label>
                                <select class="form-select" name="year_level">
                                    <option value="">All</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-users-viewfinder"></i> Generate Roster
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>