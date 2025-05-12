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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">
    @include('layout.navbar')
    @include('layout.sidebar')

<main class="container mt-5 pt-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="w-75">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Fine Configuration -->
        <div class="card shadow mb-5 border-0 rounded-2 p-4">
            <h4 class="fw-bold mb-4">Fine Configuration</h4>
            <form action="{{ route('settings.updateFines') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Absentee Fine (Member)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="absent_member" class="form-control" value="{{ $fines->absent_member ?? 0 }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for members who are absent.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Absentee Fine (Officer)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="absent_officer" class="form-control" value="{{ $fines->absent_officer ?? 0 }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for officers who are absent.</small>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Late Fine (Member)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="late_member" class="form-control" value="{{ $fines->late_member ?? 0 }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for members who arrive late.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Late Fine (Officer)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="late_officer" class="form-control" value="{{ $fines->late_officer ?? 0 }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for officers who arrive late.</small>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Save Fines</button>
                </div>
            </form>
        </div>

        <!-- Academic Year and Term Configuration -->
        <div class="card shadow border-0 rounded-2 p-4">
            <h4 class="fw-bold mb-4">Academic Year & Term Settings</h4>
            <form action="{{ route('settings.updateAcademicYear') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Academic Year</label>
                        <select name="academic_year" class="form-select">
                            <option value="2024-2025" {{ ($settings->academic_year ?? '') === '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                            <option value="2025-2026" {{ ($settings->academic_year ?? '') === '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                            <option value="2026-2027" {{ ($settings->academic_year ?? '') === '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                            <!-- Add more years as needed -->
                        </select>
                        <small class="form-text text-muted">Select the current academic year.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Term</label>
                        <select name="term" class="form-select">
                            <option value="1st Semester" {{ ($settings->term ?? '') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ ($settings->term ?? '') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ ($settings->term ?? '') === 'Summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                        <small class="form-text text-muted">Select the current academic term.</small>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Save Academic Settings</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
