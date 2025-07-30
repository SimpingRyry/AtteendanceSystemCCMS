<div class="modal fade" id="excuseModal" tabindex="-1" aria-labelledby="excuseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
<form method="POST" action="{{ route('excuse.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="excuseModalLabel">Submit Excuse</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <input type="hidden" name="attendance_id" id="modalAttendanceId">
                <div class="mb-2">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" id="reason" required></textarea>
                </div>
                <div class="mb-2">
                    <label for="excuse_letter" class="form-label">Upload Excuse Letter</label>
                    <input type="file" class="form-control" name="excuse_letter" id="excuse_letter" accept=".pdf,.jpg,.png" required>
                </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit Excuse</button>
          </div>
        </div>
    </form>
  </div>
</div>
<div class="modal fade" id="viewExcuseModal" tabindex="-1" aria-labelledby="viewExcuseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header bg-info text-white rounded-top-4">
        <h5 class="modal-title fw-bold" id="viewExcuseModalLabel">
          <i class="bi bi-file-earmark-text me-2"></i> Excuse Letter & Reason
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body px-4 py-3">
        <div class="mb-2"><strong>Student ID:</strong> <span id="modalStudentId"></span></div>
        <div class="mb-2"><strong>Name:</strong> <span id="modalStudentName"></span></div>
        <div class="mb-2"><strong>Event:</strong> <span id="modalEventName"></span></div>
        <div class="mb-2"><strong>Date:</strong> <span id="modalDate"></span></div>
        <div class="mb-3"><strong>Reason:</strong> <span id="modalReason"></span></div>

        <div class="text-center mb-3">
          <strong>Excuse Letter:</strong><br>
          <img id="modalLetterImage" src="" alt="Excuse Letter" class="img-thumbnail shadow-sm mt-2" style="max-height: 300px;">
        </div>

        <div class="text-center">
          <a id="downloadExcuseBtn" href="#" class="btn btn-outline-primary" download target="_blank">
            <i class="bi bi-download me-1"></i>Download Excuse Letter
          </a>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewModal = document.getElementById('viewExcuseModal');

        viewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            viewModal.querySelector('#modalStudentId').textContent = button.getAttribute('data-student');
            viewModal.querySelector('#modalStudentName').textContent = button.getAttribute('data-name');
            viewModal.querySelector('#modalEventName').textContent = button.getAttribute('data-event');
            viewModal.querySelector('#modalDate').textContent = button.getAttribute('data-date');
            viewModal.querySelector('#modalReason').textContent = button.getAttribute('data-reason');
            viewModal.querySelector('#modalLetterImage').src = button.getAttribute('data-letter');
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('excuseModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const attendanceId = button.getAttribute('data-attendanceid');
            modal.querySelector('#modalAttendanceId').value = attendanceId;
        });
    });
</script>
