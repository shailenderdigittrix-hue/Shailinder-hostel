<!DOCTYPE html>
    <html lang="en">
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5" />
        <meta name="author" content="HOSTEL" />
        <meta name="keywords" content="HOSTEL CRM" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link rel="shortcut icon" href="{{ asset('public/favicon.ico') }}" />
        <title>Dashboard</title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

        <!-- Toastr CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Custom Styles -->
        <link rel="stylesheet" href="{{ asset('public/style.css') }}">


        <style>
            /* ===== General Reset ===== */
            body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
            }
            /* ===== Sidebar Styling ===== */
            .sidebar {
            width: 240px;
            background-color: #1e293b; /* dark navy blue */
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            color: #fff;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            z-index: 1000;
            }
            .sidebar-brand {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            transition: all 0.3s ease;
            }
            .sidebar-brand img {
            width: 150px;
            height: auto;
            transition: all 0.3s ease;
            }
            /* ===== When sidebar collapses ===== */
            .sidebar-collapsed .sidebar-brand {
            padding: 10px;
            }
            .sidebar-collapsed .sidebar-brand img {
            width: 45px;   /* smaller size when collapsed */
            height: auto;
            }
            /* Sidebar Nav */
            .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 10px 0;
            }
            .sidebar-item {
            margin: 5px 0;
            }
            .sidebar-link {
            color: #cbd5e1 !important;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: 0.2s;
            font-size: 15px;
            }
            .sidebar-link:hover {
            background-color: #334155;
            color: #fff !important;
            border-left-color: #3b82f6;
            }
            .sidebar-link.active {
            background-color: #334155;
            border-left-color: #3b82f6;
            color: #fff !important;
            }
            .sidebar-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            }
            /* Collapsible dropdown */
            .sidebar-dropdown a {
            font-size: 14px;
            padding: 8px 40px;
            color: #cbd5e1;
            }
            .sidebar-dropdown a:hover {
            color: #fff;
            }
            /* ===== Navbar Styling ===== */
            .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            margin-left: 240px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 900;
            }
            .navbar .btn {
            color: #1e293b;
            border: none;
            }
            .navbar .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            /* ===== Main Content Area ===== */
            .main-content {
            margin-left: 240px;
            padding: 2rem;
            transition: all 0.3s ease;
            }
            /* ===== Sidebar Toggle ===== */
            .sidebar-collapsed .sidebar {
            width: 70px;
            }
            .sidebar-collapsed .navbar {
            margin-left: 70px;
            }
            .sidebar-collapsed .main-content {
            margin-left: 70px;
            }
            .sidebar-collapsed .sidebar .sidebar-link span,
            .sidebar-collapsed .sidebar .sidebar-dropdown {
            display: none;
            }
            /* ===== Scrollbar ===== */
            .sidebar::-webkit-scrollbar {
            width: 6px;
            }
            .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
            }
            /* ===== Footer ===== */
            footer {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            padding: 15px;
            border-top: 1px solid #e5e7eb;
            background-color: #fff;
            margin-left: 240px;
            position: fixed;
            bottom: 0;
            width: calc(100% - 240px);
            }
            .sidebar-collapsed footer {
            margin-left: 70px;
            width: calc(100% - 70px);
            }
            /* ===== Main Content Positioning ===== */
            main.content {
            margin-left: 240px;
            /* margin-top: 70px; */
            padding: 20px;
            background-color: #f8f9fb;
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease-in-out;
            }
            .sidebar-collapsed main.content {
            margin-left: 70px;
            }
            .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
            }
            .feather-icon {
            width: 48px;
            height: 48px;
            }
            span.bell-number {
            position: absolute;
            left: 40px !important;
            top: 7px !important;
            }
        </style>

        @stack('styles')
    </head>
    <body>
        <div class="wrapper">
            <!-- <div class="content-wrapper"> -->
            @include('backend.layouts.sidebar')
            <div class="main" id="mainContent">
                @include('backend.layouts.navbar')
                <main class="content">
                    <div class="container-fluid p-0">
                        @yield('content')
                    </div>
                </main>
                <footer class="footer">
                    @include('backend.layouts.footer')
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Feather icons script -->
        <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
        <!-- Bootstrap JS (optional for collapse menus, etc.) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- <script src="https://unpkg.com/feather-icons"></script> -->
        <!-- <script src="{{ asset('/public/backend/assets/static/js/app.js') }}"></script> -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
        
        <script>
            // document.addEventListener('contextmenu', function(e) {
            //     e.preventDefault(); 
            // });
            
            // Disable keyboard shortcuts for DevTools and context menu
            document.addEventListener('keydown', function(e) {
                // F12 (opens DevTools)
                if (e.key === "F12") {
                    e.preventDefault();
                    // alert("F12 is disabled!");
                }
            
                // Ctrl + Shift + I / J / C (common DevTools shortcuts)
                if (e.ctrlKey && e.shiftKey && ["I", "J", "C"].includes(e.key.toUpperCase())) {
                    e.preventDefault();
                    // alert("DevTools shortcut disabled!");
                }
            
                // Ctrl + U (View Page Source)
                if (e.ctrlKey && e.key.toUpperCase() === "U") {
                    e.preventDefault();
                    // alert("View source is disabled!");
                }
            
                // Shift + F10 (context menu)
                if (e.shiftKey && e.key === "F10") {
                    e.preventDefault();
                    // alert("Context menu shortcut disabled!");
                }
            
                // Context Menu key
                if (e.key === "ContextMenu") {
                    e.preventDefault();
                    // alert("Context menu key disabled!");
                }
            });
            
        </script>

        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        <script>
            // Pusher.logToConsole = true;
            
            // ------------
            // var pusher = new Pusher('8e7dcbba961d12274052', {
            //     cluster: '{{ env('PUSHER_APP_CLUSTER', 'ap2') }}',
            // });

            var pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
                cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
            });

            // ------------
            var channel = pusher.subscribe('event-channel');
            const authUserId = {!! json_encode(auth()->user()->id ?? null) !!};
            const authUserRole = {!! json_encode(auth()->user()?->getRoleNames()->first() ?? null) !!};

            // const authUserRole = @json(auth()->user()->getRoleNames()->first());
            const allowedRoles = ['Hostel Warden', 'Admin'];
        
            // Subscribe to channels
            var adminChannel = pusher.subscribe('admin-channel');
            var wardenChannel = pusher.subscribe('warden-channel');
        
            // Common event listener
            function handleViolation(data) {
                if (data.user_id == authUserId || allowedRoles.includes(data.role)) {
                    toastr.success(data.message);
                }
            }
            
            // adminChannel.bind('admin-channel-event', handleViolation);
            wardenChannel.bind(`warden-channel-event-${authUserId}`, function (data) {
                console.log("warden-channel-event", data);
                // alert(data.message);
                toastr.success(JSON.stringify(data.message));
            });
            
            adminChannel.bind(`admin-channel-event-${authUserId}`, function(data) {
                console.log("admin-channel-event", data);
                // alert(data.message);
                toastr.success(JSON.stringify(data.message));
            });
            
            //  ------------
            channel.bind('notification-event-', function(data) {
                toastr.success(JSON.stringify(data));
            });
            
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                feather.replace();
            
                const toggleBtn = document.getElementById('sidebarToggleBtn');
                const sidebar = document.getElementById('sidebar_Admin');
            
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Line chart
                const ctxLine = document.getElementById("chartjs-dashboard-line").getContext("2d");
                const gradient = ctxLine.createLinearGradient(0, 0, 0, 225);
                gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
                gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
            
                new Chart(ctxLine, {
                    type: "line",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: "Sales ($)",
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: window.theme.primary,
                            data: [2115, 1562, 1584, 1892, 1587, 1923, 2566, 2448, 2805, 3438, 2917, 3327]
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            filler: { propagate: false }
                        },
                        interaction: {
                            intersect: false
                        },
                        scales: {
                            x: {
                                reverse: true,
                                grid: { color: "rgba(0,0,0,0.0)" }
                            },
                            y: {
                                ticks: { stepSize: 1000 },
                                grid: { color: "rgba(0,0,0,0.0)" }
                            }
                        }
                    }
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Pie chart
                new Chart(document.getElementById("chartjs-dashboard-pie"), {
                    type: "pie",
                    data: {
                        labels: ["Chrome", "Firefox", "IE"],
                        datasets: [{
                            data: [4306, 3801, 1689],
                            backgroundColor: [window.theme.primary, window.theme.warning, window.theme.danger],
                            borderWidth: 5
                        }]
                    },
                    options: {
                        responsive: !window.MSInputMethodContext,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: "75%"
                    }
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Bar chart
                new Chart(document.getElementById("chartjs-dashboard-bar"), {
                    type: "bar",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: "This year",
                            backgroundColor: window.theme.primary,
                            borderColor: window.theme.primary,
                            hoverBackgroundColor: window.theme.primary,
                            hoverBorderColor: window.theme.primary,
                            data: [54, 67, 41, 55, 62, 45, 55, 73, 60, 76, 48, 79],
                            barPercentage: 0.75,
                            categoryPercentage: 0.5
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                grid: { display: false },
                                stacked: false,
                                ticks: { stepSize: 20 }
                            },
                            x: {
                                stacked: false,
                                grid: { color: "transparent" }
                            }
                        }
                    }
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // jsVectorMap initialization
                var markers = [
                    { coords: [31.230391, 121.473701], name: "Shanghai" },
                    { coords: [28.704060, 77.102493], name: "Delhi" },
                    { coords: [6.524379, 3.379206], name: "Lagos" },
                    { coords: [35.689487, 139.691711], name: "Tokyo" },
                    { coords: [23.129110, 113.264381], name: "Guangzhou" },
                    { coords: [40.7127837, -74.0059413], name: "New York" },
                    { coords: [34.052235, -118.243683], name: "Los Angeles" },
                    { coords: [41.878113, -87.629799], name: "Chicago" },
                    { coords: [51.507351, -0.127758], name: "London" },
                    { coords: [40.416775, -3.703790], name: "Madrid " }
                ];
            
                var map = new jsVectorMap({
                    map: "world",
                    selector: "#world_map",
                    zoomButtons: true,
                    markers: markers,
                    markerStyle: {
                        initial: {
                            r: 9,
                            strokeWidth: 7,
                            stokeOpacity: 0.4,
                            fill: window.theme.primary
                        },
                        hover: {
                            fill: window.theme.primary,
                            stroke: window.theme.primary
                        }
                    },
                    zoomOnScroll: false
                });
            
                window.addEventListener("resize", () => {
                    map.updateSize();
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var date = new Date(Date.now() - 5 * 24 * 60 * 60 * 1000);
                var defaultDate = date.getUTCFullYear() + "-" + (date.getUTCMonth() + 1) + "-" + date.getUTCDate();
                document.getElementById("datetimepicker-dashboard").flatpickr({
                    inline: true,
                    prevArrow: "<span title=\"Previous month\">&laquo;</span>",
                    nextArrow: "<span title=\"Next month\">&raquo;</span>",
                    defaultDate: defaultDate
                });
            });
        </script>

        <!-- <script src="{{ asset('/public/backend/assets/js/jquery.cookie.js') }}" type="text/javascript"></script> -->
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
        @stack('scripts')
       
       <script type="text/javascript">
            $(document).ready(function () {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut"
                };
            
                @if (session('success'))
                    toastr.success(@json(session('success')));
                @endif
            
                @if (session('error'))
                    toastr.error(@json(session('error')));
                @endif
            
                @if (session('info'))
                    toastr.info(@json(session('info')));
                @endif
            
                @if (session('warning'))
                    toastr.warning(@json(session('warning')));
                @endif
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggleBtn = document.getElementById('sidebarToggleBtn');
                const body = document.body;
            
                toggleBtn.addEventListener('click', () => {
                    body.classList.toggle('sidebar-collapsed');
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const currentUrl = window.location.href;
                document.querySelectorAll(".sidebar-link").forEach(link => {
                    if (link.href && currentUrl.includes(link.href)) {
                        link.classList.add("active");
                    }
                });
            });
        </script>
        <script>
            feather.replace();
        </script>
    </body>
</html>