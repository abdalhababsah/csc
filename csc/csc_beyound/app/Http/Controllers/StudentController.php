<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $students = User::where('role', 'student')->get();
            return response()->json($students);
        }
        return view('admin.students.students');
    }

    public function store(Request $request)
    {
        $student = new User();
        $student->name = $request->name;
        $student->email = $request->email;
        $student->password = Hash::make($request->password); // Use Hash facade
        $student->role = 'student';
        $student->activated = $request->activated ?? 0; // Set default as 0 if not provided
        $student->save();

        return response()->json($student);
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
    public function show($id)
{
    $student = User::find($id);

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    return response()->json($student);
}
}
