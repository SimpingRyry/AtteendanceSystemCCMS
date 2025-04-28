@php
    $user = Auth::user();
@endphp

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-floaty custom-navbar fixed-top" style="background-color:#232946;">
  <div class="container-fluid">

    <!-- Offcanvas toggle -->
    <button class="navbar-toggler me-2" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
      aria-controls="offcanvasExample">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Brand -->
    <a class="navbar-brand text-white" href="#">TiCKTAX</a>

    <!-- Profile Button -->
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 text-white profile-button" href="#">
          <img src="{{ asset('images/' . ($user->image ?? 'default.png')) }}" 
               alt="Profile" class="rounded-circle profile-img" style="width: 40px; height: 40px; object-fit: cover;">
          <div class="d-flex flex-column lh-sm">
            <span class="profile-name">{{ $user->name ?? 'No Name' }}</span>
            <small class="profile-role">{{ $user->role ?? 'No Role' }}</small>
          </div>
        </a>
      </li>
    </ul>

  </div>
</nav>