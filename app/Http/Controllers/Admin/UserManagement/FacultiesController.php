<?php
namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller; 
use App\Models\Faculty; 
use Illuminate\Http\Request;

class FacultiesController extends Controller
{
    public function getDepartments($id)
    {
        $faculty = Faculty::with('departments')->find($id);


        if (!$faculty) {
            return response()->json(['error' => 'Faculty not found'], 404);
        }


        return response()->json($faculty->departments);
    }
}

