

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>

</head>

<body style="background: radial-gradient(circle at top left, white, #e0f7ff, #b3e5fc);">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <!------------------------------------------------------ MAIN_BOX -------------------------------------------------------->
    <main>
        <div class="container outer-box mt-5 pt-5 pb-4 shadow">
            {{-- Your content here --}}
        </div>
    </main>

    <!-- Chart Initialization Script -->
    <script>
        const ctxAbsence = document.getElementById('absenceChart');
        const absenceChart = new Chart(ctxAbsence, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                datasets: [{
                    label: 'Absents',
                    data: [1, 2, 1, 0],
                    borderColor: '#f87171',
                    backgroundColor: 'rgba(248,113,113,0.2)',
                    fill: true
                }]
            }
        });

        const ctxBar = document.getElementById('barChart');
        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Events Attended', 'Late Attendance', 'Did not attend'],
                datasets: [{
                    label: 'Count',
                    data: [8, 2, 1],
                    backgroundColor: ['#4ade80', '#facc15', '#f87171']
                }]
            },
            options: {
                indexAxis: 'y'
            }
        });

        const ctxPie = document.getElementById('pieChart');
        const pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Attended', 'Absent', 'Late'],
                datasets: [{
                    data: [75, 15, 10],
                    backgroundColor: ['#60a5fa', '#f87171', '#facc15']
                }]
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>

</html>
