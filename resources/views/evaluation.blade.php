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
    <link rel="stylesheet" href="{{ asset('css/evaluation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">

    <title>CCMS Attendance System</title>
</head>

<body style="background-color: #fffffe;">

    {{-- Navigation --}}
    @include('layout.navbar')

    {{-- Sidebar --}}
    @include('layout.sidebar')

    <main>
        <div class="container outer-box mt-5 pt-5 pb-4">
            <div class="container inner-glass shadow p-4" id="main_box">
                <div class="mb-4">
                    <h2 class="fw-bold" style="color: #232946;">Evaluation</h2>
                    <small style="color: #989797;">Manage /</small>
                    <small style="color: #444444;">Evaluation</small>
                </div>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Evaluation Creator hehe -->
                {{-- ========================================= --}}
                {{-- Trigger button (inside the main container)--}}
                {{-- ========================================= --}}
                <button class="btn btn-success mb-3" type="button"
                    data-bs-toggle="collapse" data-bs-target="#evaluationCreator"
                    aria-expanded="false" aria-controls="evaluationCreator">
                    <i class="fa fa-plus me-1"></i> Create Evaluation
                </button>

                {{-- ========================================= --}}
                {{-- Evaluation Creator (collapsed by default) --}}
                {{-- ========================================= --}}
                <div class="collapse" id="evaluationCreator">
                    <form action="{{ route('evaluation.store') }}" method="POST" id="evaluationForm" class="border rounded p-4 shadow-sm">
                        @csrf

                        {{-- === Builder Mode === --}}
                        <div id="builder_mode">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Filter by Event</label>
                                <select class="form-select" name="event_id" id="event_id" required>
                                    <option value="">Select event</option>
                                    @foreach($eventNames as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-3">
                                <label class="form-label fw-semibold">Evaluation Title</label>
                                <input type="text" name="title" class="form-control" id="eval_title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2" id="eval_desc"></textarea>
                            </div>

                            <div id="questions_container"></div>

                            <button type="button" class="btn btn-secondary my-2" onclick="addQuestion()">Add Question</button>
                            <button type="button" class="btn btn-info my-2 ms-2" onclick="previewForm()">Preview</button>
                        </div>

                        {{-- === Preview Mode === --}}
                        <div id="preview_mode" class="d-none">
                            <h4 id="preview_title" class="fw-bold"></h4>
                            <p id="preview_desc" class="text-muted"></p>
                            <div id="preview_questions" class="mb-4"></div>

                            <button type="button" class="btn btn-warning" onclick="backToEdit()">Back to Edit</button>
                            <input type="hidden" name="questions_json" id="questions_json">
                            <button type="submit" class="btn btn-success">Save Evaluation</button>
                        </div>
                    </form>
                </div>
                <h2 class="fw-bold text-center" style="color:#232946;">Evaluation Table</h2>


                {{-- 1️⃣  TABLE LIST ----------------------------------------------------- --}}
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th style="width:200px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                        <tr>
                            <td>{{ $evaluation->title }}</td>
                            <td>{{ Str::limit($evaluation->description, 80) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    data-id="{{ $evaluation->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewModal">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-warning edit-btn"
                                    data-id="{{ $evaluation->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                    <i class="fa fa-pen"></i>
                                </button>

                                <button class="btn btn-sm btn-success send-eval-btn"
                                    data-id="{{ $evaluation->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#sendEvaluationModal">
                                    <i class="fa fa-paper-plane"></i>
                                </button>

                                <form action="{{ route('evaluation.destroy',$evaluation) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this evaluation?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- keep your “Create Evaluation” collapsible form here – unchanged --}}


            </div>
        </div>
    </main>

    <div class="modal fade" id="sendEvaluationModal" tabindex="-1" aria-labelledby="sendEvaluationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('evaluation.send') }}" method="POST">
        @csrf
        <input type="hidden" name="evaluation_id" id="send_eval_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Evaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Event</label>
                    <select class="form-select" name="event_id" required>
                        <option value="">Choose event</option>
                        @foreach($eventNames as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Send</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>
    {{-- Success Modal (keep this where it already is) --}}
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    {{ session('success') ?? 'Evaluation saved successfully!' }}
                </div>
            </div>
        </div>
    </div>
    <!-- View modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="modalDescription" class="mb-4"></p>
                    <div id="modalQuestions"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Evaluation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="edit_title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="edit_desc" rows="3"></textarea>
                    </div>

                    <!-- NEW: Questions section -->
                    <div class="mb-3">
                        <label class="form-label">Questions</label>
                        <div id="editQuestionsContainer"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addQuestionBtn">Add Question</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>



    {{-- Auto-show if flash exists --}}
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
    @endif

    <script>
    document.querySelectorAll('.send-eval-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const evalId = this.getAttribute('data-id');
            document.getElementById('send_eval_id').value = evalId;
           
        });
    });
</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* -------------------------------------------------
               DOM handles
            --------------------------------------------------*/
            const editModal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const questionsContainer = document.getElementById('editQuestionsContainer');
            const addQuestionBtn = document.getElementById('addQuestionBtn');

            /* -------------------------------------------------
               Helper → build one question block
            --------------------------------------------------*/
            function createQuestionInput(idx, q = {}) {
                const div = document.createElement('div');
                div.classList.add('border', 'p-2', 'rounded', 'mb-2', 'question-block');
                div.innerHTML = `
            <input type="hidden" name="questions[${idx}][id]" value="${q.id ?? ''}">

            <label class="form-label mb-0">Question</label>
            <input type="text"
                   name="questions[${idx}][question]"
                   class="form-control mb-1"
                   value="${q.question ?? ''}"
                   required>

            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="questions[${idx}][type]" class="form-select q-type-select">
                        <option value="short"     ${q.type==='short'?'selected':''}>Short answer</option>
                        <option value="paragraph" ${q.type==='paragraph'?'selected':''}>Paragraph</option>
                        <option value="mcq"       ${q.type==='mcq'?'selected':''}>Multiple choice</option>
                        <option value="checkbox"  ${q.type==='checkbox'?'selected':''}>Checkboxes</option>
                    </select>
                </div>

                <div class="col-auto">
                    <div class="form-check">
                        <input  class="form-check-input" type="checkbox"
                                name="questions[${idx}][is_required]"
                                ${q.is_required ? 'checked' : ''}>
                        <label class="form-check-label">Required</label>
                    </div>
                </div>

                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-sm btn-danger remove-question-btn">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <textarea class="form-control mt-2 options-box"
                      placeholder="Comma-separated options (e.g. Yes,No,Maybe)"
                      ${['mcq','checkbox'].includes(q.type ?? '') ? '' : 'hidden'}>${(q.options ?? []).join(', ')}</textarea>
        `;
                return div;
            }

            /* -------------------------------------------------
               Re-index helper (after delete)
            --------------------------------------------------*/
            function reindex() {
                [...questionsContainer.children].forEach((div, i) => {
                    div.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/questions\[\d+]/, `questions[${i}]`);
                    });
                });
            }

            /* -------------------------------------------------
               1️⃣  Open modal → fetch & render
            --------------------------------------------------*/
            editModal.addEventListener('show.bs.modal', async e => {
                const id = e.relatedTarget.dataset.id;

                const res = await fetch(`{{ url('evaluation') }}/${id}/questions`);
                const data = await res.json();

                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_title').value = data.title;
                document.getElementById('edit_desc').value = data.description ?? '';

                questionsContainer.innerHTML = '';
                (data.questions ?? []).forEach((q, idx) => {
                    questionsContainer.appendChild(createQuestionInput(idx, q));
                });
            });

            /* -------------------------------------------------
               2️⃣  Add blank question
            --------------------------------------------------*/
            addQuestionBtn.addEventListener('click', () => {
                const idx = questionsContainer.children.length;
                questionsContainer.appendChild(createQuestionInput(idx));
            });

            /* -------------------------------------------------
               3️⃣  Remove question (delegated)
            --------------------------------------------------*/
            questionsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-question-btn') || e.target.closest('.remove-question-btn')) {
                    e.target.closest('.question-block').remove();
                    reindex();
                }
            });

            /* -------------------------------------------------
               4️⃣  Toggle options textarea when type changes
            --------------------------------------------------*/
            questionsContainer.addEventListener('change', e => {
                if (e.target.classList.contains('q-type-select')) {
                    const block = e.target.closest('.question-block');
                    const box = block.querySelector('.options-box');
                    box.hidden = !['mcq', 'checkbox'].includes(e.target.value);
                }
            });

            /* -------------------------------------------------
               5️⃣  Submit → collect payload & PUT
            --------------------------------------------------*/
            form.addEventListener('submit', async ev => {
                ev.preventDefault();
                const evalId = document.getElementById('edit_id').value;

                const questions = [];
                [...questionsContainer.children].forEach(block => {
                    const idField = block.querySelector('input[type="hidden"]').value;
                    const textField = block.querySelector('input[name$="[question]"]').value.trim();
                    const typeField = block.querySelector('.q-type-select').value;
                    const reqFlag = block.querySelector('input[type="checkbox"]').checked;
                    const optsBox = block.querySelector('.options-box');
                    const options = optsBox && !optsBox.hidden
    ? optsBox.value.split(',').map(o => o.trim()).filter(Boolean)
    : [];

                    questions.push({
                        id: idField || null,
                        question: textField,
                        type: typeField,
                        options: options,
                        is_required: reqFlag,
                    });
                });

                const payload = {
                    _token: '{{ csrf_token() }}',
                    title: document.getElementById('edit_title').value.trim(),
                    description: document.getElementById('edit_desc').value.trim(),
                    questions_json: JSON.stringify(questions),
                };

                try {
                    const res = await fetch(`{{ url('evaluation') }}/${evalId}`, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (res.ok) {
                        // reload page or you can also close modal & update UI dynamically
                        location.reload();
                    } else {
                        const err = await res.json();
                        alert(`Save failed: ${err.message || 'Unknown error'}`);
                        console.error(err);
                    }
                } catch (error) {
                    alert('Save failed: Network error');
                    console.error(error);
                }
            });

        });
    </script>





    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const viewModal = document.getElementById('viewModal');
            const titleEl = document.getElementById('modalTitle');
            const descEl = document.getElementById('modalDescription');
            const questionsEl = document.getElementById('modalQuestions');

            viewModal.addEventListener('show.bs.modal', async (e) => {
                const btn = e.relatedTarget; // button that triggered the modal
                const id = btn.getAttribute('data-id');

                // fetch evaluation + questions
                const res = await fetch(`{{ url('evaluation') }}/${id}/questions`);
                const data = await res.json();

                // fill modal
                titleEl.textContent = data.title;
                descEl.textContent = data.description ?? '—';

                questionsEl.innerHTML = '';
                data.questions.forEach((q, i) => {
                    questionsEl.insertAdjacentHTML('beforeend', `
              <div class="mb-3">
                  <strong>${i+1}. ${q.question} ${q.is_required ? '<span class="text-danger">*</span>':''}</strong>
                  <div><em>Type:</em> ${q.type}</div>
              </div>
          `);
                });
            });
        });
    </script>



    <script>
        /* -------------------------------------------------
   SIMPLE FORMS  –  BUILDER  +  PREVIEW
--------------------------------------------------*/
        let questions = [];

        function addQuestion() {
            const i = questions.length;
            const box = document.getElementById('questions_container');

            box.insertAdjacentHTML('beforeend', `
        <div class="card mb-3 p-3 shadow-sm">
            <div class="mb-2">
                <label class="fw-semibold">Question</label>
                <input type="text" name="questions[${i}][question]" class="form-control" required>
            </div>

            <div class="mb-2">
                <label class="fw-semibold">Type</label>
                <select name="questions[${i}][type]" class="form-select"
                        onchange="toggleOptions(this, ${i})">
                    <option value="text">Short answer</option>
                    <option value="textarea">Paragraph</option>
                    <option value="radio">Multiple choice</option>
                    <option value="checkbox">Checkboxes</option>
                </select>
            </div>

            <div id="opts_${i}" class="mb-2 d-none">
                <label class="fw-semibold">Options (one per line)</label>
                <textarea name="questions[${i}][options]" class="form-control" rows="3"
                          placeholder="e.g. Yes&#10;No&#10;Maybe"></textarea>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="questions[${i}][is_required]" class="form-check-input" value="1">
                <label class="form-check-label">Required</label>
            </div>

            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.card').remove()">Remove</button>
        </div>
    `);

            questions.push(i);
        }

        function toggleOptions(select, idx) {
            document.getElementById(`opts_${idx}`).classList
                .toggle('d-none', !(select.value === 'radio' || select.value === 'checkbox'));
        }

        function previewForm() {
            /* -------- collect data -------- */
            const fd = new FormData(document.getElementById('evaluationForm'));
            const list = [];

            fd.forEach((val, key) => {
                const m = key.match(/^questions\[(\d+)]\[(.+)]$/);
                if (!m) return;

                const [, idx, field] = m;
                list[idx] ??= {
                    options: []
                };

                switch (field) {
                    case 'is_required':
                        list[idx][field] = true;
                        break;
                    case 'options':
                        /* textarea → array (trim empty) */
                        list[idx][field] = val.split('\n').map(v => v.trim()).filter(Boolean);
                        break;
                    default:
                        list[idx][field] = val;
                }
            });

            /* -------- render preview -------- */
            document.getElementById('preview_title').textContent =
                document.getElementById('eval_title').value;
            document.getElementById('preview_desc').textContent =
                document.getElementById('eval_desc').value;

            const tgt = document.getElementById('preview_questions');
            tgt.innerHTML = '';

            list.forEach((q, i) => {
                const wrap = document.createElement('div');
                wrap.className = 'mb-3';

                wrap.insertAdjacentHTML('beforeend',
                    `<label class="form-label fw-semibold">${i + 1}. ${q.question}${q.is_required ? ' <span class="text-danger">*</span>' : ''}</label>`
                );

                let field;
                switch (q.type) {
                    case 'text':
                        field = `<input type="text" class="form-control" disabled>`;
                        break;
                    case 'textarea':
                        field = `<textarea class="form-control" disabled></textarea>`;
                        break;
                    case 'radio':
                    case 'checkbox':
                        field = q.options.length ? q.options : ['Option 1'];
                        field = field.map(opt => `
                      <div class="form-check">
                          <input type="${q.type}" class="form-check-input" disabled>
                          <label class="form-check-label">${opt}</label>
                      </div>`).join('');
                        break;
                }
                wrap.insertAdjacentHTML('beforeend', field);
                tgt.appendChild(wrap);
            });

            /* send JSON ready for controller */
            document.getElementById('questions_json').value = JSON.stringify(list);

            /* toggle screens */
            document.getElementById('builder_mode').classList.add('d-none');
            document.getElementById('preview_mode').classList.remove('d-none');
        }

        function backToEdit() {
            document.getElementById('builder_mode').classList.remove('d-none');
            document.getElementById('preview_mode').classList.add('d-none');
        }
    </script>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>