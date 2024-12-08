<?php

namespace App\Http\Controllers\Responder;

use App\Models\Questionnaire;
use App\Models\StudentDetail;
use App\Models\QuestionnaireTarget;
use App\Models\courseEnrollments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ResponderHomeController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $role = $user->getRoleNames()->first();

            $questionnaires = $role === 'student' ? $this->getQuestionnairesForStudent($user) : collect();

            return view('responder.home', compact('questionnaires'));
        } catch (\Throwable $e) {
            // Log the error for debugging purposes
            \Log::error('Error in ResponderController@index: ' . $e->getMessage());

            // Return a custom error view
            return response()->view('errors.500', [], 500);
        }
    }


    private function getQuestionnairesForStudent($user)
    {
        $studentDetails = $this->getStudentDetails($user);

        if (!$studentDetails) {
            return collect();
        }

        return $this->getAvailableQuestionnaireTargets($studentDetails, $user->id);
    }

    private function getStudentDetails($user)
    {
        return StudentDetail::where('user_id', $user->id)->first();
    }




    public function getAvailableQuestionnaireTargets($studentDetails, $userId)
    {

        try {
        
                $results = QuestionnaireTarget::with(['questionnaire', 'courseDetail.course', 'responses' => function ($query) use ($userId) {
                    $query->byUser($userId);
                }])
                ->forRole('student')
                ->forGlobalOrLocalScope($studentDetails)
                ->scopeWithActiveNotResponded($userId)  
                ->forCourses($studentDetails)
                ->limit(25)
                ->get();
            

        } catch (\Exception $e) {
            Log::error('Error getting available questionnaire targets', [
                'exception' => $e,
            ]);

            throw $e;
        }

        $results->each(function ($result) use ($userId) {
            $result->response_exists = $result->responses->contains('user_id', $userId);
        });

        return $results;
    }

    

    
}
