<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dash_device.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background: radial-gradient(circle at top left, white, #e0f7ff, #b3e5fc);">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
        <div class="container outer-box mt-5 pt-5 pb-4 shadow">
            <div class="container inner-glass shadow p-4" id="main_box">
                <h3 class="text-center text-dark mb-4">Add Organization</h3>

                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('orgs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card glass-box p-4 mb-3" style="background-color: rgba(255, 255, 255, 0.6); border-radius: 15px;">
                        <div class="mb-3">
                            <label for="org_name" class="form-label">Organization Name</label>
                            <input type="text" name="org_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="org_logo" class="form-label">Organization Logo</label>
                            <input type="file" name="org_logo" class="form-control" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label for="bg_image" class="form-label">Background Image</label>
                            <input type="file" name="bg_image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>