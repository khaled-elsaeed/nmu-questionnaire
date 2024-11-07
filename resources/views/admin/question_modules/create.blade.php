@extends('layouts.admin')
@section('title', 'Create Questionnaire Module')
@section('links')
<!-- SweetAlert2 CSS -->
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .loading {
        pointer-events: none;
    }

    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border-radius: 0.5rem;
        background-color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 600;
    }

    .form-control,
    .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.3rem;
    }

    .question-block {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #f8f9fa;
    }

    .options-container,
    .scale-container,
    .text-scale-container {
        display: none;
    }

    .is-invalid {
        border-color: red;
    }
</style>
@endsection

@section('content')
<!-- Start col -->
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
                        <div class="invalid-feedback">
                            Please provide a module name.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="module_description" class="form-label">Module Description</label>
                        <textarea class="form-control" id="module_description" name="module_description" rows="4" required></textarea>
                        <div class="invalid-feedback">
                            Please provide a module description.
                        </div>
                    </div>
                </div>
                <div id="questions-container" class="form-section">
                    <h6>Add Questions to Module</h6>
                    <div class="question-block mb-3">
                        <label for="question_text_1" class="form-label">Question Text</label>
                        <textarea class="form-control" id="question_text_1" name="questions[0][text]" rows="2" required></textarea>
                        <div class="invalid-feedback">
                            Please enter the question text.
                        </div>
                        <div class="mb-3">
                            <label for="question_type_1" class="form-label">Question Type</label>
                            <select class="form-select" id="question_type_1" name="questions[0][type]" required>
                                <option value="">Select type...</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="text_based">Text-Based</option>
                                <option value="scaled">Scaled</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a question type.
                            </div>
                        </div>
                        <div class="options-container" id="options-container-1">
                            <label class="form-label">Options (Up to 4)</label>
                            <input type="text" class="form-control mb-2" name="questions[0][options][]" placeholder="Option 1" required>
                            <input type="text" class="form-control mb-2" name="questions[0][options][]" placeholder="Option 2" required>
                            <input type="text" class="form-control mb-2" name="questions[0][options][]" placeholder="Option 3">
                            <input type="text" class="form-control" name="questions[0][options][]" placeholder="Option 4">
                        </div>
                        <div class="scale-container" id="scale-container-1">
                            <label class="form-label">Scale Type</label>
                            <select class="form-select" id="scale_type_1" name="questions[0][scale_type]">
                                <option value="">Select scale type...</option>
                                <option value="numerical">Numerical (1 to 5)</option>
                                <option value="text">Text Descriptors</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a scale type.
                            </div>
                           
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" id="add-question-button">+ Add Another Question</button>
                <button type="submit" class="btn btn-primary">Create Module and Questions</button>
            </form>
        </div>
    </div>
</div>
<!-- End col -->
@endsection

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let questionCount = 1; // Start with the first question

        // Function to toggle visibility of options based on question type
        function toggleOptions(questionIndex) {
            const questionType = $(`#question_type_${questionIndex}`).val();
            const scaleTypeSelect = $(`#scale_type_${questionIndex}`);
            const scaleContainer = $(`#scale-container-${questionIndex}`);
            const optionsContainer = $(`#options-container-${questionIndex}`);

            // Reset visibility and required attributes
            scaleContainer.hide();
            optionsContainer.hide(); // Hide options by default
            scaleTypeSelect.removeAttr('required'); // Remove required by default

            // Show relevant options based on question type
            if (questionType === 'scaled') {
                scaleContainer.show(); // Show scale options
                scaleTypeSelect.attr('required', 'required'); // Set required when visible
            } else if (questionType === 'multiple_choice') {
                optionsContainer.show(); // Show options for multiple choice
                optionsContainer.find('input').attr('required', 'required'); // Set required for options when visible
            } else {
                optionsContainer.find('input').removeAttr('required'); // Remove required from options if not visible
            }

            
        }

        // Handle the initial selection for the first question
        toggleOptions(1);

        // Function to handle the addition of new question fields
        $('#add-question-button').on('click', function() {
            questionCount++;
            const questionBlock = `
                <div class="question-block mb-3">
                    <label for="question_text_${questionCount}" class="form-label">Question Text</label>
                    <textarea class="form-control" id="question_text_${questionCount}" name="questions[${questionCount}][text]" rows="2" required></textarea>
                    <div class="invalid-feedback">Please enter the question text.</div>
                    <div class="mb-3">
                        <label for="question_type_${questionCount}" class="form-label">Question Type</label>
                        <select class="form-select" id="question_type_${questionCount}" name="questions[${questionCount}][type]" required>
                            <option value="">Select type...</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="text_based">Text-Based</option>
                            <option value="scaled">Scaled</option>
                        </select>
                        <div class="invalid-feedback">Please select a question type.</div>
                    </div>
                    <div class="options-container" id="options-container-${questionCount}">
                        <label class="form-label">Options (Up to 4)</label>
                        <input type="text" class="form-control mb-2" name="questions[${questionCount}][options][]" placeholder="Option 1" required>
                        <input type="text" class="form-control mb-2" name="questions[${questionCount}][options][]" placeholder="Option 2" required>
                        <input type="text" class="form-control mb-2" name="questions[${questionCount}][options][]" placeholder="Option 3">
                        <input type="text" class="form-control" name="questions[${questionCount}][options][]" placeholder="Option 4">
                    </div>
                    <div class="scale-container" id="scale-container-${questionCount}">
                        <label class="form-label">Scale Type</label>
                        <select class="form-select" id="scale_type_${questionCount}" name="questions[${questionCount}][scale_type]">
                            <option value="">Select scale type...</option>
                            <option value="numerical">Numerical (1 to 5)</option>
                            <option value="text">Text Descriptors</option>
                        </select>
                        <div class="invalid-feedback">Please select a scale type.</div>
                       
                    </div>
                </div>
            `;
            $('#questions-container').append(questionBlock);
            toggleOptions(questionCount); // Initialize new question block
        });

        // Event delegation for question type change to handle dynamic elements
        $('#questions-container').on('change', 'select[id^="question_type_"]', function() {
            const questionIndex = $(this).attr('id').split('_')[2];
            toggleOptions(questionIndex);
        });

        // Event delegation for scale type change
        $('#questions-container').on('change', 'select[id^="scale_type_"]', function() {
            const questionIndex = $(this).attr('id').split('_')[2];
            const scaleType = $(this).val();
            const numericalScaleContainer = $(`#scale-container-${questionIndex} .numerical-scale-container`);
            const textScaleContainer = $(`#scale-container-${questionIndex} .text-scale-container`);

            numericalScaleContainer.toggle(scaleType === 'numerical');
            textScaleContainer.toggle(scaleType === 'text');
        });

        // Form submission with validation
        $('#create-module-form').on('submit', function(event) {
            event.preventDefault(); // Prevent default submission

            let isValid = true;

            // Validate required fields
            for (let i = 1; i <= questionCount; i++) { // Start from 1 as questionCount starts from 1
                const questionText = $(`#question_text_${i}`);
                const questionType = $(`#question_type_${i}`);
                const scaleTypeSelect = $(`#scale_type_${i}`);
                const optionsContainer = $(`#options-container-${i}`);

                if (questionText.is(':visible') && questionText.val().trim() === '') {
                    isValid = false;
                    questionText.addClass('is-invalid');
                } else {
                    questionText.removeClass('is-invalid');
                }

                if (questionType.is(':visible') && questionType.val() === '') {
                    isValid = false;
                    questionType.addClass('is-invalid');
                } else {
                    questionType.removeClass('is-invalid');
                }

                // Check scale type if applicable
                if (scaleTypeSelect.is(':visible') && scaleTypeSelect.val() === '') {
                    isValid = false;
                    scaleTypeSelect.addClass('is-invalid');
                } else {
                    scaleTypeSelect.removeClass('is-invalid');
                }

                // Validate options for multiple choice if applicable
                if (optionsContainer.is(':visible')) {
                    const optionInputs = optionsContainer.find('input[type="text"]');
                    optionInputs.each(function() {
                        if ($(this).is(':visible') && $(this).val().trim() === '') {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                }
            }

            if (!isValid) {
                alert('Please fill in all required fields correctly.');
                return; // Prevent form submission
            }

            // Proceed to submit form data via AJAX if valid
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).then(response => {
                if (response.ok) {
                    alert("Module created successfully!");
                    location.reload(); // Optionally reload to see new data
                } else {
                    alert("Error creating module.");
                }
            }).catch(error => console.error('Error:', error));
        });
    });
</script>

@endsection
