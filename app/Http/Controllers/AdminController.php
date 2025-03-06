<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $courses = Course::with(['teacher', 'students'])->get();
        $students = Student::all();
        $teachers = Teacher::all();

        return view('admin.index', compact('courses', 'students', 'teachers'));
    }
} 