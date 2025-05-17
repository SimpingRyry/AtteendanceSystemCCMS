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
    <a class="navbar-brand text-white" href="#">TICKTAX</a>

    <!-- Profile Button -->
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
  <li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 text-white profile-button" href="#">
      <img src="{{ asset('images/' . ($user->picture ?? 'default.png')) }}" 
           alt="Profile" class="rounded-circle profile-img" style="width: 40px; height: 40px; object-fit: cover;">
      <div class="d-flex flex-column lh-sm">
        <span class="profile-name">{{ $user->name ?? 'No Name' }}</span>
        <small class="profile-role">
          @php
            $org = $user->org ?? '';
            $role = $user->role ?? 'No Role';

            $orgAbbrev = match($org) {
              'Information Technology Society' => 'ITS',
              'CCMS Student Government' => 'CCMS SG',
              default => $org,
            };
          @endphp
          {{ $role }} - {{ $orgAbbrev }}
        </small>
      </div>
    </a>
  </li>
</ul>


  </div>
</nav>