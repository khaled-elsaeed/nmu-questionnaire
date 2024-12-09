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
    /**
     * Display the questionnaire form for a specific target.
     */
    public function show($id)
    {
        // Get the currently authenticated user
        $user = auth()->user();
    
        // Retrieve the target using the provided id
        $target = QuestionnaireTarget::findOrFail($id);
    
        // Check if the target is available for the authenticated user
        if (!$this->isTargetAvailableForUser($user->id, $target->id)) {
            return response()->json([
                'success' => false,
                'message' => __('This questionnaire is not available to you.'),
            ], 403); // Forbidden
        }
    
        // Retrieve the associated questionnaire, including its questions and options
        $questionnaire = Questionnaire::with('questions.options')
            ->where('id', $target->questionnaire_id)
            ->where('is_active', true)
            ->firstOrFail();
    
        // Pass the user information along with questionnaire data to the view
        return view('Responder.questionnaire.show', [
            'questionnaire' => $questionnaire,
            'targetId' => $target->id,
            'user' => $user,  // Passing the authenticated user to the view
        ]);
    }
    

    /**
     * Handle the questionnaire submission.
     */
    public function submit(Request $request, $questionnaireId)
    {
        // Sanitize and validate input
        $validatedData = $request->validate([
            'answers.*' => 'required',
            'target_id' => 'required|exists:questionnaire_targets,id',
        ]);

        $targetId = filter_var($request->input('target_id'), FILTER_SANITIZE_NUMBER_INT);
        $sanitizedAnswers = $this->sanitizeAnswers($validatedData['answers']);
        
        // Check if the target is available for the user
        $userId = auth()->id();
        if (!$this->isTargetAvailableForUser($userId, $targetId)) {
            return response()->json([
                'success' => false,
                'message' => __('This questionnaire is not available to you.'),
            ], 403); // Forbidden
        }

        // Check if the user has already responded to this target
        if (Response::hasUserResponded($userId, $targetId)) {
            return response()->json([
                'success' => false,
                'message' => __('You have already submitted responses to this questionnaire.'),
            ], 400);  // Bad Request
        }

        // Begin database transaction for response submission
        DB::beginTransaction();

        try {
            // Create the response record
            $response = Response::create([
                'questionnaire_target_id' => $targetId,
                'user_id' => $userId,
            ]);

            if (!$response) {
                throw new \Exception('Failed to create response record.');
            }

            // Save the answers
            $this->saveAnswers($response, $sanitizedAnswers);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Your responses have been submitted successfully!'),
                'response_id' => $response->id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Questionnaire Submission Error', [
                'user_id' => auth()->id(),
                'questionnaire_id' => $questionnaireId,
                'target_id' => $targetId,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('There was an issue submitting your responses. Please try again.'),
            ], 500);
        }
    }

    /**
     * Fetch the response history of the user.
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $studentDetails = $this->getStudentDetails($userId);

    
        // Get the available questionnaire targets based on the user, with the appropriate scope
        $availableQuestionnaires = $this->getAvailableQuestionnaireTargets($userId, true);
    
        return view('Responder.questionnaire.history', compact('availableQuestionnaires'));
    }
    

    /**
     * Get the available questionnaire targets for a user.
     */
    public function getAvailableQuestionnaireTargets($userId, $includeRespondedOrDeadline = false)
{
    $studentDetails = $this->getStudentDetails($userId);

    try {
        // If we want to include responded or deadline passed, use the scope withDeadlinePassedOrResponded
        if ($includeRespondedOrDeadline) {
            $results = QuestionnaireTarget::with(['questionnaire', 'courseDetail.course', 'responses' => function ($query) use ($userId) {
                $query->byUser($userId);
            }])
            ->forRole('student')
            ->forGlobalOrLocalScope($studentDetails)
            ->withDeadlinePassedOrResponded($userId)  // Use the scope for deadline passed or responded
            ->forCourses($studentDetails)
            ->limit(25)
            ->get();
        } else {
            // Otherwise, just return available targets
            $results = QuestionnaireTarget::with(['questionnaire', 'courseDetail.course', 'responses' => function ($query) use ($userId) {
                $query->byUser($userId);
            }])
            ->forRole('student')
            ->forGlobalOrLocalScope($studentDetails)
            ->scopeWithActiveNotResponded($userId)
            ->forCourses($studentDetails)
            ->limit(25)
            ->get();
        }

    } catch (\Exception $e) {
        Log::error('Error getting available questionnaire targets', [
            'user_id' => $userId,
            'student_id' => $studentDetails->id,
            'exception' => $e,
        ]);

        throw $e;
    }

    $results->each(function ($result) use ($userId) {
        $result->response_exists = $result->responses->contains('user_id', $userId);
    });

    return $results;
}


    /**
     * Check if a user is eligible to access a specific questionnaire target.
     */
    private function isTargetAvailableForUser($userId, $targetId)
    {
        $studentDetails = $this->getStudentDetails($userId);
        $target = QuestionnaireTarget::find($targetId);

        if (!$target) {
            return false;
        }

        // Get available targets and check if the given target is available for the user
        $availableTargets = $this->getAvailableQuestionnaireTargets($userId,false);

        // Check if the target ID exists in the list of available targets
        $targetAvailable = $availableTargets->contains('id', $targetId);

        return $targetAvailable;
    }

    /**
     * Sanitize the answers provided by the user.
     */
    private function sanitizeAnswers($answers)
    {
        return array_map(function ($answer) {
            return is_string($answer) ? htmlspecialchars(trim($answer), ENT_QUOTES, 'UTF-8') : $answer;
        }, $answers);
    }

    /**
     * Save the user's answers to the database.
     */
    private function saveAnswers($response, $answers)
    {
        foreach ($answers as $questionId => $answer) {
            $question = Question::findOrFail($questionId);

            if ($question->type == 'multiple_choice') {
                $option = Option::findOrFail($answer);
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'option_id' => $option->id,
                    'answer_text' => null,
                ]);
            } else {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'option_id' => null,
                    'answer_text' => $answer, // Already sanitized
                ]);
            }
        }
    }

    /**
     * Fetch the student's details by user ID.
     */
    private function getStudentDetails($userId)
    {
        return StudentDetail::where('user_id', $userId)->first();
    }
}
