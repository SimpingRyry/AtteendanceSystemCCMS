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
    <link href="https://fonts.googleapis.com/css2?family=Emerland+Serif&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">



    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <!------------------------------------------------ NAVBAR_HOME ----------------------------v---------------->
    <nav class="navbar navbar-expand-lg navbar-3d" style="background-color:#232946;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/ticktaxx_logO.png') }}" alt="TickTax Logo" height="40" class="d-inline-block align-text-top" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                </ul>
                <button
                    type="submit"
                    class="btn custom-login-btn">
                    Login
                </button>
            </div>
        </div>
    </nav>

    <main>
        <!------------------------------------------------ Dash_HOME ----------------------------v---------------->
        <section class="carousel-wrapper">
            <div id="orgCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="carousel-slide-content" onmouseenter="pauseCarousel()" onmouseleave="playCarousel()">
    <img src="{{ asset('images/ticktaxx_logO.png') }}" alt="Logo" class="carousel-logo">
                            <h2>Student Council</h2>
                            <p>The backbone of student governance, organizing events and leading initiatives for campus unity.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-slide-content" onmouseenter="pauseCarousel()" onmouseleave="playCarousel()">
    <img src="{{ asset('images/ticktaxx_logO.png') }}" alt="Logo" class="carousel-logo">
                            <h2>Tech Innovators Club</h2>
                            <p>Fostering the next generation of developers and engineers through hands-on projects and coding bootcamps.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-slide-content" onmouseenter="pauseCarousel()" onmouseleave="playCarousel()">
    <img src="{{ asset('images/ticktaxx_logO.png') }}" alt="Logo" class="carousel-logo">
                            <h2>CCMS Volunteers</h2>
                            <p>A passionate group committed to community outreach, disaster response, and social programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
    const carousel = document.querySelector('#orgCarousel');
    const carouselInstance = new bootstrap.Carousel(carousel, {
        interval: 3000,
        pause: false,
        ride: 'carousel'
    });

    function pauseCarousel() {
        carouselInstance.pause();
    }

    function playCarousel() {
        carouselInstance.cycle();
    }
</script>


</body>

</html>