<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Ticktax Attendance</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


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
                <a href="{{ url('/') }}" class="nav-item nav-link active">Home</a>
                <a href="{{ url('/about') }}" class="nav-item nav-link">About</a>
                <a href="{{ route('homeevents.show') }}" class="nav-item nav-link">Events</a>
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

    <!-- Carousel Start -->
  <div class="container-fluid p-0 wow fadeIn" data-wow-delay="0.1s">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="{{ asset('img/login_bg.png') }}" alt="Image">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <h1 class="display-2 text-light mb-5 animated slideInDown">
                                    Streamline Event Attendance & Student Records
                                </h1>
                                <a href="#" class="btn btn-primary py-sm-3 px-sm-5">Explore Features</a>
                                <a href="#" class="btn btn-light py-sm-3 px-sm-5 ms-3">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="{{ asset('img/login_bg2.png') }}" alt="Image">
                <div class="carousel-caption">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <h1 class="display-2 text-light mb-5 animated slideInDown">
                                    Monitor Fines & Manage Organizations with Ease
                                </h1>
                                <a href="#" class="btn btn-primary py-sm-3 px-sm-5">Learn More</a>
                                <a href="#" class="btn btn-light py-sm-3 px-sm-5 ms-3">See Demo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<div class="container-fluid facts py-5 pt-lg-0">
    <div class="container py-5 pt-lg-0">
        <div class="row gx-0">
            <div class="col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                <div class="bg-white shadow d-flex align-items-center h-100 p-4" style="min-height: 150px;">
                    <div class="d-flex">
                        <div class="flex-shrink-0 btn-lg-square bg-primary">
                            <i class="fa fa-calendar-check text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5>Smart Attendance Tracking</h5>
                            <span>Automatically record and verify event participation for students and members.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeIn" data-wow-delay="0.3s">
                <div class="bg-white shadow d-flex align-items-center h-100 p-4" style="min-height: 150px;">
                    <div class="d-flex">
                        <div class="flex-shrink-0 btn-lg-square bg-primary">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5>Organization & Student Management</h5>
                            <span>Maintain detailed profiles, roles, and organizational structures in one place.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeIn" data-wow-delay="0.5s">
                <div class="bg-white shadow d-flex align-items-center h-100 p-4" style="min-height: 150px;">
                    <div class="d-flex">
                        <div class="flex-shrink-0 btn-lg-square bg-primary">
                            <i class="fa fa-file-invoice-dollar text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5>Fine Monitoring System</h5>
                            <span>Track, notify, and manage fines with real-time reporting and payment updates.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- About Start -->
<div class="container-xxl py-6">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="position-relative overflow-hidden ps-5 pt-5 h-100" style="min-height: 400px;">
                    <img class="position-absolute w-100 h-100" src="{{ asset('img/login_bg.png') }}" alt="" style="object-fit: cover;">
                    <img class="position-absolute top-0 start-0 bg-white pe-3 pb-3" src="{{ asset('img/login_bg2.png') }}" alt="" style="width: 200px; height: 200px;">
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="h-100">
                    <h6 class="text-primary text-uppercase mb-2">About Us</h6>
                    <h1 class="display-6 mb-4">Empowering Organizations with Smart Attendance & Fine Management</h1>
                    <p>Our platform simplifies event attendance tracking, fine monitoring, and student record management for schools, universities, and organizations. From check-in to reporting, everything is automated, accurate, and easy to use.</p>
                    <p class="mb-4">We provide a central hub for managing members, events, and compliance records — ensuring transparency, efficiency, and accountability in every activity.</p>
                    <div class="row g-2 mb-4 pb-2">
                        <div class="col-sm-6">
                            <i class="fa fa-check text-primary me-2"></i>Real-Time Attendance Tracking
                        </div>
                        <div class="col-sm-6">
                            <i class="fa fa-check text-primary me-2"></i>Fine Monitoring & Notifications
                        </div>
                        <div class="col-sm-6">
                            <i class="fa fa-check text-primary me-2"></i>Organization & Student Management
                        </div>
                        <div class="col-sm-6">
                            <i class="fa fa-check text-primary me-2"></i>Secure & Reliable Data Storage
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<div class="bg-warning text-center d-flex align-items-center justify-content-center" 
     style="height: 200px; width: 100%;">
    <h2 class="mb-0 text-dark fw-bold">Your All-in-One Attendance, Fines, and Member Management Solution Starts Here!</h2>
</div>
    <!-- Courses Start -->
<div class="container-xxl events my-6 py-6 pb-0">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <h1 class="fw-bold">Upcoming Events</h1>
            <a href="{{ route('homeevents.show') }}" class="btn btn-outline-warning">See All</a>
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


    <!-- Carousel End -->
   <div class="container">
    <div class="row g-5">
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="text-primary text-uppercase mb-2">Why Choose Us!</h6>
            <h1 class="display-6 mb-4">The All-in-One Attendance & Fine Management Solution</h1>
            <p class="mb-5">Our system streamlines event attendance tracking, fine monitoring, and organization/student management — all in one powerful platform. We help schools, universities, and organizations stay efficient, accurate, and organized.</p>
            <div class="row gy-5 gx-4">
                <div class="col-sm-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 btn-square bg-primary me-3">
                            <i class="fa fa-check text-white"></i>
                        </div>
                        <h5 class="mb-0">Real-Time Attendance</h5>
                    </div>
                    <span>Track attendance instantly with automated logs and detailed reports.</span>
                </div>
                <div class="col-sm-6 wow fadeIn" data-wow-delay="0.2s">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 btn-square bg-primary me-3">
                            <i class="fa fa-check text-white"></i>
                        </div>
                        <h5 class="mb-0">Fine Management</h5>
                    </div>
                    <span>Automatically calculate, notify, and record penalties with full transparency.</span>
                </div>
                <div class="col-sm-6 wow fadeIn" data-wow-delay="0.3s">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 btn-square bg-primary me-3">
                            <i class="fa fa-check text-white"></i>
                        </div>
                        <h5 class="mb-0">Organization & Student Profiles</h5>
                    </div>
                    <span>Manage member details, roles, and affiliations in one secure database.</span>
                </div>
                <div class="col-sm-6 wow fadeIn" data-wow-delay="0.4s">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 btn-square bg-primary me-3">
                            <i class="fa fa-check text-white"></i>
                        </div>
                        <h5 class="mb-0">Secure & Reliable</h5>
                    </div>
                    <span>Protect sensitive data with advanced security and backup systems.</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
            <div class="position-relative overflow-hidden pe-5 pt-5 h-100" style="min-height: 400px;">
                <img class="position-absolute w-100 h-100" src="images/carousel_image3.jpg" alt="" style="object-fit: cover;">
                <img class="position-absolute top-0 end-0 bg-white ps-3 pb-3" src="{{asset('images/carousel_image4.jpg')}}" alt="" style="width: 200px; height: 200px">
            </div>
        </div>
    </div>
</div>
    <!-- Features End -->


    <!-- Team Start -->
<div class="container-xxl py-6">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h6 class="text-primary text-uppercase mb-2">Our Organizations</h6>
            <h1 class="display-6 mb-4">Be a part of the organizations</h1>
        </div>

     <div class="owl-carousel team-carousel">
    @foreach($organizations as $org)
        <div class="team-item position-relative">
            <div class="position-relative">
                <img class="img-fluid" 
                     src="{{ $org->org_logo ? asset('images/org_list/' . $org->org_logo) : asset('img/default-logo.png') }}" 
                     alt="{{ $org->org_name }}">
                <div class="team-social text-center">
                    <a class="btn btn-primary m-1" href="{{ url('/organization/' . $org->id) }}">View</a>
                </div>
            </div>
            <div class="bg-light text-center p-4 org-name-box">
                <h5 class="mt-2 mb-0">{{ $org->org_name }}</h5>
            </div>
        </div>
    @endforeach
</div>
    </div>
</div>

    <!-- Team End -->


    <!-- Testimonial Start -->
 
    <!-- Testimonial End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer my-6 mb-0 py-6 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Get In Touch</h4>
                    <h2 class="text-primary mb-4"><i class="fa fa-car text-white me-2"></i>Drivin</h2>
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
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Newsletter</h4>
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control p-3 border-0" placeholder="Your Email Address">
                            <button class="btn btn-primary">Sign Up</button>
                        </div>
                    </form>
                    <h6 class="text-white mt-4 mb-3">Follow Us</h6>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light me-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light me-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light me-1" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light me-0" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
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
                    &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                    Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                    <br>Distributed By: <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    {{-- Rest of your HTML content converted to Blade stays the same, just replace "img/..." with asset(), 
         "css/..." with asset(), "lib/..." with asset(), and href links to url() --}}
    
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>
<script>
$(document).ready(function(){
    $('.team-carousel').owlCarousel({
        loop: true,
        margin: 30,
        center: true, // Middle card centered
        autoplay: true,
        autoplayTimeout: 2500,
        autoplayHoverPause: true,
        smartSpeed: 800,
        dots: false,
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left-circle fs-3"></i>',
            '<i class="bi bi-arrow-right-circle fs-3"></i>'
        ],
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            768: { items: 3 },
            992: { items: 4 }
        }
    });
});
</script>
</body>
</html>
