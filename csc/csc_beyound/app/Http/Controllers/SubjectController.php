<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
 
    public function index(Request $request)
    {
        $subjects = Subject::with('students')->get();
        $allStudents = User::where('role', 'student')->where('activated', 1)->get();
    
        if ($request->ajax()) {
            return response()->json([
                'subjects' => $subjects,
                'allStudents' => $allStudents,
            ]);
        } else {
            return view('admin.subjects.subject');
        }
    }
    
    public function store(Request $request)
    {
        $rules = [
            'sub_name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ];

        $validated = $request->validate($rules);

        $subject = Subject::create($validated);

        return response()->json($subject, Response::HTTP_CREATED);
    }


    public function edit($id)
{
    $subject = Subject::with('students')->find($id);
    $allStudents = User::where('role', 'student')->where('activated', 1)->get();

    if ($subject) {
        return response()->json([
            'subject' => $subject,
            'allStudents' => $allStudents,
        ]);
    } else {
        return response()->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
    }
}


    public function update(Request $request, $id)
    {
        $rules = [
            'sub_name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'student_ids' => 'array',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $subject = Subject::findOrFail($id);
            $subject->update($validated);

            if ($request->has('student_ids')) {
                $studentIds = $request->student_ids;
                $subject->students()->sync($studentIds);
            }

            DB::commit();
            return response()->json($subject);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while updating the subject.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $deleted = Subject::destroy($id);
        if ($deleted) {
            return response()->json(['success' => 'Subject deleted successfully']);
        } else {
            return response()->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function show(Request $request)
{
    $subjects = Subject::all(); 

    if ($request->ajax()) {
        return response()->json([
            'subjects' => $subjects
        ]);
    }

    return view('admin.subjects.show', compact('subjects'));
}
}
