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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    

    <title>CCMS Attendance System</title>
</head>

<body>

    @include('layout.navbar')
    @include('layout.sidebar')
    <main>
<div class="container outer-box mt-5 pt-5 pb-4 shadow">
    <div class="container-fluid" id="mainContainer">
        <!-- Heading -->
        <div class="mb-3">
            <h2 class="fw-bold" style="color: #232946;">Officers</h2>
            <small style="color: #989797;">Manage /</small>
            <small style="color: #444444;">Officers</small>
        </div>

        <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficerModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add Officer
    </button>
</div>

        <!-- Main Card -->
        <div class="card shadow-sm p-4">

            <!-- Filters and Search -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="roleFilter" class="form-label">Filter by Role</label>
                    <select id="roleFilter" class="form-select" onchange="applyFilters()">
                        <option value="">All Roles</option>
                        <option value="Member">Member</option>
                        <option value="Officer">Officer</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="deptFilter" class="form-label">Filter by Organization</label>
                    <select id="deptFilter" class="form-select" onchange="applyFilters()">
                        <option value="">All Organizations</option>
                        <option value="ITS">ITS</option>
                        <option value="PRAXIS">PRAXIS</option>
                        <option value="CCMS-SG">CCMS-SG</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Enter search..." oninput="applyFilters()">
                </div>
            </div>

            <!-- Student List -->
                <h5 class="mb-3 fw-bold" style="color: #232946;">User List</h5>

                @if($users->isEmpty())
                    <p>No users found.</p>
                @else
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Org</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->user_ID }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>{{ $user->org }}</td>
                                        
                                        <td>
                                        <button type="button" class="btn btn-sm btn-warning me-1" 
                        onclick="openEditModal('{{ $user->user_ID }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->org }}')">
                        Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" >
                        Delete
                    </button>
                </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Pagination (optional, no change here) -->
                <div class="d-flex justify-content-end mt-3">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

        </div>

    
    </div>
</div>
</main>

<div class="modal fade" id="addOfficerModal" tabindex="-1" aria-labelledby="addOfficerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addOfficerForm" method="POST" action="{{ route('officers.add') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addOfficerModalLabel">Add Officer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Full Name Search Field -->
          <div class="mb-3 position-relative">
    <label for="officerName" class="form-label">Full Name</label>
    <input type="text" class="form-control" id="officerInput" name="officerInput" autocomplete="off" placeholder="Type @ to search members...">
    <div id="mentionDropdown" class="list-group position-absolute w-100" style="z-index: 999; display: none;"></div>
    <input type="hidden" name="user_id" id="selectedUserId">
</div>

          <!-- Officer Position Dropdown -->
          <div class="mb-3">
            <label for="officerPosition" class="form-label">Officer Position</label>
            <select class="form-select" id="officerPosition" name="officerPosition" required>
              <option value="">Select Position</option>
              <option value="President">President</option>
              <option value="Vice President">Vice President</option>
              <option value="Secretary">Secretary</option>
              <option value="Treasurer">Treasurer</option>
              <!-- Add more as needed -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Officer</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editUserForm" method="POST" action="{{ route('users.update') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="editUserId" class="form-label">User ID</label>
            <input type="text" class="form-control" id="editUserId" name="user_id" readonly>
          </div>

          <div class="mb-3">
            <label for="editUserName" class="form-label">Name</label>
            <input type="text" class="form-control" id="editUserName" name="name">
          </div>

          <div class="mb-3">
            <label for="editUserEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editUserEmail" name="email">
          </div>

          <div class="mb-3">
            <label for="editUserRole" class="form-label">Role</label>
            <select class="form-select" id="editUserRole" name="role">
              <option value="member">Member</option>
              <option value="officer">Officer</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="editUserOrg" class="form-label">Organization</label>
            <select class="form-select" id="editUserOrg" name="org">
              <option value="ITS">ITS</option>
              <option value="PRAXIS">PRAXIS</option>
              <option value="CCMS-SG">CCMS-SG</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
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




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>
    
    
</body>

<script>
let orgMembers = [];

document.addEventListener("DOMContentLoaded", () => {
    const officerInput = document.getElementById("officerInput");
    const mentionDropdown = document.getElementById("mentionDropdown");

    // Load members of current user's org
    fetch('/org-members') // Laravel route must return only members of the user's org
        .then(res => res.json())
        .then(data => orgMembers = data);

    officerInput.addEventListener("input", (e) => {
        const value = e.target.value;
        const atIndex = value.lastIndexOf("@");

        if (atIndex !== -1) {
            const query = value.substring(atIndex + 1).toLowerCase();
            const matches = orgMembers.filter(user =>
                user.name.toLowerCase().includes(query) || user.email.toLowerCase().includes(query)
            );

            if (matches.length) {
                mentionDropdown.innerHTML = matches.map(user => `
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" data-id="${user.id}" data-name="${user.name}">
                        <img src="${user.picture || '/default-avatar.png'}" class="rounded-circle me-2" width="30" height="30">
                        <div>
                            <strong>${user.name}</strong><br>
                            <small>${user.email}</small>
                        </div>
                    </a>
                `).join("");
                mentionDropdown.style.display = "block";
            } else {
                mentionDropdown.style.display = "none";
            }
        } else {
            mentionDropdown.style.display = "none";
        }
    });

    mentionDropdown.addEventListener("click", function (e) {
        e.preventDefault();
        const item = e.target.closest("a");
        if (item) {
            const name = item.dataset.name;
            const id = item.dataset.id;
            const value = officerInput.value;
            const atIndex = value.lastIndexOf("@");
            officerInput.value = value.substring(0, atIndex) + name;
            document.getElementById("selectedUserId").value = id;
           
            mentionDropdown.style.display = "none";
        }
    });

    document.addEventListener("click", function (e) {
        if (!mentionDropdown.contains(e.target) && e.target !== officerInput) {
            mentionDropdown.style.display = "none";
        }
    });
});
</script>



<script>
function fillModalData(button) {
    // Find the row (`<tr>`) that contains the clicked button
    var row = button.closest('tr');

    // Get all cells (`<td>`) in that row
    var cells = row.getElementsByTagName('td');

    // Now fill the modal inputs
    document.getElementById('modalStudentId').value = cells[1].innerText.trim();
    document.getElementById('modalStudentName').value = cells[2].innerText.trim();
    document.getElementById('modalGender').value = cells[3].innerText.trim();
    document.getElementById('modalCourse').value = cells[4].innerText.trim();
    document.getElementById('modalYear').value = cells[5].innerText.trim();
    document.getElementById('modalSection').value = cells[6].innerText.trim();
    document.getElementById('modalContactNumber').value = cells[7].innerText.trim();
    document.getElementById('modalBirthDate').value = cells[8].innerText.trim();
    document.getElementById('modalAddress').value = cells[9].innerText.trim();
}
</script>



<script>
function applyFilters() {
    var searchInput = document.getElementById("searchInput").value.toLowerCase();
    var roleFilter = document.getElementById("roleFilter").value;
    var deptFilter = document.getElementById("deptFilter").value;
    var table = document.getElementById("studentsTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var tds = tr[i].getElementsByTagName("td");
        var name = tds[2]?.textContent.toLowerCase() || "";
        var role = tds[10]?.textContent || "";
        var dept = tds[4]?.textContent || "";

        if ((name.includes(searchInput) || searchInput == "") &&
            (roleFilter == "" || role == roleFilter) &&
            (deptFilter == "" || dept == deptFilter)) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

<script>
function openEditModal(userID, name, email, role, org) {
    document.getElementById('editUserId').value = userID;
    document.getElementById('editUserName').value = name;
    document.getElementById('editUserEmail').value = email;
    document.getElementById('editUserRole').value = role;
    document.getElementById('editUserOrg').value = org;

    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}

function deleteUser(userID) {
    if (confirm("Are you sure you want to delete this user?")) {
        console.log("Deleting user ID:", userID);
        // Your delete logic here
    }
}
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif



</html>
