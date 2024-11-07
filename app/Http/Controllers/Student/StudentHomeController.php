<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class StudentHomeController extends Controller
{
    public function showHomePage()
    {
        return view('student.home');
    }
}
