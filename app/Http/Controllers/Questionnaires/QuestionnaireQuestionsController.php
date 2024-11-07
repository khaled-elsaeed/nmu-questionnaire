<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;

class QuestionnaireQuestionsController extends Controller
{
    public function index()
    {
        return QuestionnaireQuestion::with(['questionnaire', 'question'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'questionnaire_id' => 'required|exists:questionnaires,id',
            'question_id' => 'required|exists:questions,id',
            'display_order' => 'required|integer',
            'is_mandatory' => 'required|boolean'
        ]);
        return QuestionnaireQuestion::create($request->all());
    }

    public function show($id)
    {
        return QuestionnaireQuestion::with(['questionnaire', 'question'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $qQuestion = QuestionnaireQuestion::findOrFail($id);
        $qQuestion->update($request->all());
        return $qQuestion;
    }

    public function destroy($id)
    {
        QuestionnaireQuestion::destroy($id);
        return response()->noContent();
    }
}
