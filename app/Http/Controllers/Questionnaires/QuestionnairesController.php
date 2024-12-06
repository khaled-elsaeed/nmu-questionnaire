<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionModule;
use App\Models\Faculty;
use App\Models\QuestionnaireTarget;
use App\Models\Questionuse;
use App\Models\CourseDetail;
use App\Services\QuestionnaireService;

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



public function results()
{
    try {
        $questionnaires = QuestionnaireTarget::with('questionnaire')->get();

        return view('admin.questionnaires.result', compact('questionnaires'));
    } catch (\Exception $exception) {
        Log::error('Failed to retrieve questionnaires in results method: ' . $exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Unable to retrieve questionnaires.'], 500);
    }
}


public function showStats($questionnaireTargetId)
{
   
    // Return stats to the view
    return $this->questionnaireService->showStats($questionnaireTargetId);
}





    
    

    
    



}
