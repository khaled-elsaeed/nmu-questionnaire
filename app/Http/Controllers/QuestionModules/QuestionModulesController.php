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
use Illuminate\Support\Facades\DB;


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
        DB::beginTransaction();  // Start a database transaction
        
        try {
            // Validate module-level inputs
            $request->validate([
                'module_name' => 'required|string|max:255',
                'module_description' => 'nullable|string',
            ]);
        
            // Sanitize module-level inputs
            $moduleName = strip_tags($request->input('module_name'));
            $moduleDescription = strip_tags($request->input('module_description', null));
    
            // Check if the module already exists
            if ($this->isModuleExist($moduleName)) {
                throw new \Exception("A module with this name already exists.");
            }
        
            // Create the question module
            $module = QuestionModule::create([
                'name' => $moduleName,
                'description' => $moduleDescription,
            ]);
        
            // Decode and sanitize the JSON-encoded questions input
            $questions = json_decode($request->input('questions'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON format in 'questions' input.");
            }
        
            foreach ($questions as $questionData) {
                // Sanitize question text
                $questionText = strip_tags($questionData['text']);
                $questionType = strip_tags($questionData['type']);
        
                // Check if the question text is empty after sanitization
                if (empty($questionText)) {
                    throw new \Exception("Question text cannot be empty.");
                }
    
                // Check if the question already exists in other modules
                if ($this->isQuestionExist($questionText)) {
                    throw new \Exception("This question already exists in another module.");
                }
    
                // Append scale type for scaled questions
                if ($questionType === 'scaled' && !empty($questionData['scale_type'])) {
                    $questionType .= '_' . strip_tags($questionData['scale_type']);
                }
        
                // Create the question
                $question = $module->questions()->create([
                    'text' => $questionText,
                    'type' => $questionType,
                ]);
        
                // Handle multiple-choice options
                if ($questionData['type'] === 'multiple_choice' && !empty($questionData['options'])) {
                    foreach ($questionData['options'] as $option) {
                        $sanitizedOption = strip_tags($option);
                        if (!empty($sanitizedOption)) { // Ignore empty options
                            $question->options()->create(['text' => $sanitizedOption]);
                        }
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
        
            DB::commit();  // Commit the transaction if everything is successful
        
            return response()->json([
                'success' => true,
                'message' => 'Module and Questions created successfully.',
                'module' => $module,
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();  // Rollback transaction on validation errors
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            DB::rollBack();  // Rollback transaction on unexpected errors
            // Log unexpected errors for debugging
            Log::error('Failed to create module and questions: ' . $exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the module and questions.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
    
    

public function isModuleExist($moduleName)
{
    return QuestionModule::where('name', $moduleName)->exists();
}

public function isQuestionExist($questionText, $excludeModuleId = null)
{
    return Question::where('text', $questionText)
                    ->where(function ($query) use ($excludeModuleId) {
                        if ($excludeModuleId) {
                            $query->where('module_id', '!=', $excludeModuleId);
                        }
                    })
                    ->exists();
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
