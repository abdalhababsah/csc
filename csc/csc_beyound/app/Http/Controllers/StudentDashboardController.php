<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index(){
        if (Auth::user()->role === 'student') {
            $attendedSubjects = Auth::user()->subjects;
    
            return view('student.dashboard', ['attendedSubjects' => $attendedSubjects]);
        }
    
        return view('dashboard');
    }
}
