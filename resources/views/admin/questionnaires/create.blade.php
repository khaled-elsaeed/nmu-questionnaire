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

        /* CSS Grid */
    #accordionoutline, #facultyaccordionoutline, #courseaccordionoutline {
        display: flex;
        flex-wrap: wrap;  /* Allow the cards to wrap to the next line if there's not enough space */
        gap: 20px;  /* Adjust the space between cards */
    }

    .card {
        margin: 10px 0 10px 0;
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

    .form-control,
    .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
                        <label class="form-check-label" for="all_faculty">All Faculties</label>
                        </div>
                    <div class="form-check">
                            <input class="form-check-input" type="radio" name="faculty_option" value="specific_faculty" id="specific_faculty">
                            <label class="form-check-label" for="specific_faculty">Specific Faculty</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="faculty_option" value="all_courses" id="all_courses">
                            <label class="form-check-label" for="all_courses">All Courses</label>
                            </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="faculty_option" value="specific_course" id="specific_course">
                            <label class="form-check-label" for="specific_course">Specific Course</label>
                        </div>
                        <div id="specific-faculty-options" style="display: none; padding-left: 2rem;">
                            <div id="facultyaccordionoutline" class="accordion">

                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecificFacultyModal" style="margin: 10px 0 10px 0;">
                                Add Faculty
                            </button>

                        </div>
                        <div id="specific-course-options" style="display: none; padding-left: 2rem;">
                            <div id="courseaccordionoutline" class="accordion">

                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecificCourseModal" style="margin: 10px 0 10px 0;">
                                Add Course
                            </button>

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

<div class="modal fade" id="addSpecificFacultyModal" tabindex="-1" aria-labelledby="addSpecificFacultyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSpecificFacultyModalLabel">Select Faculties, Departments, and Programs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="faculty-dropdown">Select Faculty</label>
                    <select class="form-select" id="faculty-dropdown" name="course" style="margin: 10px 0 10px 0;">
                        <option value="" selected disabled>Select a faculty</option>
                        @foreach($faculties as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="department-selection" style="display:none; margin: 10px 0 10px 0;">
                    <h6>Select Departments</h6>
                    <div id="department-checkboxes">
                    </div>
                </div>
                <div id="program-selection" style="display:none; margin: 10px 0 10px 0;" >
                    <h6>Select Programs</h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-specific-faculty-selections">Save Selections</button>
            </div>
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

<div class="modal fade" id="addSpecificCourseModal" tabindex="-1" aria-labelledby="addSpecificCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSpecificCourseModalLabel">Select Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="course-dropdown">Select Course</label>
                    <select class="form-select" id="course-dropdown" name="course">
                    <option value="" selected disabled>Select a Course</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-specific-course-selections">Add</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
    const selectedQuestions = {};


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
                    $('#question-list').empty();
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


    $(document).on('change', '.module-question', function() {
        const questionId = $(this).val();
        const questionText = $(this).data('text');
        const questionType = $(this).data('type');

        if (this.checked) {
            selectedQuestions[questionId] = {
                text: questionText,
                type: questionType
            };
        } else {
            delete selectedQuestions[questionId];
        }
        updateSelectedQuestions();
    });


    function updateSelectedQuestions() {
        $('#selected-questions').empty();
        let index = 0;
        $.each(selectedQuestions, function(id, question) {
            $('#selected-questions').append(`
                   <div class="accordion-item">
                       <h2 class="accordion-header" id="heading${index}">
                           <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}" style="margin: 10px 0 10px 0;">
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


    window.removeQuestion = function(id) {
        delete selectedQuestions[id];
        $(`.module-question[value="${id}"]`).prop('checked', false);
        updateSelectedQuestions();
    }


    window.showOptions = function(element) {
        const questionId = $(element).data('question-id');

        $('#options-list').html(`Options for question ${questionId}`);
        $('#optionsModal').modal('show');
    }


    $('#students').on('change', function() {
        $('#faculty-options').toggle(this.checked);
    });


    $('input[name="faculty_option"]').on('change', function() {
        $('#specific-faculty-options').toggle(this.value === 'specific_faculty');
        $('#specific-course-options').toggle(this.value === 'specific_course');


        if (this.value === 'all_faculty') {
            $('.specific-faculty-option, .faculty-department-option').prop('checked', false).prop('disabled', true);
            $('#specific-faculty-options').find('input[type="checkbox"]').prop('checked', false);
        } 
        else {
            $('.specific-faculty-option, .faculty-department-option').prop('disabled', false);
        }
    });


    //---------------------------------------------------------//
    let specificFaculties = {};


    function populateFacultyAccordion() {
    const accordionContainer = $('#facultyaccordionoutline');
    accordionContainer.empty();

    for (const facultyId in specificFaculties) {
        const facultyData = specificFaculties[facultyId];

        const facultyCard = `
            <div class="card">
                <div class="card-header" id="heading${facultyId}">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-faculty-${facultyId}" aria-expanded="false"
                            aria-controls="collapse-faculty-${facultyId}">
                            <i class="feather icon-award me-2"></i>${facultyData.facultyName}
                        </button>
                    </h2>
                </div>
                <div id="collapse-faculty-${facultyId}" class="collapse" aria-labelledby="heading${facultyId}">
                    <div class="card-body">
                        ${facultyData.departments.map(department => {
                            return `
                                <div class="department-card">
                                    <h6>${department.departmentName}</h6>
                                    <div class="department-programs" id="programs-${facultyId}-${department.departmentId}" style="margin: 10px 0 10px 0;">
                                        ${department.programs && department.programs.length > 0 ? `
                                            <ul>
                                                ${department.programs.map(program => {
                                                    return `<li>${program.programName}</li>`;
                                                }).join('')}
                                            </ul>
                                        ` : `<p>No programs available</p>`}
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            </div>
        `;

        accordionContainer.append(facultyCard);
    }
}




    $('#faculty-dropdown').on('change', function() {
        const facultyId = $(this).val();


        if (!specificFaculties[facultyId]) {
            specificFaculties[facultyId] = {
                facultyId: facultyId,
                facultyName: $(this).find('option:selected').text(),
                departments: []
            };
        }

        ///----------------------------------------------------------////

        const departmentSelection = $('#department-selection');
        const departmentCheckboxesDiv = $('#department-checkboxes');


        $('#program-selection').empty();
        $('#program-selection').hide();


        const url = `{{ route('faculties.departments', ':id') }}`.replace(':id', facultyId);
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                departmentCheckboxesDiv.empty();

                if (data.length > 0) {
                    data.forEach(function(department) {
                        departmentCheckboxesDiv.append(`
                        <div class="form-check">
                            <input class="form-check-input department-option" type="checkbox" name="faculty_${facultyId}_department[]" value="${department.id}" id="faculty_${facultyId}_department_${department.id}">
                            <label class="form-check-label" for="faculty_${facultyId}_department_${department.id}">${department.name}</label>
                        </div>
                    `);


                        $('#program-selection').append(`
                        <div id="program-selection-${department.id}" class="program-section" style="display:none;">
                            <h6>Programs for ${department.name}</h6>
                            <div id="program-checkboxes-${department.id}">
                                <!-- Program checkboxes will be dynamically loaded here -->
                            </div>
                        </div>
                    `);
                    });
                    departmentSelection.show();
                } else {
                    departmentCheckboxesDiv.append('<p>No departments available.</p>');
                }
            },
            error: function() {
                swal('Error!', 'Unable to fetch departments. Please try again.', 'error');
            }
        });
    });


    $(document).on('change', '.department-option', function() {
        const departmentId = this.value;
        const facultyId = $('#faculty-dropdown').val();
        const departmentSelectionDiv = $('#department-selection');
        const programsParentSelectionDiv = $('#program-selection');
        const programSelectionDiv = $('#program-selection-' + departmentId);
        const programCheckboxesDiv = $('#program-checkboxes-' + departmentId);

        const faculty = specificFaculties[facultyId];
        let department = faculty?.departments.find(d => d.departmentId == departmentId);

        if (this.checked) {
            if (!department) {
                department = {
                    departmentId: departmentId,
                    departmentName: $(this).next('label').text(),
                    programs: []
                };
                faculty.departments.push(department);
            }

            const url = `{{ route('departments.programs', ':id') }}`.replace(':id', departmentId);
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    programCheckboxesDiv.empty();

                    if (data.length > 0) {
                        data.forEach(function(program) {
                            programCheckboxesDiv.append(`
                            <div class="form-check">
                                <input class="form-check-input program-option" type="checkbox" 
                                    name="department_${departmentId}_program[]" value="${program.id}" 
                                    id="program_${program.id}" data-program-id="${program.id}">
                                <label class="form-check-label" for="program_${program.id}">${program.name}</label>
                            </div>
                        `);
                        });

                        programsParentSelectionDiv.show();
                        programSelectionDiv.show();
                    } else {
                        programCheckboxesDiv.append('<p>No programs available.</p>');
                    }
                },
                error: function() {
                    swal('Error!', 'Unable to fetch programs. Please try again.', 'error');
                }
            });
        } else {
            if (department) {
                faculty.departments = faculty.departments.filter(d => d.departmentId != departmentId);
            }

            programSelectionDiv.hide();
            programCheckboxesDiv.empty();
        }
    });

    $(document).on('change', '.program-option', function() {
        const facultyId = $('#faculty-dropdown').val();
        const departmentId = $(this).closest('.program-section').attr('id').split('-')[2]; // Corrected index
        const programId = this.value;

        const faculty = specificFaculties[facultyId];
        const department = faculty?.departments.find(d => d.departmentId == departmentId);

        if (this.checked) {
            if (department && !department.programs.some(p => p.programId == programId)) {
                const selectedProgram = {
                    programId,
                    programName: $(this).next('label').text()
                };
                department.programs.push(selectedProgram);
            }
        } else {
            if (department) {
                department.programs = department.programs.filter(p => p.programId != programId);
            }
        }

        console.log('Updated specificFaculties:', specificFaculties);
    });

//---//
    $('#save-specific-faculty-selections').on('click', function() {
    let isValid = true;
    let errorMessage = '';

    // Check if a faculty is selected
    const selectedFacultyId = $('#faculty-dropdown').val();
    if (!selectedFacultyId) {
        isValid = false;
        errorMessage = 'Please select a faculty.';
    }

    if (isValid) {
        // Loop through each faculty to check if at least one department is selected
        $('#faculty-dropdown').each(function() {
            const facultyId = $(this).val();
            if (!facultyId || !specificFaculties[facultyId]) {
                return; // Skip if no faculty is selected or faculty does not exist
            }

            const departmentCheckboxes = $(`#department-checkboxes input[name="faculty_${facultyId}_department[]"]:checked`);
            
            if (departmentCheckboxes.length === 0) {
                isValid = false;
                errorMessage = `Please select at least one department for ${specificFaculties[facultyId].facultyName}.`;
            } else {
                // Loop through each selected department and check if programs are selected
                departmentCheckboxes.each(function() {
                    const departmentId = $(this).val();
                    const programCheckboxes = $(`#program-checkboxes-${departmentId} input:checked`);
                    
                    if (programCheckboxes.length === 0) {
                        isValid = false;
                        errorMessage = `Please select at least one program for ${$(this).closest('.form-check').find('label').text()}.`;
                    }
                });
            }
        });
    }

    // If no selection is made or if a faculty is not selected, show the error message
    if (!isValid) {
        swal('Error!', errorMessage, 'error');
    } else {
        // Proceed with resetting the fields and closing the modal
        console.log(specificFaculties);  // Debugging log
        populateFacultyAccordion();  // Re-populate accordion

        // Reset dropdowns and checkboxes
        $('#faculty-dropdown').val('').prop('selected', true);
        $('#department-checkboxes').empty();
        $('#program-selection').empty();
        $('#department-selection').hide();
        $('#program-selection').hide();

        // Hide the modal
        $('#addSpecificFacultyModal').modal('hide');

        // Optionally show success message
        swal('Success!', 'Selections have been saved and form reset.', 'success');
    }
});

//---//




let specificCourses = {};


function populateCourseAccordion() {
    const accordionContainer = $('#courseaccordionoutline');
    accordionContainer.empty();

    for (const courseId in specificCourses) {
        const courseData = specificCourses[courseId];

        const courseCard = `
            <div class="card">
                <div class="card-header" id="heading${courseId}">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-course-${courseId}" aria-expanded="false"
                        aria-controls="collapse-course-${courseId}">
                            <i class="feather icon-award me-2"></i>${courseData.courseName}
                        </button>
                    </h2>
                </div>
                <div id="collapse-course-${courseId}" class="collapse" aria-labelledby="heading${courseId}">
                    <div class="card-body">
                        <!-- You can add content here if required -->
                        <p>Course details or other content here.</p>
                    </div>
                </div>
            </div>
        `;

        accordionContainer.append(courseCard);
    }
}



$('#course-dropdown').on('change', function() {
    const courseId = $(this).val();
    console.log(courseId);

    if (!specificCourses[courseId]) {
        specificCourses[courseId] = {
            courseId: courseId,
            courseName: $(this).find('option:selected').text(),
        };
    }

});

$('#save-specific-course-selections').on('click', function() {
    let isValid = true;
    let errorMessage = '';

    // Check if a course is selected
    const selectedCourseId = $('#course-dropdown').val();
    if (!selectedCourseId) {
        isValid = false;
        errorMessage = 'Please select a course.';
    }

    // If no course is selected, show the error message
    if (!isValid) {
        swal('Error!', errorMessage, 'error');
    } else {
        // Proceed with resetting the fields and closing the modal
        console.log(specificCourses);  // Debugging log
        populateCourseAccordion();  // Re-populate accordion

        // Reset the dropdowns
        $('#course-dropdown').val('').prop('selected', true);

        // Hide the modal
        $('#addSpecificCourseModal').modal('hide');

        // Optionally show success message
        swal('Success!', 'Course selection has been saved and form reset.', 'success');
    }
});



    function handleQuestionnaireSubmit(e) {
        e.preventDefault();

        if (!validateSubmissionForm()) {
            return;
        }

        const formData = prepareQuestionnaireData();
        submitQuestionnaire(formData);
    }

    function validateSubmissionForm() {
        const selectedQuestionIds = Object.keys(selectedQuestions);
        if (selectedQuestionIds.length === 0) {
            swal('Error!', 'Please select at least one question.', 'error');
            return false;
        }

        const hasAudience = $('input[name="audience[]"]:checked').length > 0;
        if (!hasAudience) {
            swal('Error!', 'Please select at least one target audience.', 'error');
            return false;
        }

        return true;
    }

   // Prepare all data for submission
function prepareQuestionnaireData() {
    const formData = $('#create-questionnaire-form').serializeArray();
    const audiences = prepareAudienceData();

    // Add selected questions
    Object.keys(selectedQuestions).forEach(questionId => {
        formData.push({
            name: 'questions[]',
            value: questionId
        });
    });

    // Add audience data as JSON string
    formData.push({
        name: 'audience_data',
        value: JSON.stringify(audiences)
    });

    return formData;
}

// Prepare the audience data (students, teaching assistants, staff)
function prepareAudienceData() {
    const studentAudience = [];
    const teachingAssistanceAudience = [];
    const staffAudience = [];

    // Loop through all selected audience checkboxes
    $('input[name="audience[]"]:checked').each(function() {
        const audienceRole = $(this).val();

        switch (audienceRole) {
            case 'student':
                studentAudience.push(buildStudentAudience());
                break;
            case 'teaching_assistant':
                teachingAssistanceAudience.push(buildTeachingAssistanceAudience());
                break;
            case 'staff':
                staffAudience.push(buildStaffAudience());
                break;
        }
    });

    // Return audience data as structured object
    return {
        students: studentAudience,
        teaching_assistant: teachingAssistanceAudience,
        staff: staffAudience
    };
}

//---------------//
// Build the audience data for 'student' role
function buildStudentAudience() {
    const audienceData = {
        role_name: 'student',
        scope_type: '',
        faculties: [],
        courses: [],
    };

    // Check if faculty options are visible
    if ($('#faculty-options').is(':visible')) {
        const facultyOption = $('input[name="faculty_option"]:checked').val();

        if (facultyOption === 'all_faculty') {
            // For global scope (all faculties)
            audienceData.scope_type = 'global';
            audienceData.faculties.push({
                id: 'all',
                departments: []  // No specific departments for global scope
            });
        } else if (facultyOption === 'specific_faculty') {
            // For local scope (specific faculties)
            audienceData.scope_type = 'local';
            audienceData.faculties = Object.values(specificFaculties).map(faculty => ({
                id: faculty.facultyId,
                name: faculty.facultyName,
                departments: faculty.departments.map(dept => ({
                    id: dept.departmentId,
                    name: dept.departmentName,
                    programs: dept.programs.map(prog => ({
                        id: prog.programId,
                        name: prog.programName
                    }))
                }))
            }));
        } else if (facultyOption == 'all_courses'){
            // For global scope (all faculties)
            audienceData.scope_type = 'global';
            audienceData.courses.push({
                id: 'all',
            });
        }

        else if (facultyOption == 'specific_course'){
            // For global scope (all faculties)
            audienceData.scope_type = 'local';
            audienceData.courses = Object.values(specificCourses).map(course => ({
                id: course.courseId,
                name: course.courseName,
            }));
        }
    }

    

    return audienceData;
}
//---------------//

// Build the audience data for 'teaching_assistant' role
function buildTeachingAssistanceAudience() {
    return {
        role_name: 'teaching_assistant',
        scope_type: 'global',  // Default to global scope for simplicity
        faculties: [{ id: 'all', departments: [] }]  // No departments for teaching assistants
    };
}

// Build the audience data for 'staff' role
function buildStaffAudience() {
    return {
        role_name: 'staff',
        scope_type: 'global',  // Default to global scope for simplicity
        faculties: [{ id: 'all', departments: [] }]  // No departments for staff
    };
}

// Submit the form data via AJAX
function submitQuestionnaire(formData) {
    $.ajax({
        type: 'POST',
        url: `{{ route('admin.questionnaires.store') }}`,  // Update with actual route if necessary
        data: formData,
        success: function(response) {
            swal('Success!', 'Questionnaire created successfully!', 'success')
                .then(() => {
                    window.location.href = `{{ route('admin.questionnaires.create') }}`;  // Redirect on success
                });
        },
        error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let errorMessage = 'Please correct the following errors:\n';

            // Format error messages
            for (const error in errors) {
                errorMessage += `- ${errors[error].join(', ')}\n`;
            }

            swal('Error!', errorMessage, 'error');
        }
    });
}


    // Bind form submission handler
    $('#create-questionnaire-form').on('submit', handleQuestionnaireSubmit);


});
</script>
@endsection