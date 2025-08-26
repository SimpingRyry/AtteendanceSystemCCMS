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
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->org }}</td>
            <td>
                <!-- Edit button -->
                <button type="button" class="btn btn-sm btn-warning me-1 border-0 shadow-none"
    onclick="openPasswordModal('{{ $user->id }}')">
    <i class="bi bi-pencil-square"></i>
</button>

                <!-- Delete button (hidden if it's the logged-in user) -->
                @if(Auth::id() !== $user->id)
                    <form action="" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger border-0 shadow-none"
                            onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
</tbody>
                        </table>
                    </div>
                @endif

                <!-- Pagination -->
               <div class="d-flex justify-content-end mt-3">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-info" id="editPasswordLabel">Edit User Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPasswordForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="password" required>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Password</button>
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
    @if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif
<script>
function openPasswordModal(userId) {
    // Set the form action dynamically for the selected user
    let form = document.getElementById('editPasswordForm');
    form.action = "/users/" + userId + "/update-password";  // ✅ use your existing route

    // Reset fields
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('editPasswordModal'));
    modal.show();
}
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
