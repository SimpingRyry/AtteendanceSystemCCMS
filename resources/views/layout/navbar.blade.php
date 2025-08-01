@php
  use App\Models\Notification;

  $user = Auth::user();

  $notifications = Notification::where('user_id', $user->id)
      ->latest()
      ->take(10)
      ->get();

  $unreadCount = Notification::where('user_id', $user->id)
      ->whereNull('read_at') // or ->where('read', false) if your schema uses boolean
      ->count();

@endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
  .notification-item.text-muted {
  background-color: #f1f1f1;
  color: #999;
}
</style>

<nav class="navbar navbar-expand-lg custom-navbar fixed-top" style="background-color:#232946;">
  <div class="container-fluid d-flex align-items-center">

    <!-- Left Side: Hamburger and Brand -->
    <div class="d-flex align-items-center">
      <!-- Hamburger menu -->
      <button class="navbar-toggler border-0 me-2" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Brand -->
      <a class="navbar-brand text-white mb-0 d-none d-lg-block" href="#" style="font-weight: bold;">TICKTAX</a>
      </div>

    <!-- Right Side: Profile and Notification -->
    <div class="d-flex align-items-center gap-3 ms-auto">
      <!-- Profile -->
      <a class="nav-link d-flex align-items-center gap-2 text-white profile-button" href="#">
        <img src="{{ asset('uploads/' . ($user->picture ?? 'default.png')) }}" 
             alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
        <div class="d-flex flex-column lh-sm text-start">
          <span class="profile-name">{{ $user->name ?? 'No Name' }}</span>
          <small class="profile-role">
    @php
        $org = $user->org ?? '';
        $role = str_replace(' - Officer', '', $user->role) ?? 'No Role';

        $orgAbbrev = '';
        if (!empty($org)) {
            $orgAbbrev = match($org) {
                'Information Technology Society' => '- ITS',
                'CCMS Student Government' => '- CCMS SG',
                default => '- ' . $org,
            };
        }
    @endphp
    {{ $role }}{{ $orgAbbrev }}
</small>
        </div>
      </a>

      <!-- Notification Bell -->
<div class="dropdown">
  <a class="nav-link text-white position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-bell fs-5"></i>
    @if($unreadCount > 0)
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ $unreadCount }}
        <span class="visually-hidden">unread notifications</span>
      </span>
    @endif
  </a>

  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
    @forelse ($notifications as $notif)
      <li class="dropdown-item small notification-item {{ $notif->read_at ? 'text-muted' : '' }}"
          data-id="{{ $notif->id }}"
          style="{{ $notif->read_at ? 'opacity: 0.6;' : '' }}">
        <strong>{{ $notif->title }}</strong><br>
        <span>{{ $notif->message }}</span><br>
        <small class="text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
      </li>
    @empty
      <li class="dropdown-item text-muted">No new notifications</li>
    @endforelse

    @if ($notifications->count() >= 5) {{-- or whatever threshold you prefer --}}
      <li><hr class="dropdown-divider"></li>
      <li>
        <a href="/notification" class="dropdown-item text-center text-primary">
          See More
        </a>
      </li>
    @endif
  </ul>
</div>
    </div>
    
  </div>
</nav>



<style>
  .navbar-toggler-icon {
  filter: invert(1);
}
</style>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dropdownElement = document.getElementById('notificationDropdown');

    if (dropdownElement) {
      dropdownElement.closest('.dropdown').addEventListener('show.bs.dropdown', function () {
        const badge = dropdownElement.querySelector('.badge');
        if (badge) {
          badge.remove(); // Remove badge when dropdown is opened
        }

        // 🔽 Mark all as read when dropdown opens
        fetch('/notifications/mark-all-read', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        }).then(response => {
          if (response.ok) {
            // Optional: visually mark all as read
            document.querySelectorAll('.notification-item').forEach(item => {
              item.classList.add('text-muted');
              item.style.opacity = 0.6;
            });
          }
        });
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const notifItems = document.querySelectorAll('.notification-item');
    const badge = document.querySelector('#notificationDropdown .badge');

    notifItems.forEach(item => {
      item.addEventListener('click', function () {
        const notifId = this.dataset.id;
        const self = this;

        // Prevent repeated clicks
        if (self.classList.contains('text-muted')) return;

        fetch(`/notifications/read/${notifId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        }).then(response => {
          if (response.ok) {
            self.classList.add('text-muted');
            self.style.opacity = 0.6;

            if (badge) {
              let count = parseInt(badge.textContent.trim());
              count = Math.max(count - 1, 0);
              if (count === 0) {
                badge.remove();
              } else {
                badge.textContent = count;
              }
            }
          }
        });
      });
    });
  });
</script>
