<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Make sure to include the base Controller


class DepartmentsController extends Controller
{
    public function getPrograms($id)
    {
        $department = Department::with('programs')->find($id);
        return response()->json($department->programs);
    }
}
