<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Questionnaire;
use Illuminate\Http\Request;

use App\Models\QuestionModule;
use App\Models\Faculty;

use App\Http\Controllers\Controller;


use App\Models\QuestionnaireTarget;

use App\Models\Question;

use Illuminate\Support\Facades\Log;

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
            $faculties = Faculty::pluck('name', 'id'); // This returns a collection of faculties
            $modules = QuestionModule::pluck('name', 'id');
            return view('admin.questionnaires.create', compact('modules','faculties'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve modules: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve modules.'], 500);
        }
    }
    


    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_active' => 'required|boolean',
            'questions' => 'required|array|min:1', // Require at least one question
            'questions.*' => 'exists:questions,id',
            'audience_data' => 'required|string', // Expecting JSON string of audience data
        ]);
    
        try {
            \DB::transaction(function () use ($request) {
                $questionnaire = Questionnaire::create($request->only(['title', 'description', 'start_date', 'end_date', 'is_active']));
    
                // Attach questions to the questionnaire with default values
                $questionnaire->questions()->attach($request->questions, ['display_order' => 0, 'is_mandatory' => false]);
    
                // Decode the JSON audience data
                $audiences = json_decode($request->input('audience_data'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON format in audience_data");
                }
    
                // Process each audience target
                foreach ($audiences as $audience) {
                    foreach ($audience['faculties'] as $facultyData) {
                        $facultyId = $facultyData['id'] === 'all' ? null : $facultyData['id']; // 'all' means no specific faculty
    
                        // Handle faculty-wide target without specific departments
                        if (empty($facultyData['departments'])) {
                            QuestionnaireTarget::create([
                                'questionnaire_id' => $questionnaire->id,
                                'faculty_id' => $facultyId,
                                'dept_id' => null,
                                'program_id' => null,
                                'role_name' => $audience['role_name'],
                                'level' => $audience['level'],
                                'scope_type' => $audience['scope_type'],
                            ]);
                        } else {
                            // Handle each department within the faculty
                            foreach ($facultyData['departments'] as $departmentData) {
                                $deptId = $departmentData['id'];
    
                                if (empty($departmentData['programs'])) {
                                    // Target department-wide without specific programs
                                    QuestionnaireTarget::create([
                                        'questionnaire_id' => $questionnaire->id,
                                        'faculty_id' => $facultyId,
                                        'dept_id' => $deptId,
                                        'program_id' => null,
                                        'role_name' => $audience['role_name'],
                                        'level' => $audience['level'],
                                        'scope_type' => $audience['scope_type'],
                                    ]);
                                } else {
                                    // Handle each program within the department
                                    foreach ($departmentData['programs'] as $programData) {
                                        $programId = $programData['id'];
                                        QuestionnaireTarget::create([
                                            'questionnaire_id' => $questionnaire->id,
                                            'faculty_id' => $facultyId,
                                            'dept_id' => $deptId,
                                            'program_id' => $programId,
                                            'role_name' => $audience['role_name'],
                                            'level' => $audience['level'],
                                            'scope_type' => $audience['scope_type'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            });
    
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Error creating questionnaire: ' . $e->getMessage());
            return response()->json([
                'message' => 'There was an error creating the questionnaire. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
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
