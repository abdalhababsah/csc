<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth;
use App\Models\User;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->paginate(10);
            return view('admin.students.students', compact('students'));
    }
    public function show()
    {
        $students = User::where('role', 'student')->paginate(10);
            return view('admin.students.students', compact('students'));
    }


    public function store(Request $request)
    {
        $student = new User();
        $student->name = $request->name;
        $student->email = $request->email;
        $student->password = bcrypt($request->password);
        $student->role = 'student';
        $student->activated = 1; 
        $student->save();

        return response()->json($student);
    }

    public function edit($id)
    {
        $student = User::find($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = User::find($id);
        $student->update($request->all());

        return response()->json($student);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['success' => 'Student deleted successfully']);
    }
    
}
