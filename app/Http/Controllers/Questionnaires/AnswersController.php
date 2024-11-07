<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Answer;
use Illuminate\Http\Request;

class AnswersController extends Controller
{
    public function index()
    {
        return Answer::with(['response', 'question', 'option'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'response_id' => 'required|exists:responses,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:choices,id',
            'answer_text' => 'required|string'
        ]);
        return Answer::create($request->all());
    }

    public function show($id)
    {
        return Answer::with(['response', 'question', 'option'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $answer = Answer::findOrFail($id);
        $answer->update($request->all());
        return $answer;
    }

    public function destroy($id)
    {
        Answer::destroy($id);
        return response()->noContent();
    }
}

