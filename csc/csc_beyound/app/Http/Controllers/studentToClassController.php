<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB; 
use App\Models\Subject;

use Illuminate\Http\Request;

class studentToClassController extends Controller
{
    public function index($subjectId)
    {
        $subject = Subject::with(['students' => function($query) {
            $query->select('users.id', 'name', 'email', 'student_subject.mark', 'student_subject.mid_mark')
                  ->where('users.role', 'student'); 
        }])->find($subjectId);

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        return view('admin.studenttoclass.studenttoclass', ['subject' => $subject]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id', 
            'mid_mark' => 'nullable|integer',
            'mark' => 'nullable|integer',
        ]);
    
        try {
            DB::table('student_subject')
                ->where('student_id', $validated['student_id'])
                ->where('subject_id', $validated['subject_id'])
                ->update([
                    'mid_mark' => $validated['mid_mark'],
                    'mark' => $validated['mark'],
                ]);
    
            $updatedMarks = DB::table('student_subject')
                ->where('student_id', $validated['student_id'])
                ->where('subject_id', $validated['subject_id'])
                ->first(['mid_mark', 'mark']);
    
            if ($updatedMarks) {
                return response()->json([
                    'mid_mark' => $updatedMarks->mid_mark,
                    'mark' => $updatedMarks->mark
                ]);
            } else {
                return response()->json(['message' => 'Failed to update marks'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
    }
    
    
    


