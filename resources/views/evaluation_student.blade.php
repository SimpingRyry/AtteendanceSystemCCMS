{{--  resources/views/evaluation_student.blade.php  --}}
@php( $evaluations = $evaluations ?? collect() )   {{-- ✅ makes sure the var exists --}}

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

        {{-- =====  Cards grid  ===== --}}
        <div class="row g-4 mt-4">
          @forelse ($evaluations as $eval)
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card h-100 shadow-sm evaluation-card"
                   data-id="{{ $eval->id }}" style="cursor:pointer">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title">{{ $eval->title }}</h5>
                  <p class="card-text text-muted flex-grow-1">
                    {{ Str::limit($eval->description, 90) }}
                  </p>
                  <button class="btn btn-primary mt-auto w-100">Answer</button>
                </div>
              </div>
            </div>
          @empty
            <p class="mt-3">No evaluations are currently available.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</main>

{{-- =====  Answer-Modal  ===== --}}
<div class="modal fade" id="evalModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="evalForm" class="modal-content">
      @csrf
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
        <button class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<!-- Page-script -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* -------------------------------------------------
       Handles
    --------------------------------------------------*/
    const cards      = document.querySelectorAll('.evaluation-card');
    const modalEl    = document.getElementById('evalModal');
    const bsModal    = new bootstrap.Modal(modalEl);

    const form       = document.getElementById('evalForm');
    const titleEl    = document.getElementById('modalTitle');
    const descEl     = document.getElementById('modalDesc');
    const qContainer = document.getElementById('modalQuestions');

    let currentEvalId = null;

    /* -------------------------------------------------
       Card click → fetch & open modal
    --------------------------------------------------*/
    cards.forEach(card => {
        card.addEventListener('click', async () => {
            currentEvalId = card.dataset.id;

            const res  = await fetch(`/student/evaluation/${currentEvalId}/json`);
            if(!res.ok){ alert('Failed to load evaluation'); return; }
            const data = await res.json();

            // header
            titleEl.textContent = data.title;
            descEl.textContent  = data.description ?? '—';

            // questions
            qContainer.innerHTML = '';
            data.questions
                .sort((a,b)=>a.order-b.order)
                .forEach((q,idx)=>qContainer.insertAdjacentHTML('beforeend', renderQuestion(q, idx)));

            bsModal.show();
        });
    });

    /* helper returns HTML block */
    const renderQuestion = (q, idx) => {
        const base = `responses[${idx}]`;
        let html = `<div class="mb-4">
              <input type="hidden" name="${base}[question_id]" value="${q.id}">
              <label class="form-label fw-semibold">
                   ${q.order}. ${q.question}${q.is_required?' <span class="text-danger">*</span>':''}
              </label>`;

        switch(q.type){
            case 'short':
            case 'text':
                html += `<input type="text" class="form-control"
                         name="${base}[answer]" ${q.is_required?'required':''}>`;
                break;

            case 'paragraph':
            case 'textarea':
                html += `<textarea rows="3" class="form-control"
                         name="${base}[answer]" ${q.is_required?'required':''}></textarea>`;
                break;

            case 'mcq':
            case 'radio':
                (q.options ?? []).forEach(opt=>{
                    html += `
                      <div class="form-check">
                          <input class="form-check-input" type="radio"
                                 name="${base}[answer]" value="${opt}"
                                 ${q.is_required?'required':''}>
                          <label class="form-check-label">${opt}</label>
                      </div>`;
                });
                break;

            case 'checkbox':
                (q.options ?? []).forEach(opt=>{
                    html += `
                      <div class="form-check">
                          <input class="form-check-input" type="checkbox"
                                 name="${base}[answer][]" value="${opt}">
                          <label class="form-check-label">${opt}</label>
                      </div>`;
                });
                break;
        }
        return html + '</div>';
    };

    /* -------------------------------------------------
       Submit answers
    --------------------------------------------------*/
    form.addEventListener('submit', async e => {
        e.preventDefault();

        const url  = `/student/evaluation/${currentEvalId}/answer`;
        const data = new FormData(form);

        try{
            const res = await fetch(url, {
                method : 'POST',
                headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                body   : data
            });

            if(res.ok){
                bsModal.hide();
                location.reload();
            }else{
                const err = await res.json();
                alert(err.message || 'Submission failed');
            }
        }catch(err){
            alert('Network error');
            console.error(err);
        }
    });

});
</script>
</body>
</html>
