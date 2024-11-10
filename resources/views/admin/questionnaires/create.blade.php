@extends('layouts.admin')
@section('title', 'Create Questionnaire')
@section('links')
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border-radius: 0.5rem;
        background-color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .question-block {
        padding: 1rem;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    #question-list .form-check .form-check-input {
        margin-left: 0 !important;
    }
    .options-list {
        margin-top: 1rem;
    }
    .accordion-button {
        background: none;
        border: none;
        text-align: left;
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
        color: #007bff;
    }
    .accordion-button:hover {
        background-color: #f1f1f1;
        border-radius: 0.5rem;
    }
    .accordion-item {
        margin-bottom: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
    }
    .accordion-header {
        padding: 0;
    }
    .accordion-body {
        padding: 1rem;
        background-color: #f8f9fa;
    }
    .badge {
        margin-left: 1rem;
    }
    /* Styles for input fields and selects */
    .form-control,
    .form-select {
        border: 1px solid #ced4da; /* Light gray border */
        border-radius: 0.25rem; /* Slightly rounded corners */
    }
    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff; /* Blue border on focus */
        box-shadow: 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Subtle blue shadow */
    }
</style>
@endsection
@section('content')
<div class="col-lg-12">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Create Questionnaire</h5>
        </div>
        <div class="card-body">
            <form id="create-questionnaire-form">
                @csrf
                
                <div class="form-section">
                    <h6>Questionnaire Details</h6>
                    <div class="mb-3">
                        <label for="questionnaire_name" class="form-label">Questionnaire Name</label>
                        <input type="text" class="form-control" id="questionnaire_name" name="title" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Is Active</label>
                        <select class="form-select" id="is_active" name="is_active" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                
                <div class="form-section">
                    <h6>Select Question Module</h6>
                    <select class="form-select" id="module_select" name="module_id" required>
                        <option value="">Choose a module...</option>
                        @foreach($modules as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('module_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                
                <div id="questions-container" class="form-section">
                    <h6>Available Questions in Module</h6>
                    <div id="question-list" class="mb-3"></div>
                </div>

                
                <div class="form-section bg-light">
                    <h6>Selected Questions</h6>
                    <div id="selected-questions" class="accordion" id="accordionSelectedQuestions"></div>
                </div>


<div class="form-section">
    <h6>Target Audience</h6>
    
    
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="audience[]" value="student" id="students">
        <label class="form-check-label" for="students">Students</label>
    </div>

   
<div id="faculty-options" style="display: none; padding-left: 1.5rem;">
    <div class="form-check">
        <input class="form-check-input" type="radio" name="faculty_option" value="all_faculty" id="all_faculty">
        <label class="form-check-label" for="all_faculty">All Faculty</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="faculty_option" value="specific_faculty" id="specific_faculty">
        <label class="form-check-label" for="specific_faculty">Specific Faculty</label>
    </div>

    
    <div id="specific-faculty-options" style="display: none; padding-left: 2rem;">
        <h6>Select Specific Faculty, Department, and Program</h6>
        @foreach($faculties as $id => $name)
    
    <div class="form-check">
        <input class="form-check-input specific-faculty-option" type="checkbox" name="faculty_specific[]" value="{{ $id }}" id="faculty_{{ $id }}">
        <label class="form-check-label" for="faculty_{{ $id }}">{{ $name }}</label>
    </div>

    <div id="faculty_{{ $id }}-departments" style="display: none; padding-left: 1.5rem;">
        <h6>Departments</h6>
        <div id="departments_{{ $id }}"></div> 
    </div>
@endforeach



        
    </div>
</div>


    
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="audience[]" value="teaching_assistant" id="teaching_assistant">
        <label class="form-check-label" for="teaching_assistant">Teaching Assistants</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="audience[]" value="staff" id="staff">
        <label class="form-check-label" for="staff">Staff</label>
    </div>

    <div class="invalid-feedback">Please select at least one target audience.</div>
</div>




                
                <button type="submit" class="btn btn-primary">Create Questionnaire</button>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="optionsModalLabel">Question Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="options-list" class="options-list"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    const selectedQuestions = {}; // Store selected questions by question ID

    // Fetch questions when a module is selected
    $('#module_select').on('change', function() {
        const moduleId = $(this).val();
        if (moduleId) {
            const url = `{{ route('admin.question-modules.questions', ':id') }}`.replace(':id', moduleId);

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    $('#question-list').empty(); // Clear previous questions
                    if (data.questions && data.questions.length > 0) {
                        data.questions.forEach(question => {
                            const isChecked = selectedQuestions[question.id] ? 'checked' : '';
                            $('#question-list').append(`
                                <div class="form-check question-block d-flex align-items-center justify-content-between p-3 bg-light border rounded mb-2">
                                    <div class="d-flex align-items-center">
                                        <input class="form-check-input module-question me-2" 
                                               type="checkbox" 
                                               value="${question.id}" 
                                               data-text="${question.text}" 
                                               data-type="${question.type}" 
                                               ${isChecked}>
                                        <label class="form-check-label me-4">${question.text}</label>
                                    </div>
                                    <span class="badge bg-secondary">${question.type}</span>
                                    <button class="btn btn-info btn-sm" data-question-id="${question.id}" onclick="showOptions(this)">Options</button>
                                </div>
                            `);
                        });
                    } else {
                        $('#question-list').append('<div>No questions available for this module.</div>');
                    }
                })
                .catch(error => {
                    console.error('Error fetching questions:', error);
                    swal('Error!', 'Unable to fetch questions. Please try again.', 'error');
                });
        } else {
            $('#question-list').empty();
        }
    });

    // Handle selecting/deselecting questions
    $(document).on('change', '.module-question', function() {
        const questionId = $(this).val();
        const questionText = $(this).data('text');
        const questionType = $(this).data('type');

        if (this.checked) {
            selectedQuestions[questionId] = { text: questionText, type: questionType };
        } else {
            delete selectedQuestions[questionId];
        }
        updateSelectedQuestions();
    });

    // Display selected questions
    function updateSelectedQuestions() {
        $('#selected-questions').empty();
        let index = 0; // Index for accordion items
        $.each(selectedQuestions, function(id, question) {
            $('#selected-questions').append(`
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                            ${question.text}
                        </button>
                    </h2>
                    <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#accordionSelectedQuestions">
                        <div class="accordion-body">
                            Type: ${question.type}
                            <button class="btn btn-danger btn-sm float-end" onclick="removeQuestion('${id}')">Remove</button>
                        </div>
                    </div>
                </div>
            `);
            index++;
        });
    }

    // Remove question from selected
    window.removeQuestion = function(id) {
        delete selectedQuestions[id];
        $(`.module-question[value="${id}"]`).prop('checked', false); // Uncheck the original checkbox
        updateSelectedQuestions();
    }

    // Show question options in modal
    window.showOptions = function(element) {
        const questionId = $(element).data('question-id');
        // Fetch options logic if needed or show predefined options
        $('#options-list').html(`Options for question ${questionId}`); // Replace with actual data if necessary
        $('#optionsModal').modal('show');
    }

    // Show faculty options if "Students" is selected
    $('#students').on('change', function() {
        $('#faculty-options').toggle(this.checked);
    });

    // Show specific faculty options if "Specific Faculty" is selected
    $('input[name="faculty_option"]').on('change', function() {
        $('#specific-faculty-options').toggle(this.value === 'specific_faculty');
        
        if (this.value === 'all_faculty') {
            $('.specific-faculty-option, .faculty-department-option').prop('checked', false).prop('disabled', true);
            $('#specific-faculty-options').find('input[type="checkbox"]').prop('checked', false);
        } else {
            $('.specific-faculty-option, .faculty-department-option').prop('disabled', false);
        }
    });

    // Toggle departments when a faculty is selected
    $('.specific-faculty-option').on('change', function() {
        const facultyId = $(this).val(); // Get faculty ID from the value of the checkbox
        const departmentsDiv = $(`#faculty_${facultyId}-departments`);
        
        if (this.checked) {
            const url = `{{ route('faculties.departments', ':id') }}`.replace(':id', facultyId);

            $.ajax({
                url: url, // Adjust URL as necessary
                method: 'GET',
                success: function(data) {
                    departmentsDiv.empty(); // Clear previous entries

                    if (data.length > 0) {
                        data.forEach(function(department) {
                            departmentsDiv.append(`
                                <div class="form-check">
                                    <input class="form-check-input faculty-department-option" type="checkbox" name="faculty_${facultyId}_department[]" value="${department.id}" id="faculty_${facultyId}_department_${department.id}">
                                    <label class="form-check-label" for="faculty_${facultyId}_department_${department.id}">${department.name}</label>
                                </div>
                                <div id="faculty_${facultyId}_department_${department.id}-programs" style="display: none; padding-left: 1.5rem;">
                                    <h6>Programs</h6>
                                    <div id="programs_${facultyId}_${department.id}"></div> 
                                </div>
                            `);
                        });
                    } else {
                        departmentsDiv.append('<p>No departments available.</p>');
                    }
                },
                error: function() {
                    swal('Error!', 'Unable to fetch departments. Please try again.', 'error');
                }
            });
            departmentsDiv.show(); // Show departments section
        } else {
            departmentsDiv.hide(); // Hide departments if unchecked
        }
    });

    // Toggle programs when a department is selected
    $(document).on('change', '.faculty-department-option', function() {
        const departmentId = this.value; // Get department ID
        const facultyId = $(this).attr('id').split('_')[1]; // Extract faculty ID from checkbox ID
        const programsDiv = $(`#faculty_${facultyId}_department_${departmentId}-programs`);

        if (this.checked) {
            const url = `{{ route('departments.programs', ':id') }}`.replace(':id', departmentId);
            $.ajax({
                url: url, // Adjust URL as necessary
                method: 'GET',
                success: function(data) {
                    programsDiv.empty(); // Clear previous entries

                    if (data.length > 0) {
                        data.forEach(function(program) {
                            programsDiv.append(`
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="faculty_${facultyId}_department_${departmentId}_program[]" value="${program.id}" id="program_${program.id}">
                                    <label class="form-check-label" for="program_${program.id}">${program.name}</label>
                                </div>
                            `);
                        });
                    } else {
                        programsDiv.append('<p>No programs available.</p>');
                    }
                },
                error: function() {
                    swal('Error!', 'Unable to fetch programs. Please try again.', 'error');
                }
            });
            programsDiv.show(); // Show programs section
        } else {
            programsDiv.hide(); // Hide programs if unchecked
        }
    });

    // Add an event to reset selections if a checkbox is unchecked
    $('.form-check-input').on('change', function() {
        if (!this.checked) {
            const relatedDepartmentsSectionId = $(this).attr('id') + '-departments';
            const programsSectionId = $(this).attr('id') + '-programs';
            $(`#${relatedDepartmentsSectionId}, #${programsSectionId}`).hide();
            $(`#${relatedDepartmentsSectionId} input, #${programsSectionId} input`).prop('checked', false);
        }
    });

    $('#create-questionnaire-form').on('submit', function(e) {
    e.preventDefault();

    // Collect selected questions
    const selectedQuestionIds = Object.keys(selectedQuestions);

    // Validate if there are any selected questions
    if (selectedQuestionIds.length === 0) {
        swal('Error!', 'Please select at least one question.', 'error');
        return;
    }

    // Collect target audience data in a structured format
    const audiences = [];
    $('input[name="audience[]"]:checked').each(function() {
        const audience = $(this).val();
        const audienceData = { role_name: audience, level: 1, scope_type: 'Local', faculties: [] };

        // If audience is 'student' and 'faculty' options are selected
        if (audience === 'student' && $('#faculty-options').is(':visible')) {
            const facultyOption = $('input[name="faculty_option"]:checked').val();

            if (facultyOption === 'all_faculty') {
                // If "All Faculty" is selected, add all faculties without specific departments or programs
                audienceData.faculties.push({ id: 'all', departments: [] });
            } else if (facultyOption === 'specific_faculty') {
                // Collect specific faculties and their departments and programs
                $('.specific-faculty-option:checked').each(function() {
                    const facultyId = $(this).val();
                    const facultyData = { id: facultyId, departments: [] };

                    // Add selected departments under each faculty
                    $(`input[name="faculty_${facultyId}_department[]"]:checked`).each(function() {
                        const departmentId = $(this).val();
                        const departmentData = { id: departmentId, programs: [] };

                        // Add selected programs under each department
                        $(`input[name="faculty_${facultyId}_department_${departmentId}_program[]"]:checked`).each(function() {
                            departmentData.programs.push({ id: $(this).val() });
                        });

                        facultyData.departments.push(departmentData);
                    });

                    audienceData.faculties.push(facultyData);
                });
            }
        }

        audiences.push(audienceData);
    });

    // Validate if at least one target audience is selected
    if (audiences.length === 0) {
        swal('Error!', 'Please select at least one target audience.', 'error');
        return;
    }

    // Serialize form data and prepare the data structure to be sent
    const formData = $(this).serializeArray();

    // Add selected questions to form data
    selectedQuestionIds.forEach(questionId => {
        formData.push({ name: 'questions[]', value: questionId });
    });

    // Add audience data to form data as JSON
    formData.push({ name: 'audience_data', value: JSON.stringify(audiences) });

    // Submit the form data via AJAX
    $.ajax({
        type: 'POST',
        url: '{{ route("admin.questionnaires.store") }}',
        data: formData,
        success: function(response) {
            swal('Success!', 'Questionnaire created successfully!', 'success').then(() => {
                window.location.href = '{{ route("admin.questionnaires.create") }}';
            });
        },
        error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let errorMessage = 'Please correct the following errors:\n';
            for (const error in errors) {
                errorMessage += `- ${errors[error].join(', ')}\n`;
            }
            swal('Error!', errorMessage, 'error');
        }
    });
});


});
</script>
@endsection
