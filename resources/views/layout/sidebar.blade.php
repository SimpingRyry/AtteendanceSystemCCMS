<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start sidebar-nav" style="background-color:#232946;" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    @php
        $org = Auth::user()->organization;
    @endphp

    <!-- Org Logo & Name -->
    <div class="text-center pt-4 pb-3">
        @if ($org && $org->org_logo)
            <img src="{{ asset('images/org_list/' . $org->org_logo) }}" alt="{{ $org->org_name }} Logo"
                 class="rounded-circle mb-3 border border-secondary" width="100" height="100" style="object-fit: cover;">
        @else
            <img src="{{ asset('images/human.png') }}" alt="Default Org Logo"
                 class="rounded-circle mb-3 border border-secondary" width="100" height="100" style="object-fit: cover;">
        @endif

        @if ($org)
            <h5 class="mt-1 fw-semibold text-light" style="letter-spacing: 1px; text-shadow: 0 0 3px rgba(0,0,0,0.5);">
                {{ $org->org_name }}
            </h5>
        @endif
    </div>

    <hr class="divider">

    <!-- Offcanvas Body -->
    <div class="offcanvas-body p-3">
    <nav class="navbar-dark">
        <ul class="navbar-nav">

            @if (Auth::user()->role === 'Member')
                <!-- MEMBER MENU -->
                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">CORE</div>
                </li>

                <li>
                    <a href="{{ url('student_dashboard') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('student_dashboard') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/dashh_ico.png') }}" class="sidebar-icon"> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('events') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('events') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/event_ico.png') }}" class="sidebar-icon"> <span>Events</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('student_payment') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('student_payment') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/payment_ico.png') }}" class="sidebar-icon"> <span>Fines / Statement of Accounts</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('profile') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('profile') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/user_prof.png') }}" class="sidebar-icon"> <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('evaluation_student') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('evaluation_student') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/evaluation_ico.png') }}" class="sidebar-icon"> <span>Evaluation</span>
                    </a>
                </li>
                 <li>
                    <a href="{{ url('notification') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('notification') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/evaluation_ico.png') }}" class="sidebar-icon"> <span>Notifications</span>
                    </a>
                </li>

            @elseif (Auth::user()->role === 'OSSD')
                <!-- OSSD MENU -->
                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">CORE</div>
                </li>

                <li>
                    <a href="{{ url('ossd-dashboard') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('ossd-dashboard') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/dashh_ico.png') }}" class="sidebar-icon"> <span>Dashboard</span>
                    </a>
                </li>

                <li class="my-2"><hr class="divider"></li>

                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">MANAGE</div>
                </li>

                <li>
                    <a class="nav-link px-3 text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manageMenu" role="button" aria-expanded="true" aria-controls="manageMenu">
                        <span>
                            <img src="{{ asset('images/manage.png') }}" class="sidebar-icon me-2">Manage
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse show" id="manageMenu">
                        <ul class="navbar-nav ps-3">

                             <li>
                                <a href="{{ url('manage_orgs_page') }}" class="nav-link text-white {{ request()->is('manage_orgs_page') ? 'active-link' : '' }}">
                                    <img src="{{ asset('images/org_ico.png') }}" class="sidebar-icon"> Organization
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('student') }}" class="nav-link text-white {{ request()->is('student') ? 'active-link' : '' }}">
                                    <img src="{{ asset('images/user_prof.png') }}" class="sidebar-icon"> Students
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('advisers') }}" class="nav-link text-white {{ request()->is('advisers') ? 'active-link' : '' }}">
                                    <img src="{{ asset('images/instruct_ico.png') }}" class="sidebar-icon"> Advisers
                                </a>
                            </li>
                           
                            
                           

                             <li><a href="{{ url('officers') }}" class="nav-link text-white {{ request()->is('officers') ? 'active-link' : '' }}"><img src="{{ asset('images/add_account.png') }}" class="sidebar-icon"> Officers</a></li>

                        </ul>
                    </div>
                </li>
                <li class="mt-3 mb-2">
                    <div class="small fw-bold text-uppercase core-section">SYSTEM SETTINGS</div>
                </li>
                <li>
                    <a class="nav-link px-3 text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#systemSettings" role="button" aria-expanded="false" aria-controls="systemSettings">
                        <span><img src="{{ asset('images/system.png') }}" class="sidebar-icon me-2">Settings</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="systemSettings">
                        <ul class="navbar-nav ps-3">
                            <li><a href="{{ url('config') }}" class="nav-link text-white {{ request()->is('config') ? 'active-link' : '' }}"><img src="{{ asset('images/config_ico.png') }}" class="sidebar-icon"> Configure</a></li>
                        </ul>
                    </div>
                </li>

            @else
                <!-- OTHER ROLES (Adviser, Officer, Super Admin, etc.) -->
                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">CORE</div>
                </li>

                <li>
                    @if (Auth::user()->role === 'Super Admin')
                        <a href="{{ url('super_dashboard') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('super_dashboard') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/dashh_ico.png') }}" class="sidebar-icon"> <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ url('dashboard_page') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('dashboard_page') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/dashh_ico.png') }}" class="sidebar-icon"> <span>Dashboard</span>
                        </a>
                    @endif
                </li>

                <li class="my-2"><hr class="divider"></li>

                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">MANAGE</div>
                </li>

                <!-- Full Manage Collapsible -->
                <li>
                    <a class="nav-link px-3 text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manageMenu" role="button" aria-expanded="false" aria-controls="manageMenu">
                        <span>
                            <img src="{{ asset('images/manage.png') }}" class="sidebar-icon me-2">Manage
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="manageMenu">
                        <ul class="navbar-nav ps-3">
                            <li><a href="{{ url('student') }}" class="nav-link text-white {{ request()->is('student') ? 'active-link' : '' }}"><img src="{{ asset('images/user_prof.png') }}" class="sidebar-icon"> Students</a></li>
                            <li><a href="{{ url('members') }}" class="nav-link text-white {{ request()->is('members') ? 'active-link' : '' }}"><img src="{{ asset('images/instruct_ico.png') }}" class="sidebar-icon"> Members</a></li>
                            <li><a href="{{ url('events') }}" class="nav-link text-white {{ request()->is('events') ? 'active-link' : '' }}"><img src="{{ asset('images/event_ico.png') }}" class="sidebar-icon"> Events</a></li>
                               <li>
                    <a href="{{ url('notification') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('notification') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/notification.png') }}" class="sidebar-icon"> <span>Notifications</span>
                    </a>
                </li>
                                            <li><a href="{{ url('officers') }}" class="nav-link text-white {{ request()->is('officers') ? 'active-link' : '' }}"><img src="{{ asset('images/add_account.png') }}" class="sidebar-icon"> Officers</a></li>

                            @if (Auth::user()->role !== 'OSSD')
                            <li><a href="{{ url('attendance') }}" class="nav-link text-white {{ request()->is('attendance') ? 'active-link' : '' }}"><img src="{{ asset('images/attendance.png') }}" class="sidebar-icon"> Attendance</a></li>
                            <li><a href="{{ url('payment') }}" class="nav-link text-white {{ request()->is('payment') ? 'active-link' : '' }}"><img src="{{ asset('images/payment_ico.png') }}" class="sidebar-icon"> Payment</a></li>
                            <li><a href="{{ url('reports') }}" class="nav-link text-white {{ request()->is('reports') ? 'active-link' : '' }}"><img src="{{ asset('images/record_ico.png') }}" class="sidebar-icon"> Reports</a></li>
                            @endif

                            <li><a href="{{ url('clearance') }}" class="nav-link text-white {{ request()->is('clearance') ? 'active-link' : '' }}"><img src="{{ asset('images/evaluation_ico.png') }}" class="sidebar-icon"> Clearance</a></li>
                            <li><a href="{{ url('evaluation') }}" class="nav-link text-white {{ request()->is('evaluation') ? 'active-link' : '' }}"><img src="{{ asset('images/evaluation_ico.png') }}" class="sidebar-icon"> Evaluation</a></li>
                            
                            @if (Auth::user()->role === 'Super Admin')
                            <li><a href="{{ url('accounts') }}" class="nav-link text-white {{ request()->is('accounts') ? 'active-link' : '' }}"><img src="{{ asset('images/add_account.png') }}" class="sidebar-icon"> Accounts</a></li>
                            <li><a href="{{ url('OSSD') }}" class="nav-link text-white {{ request()->is('OSSD') ? 'active-link' : '' }}"><img src="{{ asset('images/office.png') }}" class="sidebar-icon"> OSSD</a></li>
                            <li><a href="{{ url('manage_orgs_page') }}" class="nav-link text-white {{ request()->is('manage_orgs_page') ? 'active-link' : '' }}"><img src="{{ asset('images/org_ico.png') }}" class="sidebar-icon"> Organization</a></li>
                            @elseif (Auth::user()->role === 'Adviser')
                            <li><a href="{{ url('officers') }}" class="nav-link text-white {{ request()->is('officers') ? 'active-link' : '' }}"><img src="{{ asset('images/add_account.png') }}" class="sidebar-icon"> Officers</a></li>
                            @endif
                        </ul>
                    </div>
                </li>

                @if (Auth::user()->role !== 'officer' && Auth::user()->role !== 'OSSD')
                <!-- SYSTEM SETTINGS -->
                <li class="mt-3 mb-2">
                    <div class="small fw-bold text-uppercase core-section">SYSTEM SETTINGS</div>
                </li>
                <li>
                    <a class="nav-link px-3 text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#systemSettings" role="button" aria-expanded="false" aria-controls="systemSettings">
                        <span><img src="{{ asset('images/system.png') }}" class="sidebar-icon me-2">Settings</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="systemSettings">
                        <ul class="navbar-nav ps-3">
                            <li><a href="{{ url('device_page') }}" class="nav-link text-white {{ request()->is('device_page') ? 'active-link' : '' }}"><img src="{{ asset('images/device_ico.png') }}" class="sidebar-icon"> Device</a></li>
                            <li><a href="{{ url('config') }}" class="nav-link text-white {{ request()->is('config') ? 'active-link' : '' }}"><img src="{{ asset('images/config_ico.png') }}" class="sidebar-icon"> Configure</a></li>
                            <li><a href="{{ url('logs') }}" class="nav-link text-white {{ request()->is('logs') ? 'active-link' : '' }}"><img src="{{ asset('images/loggs_ico.png') }}" class="sidebar-icon"> Logs</a></li>
                        </ul>
                    </div>
                </li>
                @endif

            @endif

            <!-- Divider -->
            <li class="my-2"><hr class="divider"></li>

            <!-- Logout -->
            <li>
                <a href="#" class="nav-link px-3 d-flex text-white align-items-center" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('images/logout_ico.png') }}" class="sidebar-icon"> <span>Log-out</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </nav>
</div>
</div>
