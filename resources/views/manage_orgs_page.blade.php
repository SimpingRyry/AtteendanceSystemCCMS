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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dash_device.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

<main>
    <div class="container outer-box mt-5 pt-5 pb-4">
        <div class="container bg-white rounded-4 shadow-sm p-4" id="main_box">
            <!-- Heading -->
            <div class="mb-3">
                <h2 class="fw-bold text-dark">Manage Orgs</h2>
                <small class="text-muted">Manage /</small>
                <small class="text-secondary">Orgs</small>
            </div>

            <!-- Add Organization Button -->
            <div class="text-end mb-3">
                <button class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#addOrgModal" title="Add Organization">
                    <i class="bi bi-plus-lg"></i> Add Organization
                </button>
            </div>

            <!-- Organization Table -->
            <div class="card border-0 shadow-sm p-4">
                <h5 class="mb-3 text-dark">Organization List</h5>
                <div class="table-responsive">
                    <table class="table align-middle text-center table-borderless">
                         <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Organization Name</th>
                                <th>Logo</th>
                                <th>Description</th>
                                <th>Background</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse ($org_list as $org)
                                <tr class="border-bottom">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $org->org_name }}</td>
                                    <td>
                                        <img src="{{ asset('images/' . $org->org_logo) }}" alt="Logo" width="50" class="rounded">
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;">{{ $org->description }}</td>
                                    <td>
                                        <img src="{{ asset('images/' . $org->bg_image) }}" alt="Background" width="50" class="rounded">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editOrgModal{{ $org->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrgModal{{ $org->id }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No organizations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

    {{-- Modal: Add Organization --}}
    <div class="modal fade" id="addOrgModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow glassy-bg">
            <div class="modal-header">
                <h5 class="modal-title">Add Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orgs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="org_name" class="form-label">Organization Name <span class="text-danger">*</span></label>
                        <input type="text" name="org_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="org_logo" class="form-label">Organization Logo <span class="text-danger">*</span></label>
                        <input type="file" name="org_logo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="bg_image" class="form-label">Background Image <span class="text-danger">*</span></label>
                        <input type="file" name="bg_image" class="form-control" accept="image/*" required>
                    </div>
                    <hr>
                    <h5 class="mt-4">Organization Adviser Account</h5>
                    <div class="mb-3">
                        <label for="username" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="org_admin_org" class="form-label">Organization <span class="text-danger">*</span></label>
                        <input type="text" id="org_admin_org" name="org_admin_org" class="form-control" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Organization</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-success mb-3">
          <i class="bi bi-check-circle-fill" style="font-size: 60px;"></i>
        </div>
        <h5 class="text-success">Success!</h5>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn btn-success mt-2" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endif

</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const orgInput = document.querySelector('[name="org_name"]');
        const adminOrgInput = document.querySelector('#org_admin_org');

        orgInput.addEventListener('input', () => {
            adminOrgInput.value = orgInput.value;
        });
    });
</script>
    @foreach ($org_list as $org)
    {{-- Modal: Edit Organization --}}
    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="editOrgModal{{ $org->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('orgs.update', $org->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Organization</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Name & Description --}}
                        <div class="mb-3">
                            <label class="form-label">Organization Name</label>
                            <input type="text" name="org_name" value="{{ $org->org_name }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $org->description }}</textarea>
                        </div>

                        {{-- Logo and Background Preview with Replace Buttons --}}
                        <div class="row mb-3 align-items-center">
                            {{-- Logo --}}
                            <div class="col-md-6 text-center">
                                <label class="form-label">Current Logo</label>
                                <div class="mb-2">
                                    <img src="{{ asset('images/' . $org->org_logo) }}" alt="Logo" width="100">
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('logoInput{{ $org->id }}').click()">Replace Logo</button>
                                    <input type="file" id="logoInput{{ $org->id }}" name="org_logo" class="d-none" accept="image/*">
                                </div>
                            </div>

                            {{-- Background --}}
                            <div class="col-md-6 text-center">
                                <label class="form-label">Current Background</label>
                                <div class="mb-2">
                                    <img src="{{ asset('images/' . $org->bg_image) }}" alt="Background" width="100">
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('bgInput{{ $org->id }}').click()">Replace Background</button>
                                    <input type="file" id="bgInput{{ $org->id }}" name="bg_image" class="d-none" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Confirm Deletion --}}
    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="deleteOrgModal{{ $org->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('orgs.destroy', $org->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Are you sure you want to delete <strong>{{ $org->org_name }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>