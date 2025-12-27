




<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> Admin</title>

        <!-- plugins:css -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/feather/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/ti-icons/css/themify-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/css/vendor.bundle.base.css') }}">
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>  
        <!-- endinject -->
    
        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/vendors/ti-icons/css/themify-icons.css') }}">
        
        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <!-- DataTables Buttons CSS -->
        

        <!-- inject:css -->
        <link rel="stylesheet" href="{{ asset('/public/backend/assets/css/style.css') }}">

        <!-- endinject -->
        <link rel="shortcut icon" href="{{ asset('/public/favicon.ico') }}" />
        <style>
            .toast-success {
                background-color: #51A351 !important;  /* green */
                color: #fff !important;
            }

            .toast-error {
                background-color: #BD362F !important;  /* red */
                color: #fff !important;
            }

            .toast-info {
                background-color: #2F96B4 !important;  /* blue */
                color: #fff !important;
            }

            .toast-warning {
                background-color: #F89406 !important;  /* orange */
                color: #fff !important;
            }

        </style>
        @stack('styles')
    </head>

    <body>
        <div class="container-scroller">
            @include('backend.layouts.navbar')
            <div class="container-fluid page-body-wrapper">
                @include('backend.layouts.sidebar')
                <div class="main-panel">
                    @yield('content')
                    <div class="row">
                        @include('backend.layouts.footer')
                    </div>
                </div>
            </div>
        </div>

        <!-- plugins:js -->
        <!-- <script src="{{ asset('/public/backend/assets/vendors/js/vendor.bundle.base.js') }}"></script> -->
        <!-- <script src="https://crmiitm.digittrix.com/public/backend/assets/vendors/js/vendor.bundle.base.js:2:27028)"></script> -->
        <!-- endinject -->

        <!-- Plugin js for this page -->
        <!-- <script src="{{ asset('/public/backend/assets/js/dataTables.select.min.js') }}"></script> -->
        
        <!-- DataTables JS -->
         
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        
        
        <!-- inject:js -->
        <script src="{{ asset('/public/backend/assets/js/off-canvas.js') }}"></script>
        <script src="{{ asset('/public/backend/assets/js/template.js') }}"></script>
        <script src="{{ asset('/public/backend/assets/js/settings.js') }}"></script>
        <script src="{{ asset('/public/backend/assets/js/todolist.js') }}"></script>
        <!-- endinject -->
        
        <!-- Custom js for this page-->
        <script src="{{ asset('/public/backend/assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
        <!-- End custom js for this page-->
        @stack('scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                // toastr.success("Test success!");

                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
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
        

    </body>
</html>