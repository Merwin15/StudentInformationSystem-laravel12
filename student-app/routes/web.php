<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Include authentication routes
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'password'])->name('password.update');
    
    // Student Routes
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/courses', [StudentController::class, 'courses'])->name('students.courses');
    Route::post('/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
    Route::post('/students/{student}/courses/{course}/add', [StudentController::class, 'addCourse'])->name('students.courses.add');
    Route::delete('/students/{student}/courses/{course}/remove', [StudentController::class, 'removeCourse'])->name('students.courses.remove');
    Route::delete('/students/delete-all', [StudentController::class, 'deleteAll'])
        ->name('students.delete-all');
    
    // Teacher Routes
    Route::resource('teachers', TeacherController::class);
    Route::get('/teachers/{teacher}/courses', [TeacherController::class, 'courses'])->name('teachers.courses');
    
    // Course Routes
    Route::resource('courses', CourseController::class);
    Route::get('/courses/{course}/enroll', [CourseController::class, 'showEnrollment'])->name('courses.enroll');
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll.store');
    Route::delete('/courses/{course}/students/{student}', [CourseController::class, 'removeStudent'])->name('courses.students.remove');
    Route::put('/courses/{course}/grades', [CourseController::class, 'updateGrades'])->name('courses.grades.update');
    Route::delete('/courses/{course}/grades/{student}', [CourseController::class, 'deleteGrade'])->name('courses.grades.destroy');
    Route::put('/courses/{course}/students/{student}/grade', [CourseController::class, 'updateStudentGrade'])
        ->name('courses.student.grade.update');

    // Admin routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        // Add admin-specific routes here
    });
});
