{{-- resources/views/team.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Drivin - Driving School Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style2.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center border-end px-4 px-lg-5">
            <h2 class="m-0">TICKTAX</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="{{ url('/') }}" class="nav-item nav-link">Home</a>
                <a href="{{ url('/') }}#about" class="nav-item nav-link">About</a>
                <a href="{{ url('/home-event') }}" class="nav-item nav-link">Events</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Organizations</a>
                    <div class="dropdown-menu bg-light m-0">
                        @foreach($organizations as $org)
                            <a href="{{ url('/organization/' . $org->id) }}" class="dropdown-item">
                                {{ $org->org_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ url('login') }}" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Login<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-6 my-6 mt-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center">
            <h1 class="display-4 text-white animated slideInDown mb-4">{{ $organization->org_name }}</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Organizations</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">{{ $organization->org_name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Sub Tabs Start -->
    <div class="container mt-5">
        <ul class="nav nav-tabs justify-content-center" id="orgTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about"
                    type="button" role="tab" aria-controls="about" aria-selected="true">
                    About
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events"
                    type="button" role="tab" aria-controls="events" aria-selected="false">
                    Events
                </button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="orgTabsContent">
            <!-- About Tab -->
            <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
                <div class="row align-items-center">
                    <!-- Left: Description -->
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <h6 class="text-primary text-uppercase mb-2">About Us</h6>
                        <h1 class="display-6 mb-4">{{ $organization->org_name }}</h1>
                        <p class="mb-4">{{ $organization->description }}</p>
                    </div>
                    <!-- Right: Logo -->
                    <div class="col-lg-6 text-center wow fadeInUp" data-wow-delay="0.3s">
                        @if($organization->org_logo)
                            <img src="{{ asset('images/org_list/' . $organization->org_logo) }}" alt="{{ $organization->org_name }}" class="img-fluid" style="max-width: 300px;">
                        @else
                            <img src="{{ asset('img/default-logo.png') }}" alt="Default Logo" class="img-fluid" style="max-width: 300px;">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Events Tab -->
            <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                <div class="container-xxl events my-6 py-6 pb-0">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
                            <h1 class="fw-bold">Upcoming Events</h1>
                        </div>
                        <div class="row g-4 justify-content-center">
                            @forelse($events as $event)
                                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.{{ $loop->iteration }}s">
                                    <a href="{{ route('events.show', $event->id) }}" class="text-decoration-none text-dark">
                                        <div class="event-item bg-white h-100 shadow-sm">
                                            <div class="position-relative">
                                                <img class="img-fluid w-100" 
                                                    src="{{ asset('images/'.$event->attached_memo) ?? asset('img/default-event.jpg') }}" 
                                                    alt="{{ $event->name }}">
                                                <div class="position-absolute top-0 start-0 bg-warning text-white text-center px-3 py-2">
                                                    <div class="fs-4 fw-bold">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</div>
                                                    <div class="small text-uppercase">{{ \Carbon\Carbon::parse($event->event_date)->format('F') }}</div>
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <p class="text-muted small mb-2">
                                                    <i class="fa fa-map-marker-alt text-warning me-2"></i>{{ $event->venue }}
                                                </p>
                                                <h5 class="fw-bold">{{ $event->name }}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <p class="text-muted">No upcoming events</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sub Tabs End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer my-6 mb-0 py-6 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Get In Touch</h4>
                    <h2 class="text-primary mb-4">Ticktax</h2>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@example.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Popular Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright text-light py-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a href="#">Ticktax.com</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Designed By <a href="https://htmlcodex.com">Ticktax</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
