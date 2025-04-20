<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-floaty custom-navbar fixed-top" style="background-color: #111115;">
  <div class="container-fluid">

    <!-- Offcanvas toggle -->
    <button class="navbar-toggler me-2" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
      aria-controls="offcanvasExample">
      <span class="navbar-toggler-icon" data-bs-target="#offcanvasExample"></span>
    </button>

    <!-- Brand -->
    <a class="navbar-brand text-white" href="#">TiCKTAX</a>

    <!-- Profile Button -->
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 text-white profile-button" href="#">
          <img src="{{ asset('images/arden_pik.jpg') }}" alt="Profile" class="rounded-circle profile-img">
          <div class="d-flex flex-column lh-sm">
            <span class="profile-name">Profile</span>
            <small class="profile-role">Admin</small>
          </div>
        </a>
      </li>
    </ul>

  </div>
</nav>
