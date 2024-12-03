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

        #accordionoutline, #facultyaccordionoutline, #courseaccordionoutline {
            display: flex;
            flex-wrap: wrap;  /* Allow the cards to wrap to the next line if there's not enough space */
            gap: 20px;  /* Adjust the space between cards */
        }

        .card {
            margin: 10px 0 10px 0;
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
                            <textarea class="form-control" id="description" name="description"></textarea>
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

                    </div>
                    <div class="form-section">
                        <h6>Select Question Module</h6>
                        <select class="form-select" id="module_select" name="module_id" required>
                            <option value="">Choose a module...</option>
                            @foreach ($modules as $id => $name)
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
                            <input class="form-check-input" type="checkbox" name="audience[]" value="student"
                                id="students">
                            <label class="form-check-label" for="students">Students</label>
                        </div>
                        <div id="faculty-options" style="display: none; padding-left: 1.5rem;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="faculty_option" value="all_faculty"
                                    id="all_faculty">
                                <label class="form-check-label" for="all_faculty">All Faculties</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="faculty_option"
                                    value="specific_faculty" id="specific_faculty">
                                <label class="form-check-label" for="specific_faculty">Specific Faculty</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="faculty_option" value="all_courses"
                                    id="all_courses">
                                <label class="form-check-label" for="all_courses">All Courses</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="faculty_option" value="specific_course"
                                    id="specific_course">
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
                            <input class="form-check-input" type="checkbox" name="audience[]" value="teaching_assistant"
                                id="teaching_assistant">
                            <label class="form-check-label" for="teaching_assistant">Teaching Assistants</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="audience[]" value="staff"
                                id="staff">
                            <label class="form-check-label" for="staff">Staff</label>
                        </div>
                        <div class="invalid-feedback">Please select at least one target audience.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Questionnaire</button>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel"
        aria-hidden="true">
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

    <div class="modal fade" id="addSpecificFacultyModal" tabindex="-1" aria-labelledby="addSpecificFacultyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSpecificFacultyModalLabel">Select Faculties, Departments, and Programs
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="faculty-dropdown">Select Faculty</label>
                        <select class="form-select" id="faculty-dropdown" name="course" style="margin: 10px 0 10px 0;">
                            <option value="" selected disabled>Select a faculty</option>
                            @foreach ($faculties as $id => $name)
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
                    <button type="button" class="btn btn-primary" id="save-specific-faculty-selections">Save
                        Selections</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel"
        aria-hidden="true">
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

    <div class="modal fade" id="addSpecificCourseModal" tabindex="-1" aria-labelledby="addSpecificCourseModalLabel"
        aria-hidden="true">
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
                            @foreach ($courses as $course)
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
    <script src="{{ asset('js/pages/questionnaire-create.js') }}"></script>

    <script>
        window.routes = {
            facultyDepartment: "{{ route('faculties.departments', ':id') }}",
            departmentPrograms: "{{ route('departments.programs', ':id') }}",
            storeQuestionnaire: "{{ route('admin.questionnaires.store') }}",
            createQuestionnaire: "{{ route('admin.questionnaires.create') }}",
            questionModuleQuestions: "{{ route('admin.question-modules.questions', ':id') }}"
        };
    </script>
@endsection
