    $(document).ready(function() {
    const selectedQuestions = {};


    $('#module_select').on('change', function() {
        const moduleId = $(this).val();
        if (moduleId) {
            const url = window.routes.questionModuleQuestions.replace(':id', moduleId);

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
            var formattedType = '';
                    if (question.type === 'scaled_text') {
                        formattedType = "Scale (description)";
                    } else {
                        formattedType = "Other Type";  // or some default value
                    }
                    
                    // Append a new accordion item for each question
                    $('#selected-questions').append(`
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                                    ${question.text}
                                </button>
                            </h2>
                            <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#accordionSelectedQuestions">
                                <div class="accordion-body">
                                    Type: ${formattedType}  
                                </div>
                            </div>
                        </div>
                    `);
                    index++;
        });
    }

    // Function to toggle the visibility of options in the accordion item
    function toggleOptions(index) {
        const optionsDiv = $(`#options-${index}`);
        const optionsButton = $(`[data-question-id='${index}']`);
    
        // Toggle options visibility
        optionsDiv.toggle();
    
        // Change button text based on visibility
        if (optionsDiv.is(":visible")) {
            optionsButton.text("Hide Options");
        } else {
            optionsButton.text("Show Options");
        }
    }
    
    

    // Remove a question from the selected list
    window.removeQuestion = function(id) {
        delete selectedQuestions[id];
        $(`.module-question[value="${id}"]`).prop('checked', false);
        updateSelectedQuestions();
    }

    // Show options for a specific question
    window.showOptions = function(element) {
        const questionId = $(element).data('question-id');

        $('#options-list').html(`Options for question ${questionId}`);
        $('#optionsModal').modal('show');
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
                            data-bs-target="#collapse${facultyId}" aria-expanded="false"
                            aria-controls="collapse${facultyId}">
                            <i class="feather icon-award me-2"></i>${facultyData.facultyName}
                        </button>
                    </h2>
                </div>
                <div id="collapse${facultyId}" class="collapse" aria-labelledby="heading${facultyId}"
                    data-parent="#facultyaccordionoutline">
                    <div class="card-body">
                        ${facultyData.departments.map(department => {
                            return `
                                <div class="department-card">
                                    <h6>${department.departmentName}</h6>
                                    <div class="department-programs" id="programs-${facultyId}-${department.departmentId}">
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


        const departmentSelection = $('#department-selection');
        const departmentCheckboxesDiv = $('#department-checkboxes');


        $('#program-selection').empty();
        $('#program-selection').hide();


        const url = window.routes.facultyDepartment.replace(':id', facultyId);
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

            const url = window.routes.departmentPrograms.replace(':id', departmentId);
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
        console.log(specificFaculties);
        populateFacultyAccordion();

        $('#faculty-dropdown').val('').prop('selected', true);
        $('#department-checkboxes').empty();
        $('#program-selection').empty();
        $('#department-selection').hide();
        $('#program-selection').hide();

        $('#addSpecificFacultyModal').modal('hide');
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
                        data-bs-target="#collapse${courseId}" aria-expanded="false"
                        aria-controls="collapse${courseId}">
                        <i class="feather icon-award me-2"></i>${courseData.courseName}
                    </button>
                </h2>
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
        console.log(specificCourses);
        populateCourseAccordion();
        $('#addSpecificCourseModal').modal('hide');
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
        url: window.routes.storeQuestionnaire,  // Update with actual route if necessary
        data: formData,
        success: function(response) {
            swal('Success!', 'Questionnaire created successfully!', 'success')
                .then(() => {
                    window.location.href = window.routes.createQuestionnaire;  // Redirect on success
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
