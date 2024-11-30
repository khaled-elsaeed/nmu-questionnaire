<?php

namespace App\Http\Controllers\Responder;

use App\Models\Questionnaire;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
        try {
            $academicYear = now()->year;
            $term = 'fall';

            try {
                $query = "
                SELECT q.*
                FROM `questionnaires` q
                JOIN `questionnaire_targets` qt ON q.`id` = qt.`questionnaire_id`
                WHERE qt.`role_name` = 'student'
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
                AND NOT EXISTS (
                    SELECT 1
                    FROM `responses` r
                    WHERE q.`id` = r.`questionnaire_id`
                    AND r.`user_id` = :user_id
                )
                AND q.`end_date` > NOW()
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

                return $results;

            } catch (\Exception $e) {
                Log::error('Error getting targeted questionnaires: ' . $e->getMessage(), [
                    'user_id' => $userId,
                    'student_id' => $studentDetails->id,
                    'exception' => $e,
                ]);

                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in getTargetedQuestionnaires: ' . $e->getMessage(), [
                'user_id' => $userId,
                'student_id' => $studentDetails->id,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
