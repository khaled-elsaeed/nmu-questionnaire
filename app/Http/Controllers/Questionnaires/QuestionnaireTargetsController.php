<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\QuestionnaireTarget;
use Illuminate\Http\Request;

class QuestionnaireTargetsController extends Controller
{
    public function index()
    {
        return QuestionnaireTarget::with(['questionnaire', 'department', 'program', 'faculty'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'questionnaire_id' => 'required|exists:questionnaires,id',
            'dept_id' => 'nullable|exists:departments,id',
            'program_id' => 'nullable|exists:programs,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'role_name' => 'required|string|in:admin,student,teacher',
            'scope_type' => 'required|string|in:local,global'
        ]);
        return QuestionnaireTarget::create($request->all());
    }

    public function show($id)
    {
        return QuestionnaireTarget::with(['questionnaire', 'department', 'program', 'faculty'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $target = QuestionnaireTarget::findOrFail($id);
        $target->update($request->all());
        return $target;
    }

    public function destroy($id)
    {
        QuestionnaireTarget::destroy($id);
        return response()->noContent();
    }
}
