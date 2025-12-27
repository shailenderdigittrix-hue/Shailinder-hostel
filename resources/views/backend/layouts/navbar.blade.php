<nav class="navbar navbar-expand navbar-light navbar-bg">
    <!-- Sidebar Toggle Button -->
    <button id="sidebarToggleBtn" title="Toggle Sidebar" class="btn btn-link">
        <i class="bi bi-list fs-4"></i>
    </button>
    
    <!-- Push everything else to the end -->
    <div class="ms-auto">
        <ul class="navbar-nav align-items-center mb-0">
            
            <!-- Notifications Bell -->
            <li class="nav-item dropdown me-3">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-4"></i>

                    @if(getUnreadNotificationsCount() > 0)
                        <span  class="bell-number position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ getUnreadNotificationsCount() }}
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm p-0" aria-labelledby="notificationsDropdown" 
                    style="width: 320px; max-height: 60vh; overflow-y: auto;">

                    <!-- Header -->
                    <li class="dropdown-header bg-light py-2 px-3">
                        <strong>Notifications</strong>
                    </li>
                    <li><hr class="dropdown-divider m-0"></li>

                    <!-- Notifications List -->
                    @forelse(getUnreadNotifications() as $notification)
                        <li class="dropdown-item d-flex flex-column align-items-start py-2 px-3">
                            <div class="fw-bold text-truncate" title="{{ $notification->title ?? 'Notification' }}">
                                {{ $notification->title ?? 'Notification' }}
                            </div>
                            <div class="text-truncate small" title="{{ $notification->message ?? '' }}">
                                {{ $notification->message ?? '' }}
                            </div>
                            <small class="text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</small>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                    @empty
                        <li class="dropdown-item text-center py-3">No new notifications</li>
                    @endforelse

                    <!-- Optional Footer -->
                    <li class="dropdown-footer text-center py-2">
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none">View All</a>
                    </li>

                </ul>
            </li>



            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>

                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4 me-1"></i>
                    <span class="text-dark">{{ auth()->user()->name }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-end shadow-sm">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right text-danger me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>

</nav>
