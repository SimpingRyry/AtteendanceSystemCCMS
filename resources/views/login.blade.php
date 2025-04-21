{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCMS Attendance System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .input-group { position: relative; }
        #eyeIcon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 shadow box-area" style="background: rgba(26, 28, 43, 0.16);">
        <div class="col-md-6 d-flex justify-content-center align-items-center flex-column left-box">
            <div class="featured-image mb-3">
                <img src="{{ asset('images/login_logo.png') }}" class="img-fluid featured-image" alt="Login Logo">
            </div>
            <div class="d-none d-md-block text-center">
                <p class="text-white ticktax-heading">TickTax: An IoT-Based Event</p>
                <p class="text-white ticktax-heading">Attendance and Fine Monitoring</p>
                <p class="text-white ticktax-heading">System for CCMS</p>
            </div>
        </div>

        <div class="col-md-6 right-box rounded-end-4">
            <div class="row align-items-center">
                <div class="header-text mb-4 text-md-start text-center">
                    <p class="text-white fw-semibold responsive-heading display-6 display-md-4">Hello Admin!</p>
                    <p class="text-white">Monitor your CCMS members</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="email" class="form-control form-control-lg bg-light fs-8" placeholder="Email address" required>
                    </div>
                    <div class="input-group mb-3 position-relative">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-8 rounded-3 pe-5" placeholder="Password" id="passwordInput" required>
                        <img src="{{ asset('images/open_eye.png') }}" id="eyeIcon" alt="eye" style="width: 20px;" onclick="togglePasswordVisibility()">
                    </div>

                    <div class="input-group mb-5 d-flex justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="formCheck">
                            <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                        </div>
                        <div class="forgot">
                            <small><a href="#">Forgot Password?</a></small>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <button type="submit" name="login" class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = '{{ asset("images/closed_eye.png") }}';
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = '{{ asset("images/open_eye.png") }}';
        }
    }
</script>
</body>
</html>
