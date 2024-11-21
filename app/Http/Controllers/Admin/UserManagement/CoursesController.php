<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function index()
    {
        return Course::all();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'course_code' => 'required|string']);
        return Course::create($request->all());
    }

    public function show($id)
    {
        return Course::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->all());
        return $course;
    }

    public function destroy($id)
    {
        Course::destroy($id);
        return response()->noContent();
    }
}
