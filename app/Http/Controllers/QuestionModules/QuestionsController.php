<?php

namespace App\Http\Controllers\QuestionModules;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\ActionLog; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class QuestionsController extends Controller
{
    public function index()
    {
        return Question::with('questionModule')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_module_id' => 'required|exists:question_modules,id',
            'question_text' => 'required|string',
            'question_type' => 'required|string'
        ]);
        return Question::create($request->all());
    }

    public function destroy(Request $request)
    {
        $id = $request->route('id');

        try {
            $question = Question::findOrFail($id);
            $question->delete();

            ActionLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'action' => 'Deleted Question: ' . $question->text,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.',
            ]);
        } catch (\Exception $exception) {
            Log::error('Failed to delete question: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to delete question.'], 500);
        }
    }
}
