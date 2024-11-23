<?php

namespace App\Http\Controllers\Responder;

use App\Models\Questionnaire;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    private function getTargetedQuestionnaires($studentDetails, $userId)
    {
        return Questionnaire::whereIn('id', function ($query) use ($studentDetails) {
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
            ->whereDoesntHave('responses', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('end_date', '>', now()) 
            ->orderBy('start_date', 'desc') 
            ->get();
    }
}
