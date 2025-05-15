<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start sidebar-nav" style="background-color:#232946;" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">

    <!-- Offcanvas Header with Centered Image -->
    <div class="offcanvas-header justify-content-center">
        @if (Auth::user()->org === 'ITS')
            <img src="{{ asset('images/ITS_LOGO.png') }}" alt="ITS Logo" class="rounded-circle" width="100" height="100">
        @elseif (Auth::user()->org === 'PRAXIS')
            <img src="{{ asset('images/praxis_logo.png') }}" alt="PRAXIS Logo" class="rounded-circle" width="100" height="100">
        @else
            <img src="{{ asset('images/org_ccms_logo.png') }}" alt="Org Image" class="rounded-circle" width="100" height="100">
        @endif

        <button type="button" class="btn-close position-absolute end-0 me-3 mt-3" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <hr class="divider">

    <!-- Offcanvas Body -->
    <div class="offcanvas-body p-3">
    <nav class="navbar-dark">
        <ul class="navbar-nav">

            @if (Auth::user()->role === 'member')
                <!-- MEMBER ROLE MENU -->

                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">CORE</div>
                </li>

                <li>
                    <a href="{{ url('student_dashboard') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('student_dashboard') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/dashh_ico.png') }}" alt="Dashboard" class="sidebar-icon">
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('events') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('events') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/event_ico.png') }}" alt="Events" class="sidebar-icon">
                        <span>Events</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('payment') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('payment') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/payment_ico.png') }}" alt="Payment" class="sidebar-icon">
                        <span>Fines / Statement of Accounts</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('profile') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('profile') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/user_prof.png') }}" alt="Profile" class="sidebar-icon">
                        <span>Profile</span>
                    </a>
                </li>

            @else
                <!-- NON-MEMBER FULL MENU -->

                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">CORE</div>
                </li>

                <li>
                    @if (Auth::user()->role === 'super admin')
                        <a href="{{ url('super_dashboard') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('super_dashboard') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/dashh_ico.png') }}" alt="Super Dashboard" class="sidebar-icon">
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ url('dashboard_page') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('dashboard_page') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/dashh_ico.png') }}" alt="Dashboard" class="sidebar-icon">
                            <span>Dashboard</span>
                        </a>
                    @endif
                </li>

                <li class="my-2">
                    <hr class="divider">
                </li>

                <li class="mb-2">
                    <div class="small fw-bold text-uppercase core-section">MANAGE</div>
                </li>

                <li>
                    <a href="{{ url('student') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('student') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/user_prof.png') }}" alt="Students" class="sidebar-icon">
                        <span>Students</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('advisers') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('advisers') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/instruct_ico.png') }}" alt="Advisers" class="sidebar-icon">
                        <span>Advisers</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('events') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('events') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/event_ico.png') }}" alt="Events" class="sidebar-icon">
                        <span>Events</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('payment') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('payment') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/payment_ico.png') }}" alt="Payment" class="sidebar-icon">
                        <span>Payment</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('reports') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('reports') ? 'active-link' : '' }}">
                        <img src="{{ asset('images/record_ico.png') }}" alt="Records" class="sidebar-icon">
                        <span>Reports</span>
                    </a>
                </li>

                @if (Auth::user()->role !== 'officer')
                    <li>
                        <a href="{{ url('device_page') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('device_page') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/record_ico.png') }}" alt="Device" class="sidebar-icon">
                            <span>Device</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('config') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('config') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/config.png') }}" alt="Configure" class="sidebar-icon">
                            <span>Configure</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('evaluation') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('evaluation') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/config.png') }}" alt="eval" class="sidebar-icon">
                            <span>Evaluation</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('accounts') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('accounts') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/add_account.png') }}" alt="Accounts" class="sidebar-icon">
                            <span>Accounts</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('manage_orgs_page') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('manage_orgs_page') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/org_ico.png') }}" alt="Manage Orgs" class="sidebar-icon">
                            <span>Manage Orgs</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('logs') }}" class="nav-link px-3 text-white d-flex align-items-center {{ request()->is('logs') ? 'active-link' : '' }}">
                            <img src="{{ asset('images/loggs_ico.png') }}" alt="Logs" class="sidebar-icon">
                            <span>Logs</span>
                        </a>
                    </li>
                @endif
            @endif

            <!-- Divider -->
            <li class="my-2">
                <hr class="divider">
            </li>

            <li>
                <a href="#" class="nav-link px-3 d-flex text-white align-items-center"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('images/logout_ico.png') }}" alt="Log-out" class="sidebar-icon">
                    <span>Log-out</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>

        </ul>
    </nav>
</div>
</div>
