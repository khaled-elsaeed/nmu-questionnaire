<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire; 
use App\Models\QuestionModule; 
use App\Models\Response; 
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    public function showHomePage()
    {
        $totalQuestionnaires = Questionnaire::count();

        $totalModules = QuestionModule::count();

        $activeQuestionnaires = Questionnaire::where('is_active', '=','1')->count();

        $totalResponses = Response::count();

        return view('admin.home', compact('totalQuestionnaires', 'totalModules', 'activeQuestionnaires', 'totalResponses'));
    }
}
