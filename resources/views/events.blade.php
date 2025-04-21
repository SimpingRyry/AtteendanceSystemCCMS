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

    <!-- Kalendaryo -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background: radial-gradient(circle at top left, white, #e0f7ff, #b3e5fc);">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
        <div class="container outer-box mt-5 pt-5 pb-4 shadow">
            <div class="container inner-glass shadow p-4" id="main_box">
                <h4 class="mb-4 text-center glow-text">EVENTS</h4>
                <!-- FullCalendar Display -->
                <div class="row">
                    <div class="col-12 mb-4">
                            <div class="card-body">
                                <div id="calendar"></div>
                            </div>
                    </div>
                </div>

            </div>
        </div>

        </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 600,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                }
            });
            calendar.render();

            // Disable past dates
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("eventDate").setAttribute('min', today);
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>