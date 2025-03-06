<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $course->load(['teacher', 'students' => function($query) {
            $query->withPivot('enrollment_date', 'grade');
        }]);

        return view('courses.show', compact('course'));
    }
} 