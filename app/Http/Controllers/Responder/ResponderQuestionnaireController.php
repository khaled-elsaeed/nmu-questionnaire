<?php

namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Response;

class ResponderQuestionnaireController extends Controller
{
    /**
     * Display the questionnaire for the respondent to answer.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $questionnaire = Questionnaire::with('questions.options')->findOrFail($id);

        // Ensure the questionnaire is active and visible to respondents
        if (!$questionnaire->is_active) {
            return redirect()->route('responder.questionnaires.index')
                ->with('error', 'This questionnaire is no longer available.');
        }

        return view('responder.questionnaire.show', compact('questionnaire'));
    }

    /**
     * Handle the submission of questionnaire responses.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request, $id)
    {
        $questionnaire = Questionnaire::findOrFail($id);

        // Validate answers
        $rules = [];
        foreach ($questionnaire->questions as $question) {
            $rules["answers.{$question->id}"] = $question->type === 'multiple_choice'
                ? 'required|string'
                : 'nullable|string';
        }
        $validated = $request->validate($rules);

        // Save the responses
        foreach ($validated['answers'] as $questionId => $answer) {
            Response::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'questionnaire_id' => $questionnaire->id,
                    'question_id' => $questionId,
                ],
                ['answer' => $answer]
            );
        }

        return redirect()->route('responder.questionnaire.completed', $questionnaire->id)
            ->with('success', 'Your responses have been submitted successfully.');
    }

    /**
     * Show the completed confirmation page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function completed($id)
    {
        $questionnaire = Questionnaire::findOrFail($id);

        return view('responder.questionnaire.completed', compact('questionnaire'));
    }
}
