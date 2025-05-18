@php
    $user = Auth::user();
@endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
<!-- Right-side Nav (Profile + Notification) -->
<div class="d-flex flex-row align-items-center gap-3 ms-auto">

  <!-- Profile Button -->
  <a class="nav-link d-flex align-items-center gap-2 text-white profile-button order-1" href="#">
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

  <!-- Notification Dropdown -->
  <div class="dropdown order-2">
    <a class="nav-link text-white position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-bell fs-5"></i>
      @if($notifications->count() > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          {{ $notifications->count() }}
          <span class="visually-hidden">unread notifications</span>
        </span>
      @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="min-width: 300px;">
      @forelse ($notifications as $notif)
        <li class="dropdown-item small">
          <strong>{{ $notif->title }}</strong><br>
          <span>{{ $notif->message }}</span><br>
          <small class="text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
        </li>
      @empty
        <li class="dropdown-item text-muted">No new notifications</li>
      @endforelse
    </ul>
  </div>

</div>

  </div>
</nav>
