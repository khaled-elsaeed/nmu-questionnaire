<?php

namespace App\Http\Controllers\Responder;

use App\Models\Questionnaire;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ResponderHomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first(); 

        Log::info("Fetching available questionnaires for user: {$user->id}, Role: {$role}");

        $questionnaires = $role === 'student'
            ? $this->getAvailableQuestionnairesForStudent($user)
            : collect();

        $this->logQuestionnairesResult($user, $questionnaires);

        return view('responder.home', compact('questionnaires'));
    }

    
    private function getAvailableQuestionnairesForStudent($user)
    {
        // Get student details
        $studentDetails = $this->getStudentDetails($user);

        if (!$studentDetails) {
            Log::warning("No student details found for user: {$user->id}. Returning no questionnaires.");
            return collect();
        }

        // Query questionnaires based on student details
        return Questionnaire::whereIn('id', function ($query) use ($studentDetails) {
            $query->select('questionnaire_id')
                ->from('questionnaire_targets')
                ->where('role_name', 'student') 

                // Match faculty
                ->where(function ($subQuery) use ($studentDetails) {
                    $this->addMatchCondition($subQuery, 'faculty_id', $studentDetails->faculty_id);
                })

                // Match department
                ->where(function ($subQuery) use ($studentDetails) {
                    $this->addMatchCondition($subQuery, 'dept_id', $studentDetails->department_id);
                })

                // Match program
                ->where(function ($subQuery) use ($studentDetails) {
                    $this->addMatchCondition($subQuery, 'program_id', $studentDetails->program_id);
                })

                // Match scope type
                ->where(function ($subQuery) {
                    Log::info("Checking scope type for questionnaire.");
                    $subQuery->where('scope_type', 'Global')
                             ->orWhere('scope_type', 'Local');
                });
        })->get();
    }

    
    private function getStudentDetails($user)
    {
        $studentDetails = StudentDetail::where('user_id', $user->id)->first();

        if ($studentDetails) {
            Log::info("Student details found for user: {$user->id}, Faculty ID: {$studentDetails->faculty_id}, Department ID: {$studentDetails->department_id}, Program ID: {$studentDetails->program_id}");
        } else {
            Log::warning("No student details found for user: {$user->id}");
        }

        return $studentDetails;
    }

    
    private function addMatchCondition($query, string $attribute, $value)
    {
        $query->where(function ($subQuery) use ($attribute, $value) {
            if ($value) {
                Log::info("Filtering by {$attribute}: {$value}");
                $subQuery->where($attribute, $value);
            } else {
                Log::info("Skipping {$attribute} filtering (NULL or not applicable).");
                $subQuery->whereNull($attribute);
            }
        });
    }

    
    private function logQuestionnairesResult($user, $questionnaires)
    {
        if ($questionnaires->isEmpty()) {
            Log::warning("No questionnaires found for user: {$user->id}.");
        } else {
            Log::info("Fetched questionnaires for user: {$user->id}. IDs: " . $questionnaires->pluck('id')->join(', '));
        }
    }
}
