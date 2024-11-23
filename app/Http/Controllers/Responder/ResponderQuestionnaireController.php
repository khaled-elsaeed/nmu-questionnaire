<?php

namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
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
        $questionnaire = Questionnaire::with('questions.options')->findOrFail($id);

        if (!$questionnaire->is_active) {
            return redirect()->route('responder.questionnaires.index')
                ->with('error', 'This questionnaire is no longer available.');
        }

        return view('responder.questionnaire.show', compact('questionnaire'));
    }

    public function submit(Request $request, $questionnaireId)
    {
        $validatedData = $request->validate([
            'answers.*' => 'required',  
        ]);

        DB::beginTransaction();

        try {
            $response = Response::create([
                'questionnaire_id' => $questionnaireId,
                'user_id' => auth()->id(),
            ]);

            foreach ($validatedData['answers'] as $questionId => $answer) {
                $question = Question::findOrFail($questionId);

                if ($question->type == 'multiple_choice') {
                    $option = Option::findOrFail($answer);
                    Answer::create([
                        'response_id' => $response->id,
                        'question_id' => $questionId,
                        'option_id' => $option->id,
                        'answer_text' => null,
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

            return redirect()->route('responder.home')->with('success', 'Your responses have been submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('responder.questionnaire.show', $questionnaireId)
                ->with('error', 'There was an issue submitting your responses. Please try again.');
        }
    }

    public function history()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Get both answered and available questionnaires
        $answeredQuestionnaires = $this->getAnsweredQuestionnaires($user->id);
        $availableQuestionnaires = $role === 'student' 
            ? $this->getAvailableQuestionnaires($user->id)
            : collect();

        // Prepare the data for the view
        $questionnaireData = [
            'answered' => $answeredQuestionnaires->map(function ($questionnaire) {
                return [
                    'questionnaire' => $questionnaire,
                    'status' => 'answered',
                    'response_date' => $questionnaire->responses->where('user_id', auth()->id())->first()->created_at
                ];
            }),
            'available' => $availableQuestionnaires->map(function ($questionnaire) {
                return [
                    'questionnaire' => $questionnaire,
                    'status' => 'pending',
                    'response_date' => null
                ];
            })
        ];

        Log::debug('Questionnaire Data:', $questionnaireData);
        
        return view('responder.questionnaire.history', compact('questionnaireData'));
    }

    
    private function getAnsweredQuestionnaires($userId)
    {
        return Questionnaire::whereHas('responses', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['responses' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->orderBy('end_date', 'desc')
        ->get();
    }

    
    private function getAvailableQuestionnaires($userId)
    {
        $studentDetails = $this->getStudentDetails($userId);
        
        if (!$studentDetails) {
            return collect();
        }

        // Get all targeted questionnaires
        $targetedQuestionnaires = Questionnaire::whereIn('id', function ($query) use ($studentDetails) {
            $query->select('questionnaire_id')
                ->from('questionnaire_targets')
                ->where('role_name', 'student')
                ->where(function ($subQuery) use ($studentDetails) {
                    $subQuery->orWhere(function ($q) use ($studentDetails) {
                        $q->where('scope_type', 'Local')
                          ->where(function ($filter) use ($studentDetails) {
                              $filter->where('faculty_id', $studentDetails->faculty_id)
                                    ->orWhereNull('faculty_id');
                          })
                          ->where(function ($filter) use ($studentDetails) {
                              $filter->where('dept_id', $studentDetails->department_id)
                                    ->orWhereNull('dept_id');
                          })
                          ->where(function ($filter) use ($studentDetails) {
                              $filter->where('program_id', $studentDetails->program_id)
                                    ->orWhereNull('program_id');
                          });
                    });
                    $subQuery->orWhere('scope_type', 'Global');
                });
        })
        // Exclude questionnaires that have already been answered
        ->whereDoesntHave('responses', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('end_date', '<=', now())
        ->orderBy('start_date', 'desc')
        ->get();

        return $targetedQuestionnaires;
    }

    private function getStudentDetails($userId)
    {
        return StudentDetail::where('user_id', $userId)->first();
    }
    


    
}
