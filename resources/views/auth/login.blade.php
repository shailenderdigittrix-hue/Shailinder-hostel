{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Optional custom styles -->
  <link rel="stylesheet" href="assets/css/style.css" />
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  
  <title>HostelCRM </title>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow" style="width: 100%; max-width: 400px;">
    <div class="card-body">

      <!-- Logo -->
      <div class="text-center mb-4">
        <h1>Login</h1>
        <!-- <img src="https://batrips.com/LAUNCH/assets/logo.png" alt="Logo" class="img-fluid" style="max-height: 80px;"> -->
      </div>

      <!-- Login Form -->
      <form method="POST" action="{{ route('login') }}" accept-charset="utf-8">
        @csrf
        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input 
            type="email" 
            class="form-control" 
            id="email" 
            name="email" 
            placeholder="Enter your email" 
            required
          >
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            class="form-control" 
            id="password" 
            name="password" 
            placeholder="Enter your password" 
            required
          >
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
          <button 
            type="submit" 
            class="btn text-white" 
            style="background-color: #00D2DE; border-color: #00D2DE;"
          >
            Sign In
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
</body>
</html>
