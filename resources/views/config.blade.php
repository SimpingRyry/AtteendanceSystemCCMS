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

    <main class="d-flex justify-content-center align-items-start py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container" style="max-width: 960px; width: 100%;">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="mb-4">
            <h2 class="fw-bold" style="color: #232946;">Configure</h2>
            <small style="color: #989797;">Manage /</small>
            <small style="color: #444444;">Configure</small>
        </div>

        <div class="card shadow border-0 rounded-3 p-4 mb-4">
            <h4 class="fw-bold mb-3">Fine Configuration History</h4>

            <!-- Search bar -->
            <div class="mb-3">
                <input type="text" class="form-control" id="fineSearch" placeholder="Search fine history...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="fineHistoryTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Type</th>
                            <th>Amount (₱)</th>
                            <th scope="col">Changed By</th>
                            <th scope="col">Date Changed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $index => $record)
                            <tr class="{{ $index >= 12 ? 'extra-row d-none' : '' }}">
                                <td>{{ $record->type }}</td>
                                <td>₱{{ number_format($record->amount, 0) }}</td>
                                <td>{{ $record->updated_by ? \App\Models\User::find($record->updated_by)->name : 'System' }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->changed_at)->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No fine history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($history) > 12)
                <div class="text-center mt-3">
                    <button id="toggleBtn" class="btn btn-outline-primary">See More</button>
                </div>
            @endif
        </div>

        <!-- Fine Configuration -->
        <div class="card shadow border-0 rounded-3 p-4 mb-4">
            <h4 class="fw-bold mb-4">Fine Configuration</h4>
            <form action="{{ route('settings.updateFines') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Absentee Fine (Member)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="absent_member" class="form-control" value="{{ (int) ($fines->absent_member ?? 0) }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for members who are absent.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Absentee Fine (Officer)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="absent_officer" class="form-control" value="{{ (int) ($fines->absent_officer ?? 0) }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for officers who are absent.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Late Fine (Member)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="late_member" class="form-control" value="{{ (int) ($fines->late_member ?? 0) }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for members who arrive late.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Late Fine (Officer)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="late_officer" class="form-control" value="{{ (int) ($fines->late_officer ?? 0) }}" min="0" step="1">
                        </div>
                        <small class="form-text text-muted">Set the fine for officers who arrive late.</small>
                    </div>
                </div>
                <div class="row mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Grace Period (Minutes)</label>
        <div class="input-group">
            <input type="number" name="grace_period_minutes" class="form-control" value="{{ (int) ($fines->grace_period_minutes ?? 0) }}" min="0" step="1">
            <span class="input-group-text">minutes</span>
        </div>
        <small class="form-text text-muted">Set how many minutes after the call time a student can time in before being marked late.</small>
    </div>
</div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Save Fines</button>
                </div>
            </form>
        </div>

        <!-- Academic Year and Term Settings -->
        <div class="card shadow border-0 rounded-3 p-4">
            <h4 class="fw-bold mb-4">Academic Year & Term Settings</h4>
            <form action="{{ route('settings.updateAcademicYear') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Academic Year & Term</label>
                    <input type="text" name="academic_term" class="form-control" placeholder="e.g., 24-1 First Sem A.Y. 2024-2025" value="{{ $academic_term ?? '' }}">
                    <small class="form-text text-muted">Example: 24-1 First Sem A.Y. 2024-2025</small>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Save Academic Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS toggle functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleBtn');
            let expanded = false;

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const rows = document.querySelectorAll('.extra-row');
                    rows.forEach(row => row.classList.toggle('d-none'));
                    toggleBtn.textContent = expanded ? 'See More' : 'See Less';
                    expanded = !expanded;
                });
            }
        });
    </script>
</main>
</body>
<script>
document.getElementById("fineSearch").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#fineHistoryTable tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</html>
