<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionModule;
use App\Models\Faculty;
use App\Models\QuestionnaireTarget;
use App\Models\Question;
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
            return view('admin.questionnaires.create', compact('modules', 'faculties'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve modules: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve modules.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_active' => 'required|boolean',
            'questions' => 'required|array|min:1',
            'questions.*' => 'exists:questions,id',
            'audience_data' => 'required|string',
            'module_id' => 'required|integer',
            'audience' => 'required|array|min:1',
        ]);

        try {
            \DB::transaction(function () use ($request) {
                $questionnaire = Questionnaire::create($request->only(['title', 'description', 'start_date', 'end_date', 'is_active', 'module_id']));

                $questionnaire->questions()->attach($request->questions, ['display_order' => 0, 'is_mandatory' => false]);

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

                        if ($audience['role_name'] === 'student' && isset($audience['faculties']) && is_array($audience['faculties'])) {
                            foreach ($audience['faculties'] as $facultyData) {
                                $facultyId = $facultyData['id'] === 'all' ? null : $facultyData['id'];

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
                        } elseif ($audience['role_name'] === 'teaching_assistant') {
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
                            Log::error('Invalid role or faculties data missing for audience: ' . json_encode($audience));
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
}
