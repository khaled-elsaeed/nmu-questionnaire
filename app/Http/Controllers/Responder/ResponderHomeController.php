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
use Illuminate\Support\Collection;

class ResponderHomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $questionnaires = $role === 'student' ? $this->getQuestionnairesForStudent($user) : collect();

        return view('responder.home', compact('questionnaires'));
    }

    private function getQuestionnairesForStudent($user)
    {
        $studentDetails = $this->getStudentDetails($user);

        if (!$studentDetails) {
            return collect();
        }

        return $this->getTargetedQuestionnaires($studentDetails, $user->id);
    }

    private function getStudentDetails($user)
    {
        return StudentDetail::where('user_id', $user->id)->first();
    }


public function getTargetedQuestionnaires($studentDetails, $userId)
{
    try {
        $results = QuestionnaireTarget::with('questionnaire')
            ->where('role_name', 'student')
            ->where(function ($query) use ($studentDetails) {
                $query->where('scope_type', 'global')
                      ->orWhere(function ($query) use ($studentDetails) {
                          $query->where('scope_type', 'local')
                                ->where(function ($subQuery) use ($studentDetails) {
                                    $subQuery->where('faculty_id', $studentDetails->faculty_id)
                                             ->orWhereNull('faculty_id');
                                })
                                ->where(function ($subQuery) use ($studentDetails) {
                                    $subQuery->where('dept_id', $studentDetails->department_id)
                                             ->orWhereNull('dept_id');
                                })
                                ->where(function ($subQuery) use ($studentDetails) {
                                    $subQuery->where('program_id', $studentDetails->program_id)
                                             ->orWhereNull('program_id');
                                });
                      });
            })
            ->whereDoesntHave('responses', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function ($query) use ($studentDetails) {
                $query->whereNull('course_detail_id')
                      ->orWhereHas('courseEnrollments', function ($subQuery) use ($studentDetails) {
                          $subQuery->where('student_id', $studentDetails->id);
                      });
            })
            ->whereHas('questionnaire', function ($query) {
                $query->where('end_date', '>', now());
            })
            ->distinct() // Ensure unique results
            ->take(25)
            ->get();

        Log::info('Targeted Questionnaires Retrieved:', [
            'user_id' => $userId,
            'student_id' => $studentDetails->id,
            'questionnaires' => $results
        ]);

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
}
