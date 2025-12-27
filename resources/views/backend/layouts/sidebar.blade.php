<style>
    .sidebar-brand img {
        width: 228px;
        height: 150px;
        transition: all 0.3s ease;
        background: white;
        object-fit: contain;
    }
    .sidebar-brand {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px;
        transition: all 0.3s ease;
    }

</style>
{{-- Admin Sidebar --}}
@if(auth()->user()->hasRole('Admin'))
<nav id="sidebar_Admin" class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}">
            <!-- <img src="{{ asset('/public/ITI_logo.png') }}" alt="Logo" /> -->
            <img src="{{ asset('/public/ITI_logo3.jpg') }}" alt="Logo" />
        </a>
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i data-feather="sliders"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Hostel Management --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#hostelManagementMenu_Admin">
                <i data-feather="home"></i>
                <span>Hostel Management</span>
            </a>
            <div class="collapse" id="hostelManagementMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('students.index') }}">Students</a></li>
                    <li><a class="sidebar-link" href="{{ route('hostels.index') }}">Hostel List</a></li>
                    <li><a class="sidebar-link" href="{{ route('buildings.index') }}">Buildings</a></li>
                    <li><a class="sidebar-link" href="{{ route('rooms.index') }}">Rooms</a></li>
                    <li><a class="sidebar-link" href="{{ route('admin.alloted_rooms_list') }}">Room Allocations</a></li>
                    <li><a class="sidebar-link" href="{{ route('admin.roomVacancyStatus') }}">Live Occupancy</a></li>
                    <li><a class="sidebar-link" href="{{ route('admin.roomChangeRequestsList') }}">Room Change Requests</a></li>
                </ul>
            </div>
        </li>

        {{-- Mess Management --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#messManagementMenu_Admin">
                <i data-feather="coffee"></i>
                <span>Mess Management</span>
            </a>
            <div class="collapse" id="messManagementMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('mess.list') }}">Mess List</a></li>
                    <li><a class="sidebar-link" href="{{ route('mess.bills') }}">Mess Billing</a></li>
                </ul>
            </div>
        </li>

        {{-- Biometric Attendance --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#biometricAttendanceMenu_Admin">
                <i data-feather="clock"></i>
                <span> Attendance</span>
            </a>
            <div class="collapse" id="biometricAttendanceMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('attendence.list') }}">Attendance List</a></li>
                    <li><a class="sidebar-link" href="{{ route('student-leaves.index') }}">Student Leaves</a></li>
                </ul>
            </div>
        </li>

        {{-- Biometric Couple Apartment --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#CoupleApartmentMenu_Admin">
                <i data-feather="home"></i>
                <span> Couple Apartment</span>
            </a>
            <div class="collapse" id="CoupleApartmentMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('admin.couple-apartment.index') }}">Apartment List</a></li>
                </ul>
            </div>
        </li>

        {{-- Disciplinary Actions --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#ViolationsMenu_Admin">
                <i data-feather="alert-triangle"></i>
                <span> Disciplinary Actions</span>
            </a>
            <div class="collapse" id="ViolationsMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('violations.index') }}">Violations List</a></li>
                </ul>
            </div>
        </li>

        {{-- Settings --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#settingsMenu_Admin" role="button" aria-expanded="false" aria-controls="settingsMenu_Admin">
                <i data-feather="settings"></i>
                <span>Settings</span>
            </a>
            <div class="collapse" id="settingsMenu_Admin">
                <ul class="sidebar-dropdown list-unstyled">
                    <!--  -->
                    @can('manage users')
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i data-feather="user"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    @endcan

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('roles.list') ? 'active' : '' }}" href="{{ route('roles.list') }}">
                            <i data-feather="shield"></i>
                            <span>Roles</span>
                        </a>
                    </li>

                    @can('manage roles')
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                            <i data-feather="key"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    @endcan
                    <!--  -->
                    <li>
                        <a class="sidebar-link {{ request()->routeIs('admin.editSMTP') ? 'active' : '' }}" href="{{ route('admin.editSMTP') }}">
                            <i data-feather="user" class="me-2"></i>
                            SMTP Details
                        </a>
                    </li>
                    <!--  -->
                    <li>
                        <a class="sidebar-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                            <i data-feather="user" class="me-2"></i>
                            Notifications
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>


        {{-- Logout --}}
        <li class="sidebar-item mt-3">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link btn btn-link text-start w-100">
                    <i data-feather="power" class="text-danger"></i>
                    <span class="text-danger">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
@endif


{{-- Warden Sidebar --}}
@if(auth()->user()->hasRole('Hostel Warden'))
<nav id="sidebar_Warden" class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('warden.dashboard') }}">
            <img src="{{ asset('/public/ITI_logo3.jpg') }}" alt="Logo" />
        </a>
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('warden.dashboard') ? 'active' : '' }}" href="{{ route('warden.dashboard') }}">
                <i data-feather="sliders"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Hostel Management --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#hostelManagementMenu_Warden">
                <i data-feather="home"></i>
                <span>Hostel Management</span>
            </a>
            <div class="collapse" id="hostelManagementMenu_Warden">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('students.index') }}">Students</a></li>
                    <li><a class="sidebar-link" href="{{ route('buildings.index') }}">Buildings</a></li>
                    <li><a class="sidebar-link" href="{{ route('rooms.index') }}">Rooms</a></li>
                    <li><a class="sidebar-link" href="{{ route('warden.alloted_rooms_list') }}">Room Allocations</a></li>
                    <li><a class="sidebar-link" href="{{ route('warden.roomVacancyStatus') }}">Live Occupancy</a></li>
                </ul>
            </div>
        </li>

        {{-- Biometric Attendance --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#biometricAttendanceMenu_Warden">
                <i data-feather="activity"></i>
                <span> Attendance</span>
            </a>
            <div class="collapse" id="biometricAttendanceMenu_Warden">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('attendence.list') }}">Attendance List</a></li>
                    <li><a class="sidebar-link" href="{{ route('student-leaves.index') }}">Student Leaves</a></li>
                </ul>
            </div>
        </li>

        {{-- Disciplinary Actions --}}
        <li class="sidebar-item">
            <a class="sidebar-link collapsed" data-bs-toggle="collapse" href="#ViolationsMenu_Warden">
                <i data-feather="activity"></i>
                <span> Disciplinary Actions</span>
            </a>
            <div class="collapse" id="ViolationsMenu_Warden">
                <ul class="sidebar-dropdown list-unstyled">
                    <li><a class="sidebar-link" href="{{ route('violations.index') }}">Violations List</a></li>
                </ul>
            </div>
        </li>

        
        {{-- Logout --}}
        <li class="sidebar-item mt-3">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link btn btn-link text-start w-100">
                    <i data-feather="power" class="text-danger"></i>
                    <span class="text-danger">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
@endif


{{-- Mess Manager Sidebar --}}
@if(auth()->user()->hasRole('Mess Manager'))
<nav id="sidebar_Mess" class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('mess.dashboard') }}">
            <img src="{{ asset('/public/ITI_logo3.jpg') }}" alt="Logo" />
        </a>
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('mess.dashboard') ? 'active' : '' }}" href="{{ route('mess.dashboard') }}">
                <i data-feather="sliders"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Logout --}}
        <li class="sidebar-item mt-3">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link btn btn-link text-start w-100">
                    <i data-feather="power" class="text-danger"></i>
                    <span class="text-danger">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
@endif

