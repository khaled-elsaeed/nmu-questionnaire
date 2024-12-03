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
            $response = Response::create([
                'questionnaire_target_id' => $targetId,
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
                        'answer_text' => null, // Explicitly set to NULL
                    ]);
                }
                
                elseif ($question->type == 'text_based') {
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
            Log::error('Questionnaire Submission Error: ' . $e->getMessage());
            return redirect()->route('responder.questionnaire.show', $questionnaireId)
                ->with('error', 'There was an issue submitting your responses. Please try again.');
        }
        
    }

    public function history()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        $availableQuestionnaires = $role === 'student' 
            ? $this->getAvailableQuestionnaireTargets($user->id)
            : collect();
        
        
    
        // Log the processed data for debugging purposes
        Log::debug('Questionnaire Target Data:', $availableQuestionnaires);
        
        // Return the view
        return view('responder.questionnaire.history', compact('availableQuestionnaires'));
    }
    
    

    



    
    private function getAvailableQuestionnaireTargets($userId)
    {
        $studentDetails = $this->getStudentDetails($userId);
        
        try {
            $query = "
    SELECT 
        qt.*, 
        q.title AS questionnaire_title, 
        q.end_date AS questionnaire_end_date, 
        q.start_date AS questionnaire_start_date,
        c.name AS course_name  -- Add the course name from the courses table
    FROM 
        `questionnaire_targets` qt
    JOIN 
        `questionnaires` q ON qt.`questionnaire_id` = q.`id`
    LEFT JOIN 
        `course_details` cd ON qt.`course_detail_id` = cd.`id`  -- Corrected column name `course_detail_id`
    LEFT JOIN 
        `courses` c ON cd.`course_id` = c.`id`  -- Join the courses table for the course name
    WHERE 
        qt.`role_name` = 'student'
        AND (
            (
                qt.`scope_type` = 'local'
                AND (
                    (qt.`faculty_id` = :faculty_id OR qt.`faculty_id` IS NULL)
                    AND (qt.`dept_id` = :dept_id OR qt.`dept_id` IS NULL)
                    AND (qt.`program_id` = :program_id OR qt.`program_id` IS NULL)
                )
            )
            OR qt.`scope_type` = 'global'
        )
        AND (
            -- Check if the questionnaire has a response from the user
            EXISTS (
                SELECT 1
                FROM `responses` r
                WHERE qt.`id` = r.`questionnaire_target_id`
                AND r.`user_id` = :user_id
            )
            OR
            -- OR the deadline has passed
            q.`end_date` < NOW()
        )
        AND (
            qt.`course_detail_id` IS NULL
            OR EXISTS (
                SELECT 1
                FROM `course_enrollments` e
                WHERE e.`student_id` = :student_detail_id
                AND e.`course_detail_id` = qt.`course_detail_id`
            )
        )
    LIMIT 0, 25;
    ";
    
            $results = DB::select($query, [
                'faculty_id' => $studentDetails->faculty_id,
                'dept_id' => $studentDetails->department_id,
                'program_id' => $studentDetails->program_id,
                'user_id' => $userId,
                'student_detail_id' => $studentDetails->id,
            ]);
    
            // Add response_exists attribute to each result
            foreach ($results as $result) {
                // Check if the user has already responded
                $result->response_exists = DB::table('responses')
                                              ->where('questionnaire_target_id', $result->id)
                                              ->where('user_id', $userId)
                                              ->exists();
            }
    
            return $results;
    
        } catch (\Exception $e) {
            Log::error('Error getting targeted questionnaires: ' . $e->getMessage(), [
                'user_id' => $userId,
                'student_id' => $studentDetails->id,
                'exception' => $e,
            ]);
    
            throw $e;
        }
    }
    

    private function getStudentDetails($userId)
    {
        return StudentDetail::where('user_id', $userId)->first();
    }
    


    
}
