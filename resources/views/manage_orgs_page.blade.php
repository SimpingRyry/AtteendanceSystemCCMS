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

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="orgTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#manageOrgsTab">Manage Organizations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#orgHierarchyTab">Organization Hierarchy Configuration</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">

                <!-- Manage Orgs Tab -->
                <div class="tab-pane fade show active" id="manageOrgsTab">
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
                                                <img src="{{ asset('images/org_list/' . $org->org_logo) }}" alt="Logo" width="50" class="rounded">
                                            </td>
                                            <td class="text-truncate" style="max-width: 200px;">{{ $org->description }}</td>
                                            <td>
                                                <img src="{{ asset('images/org_list/' . $org->bg_image) }}" alt="Background" width="50" class="rounded">
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

                <!-- Org Hierarchy Tab -->
                <div class="tab-pane fade" id="orgHierarchyTab">
                    <div class="card border-0 shadow-sm p-4 mt-4">
                        <h5 class="mb-3 text-dark">Set Organization Hierarchy</h5>
                        <form method="POST" action="{{ route('orgs.setHierarchy') }}">
                            @csrf
                            <div class="row">
                                 <div class="col-md-6 mb-3">
                                    <label for="parent_org_id" class="form-label">Parent Organization</label>
                                    <select class="form-select" name="parent_org_id">
                                        <option value="">No Parent (Top-level Org)</option>
                                        @foreach ($org_list as $org)
                                            <option value="{{ $org->id }}">{{ $org->org_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="child_org_id" class="form-label">Child Organization</label>
                                    <select class="form-select" name="child_org_id" required>
                                        <option value="">Select Child Organization</option>
                                        @foreach ($org_list as $org)
                                            <option value="{{ $org->id }}">{{ $org->org_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                               
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Set Hierarchy</button>
                            </div>
                        </form>

                        <div class="mt-4">
    <h6>Current Organization Hierarchy:</h6>
    <ul class="list-group">
        @foreach ($org_list->where('parent_id', null) as $topLevelOrg)
            <li class="list-group-item">
                <strong>{{ $topLevelOrg->org_name }}</strong>
                @if ($topLevelOrg->children->count())
                    <ul class="ms-4 mt-2">
                        @foreach ($topLevelOrg->children as $child)
                            @include('partials.org-tree', ['org' => $child])
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
                    </div>
                </div>

            </div> <!-- End Tab Content -->

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

            <form action="{{ route('orgs.store') }}" method="POST" enctype="multipart/form-data" id="addOrgForm">
                @csrf

                <!-- Step Indicators -->
                <div class="d-flex justify-content-center mb-4 mt-3">
                    <div class="step-indicator text-center mx-2" id="stepIndicator1">
                        <div class="circle">1</div>
                        <small>Organization</small>
                    </div>
                    <div class="step-indicator text-center mx-2" id="stepIndicator2">
                        <div class="circle">2</div>
                        <small>Adviser</small>
                    </div>
                    <div class="step-indicator text-center mx-2" id="stepIndicator3">
                        <div class="circle">3</div>
                        <small>President</small>
                    </div>
                    <div class="step-indicator text-center mx-2" id="stepIndicator4">
                        <div class="circle">4</div>
                        <small>Confirm</small>
                    </div>
                </div>

                <div class="modal-body">

                    <!-- Step 1 -->
                    <div class="form-step step-1">
                        <h5>Step 1: Organization Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" name="org_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Organization Logo <span class="text-danger">*</span></label>
                            <input type="file" name="org_logo" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Background Image <span class="text-danger">*</span></label>
                            <input type="file" name="bg_image" class="form-control" accept="image/*" required>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="form-step step-2 d-none">
                        <h5>Step 2: Adviser Account</h5>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="adviser_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="adviser_email" class="form-control">
                        </div>
                       <div class="mb-3">
  <label class="form-label">Password</label>
  <div class="input-group">
    <input type="password" name="adviser_password" class="form-control" id="adviserPassword">
    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('adviserPassword', this)">
      <i class="bi bi-eye"></i>
    </button>
  </div>
</div>             </div>

                    <!-- Step 3 -->
                    <div class="form-step step-3 d-none">
                        <h5>Step 3: President Account (Optional)</h5>
                        <div class="form-check mb-3">
                            <input type="checkbox" id="skipPresident" class="form-check-input">
                            <label class="form-check-label" for="skipPresident">Skip adding president account</label>
                        </div>
                        <div id="presidentFields">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="president_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="president_email" class="form-control">
                            </div>
                           <div class="mb-3">
  <label class="form-label">Password</label>
  <div class="input-group">
    <input type="password" name="president_password" class="form-control" id="presidentPassword">
    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('presidentPassword', this)">
      <i class="bi bi-eye"></i>
    </button>
  </div>
</div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="form-step step-4 d-none">
                        <h5>Step 4: Confirm and Submit</h5>
                        <p class="text-muted">Please review your details before submitting.</p>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Organization:</strong> <span id="confirmOrgName"></span></li>
                            <li class="list-group-item"><strong>Adviser:</strong> <span id="confirmAdviserName"></span></li>
                            <li class="list-group-item"><strong>President:</strong> <span id="confirmPresidentName"></span></li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevStep" disabled>Previous</button>
                    <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                    <button type="submit" class="btn btn-success d-none" id="submitBtn">Add Organization</button>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scopeSelector = document.getElementById('scopeSelector');
        const unitWrapper = document.getElementById('unitSelectWrapper');
        const courseWrapper = document.getElementById('courseSelectWrapper');

        scopeSelector.addEventListener('change', function () {
            const value = this.value;
            if (value === 'unit') {
                unitWrapper.classList.remove('d-none');
                courseWrapper.classList.add('d-none');
            } else if (value === 'course') {
                courseWrapper.classList.remove('d-none');
                unitWrapper.classList.add('d-none');
            } else {
                unitWrapper.classList.add('d-none');
                courseWrapper.classList.add('d-none');
            }
        });
    });
</script>
<script>
    let currentStep = 1;
    const totalSteps = 4;

    function showStep(step) {
        document.querySelectorAll('.form-step').forEach((el, idx) => {
            el.classList.add('d-none');
            if (idx === step - 1) el.classList.remove('d-none');
        });

        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById(`stepIndicator${i}`);
            indicator.classList.toggle('active', i === step);
        }

        document.getElementById('prevStep').disabled = step === 1;
        document.getElementById('nextStep').classList.toggle('d-none', step === totalSteps);
        document.getElementById('submitBtn').classList.toggle('d-none', step !== totalSteps);
    }

    document.getElementById('nextStep').addEventListener('click', () => {
        if (currentStep < totalSteps) {
            currentStep++;
            if (currentStep === totalSteps) {
                document.getElementById('confirmOrgName').innerText = document.querySelector('[name="org_name"]').value;
                document.getElementById('confirmAdviserName').innerText = document.querySelector('[name="adviser_name"]').value || "N/A";
                const presName = document.querySelector('[name="president_name"]').value;
                document.getElementById('confirmPresidentName').innerText = presName || (document.getElementById('skipPresident').checked ? "Skipped" : "N/A");
            }
            showStep(currentStep);
        }
    });

    document.getElementById('prevStep').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    document.getElementById('skipPresident').addEventListener('change', (e) => {
        document.getElementById('presidentFields').style.display = e.target.checked ? 'none' : 'block';
    });

    document.getElementById('addOrgModal').addEventListener('hidden.bs.modal', () => {
        currentStep = 1;
        showStep(currentStep);
        document.getElementById('addOrgForm').reset();
        document.getElementById('presidentFields').style.display = 'block';
        document.getElementById('skipPresident').checked = false;
    });

    document.addEventListener('DOMContentLoaded', () => {
        showStep(currentStep);
          checkMutualExclusivity(); 
    });

    function checkMutualExclusivity() {
    const adviserFields = ['adviser_name', 'adviser_email', 'adviser_password'].map(id => document.querySelector(`[name="${id}"]`));
    const presidentFields = ['president_name', 'president_email', 'president_password'].map(id => document.querySelector(`[name="${id}"]`));
    const skipPresidentCheckbox = document.getElementById('skipPresident');

    // Adviser input changes
    adviserFields.forEach(field => {
        field.addEventListener('input', () => {
            const adviserFilled = adviserFields.some(f => f.value.trim() !== '');
            if (adviserFilled) {
                // Auto-skip and disable president fields
                skipPresidentCheckbox.checked = true;
                document.getElementById('presidentFields').style.display = 'none';
                presidentFields.forEach(f => {
                    f.value = '';
                    f.disabled = true;
                });
            } else {
                // Re-enable president fields
                skipPresidentCheckbox.checked = false;
                document.getElementById('presidentFields').style.display = 'block';
                presidentFields.forEach(f => f.disabled = false);
            }
        });
    });

    // President input changes
    presidentFields.forEach(field => {
        field.addEventListener('input', () => {
            const presFilled = presidentFields.some(f => f.value.trim() !== '');
            if (presFilled) {
                // Disable adviser fields
                adviserFields.forEach(f => {
                    f.value = '';
                    f.disabled = true;
                });
            } else {
                // Re-enable adviser fields
                adviserFields.forEach(f => f.disabled = false);
            }
        });
    });
}

    // Core validation logic
    document.getElementById('addOrgForm').addEventListener('submit', function(e) {
        const adviserName = document.querySelector('[name="adviser_name"]').value.trim();
        const adviserEmail = document.querySelector('[name="adviser_email"]').value.trim();
        const adviserPass = document.querySelector('[name="adviser_password"]').value.trim();

        const presidentName = document.querySelector('[name="president_name"]').value.trim();
        const presidentEmail = document.querySelector('[name="president_email"]').value.trim();
        const presidentPass = document.querySelector('[name="president_password"]').value.trim();

        const skipPresident = document.getElementById('skipPresident').checked;

        const adviserFilled = adviserName || adviserEmail || adviserPass;
        const presidentFilled = skipPresident || presidentName || presidentEmail || presidentPass;

        if (!adviserFilled && !presidentFilled) {
            e.preventDefault();
            alert("Please fill out either Adviser or President details.");
        } else if (adviserFilled && (!adviserName || !adviserEmail || !adviserPass)) {
            e.preventDefault();
            alert("Please complete all Adviser fields or leave them all empty.");
        } else if (!skipPresident && (presidentFilled && (!presidentName || !presidentEmail || !presidentPass))) {
            e.preventDefault();
            alert("Please complete all President fields or check 'Skip adding president account'.");
        }
    });
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

        <script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
<style>
    .step-indicator .circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #ccc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        transition: background-color 0.3s ease;
        margin-bottom: 5px;
    }

    .step-indicator.active .circle {
        background-color: #198754;
    }

    .form-step {
        animation: fadeIn 0.3s ease-in-out;
        transition: opacity 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

</html>