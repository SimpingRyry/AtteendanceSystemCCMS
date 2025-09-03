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
    <link rel="stylesheet" href="{{ asset('css/dash_device.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            <!-- Heading -->
            <div class="mb-3">
                <h2 class="fw-bold" style="color: #232946;">Device Status</h2>
                <small style="color: #989797;">Manage /</small>
                <small style="color: #444444;">Device</small>
            </div>

            <!-- Add Device Button -->
            <div class="d-flex justify-content-end mb-4">
                <button id="addDeviceBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                    <i class="fas fa-plus me-2"></i> Add Device
                </button>
            </div>

            <!-- Devices Container -->
            <div id="deviceContainer" class="row g-4">
    @foreach ($devices as $device)
<div class="col-md-6 col-lg-3">
        <div class="card p-3 rounded status-box glass-box shadow-sm position-relative">
            <h5 class="mb-3 text-center">
                <i class="fas fa-microchip me-2"></i>{{ $device->name }}
            </h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-wifi me-2"></i>Connected to Device</div>
                    <div class="indicator-circle {{ $device->is_online ? 'bg-success blinking' : 'bg-danger' }}"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-user-check me-2"></i>Accepting Attendance</div>
                   <div class="indicator-circle {{ $device->is_online ? 'bg-success blinking' : 'bg-danger' }}"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
    <div><i class="fas fa-volume-up me-2"></i>Speaker</div>
    <div class="form-check form-switch">
       <input class="form-check-input speaker-toggle"
       type="checkbox"
       role="switch"
       data-device-name="{{ $device->name }}"
       {{ $device->is_online ? '' : 'disabled' }}
       {{ $device->is_muted ? '' : 'checked' }}>
    </div>
    <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2 open-settings-btn"
    data-device-id="{{ $device->id }}"
    data-device-name="{{ $device->name }}"
    data-device-password="{{ $device->password }}"
    data-clock-format="{{ $device->clock_format }}">
    <i class="fas fa-cog"></i>
</button>
</div>
            </div>
        </div>
        
    </div>
    @endforeach
</div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeviceModalLabel">Add Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deviceForm">
                        <div class="mb-3">
                            <label for="deviceName" class="form-label">Device Name</label>
                            <input type="text" class="form-control" id="deviceName" required>
                        </div>
                        <div class="mb-3">
                            <label for="devicePassword" class="form-label">Device Password</label>
                            <input type="password" class="form-control" id="devicePassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Connect Device</button>
                    </form>
                    <div class="mt-2 text-danger" id="deviceError" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded">
      <div class="modal-header">
        <h5 class="modal-title" id="settingsModalLabel">Device Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="deviceSettingsForm">
          <input type="hidden" id="modalDeviceId">
          <div class="mb-3">
            <label for="modalDeviceName" class="form-label">Device Name</label>
            <input type="text" class="form-control" id="modalDeviceName" required>
          </div>
          <div class="mb-3">
            <label for="modalDevicePassword" class="form-label">Device Password</label>
            <input type="password" class="form-control" id="modalDevicePassword" required>
          </div>
          <div class="mb-3">
            <label for="modalClockFormat" class="form-label">Clock Format</label>
            <select class="form-select" id="modalClockFormat">
              <option value="12-hour">12-hour</option>
              <option value="24-hour">24-hour</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100">Save Settings</button>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-success mb-3">
          <i class="fa fa-check-circle" style="font-size: 60px;"></i>
        </div>
        <h5 class="text-success">Success!</h5>
        <p id="successMessage">Your changes were saved successfully.</p>
        <button type="button" class="btn btn-success mt-2" id="successOkBtn">OK</button>
      </div>
    </div>
  </div>
</div>

    <style>
        .indicator-circle {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: gray;
        }
        .bg-success {
            background-color: #28a745 !important;
        }
        .blinking {
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
<script>
          function showSuccess(message) {
    document.getElementById("successMessage").innerText = message;
    const modal = new bootstrap.Modal(document.getElementById("successModal"));
    modal.show();

    document.getElementById("successOkBtn").onclick = function () {
        modal.hide();
        window.location.reload();
    };
}
document.querySelectorAll('.open-settings-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.deviceId;
        const name = button.dataset.deviceName;
        const password = button.dataset.devicePassword;
        const format = button.dataset.clockFormat;

        document.getElementById('modalDeviceId').value = id;
        document.getElementById('modalDeviceName').value = name;
        document.getElementById('modalDevicePassword').value = password;
        document.getElementById('modalClockFormat').value = format;

        new bootstrap.Modal(document.getElementById('settingsModal')).show();
    });
});

document.getElementById('deviceSettingsForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const deviceId = document.getElementById('modalDeviceId').value;
    const name = document.getElementById('modalDeviceName').value;
    const password = document.getElementById('modalDevicePassword').value;
    const clockFormat = document.getElementById('modalClockFormat').value;

    try {
        const response = await fetch('/api/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                device_id: deviceId,
                device_name: name,
                device_password: password,
                clock_format: clockFormat
            })
        });

        const data = await response.json();
        if (response.ok) {
                            bootstrap.Modal.getInstance(document.getElementById("settingsModal")).hide();

   showSuccess("Device settings updated successfully!");

        } else {
            alert('❌ Failed: ' + data.message);
        }
    } catch (err) {
        console.error(err);
        alert('❌ Error syncing to device.');
    }
});
</script>
<script>
document.querySelectorAll('.speaker-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        const deviceName = this.dataset.deviceName;
        const isMuted = !this.checked;

        fetch(`/api/device/name/${deviceName}/mute`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_muted: isMuted })
        })
        .then(res => res.json())
        .then(data => console.log("Mute updated:", data));
    });
});
</script>


    <script>

        function showSuccess(message) {
    document.getElementById("successMessage").innerText = message;
    const modal = new bootstrap.Modal(document.getElementById("successModal"));
    modal.show();

    document.getElementById("successOkBtn").onclick = function () {
        modal.hide();
        window.location.reload();
    };
}
        document.getElementById("deviceForm").addEventListener("submit", function (e) {
            e.preventDefault();

            const name = document.getElementById("deviceName").value.trim();
            const password = document.getElementById("devicePassword").value.trim();
            const errorEl = document.getElementById("deviceError");

            fetch("/api/device-auth", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "Accept": "application/json",
                },
                body: new URLSearchParams({ name, password })
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                const card = `
<div class="col-md-6 col-lg-3">
                    <div class="card p-3 rounded status-box glass-box shadow-sm">
                        <h5 class="mb-3 text-center">
                            <i class="fas fa-microchip me-2"></i>${name}
                        </h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><i class="fas fa-wifi me-2"></i>Internet</div>
                                <div class="indicator-circle bg-success"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div><i class="fas fa-user-check me-2"></i>Accepting Attendance</div>
                                <div class="indicator-circle bg-success blinking"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div><i class="fas fa-volume-up me-2"></i>Speaker</div>
                                <div class="indicator-circle bg-success"></div>
                            </div>
                        </div>
                    </div>
                </div>`;
                document.getElementById("deviceContainer").insertAdjacentHTML("beforeend", card);
                errorEl.style.display = "none";
                document.getElementById("deviceForm").reset();
                bootstrap.Modal.getInstance(document.getElementById("addDeviceModal")).hide();
                showSuccess("Device added successfully!");
            })
            .catch(async err => {
                let msg = "An error occurred.";
                try {
                    const json = await err.json();
                    msg = json.message || msg;
                } catch (_) {}
                errorEl.innerText = msg;
                errorEl.style.display = "block";
            });
        });
    </script>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>