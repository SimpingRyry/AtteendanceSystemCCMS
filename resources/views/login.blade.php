<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCMS Attendance System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="custom-glass text-white">
        <div class="text-center mb-4">
            <h2 class="fw-bold display-6">Hello, User</h2>
            <p>Welcome back to CCMS Attendance System</p>
        </div>

        @if (session('message'))
    <div class="alert alert-success text-center">
        {{ session('message') }}
    </div>
@endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="text" name="email" class="form-control form-control-lg bg-light fs-8" placeholder="Email address" required>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" name="password" class="form-control form-control-lg bg-light fs-8 pe-5" placeholder="Password" id="passwordInput" required>
                <i class="bi bi-eye-fill position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" id="eyeIcon" onclick="togglePasswordVisibility()" style="cursor:pointer;"></i>
            </div>

            <div class="mb-4 d-flex justify-content-between">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="formCheck">
                    <label for="formCheck" class="form-check-label text-light"><small>Remember Me</small></label>
                </div>
                <div>
                    <small><a href="#" class="text-decoration-none text-light">Forgot Password?</a></small>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
            </div>
        </form>
    </div>
</div>

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
