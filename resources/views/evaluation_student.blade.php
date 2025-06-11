@php( $evaluations = $evaluations ?? collect() )

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/logs.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

  <title>CCMS Attendance System</title>
</head>
<body style="background-color:#fffffe;">

{{-- Navigation --}}
@include('layout.navbar')

{{-- Sidebar --}}
@include('layout.sidebar')

<main>
  <div class="container outer-box mt-5 pt-5 pb-4">
    <div class="container inner-glass shadow p-4" id="main_box">
      <div class="mb-4">
        <h2 class="fw-bold" style="color:#232946;">Evaluation</h2>
        <small style="color:#989797;">Evaluation lists</small>
      </div>

      <div class="container py-4">
        @if(session('success'))
          <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered mt-4">
  <thead class="table-dark">
    <tr>
      <th>Title</th>
      <th>Description</th>
      <th>Event Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  @forelse ($assignments as $assign)
<tr>
  <td>{{ $assign->event->name ?? '—' }}</td>
  <td>{{ $assign->evaluation->title }}</td>
  <td>{{ Str::limit($assign->evaluation->description, 80) }}</td>
  <td>
    <button class="btn btn-sm btn-primary evaluation-card" data-id="{{ $assign->evaluation->id }}">
      Answer
    </button>
  </td>
</tr>
@empty
<tr>
  <td colspan="4" class="text-center text-muted">No evaluations available.</td>
</tr>
@endforelse
  </tbody>
</table>
      </div>
    </div>
  </div>
</main>

{{-- Answer Modal --}}
<div class="modal fade" id="evalModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="evalForm" class="modal-content">
      @csrf
      <input type="hidden" name="evaluation_id" id="evaluation_id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p id="modalDesc" class="text-muted"></p>
        <div id="modalQuestions"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const cards = document.querySelectorAll('.evaluation-card');
  const modal = new bootstrap.Modal(document.getElementById('evalModal'));

  cards.forEach(card => {
    card.addEventListener('click', async () => {
      const id = card.dataset.id;
      const res = await fetch(`/student/evaluation/${id}/json`);
      if (!res.ok) return alert('Failed to load evaluation.');

      const data = await res.json();
      document.getElementById('modalTitle').textContent = data.title;
      document.getElementById('modalDesc').textContent = data.description || '—';
      document.getElementById('evaluation_id').value = data.id;

      const container = document.getElementById('modalQuestions');
      container.innerHTML = '';

      data.questions.forEach((q, i) => {
        const base = `responses[${i}]`;
        let html = `
          <div class="mb-4">
            <input type="hidden" name="${base}[question_id]" value="${q.id}">
            <label class="form-label fw-semibold">${q.order}. ${q.question}</label>
        `;

        switch (q.type) {
          case 'short':
            html += `<input type="text" class="form-control" name="${base}[answer]" required>`;
            break;
          case 'textarea':
            html += `<textarea class="form-control" name="${base}[answer]" rows="3" required></textarea>`;
            break;
          case 'radio':
            q.options.forEach(opt => {
              html += `
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="${base}[answer]" value="${opt}" required>
                  <label class="form-check-label">${opt}</label>
                </div>`;
            });
            break;
          case 'checkbox':
            q.options.forEach(opt => {
              html += `
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="${base}[answer][]" value="${opt}">
                  <label class="form-check-label">${opt}</label>
                </div>`;
            });
            break;
        }

        container.insertAdjacentHTML('beforeend', html + '</div>');
      });

      modal.show();
    });
  });

  // Form submission via AJAX
  document.getElementById('evalForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  try {
    const res = await fetch('/student/evaluation/submit', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
      },
      body: formData
    });

    if (res.ok) {
      alert('Your responses were submitted!');
      bootstrap.Modal.getInstance(document.getElementById('evalModal')).hide();
      form.reset();
    } else {
      const errorText = await res.text();
      alert('Error submitting answers: ' + errorText);
      console.error('Submit error:', errorText);
    }
  } catch (error) {
    alert('Network or server error: ' + error.message);
    console.error(error);
  }
});
});
</script>

</body>
</html>
