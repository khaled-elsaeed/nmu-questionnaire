<?php

namespace App\Http\Controllers\QuestionModules;

use App\Models\QuestionModule;
use App\Models\Question;
use App\Models\ActionLog; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class QuestionModulesController extends Controller
{
    public function index()
    {
        try {
            $modules = QuestionModule::with('questions.options')->get();
            return view('admin.question_modules.index', compact('modules'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve modules: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve modules.'], 500);
        }
    }

    public function create()
    {
        return view('admin.question_modules.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'module_name' => 'required|string|max:255',
                'module_description' => 'required|string',
                'questions.*.text' => 'required|string',
                'questions.*.type' => 'required|string|in:multiple_choice,text_based,scaled',
                'questions.*.options.*' => 'nullable|string|max:255',
                'questions.*.scale_type' => 'nullable|string|in:numerical,text',
            ]);

            $module = QuestionModule::create([
                'name' => $request->input('module_name'),
                'description' => $request->input('module_description')
            ]);

            foreach ($request->input('questions') as $questionData) {
                $questionType = $questionData['type'];
                if ($questionType === 'scaled' && !empty($questionData['scale_type'])) {
                    $questionType .= '_' . $questionData['scale_type'];
                }

                $question = $module->questions()->create([
                    'text' => $questionData['text'],
                    'type' => $questionType,
                ]);

                if ($questionData['type'] === 'multiple_choice' && isset($questionData['options'])) {
                    foreach ($questionData['options'] as $option) {
                        $question->options()->create(['text' => $option]);
                    }
                }
            }

            // Log the action
            ActionLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'action' => 'Created Question Module: ' . $module->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Module and Questions created successfully.',
                'module' => $module
            ]);

        } catch (ValidationException $validationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validationException->validator->errors()
            ], 422);
        } catch (\Exception $exception) {
            Log::error('Failed to create module and questions: ' . $exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the module and questions.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }



    public function getQuestions($id)
    {
        try {
            $module = QuestionModule::with('questions.options')->findOrFail($id);
            $questions = $module->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'type' => $question->type,
                    'options' => $question->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'text' => $option->text
                        ];
                    })
                ];
            });

            return response()->json(['success' => true, 'questions' => $questions], 200);

        } catch (\Exception $exception) {
            Log::error('Failed to retrieve module details: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve module details.'], 500);
        }
    }

    public function getModuleDetailsWithQuestions($id)
    {
        try {
            $module = QuestionModule::with('questions.options')->findOrFail($id);
            return view('admin.question_modules.module', compact('module'));
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve module details: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve module details.'], 500);
        }
    }

    public function getOptions($id)
    {
        try {
            $question = Question::with('options')->findOrFail($id);
            $options = $question->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'text' => $option->text
                ];
            });

            return response()->json(['success' => true, 'options' => $options], 200);
        } catch (\Exception $exception) {
            Log::error('Failed to retrieve question options: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to retrieve question options.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->route('id'); // Get the ID from the request route

        try {
            $module = QuestionModule::findOrFail($id);
            $module->delete();

            ActionLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'action' => 'Deleted Question Module: ' . $module->name,
            ]);

            return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
        } catch (\Exception $exception) {
            Log::error('Failed to delete module: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to delete module.'], 500);
        }
    }
}
