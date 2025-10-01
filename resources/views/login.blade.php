<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ticktax Attendance System</title>

  <!-- Bootstrap CSS and Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <!-- Custom Styles -->
  <style>
body {
  background: linear-gradient(to bottom, #012e57 60%, #ffffff 50%);
  position: relative;
  font-family: 'Segoe UI', sans-serif;
  min-height: 100vh;
  margin: 0;
  overflow: hidden;
}
body::before {
  content: "";
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: url('{{ asset("images/login_bg.png") }}') no-repeat center center fixed;
  background-size: cover;
  opacity: 0.2;
  z-index: -1;
}
    .custom-box {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border-radius: 1rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.3);
  overflow: hidden;
  max-width: 900px;           /* Was 800px */
  min-height: 400px;          /* Added height */
  width: 100%;
  display: flex;
  flex-direction: row;
}
    .left-side {
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.4)),
                  url('{{ asset("images/login_bg2.png") }}') no-repeat center center;
      background-size: cover;
      color: #fff;
      flex: 1;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      text-align: center;
    }

    .left-side h2 {
      font-size: 2rem;
      font-weight: bold;
    }

    .left-side p {
      font-size: 0.95rem;
    }

    .right-side {
      flex: 1;
      padding: 2rem;
      background-color: rgba(255, 255, 255, 0.25);
    }

    .right-side h2,
    .right-side p,
    .right-side label,
    .right-side a {
      color: #002244;
    }

    .form-control {
      font-size: 0.95rem;
    }

    .form-control:focus {
      border-color: #1b3b6f;
      box-shadow: 0 0 0 0.2rem rgba(27, 59, 111, 0.25);
    }

    .btn-primary {
      background-color: #1b3b6f;
      border-color: #1b3b6f;
    }

    .btn-primary:hover {
      background-color: #123057;
      border-color: #123057;
    }

    .alert {
      font-size: 0.9rem;
    }

    a.text-light:hover {
      color: #3b69ff !important;
    }

    @media (max-width: 768px) {
      .custom-box {
        flex-direction: column;
      }

      .left-side, .right-side {
        flex: unset;
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100 px-3">
  <div class="custom-box">
    <!-- Left side -->
    <div class="left-side">
      <h2>Hello, User</h2>
      <p>Welcome back to the Student Organization Attendance System</p>
    </div>

    <!-- Right side -->
    <div class="right-side">
      @if (session('message'))
        <div class="alert alert-success text-center">
          {{ session('message') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger text-center">
          {{ session('error') }}
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST">
  @csrf

  <!-- Login heading and description -->
  <div class="mb-4 text-center">
    <h3 class="fw-bold mb-1">Log In</h3>
    <p class="text-muted mb-0" style="font-size: 0.95rem;">Login with your email and password</p>
  </div>

  <div class="mb-3">
    <input type="text" name="email" class="form-control form-control-lg bg-light" placeholder="Email address" required />
  </div>

  <div class="mb-3 position-relative">
    <input type="password" name="password" class="form-control form-control-lg bg-light pe-5" placeholder="Password" id="passwordInput"  />
    <i class="bi bi-eye-fill position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" id="eyeIcon" onclick="togglePasswordVisibility()" style="cursor:pointer;"></i>
  </div>



  <div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
  </div>
</form>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script>
  function togglePasswordVisibility() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('bi-eye-fill');
      icon.classList.add('bi-eye-slash-fill');
    } else {
      input.type = 'password';
      icon.classList.remove('bi-eye-slash-fill');
      icon.classList.add('bi-eye-fill');
    }
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
