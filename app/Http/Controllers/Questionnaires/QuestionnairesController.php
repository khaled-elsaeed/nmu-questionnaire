<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionModule;
use App\Models\Faculty;
use App\Models\QuestionnaireTarget;
use App\Models\Questionuse;
use App\Models\CourseDetail;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class QuestionnairesController extends Controller
{
    public function index()
    {
        try {
            $questionnaires = Questionnaire::get();
            return view('admin.questionnaires.index', compact('questionnaires'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve questionnaires in index method: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve questionnaires.'], 500);
        }
    }

    public function create()
{
    try {
        $faculties = Faculty::pluck('name', 'id'); 
        $modules = QuestionModule::pluck('name', 'id'); 

        $courses = Course::all(); 

        return view('admin.questionnaires.create', compact('modules', 'faculties', 'courses'));
    } catch (\Exception $exception) {
        Log::error('Failed to retrieve data for questionnaire creation: ' . $exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Unable to retrieve data for questionnaire creation.'], 500);
    }
}


public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'description' => 'string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'questions' => 'required|array|min:1',
        'questions.*' => 'exists:questions,id',
        'audience_data' => 'required|string',
        'module_id' => 'required|integer',
        'audience' => 'required|array|min:1',
    ]);

    try {
        \DB::transaction(function () use ($request) {
            // Create the questionnaire
            $questionnaire = Questionnaire::create($request->only(['title', 'description', 'start_date', 'end_date', 'module_id']));
            $questionnaire->questions()->attach($request->questions, ['display_order' => 0, 'is_mandatory' => false]);

            // Decode audience data
            $audiences = json_decode($request->input('audience_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON format in audience_data");
            }

            Log::info('Audience data:', $audiences);

            foreach ($audiences as $audienceKey => $audienceGroup) {
                foreach ($audienceGroup as $audience) {
                    if (!isset($audience['role_name'])) {
                        Log::error('Missing role_name in audience data: ' . json_encode($audience));
                        continue;
                    }

                    Log::info('Processing audience with role_name: ' . $audience['role_name'], $audience);

                    if ($audience['role_name'] === 'student') {
                        // Check if faculties are provided and process them
                        if (isset($audience['faculties']) && is_array($audience['faculties']) && !empty($audience['faculties'])) {
                            foreach ($audience['faculties'] as $facultyData) {
                                // Ensure faculty ID exists before using it
                                $facultyId = isset($facultyData['id']) && $facultyData['id'] !== 'all' ? $facultyData['id'] : null;

                                // Check if departments are provided
                                if (empty($facultyData['departments'])) {
                                    QuestionnaireTarget::create([
                                        'questionnaire_id' => $questionnaire->id,
                                        'faculty_id' => $facultyId,
                                        'dept_id' => null,
                                        'program_id' => null,
                                        'role_name' => $audience['role_name'],
                                        'level' => $audience['level'] ?? null,
                                        'scope_type' => $audience['scope_type'],
                                    ]);
                                } else {
                                    // Process departments and programs
                                    foreach ($facultyData['departments'] as $departmentData) {
                                        $deptId = $departmentData['id'];
                                        if (empty($departmentData['programs'])) {
                                            QuestionnaireTarget::create([
                                                'questionnaire_id' => $questionnaire->id,
                                                'faculty_id' => $facultyId,
                                                'dept_id' => $deptId,
                                                'program_id' => null,
                                                'role_name' => $audience['role_name'],
                                                'level' => $audience['level'] ?? null,
                                                'scope_type' => $audience['scope_type'],
                                            ]);
                                        } else {
                                            foreach ($departmentData['programs'] as $programData) {
                                                $programId = $programData['id'];
                                                QuestionnaireTarget::create([
                                                    'questionnaire_id' => $questionnaire->id,
                                                    'faculty_id' => $facultyId,
                                                    'dept_id' => $deptId,
                                                    'program_id' => $programId,
                                                    'role_name' => $audience['role_name'],
                                                    'level' => $audience['level'] ?? null,
                                                    'scope_type' => $audience['scope_type'],
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // If faculties are empty, check for courses and process them
                        elseif (isset($audience['courses']) && is_array($audience['courses']) && !empty($audience['courses'])) {
                            foreach ($audience['courses'] as $course) {
                                // Check if the course has a valid id
                                if (isset($course['id'])) {
                                    $this->createQuestionnaireForCourse($course, $questionnaire->id, $audience['role_name'], $audience['level'] ?? null);
                                } else {
                                    Log::error('Missing course ID for audience: ' . json_encode($audience));
                                }
                            }
                        } else {
                            Log::error('Invalid or missing faculties and courses for audience: ' . json_encode($audience));
                        }
                    } elseif ($audience['role_name'] === 'teaching_assistant') {
                        // Global scope for teaching assistants
                        QuestionnaireTarget::create([
                            'questionnaire_id' => $questionnaire->id,
                            'faculty_id' => null,
                            'dept_id' => null,
                            'program_id' => null,
                            'role_name' => $audience['role_name'],
                            'level' => $audience['level'] ?? null,
                            'scope_type' => 'global',
                        ]);
                    } elseif ($audience['role_name'] === 'staff') {
                        // Global scope for staff
                        QuestionnaireTarget::create([
                            'questionnaire_id' => $questionnaire->id,
                            'faculty_id' => null,
                            'dept_id' => null,
                            'program_id' => null,
                            'role_name' => $audience['role_name'],
                            'level' => $audience['level'] ?? null,
                            'scope_type' => 'global',
                        ]);
                    } else {
                        Log::error('Invalid role or faculties/courses data missing for audience: ' . json_encode($audience));
                    }
                }
            }
        });

        return response()->json(['success' => true], 200);
    } catch (\Exception $e) {
        Log::error('Error creating questionnaire: ' . $e->getMessage());
        return response()->json(
            [
                'message' => 'There was an error creating the questionnaire. Please try again later.',
                'error' => $e->getMessage(),
            ],
            500
        );
    }
}


    
public function createQuestionnaireForCourse($course, $questionnaireId, $roleName, $level = null)
{
    // Log the start of the function
    Log::info('Creating questionnaire for course', [
        'course_id' => $course['id'],
        'questionnaire_id' => $questionnaireId,
        'role_name' => $roleName,
        'level' => $level,
    ]);

    if ($course['id'] === 'all') {

        $courses = Course::all(); 

        Log::info('Processing all courses for the questionnaire', [
            'questionnaire_id' => $questionnaireId,
            'role_name' => $roleName,
            'level' => $level
        ]);

        foreach ($courses as $course) {

            $courseDetail = CourseDetail::where('course_id', $course->id)->first(); 

            if ($courseDetail) {
                Log::info('Course detail found for all courses', [
                    'course_id' => $course->id,
                    'course_detail_id' => $courseDetail->id
                ]);

                QuestionnaireTarget::create([
                    'questionnaire_id' => $questionnaireId,
                    'faculty_id' => null,
                    'dept_id' => null,
                    'program_id' => null,
                    'course_detail_id' => $courseDetail->id, 
                    'role_name' => $roleName,
                    'level' => $level,
                    'scope_type' => 'local', 
                ]);

                Log::info('Questionnaire target created for course', [
                    'questionnaire_id' => $questionnaireId,
                    'course_detail_id' => $courseDetail->id,
                    'role_name' => $roleName,
                    'level' => $level
                ]);
            } else {
                Log::error('Course detail not found for course', [
                    'course_id' => $course->id
                ]);
            }
        }
    } else {
        // If the course ID is not 'all', process the specific course
        $courseDetail = CourseDetail::where('course_id', $course['id'])->first(); 

        // Log the course detail retrieval
        if ($courseDetail) {
            Log::info('Course detail found', [
                'course_id' => $course['id'],
                'course_detail_id' => $courseDetail->id
            ]);

            // Proceed to create the questionnaire target for the specific course
            QuestionnaireTarget::create([
                'questionnaire_id' => $questionnaireId,
                'faculty_id' => null,
                'dept_id' => null,
                'program_id' => null,
                'course_detail_id' => $courseDetail->id, // Now using the actual course detail ID
                'role_name' => $roleName,
                'level' => $level,
                'scope_type' => 'local', // Courses are generally local scope
            ]);

            Log::info('Questionnaire target created for course', [
                'questionnaire_id' => $questionnaireId,
                'course_detail_id' => $courseDetail->id,
                'role_name' => $roleName,
                'level' => $level
            ]);
        } else {
            // Handle the case where the course detail does not exist
            Log::error('Course detail not found for course', [
                'course_id' => $course['id']
            ]);
        }
    }
}


    
    

public function show($id)
{
    return Questionnaire::findOrFail($id);
}

public function update(Request $request, $id)
{
    $questionnaire = Questionnaire::findOrFail($id);
    $questionnaire->update($request->all());
    return $questionnaire;
}

public function destroy($id)
{
    Questionnaire::destroy($id);
    return response()->noContent();
}



public function results()
    {
        try {
            $questionnaires = Questionnaire::get();
            return view('admin.questionnaires.result', compact('questionnaires'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve questionnaires in index method: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve questionnaires.'], 500);
        }
    }


    public function showStats($id)
    {
        $questionnaire = Questionnaire::with(['questions.answers', 'questions.options'])->findOrFail($id);
    
        // Initialize stats array
        $stats = [
            'total_responses' => 0,
            'text_based_responses' => 0,
            'scaled_text_avg' => 0,
            'scaled_numerical_avg' => 0,
            'scale_avg' => 0,
            'text_based_answers' => [],
            'scaled_text_answers' => [],
            'scaled_numerical_answers' => [],
            'scale_answers' => [],
            'scaled_text_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], // Breakdown for scaled_text counts
            'scaled_numerical_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], // Breakdown for numerical counts
        ];
    
        $question_stats = []; // Array to hold stats for each question
    
        foreach ($questionnaire->questions as $question) {
            $responses = $question->answers->groupBy('response.user_id');
            $question_stat = [
                'total_responses' => $responses->count(),
                'text_based_responses' => 0,
                'scaled_text_avg' => 0,
                'scaled_numerical_avg' => 0,
                'scale_avg' => 0,
                'text_based_answers' => [],
                'scaled_text_answers' => [],
                'scaled_numerical_answers' => [],
                'scale_answers' => [],
                'scaled_text_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], 
                'scaled_numerical_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], 
            ];
    
            foreach ($responses as $response) {
                switch ($question->type) {
                    case 'text_based':
                        $question_stat['text_based_responses']++;
                        $question_stat['text_based_answers'][] = $response->pluck('answer_text')->first();
                        break;
    
                    case 'scaled_text':
                        $answers = $response->pluck('answer_text')->first();
                        $question_stat['scaled_text_answers'][] = $answers;
                        $text_scale = [
                            'Bad' => 1, 'سيء' => 1,
                            'Fair' => 2, 'مقبول' => 2,
                            'Good' => 3, 'جيد' => 3,
                            'Very Good' => 4, 'جيد جدا' => 4,
                            'Excellent' => 5, 'ممتاز' => 5
                        ];
                        $numericAnswer = $text_scale[$answers] ?? 0;
                        $question_stat['scaled_text_counts'][$numericAnswer]++;
                        break;
    
                    case 'scaled_numerical':
                        $answers = $response->pluck('answer_text')->first();
                        $question_stat['scaled_numerical_answers'][] = (int) $answers;
                        $numericAnswer = (int) $answers;
                        $question_stat['scaled_numerical_counts'][$numericAnswer]++;
                        break;
    
                    case 'scale':
                        $answers = $response->pluck('answer_text')->first();
                        $question_stat['scale_answers'][] = (int) $answers;
                        break;
                }
            }
    
            if (!empty($question_stat['scaled_numerical_answers'])) {
                $question_stat['scaled_numerical_avg'] = array_sum($question_stat['scaled_numerical_answers']) / count($question_stat['scaled_numerical_answers']);
            }
    
            if (!empty($question_stat['scale_answers'])) {
                $question_stat['scale_avg'] = array_sum($question_stat['scale_answers']) / count($question_stat['scale_answers']);
            }
    
            if (!empty($question_stat['scaled_text_answers'])) {
                $text_scale = ['Bad' => 1, 'سيء' => 1, 'Fair' => 2, 'مقبول' => 2, 'Good' => 3, 'جيد' => 3, 'Very Good' => 4, 'جيد جدا' => 4, 'Excellent' => 5, 'ممتاز' => 5];
                $numericAnswers = array_map(function ($answer) use ($text_scale) {
                    return $text_scale[$answer] ?? 0;
                }, $question_stat['scaled_text_answers']);
                $question_stat['scaled_text_avg'] = array_sum($numericAnswers) / count($numericAnswers);
            }
    
            $question_stats[] = [
                'question' => $question->text,
                'stats' => $question_stat
            ];
    
            // Update global stats
            $stats['total_responses'] += $question_stat['total_responses'];
            $stats['text_based_responses'] += $question_stat['text_based_responses'];
            $stats['scaled_text_avg'] += $question_stat['scaled_text_avg'];
            $stats['scaled_numerical_avg'] += $question_stat['scaled_numerical_avg'];
            $stats['scale_avg'] += $question_stat['scale_avg'];
        }
    
        // Return stats to the view
        return view('admin.questionnaires.stats', compact('questionnaire', 'stats', 'question_stats'));
    }
    
    

    
    



}
