<div class="table-responsive mt-3">
    <form id="bulkRevertForm" action="{{ route('attendance.bulkRevert') }}" method="POST">
        @csrf
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Student ID</th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Name</th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Program</th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Block</th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Event</th>
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Date</th>

                    @if ($event->timeouts == 4)
                        <th colspan="2" class="text-center">Morning</th>
                        <th colspan="2" class="text-center">Afternoon</th>
                        <th colspan="2" class="text-center">Status</th>
                    @else
                        <th>Time-In</th>
                        <th>Time-Out</th>
                        <th>Status</th>
                    @endif
                    <th rowspan="{{ $event->timeouts == 4 ? 2 : 1 }}">Action</th>
                </tr>
                @if ($event->timeouts == 4)
                    <tr>
                        <th></th>
                        <th>Time-In</th>
                        <th>Time-Out</th>
                        <th>Time-In</th>
                        <th>Time-Out</th>
                        <th>Morning</th>
                        <th>Afternoon</th>
                    </tr>
                @endif
            </thead>
            <tbody>
            @foreach ($attendances as $attendance)
                @php
                    $isAbsent = $event->timeouts == 4
                        ? strtolower($attendance->morning_status) === 'absent' || strtolower($attendance->afternoon_status) === 'absent'
                        : strtolower($attendance->status) === 'absent';

                    $isExcused = $event->timeouts == 4
                        ? strtolower($attendance->morning_status) === 'excused' && strtolower($attendance->afternoon_status) === 'excused'
                        : strtolower($attendance->status) === 'excused';
                @endphp
                <tr>
                    <td>
                        @if ($isAbsent && !$isExcused)
                            <input type="checkbox" name="attendance_ids[]" value="{{ $attendance->id }}">
                        @endif
                    </td>
                    <td>{{ $attendance->student_id }}</td>
                    <td>{{ $attendance->student->name ?? 'N/A' }}</td>
                    <td>{{ $attendance->student->course ?? 'N/A' }}</td>
                    <td>{{ $attendance->student->section ?? 'N/A' }}</td>
                    <td>{{ $attendance->event->name }}</td>
                    <td>{{ $attendance->date }}</td>

                    @if ($event->timeouts == 4)
                        <td>{{ $attendance->time_in1 }}</td>
                        <td>{{ $attendance->time_out1 }}</td>
                        <td>{{ $attendance->time_in2 }}</td>
                        <td>{{ $attendance->time_out2 }}</td>
                        <td>{{ $attendance->morning_status }}</td>
                        <td>{{ $attendance->afternoon_status }}</td>
                    @else
                        <td>{{ $attendance->time_in1 }}</td>
                        <td>{{ $attendance->time_out1 }}</td>
                        <td>{{ $attendance->status }}</td>
                    @endif

                    <td>
                        @if ($isExcused)
                            <button type="button" class="btn btn-info viewExcuseBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#viewExcuseModal"
                                data-student="{{ $attendance->student_id }}"
                                data-name="{{ $attendance->student->name }}"
                                data-event="{{ $attendance->event->name }}"
                                data-date="{{ $attendance->event->event_date }}"
                                data-reason="{{ $attendance->excuse_reason }}"
                                data-letter="{{ asset($attendance->excuse_letter) }}">
                                View Excuse
                            </button>
                        @elseif ($isAbsent)
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#excuseModal"
                                data-student="{{ $attendance->student_id }}"
                                data-name="{{ $attendance->student->name }}"
                                data-event="{{ $attendance->event->name }}"
                                data-date="{{ $attendance->date }}"
                                data-attendanceid="{{ $attendance->id }}">
                                File Excuse
                            </button>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button id="revertBtn" type="submit" class="btn btn-danger mt-2" disabled>
            Revert Status
        </button>
    </form>
</div>

<script>
const selectAll = document.getElementById('selectAll');
const revertBtn = document.getElementById('revertBtn');

function toggleRevertButton() {
    let checkboxes = document.querySelectorAll('input[name="attendance_ids[]"]');
    let anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    revertBtn.disabled = !anyChecked;
}

// Handle "Select All"
selectAll.addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('input[name="attendance_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleRevertButton();
});

// Handle individual checkboxes
document.addEventListener('change', function(e) {
    if (e.target && e.target.name === "attendance_ids[]") {
        toggleRevertButton();
    }
});
</script>
