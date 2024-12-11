<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function index()
    {
        return Program::with(['faculty'])->get();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'faculty_id' => 'required|exists:faculties,id']);
        return Program::create($request->all());
    }

    public function show($id)
    {
        return Program::with(['faculty'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $program->update($request->all());
        return $program;
    }

    public function destroy($id)
    {
        Program::destroy($id);
        return response()->noContent();
    }
}
