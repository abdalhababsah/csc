<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\studentToClassController;
use App\Http\Controllers\StudentDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome')
    ->name('home');;
});
Route::get('/welcomestudent', function () {
    return view('welcomestudent');
})->name('welcomestudent');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::resource('admin/dashboard', DashBoardController::class);
// Route::resource('admin/dashboard/students', StudentController::class);
// Route::resource('admin/dashboard/subjects', SubjectController::class);
// Route::get('/admin/dashboard/studenttoclass/{subjectId}', [studentToClassController::class, 'index'])->name('studenttoclass.index');
// Route::post('/admin/dashboard/studenttoclass/update-marks', [StudentToClassController::class, 'updateMarks'])->name('studenttoclass.updateMarks');



    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
    ->name('student.dashboard');

///////////////////////////////////

// DashBoardController Routes
Route::get('/admin/dashboard', [DashBoardController::class, 'index'])->name('admin.dashboard.index');

// StudentController Routes
Route::get('/admin/dashboard/students', [StudentController::class, 'index'])->name('admin.students.index');
Route::get('/admin/dashboard/students/{student}', [StudentController::class, 'show'])->name('admin.students.show');
Route::post('/admin/dashboard/students', [StudentController::class, 'store'])->name('admin.students.store');
Route::get('/admin/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit');
Route::put('/admin/dashboard/students/{student}', [StudentController::class, 'update'])->name('admin.students.update');
Route::delete('/admin/dashboard/students/{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

// SubjectController Routes
Route::get('/admin/dashboard/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
Route::post('/admin/dashboard/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
Route::get('/admin/dashboard/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('admin.subjects.edit');
Route::put('/admin/dashboard/subjects/{subject}', [SubjectController::class, 'update'])->name('admin.subjects.update');
Route::delete('/admin/dashboard/subjects/{subject}', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

// studentToClassController Routes
Route::get('/admin/dashboard/studenttoclass/{subjectId}', [StudentToClassController::class, 'index'])->name('admin.studenttoclass.index');
Route::post('/admin/dashboard/studenttoclass/update-marks', [StudentToClassController::class, 'update'])->name('admin.studenttoclass.updateMarks');

require __DIR__.'/auth.php';