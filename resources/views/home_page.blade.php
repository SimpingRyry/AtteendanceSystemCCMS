<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CCMS Attendance System</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Emerland+Serif&family=Roboto+Slab&family=Poppins&display=swap" rel="stylesheet" />
  <!-- Your Custom CSS (keep this) -->
  <link rel="stylesheet" href="{{ asset('css/home.css') }}" />
</head>

<body style="background-color: #fffffe;">
  <!-- Your Current Navbar -->
  <nav class="navbar navbar-expand-lg navbar-3d" style="background-color:#232946;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="{{ asset('images/ticktaxx_logO.png') }}" alt="TickTax Logo" height="40" class="d-inline-block align-text-top" />
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto me-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#features">Events</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
        </ul>
        <button type="button" class="btn custom-login-btn">Login</button>
      </div>
    </div>
  </nav>

  <!-- Your Current Carousel -->
  <main>
    <section class="carousel-wrapper">
      <div id="orgCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner">
  @foreach ($carouselSlides as $index => $slide)
  <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
    <div class="carousel-slide-content d-flex flex-column justify-content-center align-items-center text-center h-100" onmouseenter="pauseCarousel()" onmouseleave="playCarousel()">
      @if (!empty($slide['logos']))
      <div class="logo-container mb-3">
        @foreach ($slide['logos'] as $logo)
        <img src="{{ asset('images/' . $logo) }}" alt="Logo" class="carousel-logo me-2" />
        @endforeach
      </div>
      @endif

      @if (!empty($slide['title']))
      <h2 class="fw-bold">{{ $slide['title'] }}</h2>
      @endif

      @if (!empty($slide['subtitle']))
      <p class="text-white-50 lead">{{ $slide['subtitle'] }}</p>
      @endif
    </div>
  </div>
  @endforeach
</div>
      </div>
    </section>

    <!-- ✅ About Section (UCN-style) -->
    <section id="about" class="py-5 bg-light text-dark text-center text-md-start">
      <div class="container">
        <div class="row align-items-center gy-4">
          <div class="col-md-6">
            <img src="{{ asset('images/tikatax_logo.png') }}" alt="System Logo" class="img-fluid rounded shadow" />
          </div>
          <div class="col-md-6">
            <h2 class="fw-bold">What is CCMS: Attendance System?</h2>
            <p class="lead text-muted mt-3">
              The College of Computing and Multimedia Studies (CCMS) at Camarines Norte State College is revolutionizing attendance tracking through an IoT-driven system. Traditional methods, such as manual logs and signatures, are prone to inaccuracies, time consumption, and attendance fraud. Our system leverages biometric fingerprint authentication for reliable tracking, automates fine imposition for non-compliance, and streamlines event monitoring.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ✅ Features Section -->
    <section id="features" class="py-5 text-dark bg-white">
      <div class="container">
        <div class="text-center mb-5">
          <h3 class="fw-bold">System Features</h3>
          <p class="text-muted">Reliable. Real-time. Secure.</p>
        </div>
        <div class="row g-4">
          <!-- Feature 1 -->
          <div class="col-md-6">
            <div class="card p-4 shadow-sm h-100">
              <div class="d-flex align-items-start">
                <img src="{{ asset('images/fingerpr_ico.jpeg') }}" class="me-3 rounded-circle" width="48" alt="Biometric Icon" />
                <div>
                  <h5 class="fw-semibold">Biometric Authentication</h5>
                  <p class="text-muted small">Say goodbye to manual logs! TickTax uses fingerprint scanning for secure and fraud-proof tracking.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="col-md-6">
            <div class="card p-4 shadow-sm h-100">
              <div class="d-flex align-items-start">
                <img src="{{ asset('images/realtime_icon.png') }}" class="me-3 rounded-circle" width="48" alt="Realtime Icon" />
                <div>
                  <h5 class="fw-semibold">Real-Time Monitoring</h5>
                  <p class="text-muted small">Track attendance records instantly, ensuring transparency and reducing admin work.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ✅ Footer -->
  <footer class="bg-dark text-white py-3 text-center mt-5">
    <div class="container">
      <p class="mb-0 small">© 2025 TickTax CCMS Attendance System | Developed by Mark Joseph Beltran</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
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
