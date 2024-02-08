<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\studentToClassController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
    return view('welcome');
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






// DashBoardController Routes
Route::get('/admin/dashboard', [DashBoardController::class, 'index'])->name('admin.dashboard.index');

// StudentController Routes
Route::get('/admin/dashboard/students/show', [StudentController::class, 'index'])->name('admin.students.index');
Route::get('/admin/dashboard/students/{student}', [StudentController::class, 'show'])->name('admin.students.show');
Route::post('/admin/dashboard/students/store', [StudentController::class, 'store'])->name('admin.students.store');
Route::get('/admin/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit');
Route::put('/admin/dashboard/students/update/{student}', [StudentController::class, 'update'])->name('admin.students.update');
Route::delete('/admin/dashboard/students/delet/{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

// chats 
Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send.message');
Route::post('/set-receiver', [ChatController::class, 'setReceiver'])->name('set.receiver');
Route::get('/fetch-users', [ChatController::class, 'fetchUsers'])->name('fetch.users');
Route::get('/fetch-chat-history/{receiverId}', [ChatController::class, 'fetchChatHistory'])->name('fetch.chat.history');
// SubjectController Routes
Route::get('/admin/dashboard/subjects/show', [SubjectController::class, 'index'])->name('admin.subjects.index');
Route::post('/admin/dashboard/subjects/store', [SubjectController::class, 'store'])->name('admin.subjects.store');
Route::get('/admin/dashboard/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('admin.subjects.edit');
Route::put('/admin/dashboard/subjects/update/{subject}', [SubjectController::class, 'update'])->name('admin.subjects.update');
Route::delete('/admin/dashboard/subjects/delete/{subject}', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');



Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
->name('student.dashboard');

// studentToClassController Routes
Route::get('/admin/dashboard/studenttoclass/show/{subjectId}', [StudentToClassController::class, 'index'])->name('admin.studenttoclass.index');
Route::post('/admin/dashboard/studenttoclass/update-marks', [StudentToClassController::class, 'update'])->name('admin.studenttoclass.updateMarks');

Route::get('/chat', function () {
    return view('admin.chat.chatview');
})->name('chat');




Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');


require __DIR__.'/auth.php';
