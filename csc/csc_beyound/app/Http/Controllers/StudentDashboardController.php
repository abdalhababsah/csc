<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'student') {
            // Fetch the attended subjects with mid-term and final marks.
            $attendedSubjects = Auth::user()->subjects()->get()->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_name' => $subject->sub_name,
                    'mid_mark' => $subject->pivot->mid_mark,
                    'final_mark' => $subject->pivot->mark,
                ];
            });

            return view('student.dashboard', ['attendedSubjects' => $attendedSubjects]);
        }

        return view('dashboard');
    }
}
