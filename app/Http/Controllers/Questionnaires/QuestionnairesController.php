<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionModule;
use App\Models\Faculty;
use App\Models\QuestionnaireTarget;
use App\Models\Questionuse;
use App\Models\CourseDetail;
use App\Services\QuestionnaireService;
Use App\Exports\StatsExport;
use App\Models\User;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class QuestionnairesController extends Controller
{
    protected $questionnaireService;

    public function __construct(QuestionnaireService $questionnaireService)
    {
        $this->questionnaireService = $questionnaireService;
    }
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

        $courses = Course::all(); 

        return view('admin.questionnaires.create', compact('modules', 'faculties', 'courses'));
    } catch (\Exception $exception) {
        Log::error('Failed to retrieve data for questionnaire creation: ' . $exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Unable to retrieve data for questionnaire creation.'], 500);
    }
}


public function store(Request $request)
{
    return $this->questionnaireService->store($request);
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



public function results() {
    try {
        if (auth()->user()->hasRole('super_admin')) {
            $questionnaires = QuestionnaireTarget::withCount('responses as response_count')
                                                 ->with('questionnaire')
                                                 ->get();
        } else {
            $user = auth()->user();
            $facultyId = $this->getFacultyIdFromRole($user);
            $questionnaires = QuestionnaireTarget::whereHas('courseDetail.course', function ($query) use ($facultyId) {
                $query->where('faculty_id', $facultyId);
            })
            ->withCount('responses as response_count')
            ->with('questionnaire')
            ->get();
        }
         
        return view('admin.questionnaires.result', compact('questionnaires'));
    } catch (\Exception $exception) {
        Log::error('Failed to retrieve questionnaires in results method: ' . $exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Unable to retrieve questionnaires.'], 500);
    }
}

private function getFacultyIdFromRole(User $user) {
    foreach ($user->getRoleNames() as $role) {
        if (strpos($role, '_fac_') !== false) {
            return (int)substr($role, strpos($role, '_fac_') + 5);
        }
    }
     
    return null;
}



public function showStats($questionnaireTargetId)
{
   
    // Return stats to the view
    return $this->questionnaireService->showStats($questionnaireTargetId);
}

public function generateReport($questionnaireId)
{
    try {
        
        $stats = $this->questionnaireService->showStats($questionnaireId, 'data');

        if (isset($stats['stats']) && !empty($stats['stats'])) {
            $export = new StatsExport();
            $export->generateExcelReport($stats);
            Log::error('Stats data is empty or missing "questions" key.');
        } else {
            Log::error('Stats data is empty or missing "questions" key.');
        }
        
       
        
    } catch (\Exception $exception) {
        Log::error('Failed to generate report: ' . $exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Unable to generate report.'], 500);
    }
 
}





    
    

    
    



}
