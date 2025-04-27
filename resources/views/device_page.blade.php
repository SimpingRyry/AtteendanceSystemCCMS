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
                <button id="addDeviceBtn" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add Device
                </button>
            </div>

            <!-- Devices Container -->
            <div id="deviceContainer" class="row g-4">
                @foreach (['DEVICE 1', 'DEVICE 2', 'DEVICE 3', 'DEVICE 4'] as $device)
                <div class="col-md-3">
                <div class="card p-3 rounded status-box glass-box shadow-sm">

<h5 class="mb-3 text-center">
    <i class="fas fa-microchip me-2"></i>{{ $device }}
</h5>

<div class="d-flex flex-column gap-3">

    <div class="d-flex justify-content-between align-items-center">
        <div><i class="fas fa-power-off me-2"></i>Power</div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider round"></span>
        </label>
    </div>

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

                </div>
                @endforeach
            </div>

        </div>
    </div>

    <script>
        let deviceCount = 5; // Since you already have DEVICE 1-4
        const addDeviceBtn = document.getElementById('addDeviceBtn');
        const deviceContainer = document.getElementById('deviceContainer');

        addDeviceBtn.addEventListener('click', () => {
            const newDevice = document.createElement('div');
            newDevice.className = "col-md-3";
            newDevice.innerHTML = `
                <div class="card p-3 rounded status-box glass-box shadow-sm">

                    <h5 class="mb-3 text-center">DEVICE ${deviceCount}</h5>

                    <div class="d-flex flex-column gap-3">

                        <div class="d-flex justify-content-between align-items-center">
                            <div><i class="fas fa-power-off me-2"></i>Power</div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

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
            `;
            deviceContainer.appendChild(newDevice);
            deviceCount++;
        });
    </script>

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
        .bg-danger {
            background-color: #dc3545 !important;
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
</main>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>