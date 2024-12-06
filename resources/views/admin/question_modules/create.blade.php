@extends('layouts.admin')
@section('title', 'Create Questionnaire Module')

@section('links')
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .loading { pointer-events: none; }
    .form-section { margin-bottom: 2rem; padding: 1.5rem; border-radius: 0.5rem; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
    .form-label { font-weight: 600; }
    .form-control, .form-select { border: 1px solid #ced4da; border-radius: 0.3rem; }
    .question-block { border: 1px solid #ddd; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; background-color: #f8f9fa; }
    .is-invalid { border-color: red; }
</style>
@endsection

@section('content')
<div class="col-lg-12">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title">Create Questionnaire Module</h5>
        </div>
        <div class="card-body">
            <form id="create-module-form" method="POST" action="{{ route('admin.question-modules.store') }}">
                @csrf
                <div class="form-section">
                    <h6>Module Details</h6>
                    <div class="mb-3">
                        <label for="module_name" class="form-label">Module Name</label>
                        <input type="text" class="form-control" id="module_name" name="module_name" required>
                        <div class="invalid-feedback">Please provide a module name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="module_description" class="form-label">Module Description</label>
                        <textarea class="form-control" id="module_description" name="module_description" rows="4"></textarea>
                        <div class="invalid-feedback">Please provide a module description.</div>
                    </div>
                </div>
                <div id="questions-container" class="form-section">
                    <h6>Add Questions to Module</h6>
                    <div id="question-list"><p>No questions added yet. Click "Add Another Question" to get started.</p></div>
                </div>
                <button type="button" class="btn btn-secondary" id="add-question-button">+ Add Another Question</button>
                <button type="submit" class="btn btn-primary">Create Module and Questions</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.common.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let questionIndex = 0;
        const questions = {}; // Object to track questions

        function renderQuestionBlock(index) {
            return `
                <div class="question-block" id="question-block-${index}">
                    <label for="question_text_${index}" class="form-label">Question Text</label>
                    <textarea class="form-control question-text" id="question_text_${index}" data-index="${index}" name="questions[${index}][text]" rows="2" required></textarea>
                    <div class="invalid-feedback">Please enter the question text.</div>
                    <div class="mb-3">
                        <label for="question_type_${index}" class="form-label">Question Type</label>
                        <select class="form-select question-type" id="question_type_${index}" data-index="${index}" name="questions[${index}][type]" required>
                            <option value="">Select type...</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="text_based">Text-Based</option>
                            <option value="scaled">Scaled</option>
                        </select>
                        <div class="invalid-feedback">Please select a question type.</div>
                    </div>
                    <div class="options-container" id="options-container-${index}" style="display: none;">
                        <label class="form-label">Options (Up to 4)</label>
                        <input type="text" class="form-control mb-2 option-input" data-index="${index}" data-option="0" placeholder="Option 1">
                        <input type="text" class="form-control mb-2 option-input" data-index="${index}" data-option="1" placeholder="Option 2">
                        <input type="text" class="form-control mb-2 option-input" data-index="${index}" data-option="2" placeholder="Option 3">
                        <input type="text" class="form-control option-input" data-index="${index}" data-option="3" placeholder="Option 4">
                    </div>
                    <div class="scale-container" id="scale-container-${index}" style="display: none;">
                        <label class="form-label">Scale Type</label>
                        <select class="form-select scale-type" id="scale_type_${index}" data-index="${index}" name="questions[${index}][scale_type]">
                            <option value="">Select scale type...</option>
                            <option value="numerical">Numerical (1 to 5)</option>
                            <option value="text">Text Descriptors</option>
                        </select>
                        <div class="invalid-feedback">Please select a scale type.</div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeQuestion(${index})">Remove Question</button>
                </div>
            `;
        }

        function toggleFields(index) {
            const questionType = document.querySelector(`#question_type_${index}`).value;
            const optionsContainer = document.querySelector(`#options-container-${index}`);
            const scaleContainer = document.querySelector(`#scale-container-${index}`);

            optionsContainer.style.display = questionType === 'multiple_choice' ? 'block' : 'none';
            scaleContainer.style.display = questionType === 'scaled' ? 'block' : 'none';

            if (questionType !== 'multiple_choice') {
                delete questions[index].options;
            }

            if (questionType !== 'scaled') {
                delete questions[index].scale;
            }
        }

        function validateForm() {
            let valid = true;
            document.querySelectorAll('#create-module-form [required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            return valid;
        }

        document.querySelector('#add-question-button').addEventListener('click', function () {
            questionIndex++;
            questions[questionIndex] = { text: '', type: '', options: [], scale: '' };
            document.querySelector('#question-list').insertAdjacentHTML('beforeend', renderQuestionBlock(questionIndex));
        });

        document.querySelector('#questions-container').addEventListener('change', function (e) {
            const target = e.target;
            const index = target.dataset.index;

            if (target.classList.contains('question-text')) {
                questions[index].text = target.value;
            } else if (target.classList.contains('question-type')) {
                questions[index].type = target.value;
                toggleFields(index);
            } else if (target.classList.contains('option-input')) {
                const optionIndex = target.dataset.option;
                const value = target.value;
                questions[index].options[optionIndex] = value;

                questions[index].options = questions[index].options.filter(opt => opt && opt.trim() !== '');
            } else if (target.classList.contains('scale-type')) {
                questions[index].scale = target.value;
            }
        });

        window.removeQuestion = function (index) {
            delete questions[index];
            document.querySelector(`#question-block-${index}`).remove();
        };

        document.querySelector('#create-module-form').addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validateForm()) return;

            const formData = new FormData(this);
            Object.keys(questions).forEach(key => {
                if (!questions[key].text || !questions[key].type) {
                    delete questions[key];
                }
            });
            formData.append('questions', JSON.stringify(questions));

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal('Success!', 'Module created successfully!', 'success');
                    location.reload();
                } else {
                    swal('Error', data.error || 'Something went wrong', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal('Error', 'An error occurred while processing the form', 'error');
            });
        });
    });
</script>
@endsection
