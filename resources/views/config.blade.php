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

    <title>Ticktax Attendance System</title>
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

        @if (Auth::user()->role === 'OSSD')
            <!-- Show only Academic Settings tab for OSSD -->
            <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">Academic Settings</button>
                </li>
            </ul>

            <div class="tab-content" id="configTabContent">
                <div class="tab-pane fade show active" id="academic" role="tabpanel">
                    <div class="card shadow border-0 rounded-3 p-4">
                        <h4 class="fw-bold mb-4">Academic Year & Term Settings</h4>
                        <form action="{{ route('settings.updateAcademicYear') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Academic Year & Term</label>
                                <input type="text" name="academic_term" class="form-control" placeholder="e.g., 24-1 First Sem A.Y. 2024-2025" value="{{ $academicTerm ?? '' }}">
                                <small class="form-text text-muted">Example: 24-1 First Sem A.Y. 2024-2025</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4">Save Academic Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Nav Tabs for other roles -->
            <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="fines-tab" data-bs-toggle="tab" data-bs-target="#fines" type="button" role="tab">Fine and Time Settings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fine-history-tab" data-bs-toggle="tab" data-bs-target="#fine-history" type="button" role="tab">Fine History</button>
                </li>
                @if (Auth::user()->role === 'Adviser')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="officer-tab" data-bs-toggle="tab" data-bs-target="#officer" type="button" role="tab">Manage Officer Roles</button>
                    </li>
                @endif
            </ul>

            <div class="tab-content" id="configTabContent">
                <!-- Your original Fine Settings tab content -->
                <!-- Your original Fine History tab content -->
                <!-- Your original Officer Roles tab content -->
                {{-- Keep all content here as-is --}}
                <!-- FINE SETTINGS TAB -->
              <div class="tab-pane fade show active" id="fines" role="tabpanel">
    <div class="card shadow border-0 rounded-3 p-4 mb-4">
        <h4 class="fw-bold mb-4">Fine and Time Configuration</h4>
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

            <div class="row mb-4 mt-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Grace Period (Minutes)</label>
                    <div class="input-group">
                        <input type="number" name="grace_period_minutes" class="form-control" value="{{ (int) ($fines->grace_period_minutes ?? 0) }}" min="0" step="1">
                        <span class="input-group-text">minutes</span>
                    </div>
                    <small class="form-text text-muted">Set how many minutes after the call time a student can time in before being marked late.</small>
                </div>
            </div>

            <div class="row mb-4">
                <h5 class="fw-bold mt-4">Default Time Settings</h5>
                <p class="text-muted">Set default in/out times for scheduled events.</p>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Morning In</label>
                    <input type="time" name="morning_in" class="form-control" value="{{ $fines->morning_in ?? '08:00' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Morning Out</label>
                    <input type="time" name="morning_out" class="form-control" value="{{ $fines->morning_out ?? '12:00' }}">
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label fw-semibold">Afternoon In</label>
                    <input type="time" name="afternoon_in" class="form-control" value="{{ $fines->afternoon_in ?? '13:00' }}">
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label fw-semibold">Afternoon Out</label>
                    <input type="time" name="afternoon_out" class="form-control" value="{{ $fines->afternoon_out ?? '17:00' }}">
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
        </form>
    </div>
</div>


                <!-- FINE HISTORY TAB -->
                <div class="tab-pane fade" id="fine-history" role="tabpanel">
                    <div class="card shadow border-0 rounded-3 p-4 mb-4">
                        <h4 class="fw-bold mb-3">Fine Configuration History</h4>
                        @if($academicTerm && $acadCode)
                            <div class="mb-3">
                                <strong>Current Academic Term:</strong> {{ $academicTerm }}<br>
                                <strong>Academic Code:</strong> {{ $acadCode }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <input type="text" class="form-control" id="fineSearch" placeholder="Search fine history by academic code...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="fineHistoryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount (₱)</th>
                                        <th>Changed By</th>
                                        <th>Date Changed</th>
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
                </div>

                @if (Auth::user()->role === 'Adviser')
                <!-- OFFICER TAB -->
                <div class="tab-pane fade" id="officer" role="tabpanel">
                    <div class="card shadow border-0 rounded-3 p-4 mb-4">
                        <h4 class="fw-bold mb-4">Manage Officer Roles for Your Organization</h4>

                        <form action="{{ route('officer-roles.store') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Position Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g., President, Treasurer" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Officer Role</button>
                        </form>

                        <hr>

                        <h5 class="fw-bold mb-3">Current Officer Roles</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($officerRoles as $role)
                                        <tr>
                                            <td>{{ $role->title }}</td>
                                            <td>{{ $role->description ?? '—' }}</td>
                                            <td>
                                                <form action="{{ route('officer-roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No officer roles found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endif
    </div>
</main>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('fineSearch');

    input.addEventListener('input', function () {
        const acadCode = input.value.trim();

        if (acadCode.length === 0) return;

        fetch(`/fine-history/search/${acadCode}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#fineHistoryTable tbody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No fine history found.</td></tr>';
                return;
            }

            data.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${record.type}</td>
                    <td>₱${parseInt(record.amount).toLocaleString()}</td>
                    <td>${record.updated_by_name ?? 'System'}</td>
                    <td>${record.changed_at}</td>
                `;
                tbody.appendChild(row);
            });
        });
    });
});
</script>

    <!-- Optional: JS to search rows -->
    <script>
        document.getElementById('fineSearch').addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#fineHistoryTable tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
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
