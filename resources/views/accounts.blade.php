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
                <h2 class="fw-bold" style="color: #232946;">Accounts</h2>
                <small style="color: #989797;">Manage /</small>
                <small style="color: #444444;">Accounts</small>
            </div>

            <!-- Main Card -->
            <div class="card shadow-sm p-4">

                <!-- Filters and Search -->
              <div class="row mb-4">
    <div class="col-12 d-flex justify-content-end align-items-end gap-2">
        <div style="position: relative;">
            <!-- Filter Icon Button -->
            <button class="btn btn-outline-secondary" onclick="toggleFilterCloud()" style="border-radius: 6px;">
                <i class="bi bi-funnel-fill"></i>
            </button>

            <!-- Cloud Popup -->
            <div id="filterCloud" class="shadow p-3 rounded" style="position: absolute; top: 120%; right: 0; background-color: white; border: 1px solid #ddd; border-radius: 12px; display: none; min-width: 250px; z-index: 10;">
                <div class="mb-2">
                    <label for="roleFilter" class="form-label">Filter by Role</label>
                    <select id="roleFilter" class="form-select form-select-sm" onchange="applyFilters()">
                        <option value="">All Roles</option>
                        <option value="Member">Member</option>
                        <option value="Officer">Officer</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="deptFilter" class="form-label">Filter by Organization</label>
                    <select id="deptFilter" class="form-select form-select-sm" onchange="applyFilters()">
                        <option value="">All Organization</option>
                        <option value="ITS">ITS</option>
                        <option value="PRAXIS">PRAXIS</option>
                        <option value="CCMS-SG">CCMS-SG</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Search Input -->
        <div style="min-width: 250px;">
            <label class="form-label">Search</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Enter search..." oninput="applyFilters()">
        </div>
    </div>
</div>

                <!-- Student List -->
                <h5 class="mb-3 fw-bold" style="color: #232946;">User List</h5>

                @if($users->isEmpty())
                    <p>No users found.</p>
                @else
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-borderless align-middle text-center shadow-sm rounded" style="background-color: #f9f9f9;">
                            <thead class="table-dark text-light">
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
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td>{{ $user->user_ID }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>{{ $user->org }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning me-1 border-0 shadow-none"
                                                onclick="openEditModal('{{ $user->user_ID }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->org }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger border-0 shadow-none">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Pagination -->
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




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>
    
    <script>
    function toggleFilterCloud() {
        const cloud = document.getElementById("filterCloud");
        cloud.style.display = (cloud.style.display === "none" || cloud.style.display === "") ? "block" : "none";
    }

    // Optional: Close the filter popup if clicked outside
    document.addEventListener("click", function(event) {
        const cloud = document.getElementById("filterCloud");
        const button = event.target.closest("button");
        const insideCloud = event.target.closest("#filterCloud");
        if (!insideCloud && (!button || button.innerHTML.indexOf("bi-funnel-fill") === -1)) {
            cloud.style.display = "none";
        }
    });

    function applyFilters() {
        // Your filtering logic here
        console.log("Filters applied");
    }
</script>


</body>


<style>

    #filterCloud {
    box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
    border-radius: 16px;
    background: #fff;
}
</style>
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



</html>
