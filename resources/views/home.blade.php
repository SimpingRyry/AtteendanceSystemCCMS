<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CCMS Attendance System - One Page</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet" />
  
  <!-- FullCalendar -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/home_page.css') }}">
</head>

<body>

  <!-- Navbar -->
  <button id="sidebarToggleBtn" class="sidebar-toggle-btn">
  ☰
</button>
  <section id="home" class="position-relative">
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand me-auto" href="#">TickTax</a>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">LOGO DITO</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <li class="nav-item dropdown d-block d-md-none">
              <a class="nav-link dropdown-toggle" href="#" id="orgDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Organizations
              </a>
              <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="orgDropdown">
                <li><a class="dropdown-item" href="#">CCMS Student Government</a></li>
                <li><a class="dropdown-item" href="{{ url('orgs/its_page') }}">ITS</a></li>
                <li><a class="dropdown-item" href="{{ url('orgs/praxis_page') }}">Praxis</a></li>
              </ul>
            </li>
            <ul class="navbar-nav justify-content-center flex-grow-1 pe-3 d-none d-lg-flex" id="mainNav">
              <li class="nav-item"><a class="nav-link mx-lg-2 active" aria-current="page" href="#">Home</a></li>
              <li class="nav-item"><a class="nav-link mx-lg-2" href="#about">About</a></li>
              <li class="nav-item"><a class="nav-link mx-lg-2" href="#events">Events</a></li>
              <li class="nav-item"><a class="nav-link mx-lg-2" href="#contact">Contact Us</a></li>
            </ul>
          </div>
        </div>
        <a href="{{ route('login') }}" class="login-button">LOGIN</a>
        <button class="navbar-toggler" type="button" id="menuButton">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>
      <!-- DESKTOP LOGO SIDEBAR -->
      <div id="logoSidebar" class="logo-sidebar d-none d-lg-flex flex-column">
        <div class="organization-title">Organizations</div>
        <hr class="sidebar-divider" />
        <ul id="logoSidebarList" class="sidebar-orgs"></ul>
      </div>

      <!-- Mobile sidebar (offcanvas menu) -->
      <div id="customSidebar" class="glass-sidebar shadow-lg d-lg-none">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
          <h5 class="text-white mb-0 fw-bold">TickTax</h5>
          <button id="closeSidebarBtn" class="btn-close-white">&times;</button>
        </div>

        <ul class="sidebar-nav mt-4">
          <li><a href="#home">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#events">Events</a></li>
        </ul>

        <h6 class="text-white-50 text-uppercase mb-3 mt-3">Organizations</h6>
        <ul id="organizationList" class="sidebar-orgs"></ul>
      </div>

    </nav>

    <!-- Carousel -->
    <div id="home" class="carousel-container position-relative">
      <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

        <!-- Indicators -->
        <div class="carousel-indicators">
          @foreach ($carouselSlides as $index => $slide)
          <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
          @endforeach
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
          @foreach ($carouselSlides as $index => $slide)
          <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            <img src="{{ asset('images/' . $slide['image']) }}" class="d-block w-100" alt="Slide {{ $index + 1 }}">

            <!-- Overlay -->
            <div class="overlay-content text-center text-white position-absolute top-50 start-50 translate-middle">
              <div class="logo-container mb-3">
                @foreach ($slide['logos'] as $logo)
                <img src="{{ asset('images/' . $logo) }}" alt="Logo" height="60" class="me-2">
                @endforeach
              </div>
              <h1 class="fw-bold">{{ $slide['title'] }}</h1>
              <h2 class="fw-semibold">{{ $slide['subtitle'] }}</h2>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>


    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
    </div>
    </div>
    <!-- Card Grid -->
    <section class="container py-5">
      <div class="row g-4">
        @for ($i = 1; $i <= 6; $i++)
          <div class="col-md-4">
          <div class="card shadow-lg border-0 p-3">
            <div class="card-body text-center">
              <h5 class="card-title fw-bold">Event {{ $i }}</h5>
              <p class="card-text">Short details about Event {{ $i }}.</p>
            </div>
          </div>
      </div>
      @endfor
      </div>
    </section>
  </section>

  <!-- About Section -->
  <section id="about" class="position-relative">
    <!-- Gradient Background -->
    <div class="w-100" style="height: 20vh;">
    <img src="{{ asset('images/about_nav.png') }}" alt="Full Image" style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <!-- About Content -->
    <div class="container text-center mt-5 mb-5">
      <!-- Main Card View -->
      <div class="card border-0 p-4 mt-4">
        <div class="row gy-3 gy-md-4 mt-5 mb-5 gy-lg-0 align-items-lg-center">
          <div class="col-12 d-flex align-items-center justify-content-center">
            <!-- Logo in CardView -->
            <div class="col-5 d-flex justify-content-end">
              <div class="card p-3 border-3" style="width: 80%; border-color: #000;">
                <img src="{{ asset('images/tikatax_logo.png') }}" alt="CCMS Logo" class="img-fluid">
              </div>
            </div>
            <!-- Description -->
            <div class="col-7 ps-4">
              <h2 class="mb-3">What is CCMS: Attendance System?</h2>
              <p class="lead text-muted text-justify">
                The College of Computing and Multimedia Studies (CCMS) at Camarines Norte State College
                is revolutionizing attendance tracking through an IoT-driven system. Traditional methods,
                such as manual logs and signatures, are prone to inaccuracies, time consumption, and
                attendance fraud. Our system leverages biometric fingerprint authentication for reliable
                tracking, automates fine imposition for non-compliance, and streamlines event monitoring.
                By integrating security measures and real-time data processing, this system enhances
                accountability while reducing administrative workload.
              </p>
            </div>
          </div>
        </div>

        <div class="row gy-4 gy-md-0 gx-xxl-5 mt-5">
          <!-- First Feature -->
          <div class="col-12 col-md-6 offset-md-6">
            <div class="card p-3 shadow-sm">
              <div class="d-flex align-items-center">
                <img src="{{ asset('images/fingerpr_ico.jpeg') }}" alt="B" width="40" class="me-3 rounded-circle">
                <div>
                  <h2 class="h4 mb-3">Biometric Authentication</h2>
                  <p class="text-secondary mb-0 text-justify">
                    Say goodbye to manual logs! Our system ensures secure and fraud-proof attendance
                    tracking using fingerprint scanning technology.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Second Feature -->
          <div class="col-12 col-md-6">
            <div class="card p-3 shadow-sm">
              <div class="d-flex align-items-center">
                <img src="{{ asset('images/realtime_icon.png') }}" alt="R" width="40" class="me-3 rounded-circle">
                <div>
                  <h2 class="h4 mb-3">Real-Time Monitoring</h2>
                  <p class="text-secondary mb-0 text-justify">
                    Administrators can track attendance records instantly, ensuring transparency
                    and efficiency in student and faculty attendance tracking.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Developer Profiles -->
      <div class="card border-0 p-4 mt-5">
        <h2 class="mb-4 text-center">Meet the Developers</h2>
        <div class="row row-cols-1 row-cols-md-4 g-4 text-center">
          <!-- Developer 1 -->
          <div class="col">
            <div class="card border-0 p-3 d-flex flex-column align-items-center h-100"
              style="box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); border-radius: 12px;">
              <img src="{{ asset('images/yuki_pik.png') }}" alt="Developer 1" class="rounded-circle mx-auto mb-3"
                style="width: 120px; height: 120px; object-fit: cover;">
              <h5 class="mb-1">Yuki Golimlim</h5>
              <p class="text-muted">Lead Developer</p>
              <p class="text-secondary flex-grow-1 text-wrap" style="min-height: 50px;">
                Kung ang susi ay key, palambing naman si Yuki
              </p>
            </div>
          </div>

          <!-- Developer 2 -->
          <div class="col">
            <div class="card border-0 p-3 d-flex flex-column align-items-center h-100"
              style="box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); border-radius: 12px;">
              <img src="{{ asset('images/arden_pik.jpg') }}" alt="Developer 2" class="rounded-circle mx-auto mb-3"
                style="width: 120px; height: 120px; object-fit: cover;">
              <h5 class="mb-1">Arden Clyde De Ramos</h5>
              <p class="text-muted">UI/UX Designer</p>
              <p class="text-secondary flex-grow-1 text-wrap" style="min-height: 50px;">
                Wala lang, slay lang
              </p>
            </div>
          </div>

          <!-- Developer 3 -->
          <div class="col">
            <div class="card border-0 p-3 d-flex flex-column align-items-center h-100"
              style="box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); border-radius: 12px;">
              <img src="{{ asset('images/ver_pik.png') }}" alt="Developer 3" class="rounded-circle mx-auto mb-3"
                style="width: 120px; height: 120px; object-fit: cover;">
              <h5 class="mb-1">Ver Andre Borje</h5>
              <p class="text-muted">Software Engineer</p>
              <p class="text-secondary flex-grow-1 text-wrap" style="min-height: 50px;">
                Si Foreman
              </p>
            </div>
          </div>

          <!-- Developer 4 -->
          <div class="col">
            <div class="card border-0 p-3 d-flex flex-column align-items-center h-100"
              style="box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); border-radius: 12px;">
              <img src="{{ asset('images/papib_pik.png') }}" alt="Developer 4" class="rounded-circle mx-auto mb-3"
                style="width: 120px; height: 120px; object-fit: cover;">
              <h5 class="mb-1">Mark Joseph Beltran</h5>
              <p class="text-muted">Project Manager</p>
              <p class="text-secondary flex-grow-1 text-wrap" style="min-height: 50px;">
                Shy type na BBF
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="events" class="bg-light mt-5">
    <div class="container mt-5">
      <h2 class="text-center mb-4 mt-5">Upcoming Events</h2>
      <div id="calendar"></div>
    </div>

  </section>

  <!-- Contact Section -->
  <section id="contact" class="bg-white text-dark mt-5">
    <div class="container text-center">
      <h2 class="mb-4">Contact Us</h2>
      <p>Email: info@ticktax-ccms.com</p>
      <p>Facebook: fb.com/ticktaxccms</p>
      <p>Phone: (123) 456-7890</p>
    </div>
  </section>
  <footer class="bg-dark text-white text-center py-4">
    <div class="container">
      <p class="mb-0">© 2025 CCMS Attendance System. All Rights Reserved.</p>
      <p class="mb-0">Developed by Yuki Golimlim</p>
      <div class="mt-2">
        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#calendar').fullCalendar({
        defaultView: 'month',
        editable: false,
        events: [{
            title: 'Bday ni ver',
            start: '2025-04-06'
          },
          {
            title: 'Sana walang pasok',
            start: '2025-04-15',
            end: '2025-04-16'
          },
          {
            title: 'Sana walang pasok',
            start: '2025-04-16',
            end: '2025-04-16'
          }
        ]
      });
    });
    window.onscroll = function() {
      changeNavbarColorOnScroll()
    };

    function changeNavbarColorOnScroll() {
      var navbar = document.getElementById("navbar");
      if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    }
    // Set active class to the navbar link based on the scroll position
    document.addEventListener("DOMContentLoaded", function() {
      const links = document.querySelectorAll('.nav-link');
      const sections = document.querySelectorAll('section');

      window.addEventListener('scroll', function() {
        let current = "";

        sections.forEach(section => {
          const sectionTop = section.offsetTop;
          const sectionHeight = section.clientHeight;
          if (pageYOffset >= sectionTop - sectionHeight / 3) {
            current = section.getAttribute("id");
          }
        });

        links.forEach(link => {
          link.classList.remove("active");
          if (link.getAttribute("href").includes(current)) {
            link.classList.add("active");
          }
        });
      });
    });
    window.addEventListener('scroll', function() {
      var navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    const menuButton = document.getElementById("menuButton");
    const closeSidebarBtn = document.getElementById("closeSidebarBtn");
    const customSidebar = document.getElementById("customSidebar");

    menuButton.addEventListener("click", () => {
      customSidebar.classList.add("show");
    });

    closeSidebarBtn.addEventListener("click", () => {
      customSidebar.classList.remove("show");
    });
    const orgList = [{
      name: "CCMS Student Government",
      link: "{{ url('orgs/student_government') }}",
      image: "{{ asset('images/ccms_logo.png') }}",
      children: [{
          name: "ITS",
          image: "{{ asset('images/its_logo.png') }}",
          link: "{{ url('orgs/its_page') }}"
        },
        {
          name: "Praxis",
          image: "{{ asset('images/praxis_logo.png') }}",
          link: "{{ url('orgs/praxis_page') }}"
        }
      ]
    }];

    const mobileContainer = document.getElementById("organizationList");
    const desktopContainer = document.getElementById("logoSidebarList");

    orgList.forEach(org => {
      // === Mobile (full sidebar) ===
      const parentLiMobile = document.createElement("li");
      parentLiMobile.classList.add("dropdown-org-parent");

      const orgHeaderMobile = document.createElement("div");
      orgHeaderMobile.classList.add("dropdown-toggle-org");

      const orgLinkMobile = document.createElement("a");
      orgLinkMobile.href = org.link;
      orgLinkMobile.classList.add("org-main-link");
      orgLinkMobile.innerHTML = `<img src="${org.image}" alt=""> <span>${org.name}</span>`;

      const dropdownBtnMobile = document.createElement("button");
      dropdownBtnMobile.classList.add("org-dropdown-toggle");
      dropdownBtnMobile.innerHTML = "&#9662;"; // ▼

      orgHeaderMobile.appendChild(orgLinkMobile);
      if (org.children?.length) orgHeaderMobile.appendChild(dropdownBtnMobile);

      const childUlMobile = document.createElement("ul");
      childUlMobile.classList.add("dropdown-org-children");

      if (org.children?.length) {
        org.children.forEach(child => {
          const childLi = document.createElement("li");
          childLi.innerHTML = `<a href="${child.link}"><img src="${child.image}" alt=""> ${child.name}</a>`;
          childUlMobile.appendChild(childLi);
        });

        dropdownBtnMobile.addEventListener("click", (e) => {
          e.preventDefault();
          e.stopPropagation();
          parentLiMobile.classList.toggle("active");
        });

        parentLiMobile.appendChild(orgHeaderMobile);
        parentLiMobile.appendChild(childUlMobile);
      } else {
        parentLiMobile.appendChild(orgLinkMobile);
      }

      mobileContainer.appendChild(parentLiMobile);
      // === Expanded Sidebar (non-collapsed) ===
      const parentLi = document.createElement("li");
      parentLi.classList.add("logo-org-parent");

      const orgHeader = document.createElement("div");
      orgHeader.classList.add("logo-toggle-org");

      const orgLink = document.createElement("a");
      orgLink.href = org.link;
      orgLink.classList.add("org-main-link");
      orgLink.innerHTML = `
  <img src="${org.image}" alt="${org.name}" title="${org.name}">
  <span>${org.name}</span>
`;

      orgLink.addEventListener("click", (e) => {
        e.preventDefault();

        // Remove all existing active classes
        document.querySelectorAll(".logo-org-parent").forEach(el => el.classList.remove("active"));
        document.querySelectorAll(".logo-org-children li a").forEach(el => el.classList.remove("active"));

        // Add active state to this org
        parentLi.classList.add("active");

        // Save to localStorage
        localStorage.setItem("activeOrg", org.name);
        localStorage.removeItem("activeChildOrg");

        // Redirect
        window.location.href = org.link;
      });

      // Highlight active org on load
      if (localStorage.getItem("activeOrg") === org.name) {
        parentLi.classList.add("active");
      }

      const dropdownBtn = document.createElement("button");
      dropdownBtn.classList.add("org-dropdown-toggle");
      dropdownBtn.innerHTML = "&#9662;"; // ▼

      orgHeader.appendChild(orgLink);
      if (org.children?.length) orgHeader.appendChild(dropdownBtn);

      // Create child list
      const childUl = document.createElement("ul");
      childUl.classList.add("logo-org-children");

      org.children?.forEach(child => {
        const childLi = document.createElement("li");
        const childLink = document.createElement("a");
        childLink.href = child.link;
        childLink.innerHTML = `<img src="${child.image}" alt=""> <span>${child.name}</span>`;

        // Highlight active child
        if (localStorage.getItem("activeChildOrg") === child.name) {
          childLink.classList.add("active");
        }

        childLink.addEventListener("click", (e) => {
          // Clear all active classes
          document.querySelectorAll(".logo-org-parent").forEach(el => el.classList.remove("active"));
          document.querySelectorAll(".logo-org-children li a").forEach(el => el.classList.remove("active"));

          // Set active on child
          childLink.classList.add("active");

          // Save child state
          localStorage.setItem("activeChildOrg", child.name);
          localStorage.removeItem("activeOrg");
        });

        childLi.appendChild(childLink);
        childUl.appendChild(childLi);
      });

      // Toggle child list on button click
      dropdownBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        parentLi.classList.toggle("expanded");
      });

      parentLi.appendChild(orgHeader);
      if (org.children?.length) parentLi.appendChild(childUl);

      desktopContainer.appendChild(parentLi);
    });
    const toggleBtn = document.getElementById('sidebarToggleBtn');
const logoSidebar = document.querySelector('.logo-sidebar');

toggleBtn.addEventListener('click', () => {
  logoSidebar.classList.toggle('show');
});

  </script>

  <!-- Bootstrap Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>