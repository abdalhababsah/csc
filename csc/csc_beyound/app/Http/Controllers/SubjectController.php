<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Subject; 
use App\Models\User;
class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('students')->get();
        $allStudents = User::where('role', 'student')->get();
        return view('admin.subjects.subject', compact('subjects', 'allStudents'));
    }

    public function store(Request $request)
    {
        $rules = [
            'sub_name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ];
    
        // Validate the incoming request data
        $request->validate($rules);

        $subject = new Subject();
        $subject->sub_name = $request->sub_name;
        $subject->body = $request->body;
        $subject->status = $request->status;
        $subject->save();

        return response()->json($subject);
    }

    public function edit($id)
{
    $subject = Subject::find($id);
    if ($subject) {
        return response()->json($subject);
    } else {
        return response()->json(['error' => 'Subject not found'], 404);
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

    $request->validate($rules);
    DB::beginTransaction();
    try {
        $subject = Subject::findOrFail($id);
        $subject->sub_name = $request->sub_name;
        $subject->body = $request->body;
        $subject->status = $request->status;
        $subject->save();

        if($request->has('student_ids')) {
            $studentIds = $request->student_ids;
            $subject->students()->sync($studentIds);
        }

        DB::commit();
        return response()->json($subject);
    } catch (\Exception $exception) {
        DB::rollBack();
        return response()->json(['error' => 'An error occurred while updating the subject.'], 500);
    }
}
public function create()
{

}

public function show()
{

}




    public function destroy($id)
    {
        Subject::destroy($id);
        return response()->json(['success' => 'Subject deleted successfully']);
    }

}
