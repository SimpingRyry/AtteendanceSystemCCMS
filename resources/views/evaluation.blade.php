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

            <form action="{{ route('evaluation.store') }}" method="POST" id="evaluationForm">
                @csrf

                {{-- === Builder Mode === --}}
                <div id="builder_mode">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Evaluation Title</label>
                        <input type="text" name="title" class="form-control" id="eval_title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" id="eval_desc"></textarea>
                    </div>

                    <div id="questions_container"></div>

                    <button type="button" class="btn btn-secondary my-3" onclick="addQuestion()">Add Question</button>
                    <button type="button" class="btn btn-info my-3 ms-2" onclick="previewForm()">Preview</button>
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
    </div>
</main>
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
let questions = [];

function addQuestion() {
    const index = questions.length;
    const container = document.getElementById('questions_container');

    const questionHTML = `
        <div class="card mb-3 p-3 shadow-sm">
            <div class="mb-2">
                <label>Question</label>
                <input type="text" name="questions[${index}][question]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Type</label>
                <select name="questions[${index}][type]" class="form-select" onchange="handleTypeChange(this, ${index})">
                    <option value="text">Short Answer</option>
                    <option value="textarea">Paragraph</option>
                    <option value="radio">Multiple Choice</option>
                    <option value="checkbox">Checkboxes</option>
                </select>
            </div>
            <div class="mb-2 d-none" id="options_container_${index}">
                <label>Options (one per line)</label>
                <textarea name="questions[${index}][options]" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" name="questions[${index}][is_required]" class="form-check-input" value="1">
                <label class="form-check-label">Required</label>
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">Remove</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', questionHTML);
    questions.push(index);
}

function handleTypeChange(select, index) {
    const container = document.getElementById(`options_container_${index}`);
    if (select.value === 'radio' || select.value === 'checkbox') {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

function removeQuestion(btn) {
    const card = btn.closest('.card');
    card.remove();
}

function previewForm() {
    const formData = new FormData(document.getElementById('evaluationForm'));
    const questionList = [];
    const previewContainer = document.getElementById('preview_questions');
    previewContainer.innerHTML = '';

    for (let [key, value] of formData.entries()) {
        const match = key.match(/questions\[(\d+)]\[(.+)]/);
        if (match) {
            const [_, index, field] = match;
            if (!questionList[index]) questionList[index] = {};
            if (field === 'is_required') {
                questionList[index][field] = true;
            } else if (field === 'options') {
                questionList[index][field] = value;
            } else {
                questionList[index][field] = value;
            }
        }
    }

    document.getElementById('preview_title').innerText = document.getElementById('eval_title').value;
    document.getElementById('preview_desc').innerText = document.getElementById('eval_desc').value;

    questionList.forEach((q, i) => {
        const field = document.createElement('div');
        field.classList.add('mb-3');

        const label = document.createElement('label');
        label.className = 'form-label fw-semibold';
        label.innerText = (i + 1) + '. ' + q.question + (q.is_required ? ' *' : '');
        field.appendChild(label);

        let input;

        switch (q.type) {
            case 'text':
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                break;
            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control';
                break;
            case 'radio':
            case 'checkbox':
                input = document.createElement('div');
                const options = q.options ? q.options.split('\n') : ['Option 1'];
                options.forEach(opt => {
                    const optDiv = document.createElement('div');
                    optDiv.className = 'form-check';
                    optDiv.innerHTML = `
                        <input type="${q.type}" class="form-check-input" disabled>
                        <label class="form-check-label">${opt.trim()}</label>
                    `;
                    input.appendChild(optDiv);
                });
                break;
        }

        if (input) field.appendChild(input);
        previewContainer.appendChild(field);
    });

    document.getElementById('questions_json').value = JSON.stringify(questionList);

    // Toggle views
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