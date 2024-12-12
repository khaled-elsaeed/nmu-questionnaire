<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\Course;
use App\Models\CourseDetail;
use App\Models\QuestionnaireTarget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionnaireService
{
    public function store($request)
{
    try {
        // Validation
        $request->validate([
            'title' => 'required|string|unique:questionnaires,title',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'questions' => 'required|array|min:1',
            'questions.*' => 'exists:questions,id',
            'audience_data' => 'required|string',
            'module_id' => 'required|integer',
            'audience' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            Log::info('Sanitizing input data.', $request->only(['title', 'description', 'start_date', 'end_date', 'module_id']));
            $sanitizedData = $this->sanitizeInput($request->only(['title', 'description', 'start_date', 'end_date', 'module_id']));

            $questionnaire = Questionnaire::create($sanitizedData);

            $questionnaire->questions()->attach($request->questions, [
                'display_order' => 0,
                'is_mandatory' => false,
            ]);

            $audiences = json_decode($request->input('audience_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON format in audience_data.');
                throw new \Exception("Invalid JSON format in audience_data");
            }

            $this->processAudienceData($audiences, $questionnaire, $request);
        });

        return response()->json(['success' => true], 200);

    } catch (ValidationException $e) {
        // Handling validation errors explicitly
        Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // General exception handling
        Log::error('Error creating questionnaire: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(
            [
                'message' => 'There was an error creating the questionnaire. Please try again later.',
                'error' => $e->getMessage(),
            ],
            500
        );
    }
}


    private function sanitizeInput(array $data)
    {
        $data['title'] = filter_var(trim($data['title']), FILTER_SANITIZE_STRING);
        $data['description'] = isset($data['description']) ? filter_var(trim($data['description']), FILTER_SANITIZE_STRING) : null;
        $data['start_date'] = filter_var($data['start_date'], FILTER_SANITIZE_STRING);
        $data['end_date'] = filter_var($data['end_date'], FILTER_SANITIZE_STRING);
        $data['module_id'] = filter_var($data['module_id'], FILTER_SANITIZE_NUMBER_INT);

        return $data;
    }

    private function processAudienceData($audiences, $questionnaire, $request)
    {
        foreach ($audiences as $audienceKey => $audienceGroup) {
            foreach ($audienceGroup as $audience) {
                if (!isset($audience['role_name'])) {
                    Log::error('Missing role_name in audience data.', $audience);
                    continue;
                }

                if ($audience['role_name'] === 'student') {
                    if (isset($audience['faculties']) && is_array($audience['faculties']) && !empty($audience['faculties'])) {
                        $this->processFaculties($audience['faculties'], $questionnaire, $audience);
                    } elseif (isset($audience['courses']) && is_array($audience['courses']) && !empty($audience['courses'])) {
                        $this->processCourses($audience['courses'], $questionnaire, $audience);
                    } else {
                        Log::error('Invalid or missing faculties and courses for audience.', $audience);
                    }
                } elseif ($audience['role_name'] === 'teaching_assistant' || $audience['role_name'] === 'staff') {
                    $this->processGlobalScope($audience, $questionnaire);
                } else {
                    Log::error('Invalid role or faculties/courses data missing for audience.', $audience);
                }
            }
        }
    }

    private function processFaculties($faculties, $questionnaire, $audience)
    {
        foreach ($faculties as $facultyData) {
            $facultyId = isset($facultyData['id']) && $facultyData['id'] !== 'all' ? $facultyData['id'] : null;

            if ($facultyId === 'all') {
                $faculties = Course::distinct()->pluck('faculty_id');
                if ($faculties->isEmpty()) {
                    Log::error("No faculties found for 'all' option.");
                    throw new ValidationException("No faculties found for the selected 'all' option.");
                }
            }

            if (empty($facultyData['programs'])) {
                $this->createQuestionnaireTarget($questionnaire, $audience, $facultyId, null,);
            }else{
                $this->createQuestionnaireTarget($questionnaire, $audience, $facultyId, $facultyData['programs']);
            }
            
        }
    }
   

    private function processCourses($courses, $questionnaire, $audience)
    {
        foreach ($courses as $course) {
            if ($course['id'] === 'all') {
                // Fetch all courses
                $allCourses = CourseDetail::all();
                
                // Check if no courses are found
                if ($allCourses->isEmpty()) {
                    Log::error('No courses found for the "all" option', [
                        'questionnaire_id' => $questionnaire->id
                    ]);
                    
                    throw ValidationException::withMessages([
                        'courses' => ['No courses found for the selected "all" option.']
                    ]);
                }
                
                // Iterate over all courses to create questionnaires
                foreach ($allCourses as $allCourse) {
                    $this->createQuestionnaireForCourse(
                        $allCourse,
                        $questionnaire->id,
                        $audience['role_name'],
                        $audience['level'] ?? null
                    );
                }
            } elseif (isset($course['id'])) {
                // Handle individual courses
                $this->createQuestionnaireForCourse(
                    $course,
                    $questionnaire->id,
                    $audience['role_name'],
                    $audience['level'] ?? null
                );
            } else {
                // Log an error if the course ID is missing
                Log::error('Missing course ID for audience', [
                    'audience' => $audience,
                    'questionnaire_id' => $questionnaire->id
                ]);
            }
        }
    }
    

    private function processGlobalScope($audience, $questionnaire)
    {
        QuestionnaireTarget::create([
            'questionnaire_id' => $questionnaire->id,
            'faculty_id' => null,
            'program_id' => null,
            'role_name' => $audience['role_name'],
            'level' => $audience['level'] ?? null,
            'scope_type' => 'global',
        ]);
    }

    private function createQuestionnaireTarget($questionnaire, $audience, $facultyId, $programId)
    {
        QuestionnaireTarget::create([
            'questionnaire_id' => $questionnaire->id,
            'faculty_id' => $facultyId,
            'program_id' => $programId,
            'role_name' => $audience['role_name'],
            'level' => $audience['level'] ?? null,
            'scope_type' => $audience['scope_type'],
        ]);
    }

    public function createQuestionnaireForCourse($course, $questionnaireId, $roleName, $level = null)
    {
        if (($course['id'] ?? null) === 'all') {
            $courses = Course::whereHas('courseDetails', function ($query) {
                $query->where('term', 'spring')->where('academic_year', '2024/2024');
            })->get();

            if ($courses->isEmpty()) {
                Log::error('No courses found for the "all" option during course processing', [
                    'questionnaire_id' => $questionnaireId,
                ]);
                throw new ValidationException("No courses found for the selected 'all' option.");
            }

            foreach ($courses as $courseItem) {
                $courseDetail = $courseItem
                    ->courseDetails()
                    ->where('term', 'spring')
                    ->where('academic_year', 2024)
                    ->first();

                if ($courseDetail) {
                    try {
                        QuestionnaireTarget::create([
                            'questionnaire_id' => $questionnaireId,
                            'faculty_id' => null,
                            'program_id' => null,
                            'course_detail_id' => $courseDetail->id,
                            'role_name' => $roleName,
                            'level' => $level,
                            'scope_type' => 'local',
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create questionnaire target for course', [
                            'error' => $e->getMessage(),
                            'course_detail_id' => $courseDetail->id,
                        ]);
                    }
                } else {
                    Log::warning('No course detail found for course', [
                        'course_id' => $courseItem->id,
                    ]);
                }
            }
        } else {
            $courseDetail = CourseDetail::where('course_id', $course['id'] ?? null)->first();

            if ($courseDetail) {
                try {
                    QuestionnaireTarget::create([
                        'questionnaire_id' => $questionnaireId,
                        'faculty_id' => null,
                        'program_id' => null,
                        'course_detail_id' => $courseDetail->id,
                        'role_name' => $roleName,
                        'level' => $level,
                        'scope_type' => 'local',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create questionnaire target for specific course', [
                        'error' => $e->getMessage(),
                        'course_id' => $course['id'],
                    ]);
                }
            } else {
                Log::error('No course detail found for specific course', [
                    'course_id' => $course['id'],
                ]);
            }
        }
    }


    public function showStats($questionnaireTargetId, $returnType = 'view')
    {
        // Log the initial questionnaireTargetId
        Log::info('Fetching stats for Questionnaire Target ID: ' . $questionnaireTargetId);
    
        // Total responses (distinct users)
        $totalResponses = DB::table('responses')
            ->where('questionnaire_target_id', $questionnaireTargetId)
            ->distinct('user_id')
            ->count();
    
        // Log the total responses
        Log::info('Total Responses (Distinct Users): ' . $totalResponses);
    
        $stats = [
            'total_responses' => $totalResponses,
            'questions' => [], // Store questions with stats here
            'overall_average' => 0, // Placeholder for overall average
        ];
    
        $totalScore = 0; // This will accumulate the total score for calculating overall average
        $questionsCount = 0; // Counter for questions with calculable averages
    
        // Fetch all questions for the questionnaire target
        $questions = DB::table('questions')
            ->join('questionnaire_questions', 'questions.id', '=', 'questionnaire_questions.question_id')
            ->join('questionnaires', 'questionnaire_questions.questionnaire_id', '=', 'questionnaires.id')
            ->join('questionnaire_targets', 'questionnaires.id', '=', 'questionnaire_targets.questionnaire_id')
            ->where('questionnaire_targets.id', $questionnaireTargetId)
            ->select('questions.id', 'questions.text', 'questions.type')
            ->get();
    
        // Log the questions being processed
        Log::info('Fetched ' . $questions->count() . ' questions for Questionnaire Target ID: ' . $questionnaireTargetId);
    
        foreach ($questions as $question) {
            // Create a structure for each question's stats
            $questionStats = [
                'id' => $question->id,
                'text' => $question->text,
                'type' => $question->type,
                'stats' => null, // Placeholder for the calculated stats
            ];
    
            // Calculate stats based on the question type and associate with the question
            switch ($question->type) {
                case 'text_based':
                    $questionStats['stats'] = $this->calculateTextBasedStats($question->id, $questionnaireTargetId);
                    break;
    
                case 'multiple_choice':
                    $questionStats['stats'] = $this->calculateMultipleChoiceStats($question->id, $questionnaireTargetId);
                    if (isset($questionStats['stats']['percentages'])) {
                        $totalScore += array_sum($questionStats['stats']['percentages']); // Adding percentages as score
                        $questionsCount++;
                    }
                    break;
            }
    
            $stats['questions'][] = $questionStats;
        }
    
        // Calculate overall average (as the average of all question averages)
        if ($questionsCount > 0) {
            $stats['overall_average'] = round($totalScore / $questionsCount, 2); // Calculate average
        }
    
        $questionnaire = QuestionnaireTarget::where('id', $questionnaireTargetId)->first();
    
        // Decide the return type based on the $returnType argument
        if ($returnType === 'view') {
            // Return the stats to the view
            return view('admin.questionnaires.stats', compact('questionnaire', 'stats'));
        } elseif ($returnType === 'data') {
            // Return just the data as a JSON response or raw data
            return $stats;
        }
    
        // Default case: return a JSON response
        return response()->json(['error' => 'Invalid return type specified']);
    }
    

    

    
    private function calculateTextBasedStats($questionId, $questionnaireTargetId)
    {
        
    
        // Fetch text-based answers with counts
        $result = DB::table('answers')
            ->join('responses', 'answers.response_id', '=', 'responses.id')
            ->where('answers.question_id', $questionId)
            ->where('responses.questionnaire_target_id', $questionnaireTargetId)
            ->select('answer_text')
            ->groupBy('answer_text')
            ->get();
    
        // Log the result
        Log::info('Text-Based Stats Result: ', $result->toArray());
    
        return $result;
    }
    

    
    private function calculateMultipleChoiceStats($questionId, $questionnaireTargetId)
    {
        

        // Fetch all possible options for the question in their database order
        $options = DB::table('options')
            ->where('question_id', $questionId)
            ->orderBy('id') // Ensures options are fetched in their natural database order
            ->select('id', 'text')
            ->get()
            ->keyBy('text');

        Log::info('Multiple Choice Options: ', $options->toArray());

        // Count the answers grouped by options
        $answers = DB::table('options')
            ->join('answers', 'options.id', '=', 'answers.option_id')
            ->join('responses', 'answers.response_id', '=', 'responses.id') // INNER JOIN with responses
            ->where('options.question_id', $questionId)
            ->where('responses.questionnaire_target_id', $questionnaireTargetId)
            ->select('options.text as option_text', DB::raw('COUNT(answers.id) as option_count'))
            ->groupBy('options.text')
            ->pluck('option_count', 'option_text')
            ->toArray();

        

        // Prepare the counts and percentages while preserving the database order
        $counts = [];
        $percentages = [];
        $totalResponses = array_sum($answers);

        foreach ($options as $optionText => $option) {
            // Ensure all options are represented, even if not answered
            $counts[$optionText] = $answers[$optionText] ?? 0;
        }

        if ($totalResponses > 0) {
            foreach ($counts as $optionText => $count) {
                $percentages[$optionText] = round(($count / $totalResponses) * 100, 2);
            }
        } else {
            foreach ($counts as $optionText => $count) {
                $percentages[$optionText] = 0;
            }
        }

        
        

        return [
            'total_responses' => $totalResponses,
            'counts' => $counts,
            'percentages' => $percentages,
        ];
    }

    
    

}
