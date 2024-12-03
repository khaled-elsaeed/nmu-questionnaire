<?php

namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\QuestionnaireTarget;

use App\Models\Response;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Option;
use App\Models\StudentDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ResponderQuestionnaireController extends Controller
{
    
    public function show($id)
    {
        $target = QuestionnaireTarget::findOrFail($id);

        $questionnaire = Questionnaire::with('questions.options')
            ->where('id', $target->questionnaire_id)
            ->where('is_active', true)
            ->firstOrFail();

        return view('responder.questionnaire.show', [
            'questionnaire' => $questionnaire,
            'targetId' => $target->id, 
        ]);
    }


    public function submit(Request $request, $questionnaireId)
{
    $validatedData = $request->validate([
        'answers.*' => 'required', 
        'target_id' => 'required|exists:questionnaire_targets,id', 
    ]);

    $targetId = $request->input('target_id');
    DB::beginTransaction();

    try {
        // Create a new response record
        $response = Response::create([
            'questionnaire_target_id' => $targetId,
            'user_id' => auth()->id(),
        ]);

        // Iterate through answers and save them based on question type
        foreach ($validatedData['answers'] as $questionId => $answer) {
            $question = Question::findOrFail($questionId);

            if ($question->type == 'multiple_choice') {
                $option = Option::findOrFail($answer);
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'option_id' => $option->id,
                    'answer_text' => null, // Explicitly set to NULL
                ]);
            } elseif ($question->type == 'text_based') {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'answer_text' => $answer,
                ]);
            } elseif ($question->type == 'scaled_numerical' || $question->type == 'scaled_text') {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'answer_text' => $answer,
                ]);
            }
        }

        DB::commit();

        // Return a success JSON response
        return response()->json([
            'success' => true,
            'message' => 'Your responses have been submitted successfully!',
            'response_id' => $response->id,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        // Log the error for debugging
        Log::error('Questionnaire Submission Error: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'questionnaire_id' => $questionnaireId,
            'target_id' => $targetId,
        ]);

        // Return a failure JSON response
        return response()->json([
            'success' => false,
            'message' => 'There was an issue submitting your responses. Please try again.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function history()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $availableQuestionnaires = $role === 'student' 
            ? $this->getAvailableQuestionnaireTargets($user->id)
            : collect();
        
        // Return the view
        return view('responder.questionnaire.history', compact('availableQuestionnaires'));
    }
    
    

    



    

    public function getAvailableQuestionnaireTargets($userId) 
    { 
        $studentDetails = $this->getStudentDetails($userId); 
     
        try { 
            $results = QuestionnaireTarget::with([
                'questionnaire', 
                'courseDetail.course',
                'responses' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->where('role_name', 'student')
            ->where(function ($query) use ($studentDetails) { 
                $query->where('scope_type', 'global')
                      ->orWhere(function ($query) use ($studentDetails) { 
                          $query->where('scope_type', 'local')
                                ->where(function ($subQuery) use ($studentDetails) { 
                                    $subQuery->whereNull('faculty_id')
                                             ->orWhere('faculty_id', $studentDetails->faculty_id);
                                })
                                ->where(function ($subQuery) use ($studentDetails) { 
                                    $subQuery->whereNull('dept_id')
                                             ->orWhere('dept_id', $studentDetails->department_id);
                                })
                                ->where(function ($subQuery) use ($studentDetails) { 
                                    $subQuery->whereNull('program_id')
                                             ->orWhere('program_id', $studentDetails->program_id);
                                });
                      }); 
            })
            ->where(function ($query) use ($userId) { 
                $query->whereHas('responses', function ($subQuery) use ($userId) { 
                    $subQuery->where('user_id', $userId); 
                })
                ->orWhereHas('questionnaire', function ($subQuery) { 
                    $subQuery->where('end_date', '<', now()); 
                }); 
            })
            ->where(function ($query) use ($studentDetails) { 
                $query->whereNull('course_detail_id')
                      ->orWhereHas('courseEnrollments', function ($subQuery) use ($studentDetails) { 
                          $subQuery->where('student_id', $studentDetails->id); 
                      }); 
            })
            ->limit(25)
            ->get(); 
     
        } catch (\Exception $e) { 
            Log::error('Error getting available questionnaire targets', [ 
                'user_id' => $userId, 
                'student_id' => $studentDetails->id, 
                'exception' => $e, 
            ]); 
     
            throw $e; 
        } 
     
        // Add response_exists attribute using a more efficient method
        $results->transform(function ($result) use ($userId) {
            $result->response_exists = $result->responses->contains('user_id', $userId);
            return $result;
        });
     
        return $results; 
    }
    
    

    private function getStudentDetails($userId)
    {
        return StudentDetail::where('user_id', $userId)->first();
    }
    


    
}
