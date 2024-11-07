<?php
namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller; // Make sure to include the base Controller
use App\Models\Faculty; // Import the Faculty model
use Illuminate\Http\Request;

class FacultiesController extends Controller
{
    public function getDepartments($id)
    {
        // Find the faculty and load its departments
        $faculty = Faculty::with('departments')->find($id);

        // Check if the faculty exists
        if (!$faculty) {
            return response()->json(['error' => 'Faculty not found'], 404);
        }

        // Return the departments in JSON format
        return response()->json($faculty->departments);
    }
}

