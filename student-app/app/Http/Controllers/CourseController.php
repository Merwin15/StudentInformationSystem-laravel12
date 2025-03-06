<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::with('teacher')
            ->withCount('students')
            ->latest()
            ->get();

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $teachers = Teacher::where('status', 'active')->get();
        return view('courses.create', compact('teachers'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:courses,code|max:10',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:6',
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:active,inactive'
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['teacher', 'students' => function($query) {
            $query->withPivot('enrollment_date', 'grade');
        }]);

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $teachers = Teacher::where('status', 'active')->get();
        return view('courses.edit', compact('course', 'teachers'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:courses,code,' . $course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:6',
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:active,inactive'
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        // Check if course has enrolled students
        if ($course->students()->exists()) {
            return redirect()->route('courses.index')
                ->with('error', 'Cannot delete course with enrolled students.');
        }

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Show the student enrollment form.
     */
    public function showEnrollment(Course $course)
    {
        $availableStudents = Student::whereDoesntHave('courses', function($query) use ($course) {
            $query->where('courses.id', $course->id);
        })->get();

        return view('courses.enroll', compact('course', 'availableStudents'));
    }

    /**
     * Enroll students in the course.
     */
    public function enroll(Request $request, Course $course)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $enrollmentDate = now();

        foreach ($validated['student_ids'] as $studentId) {
            if (!$course->hasStudent($studentId)) {
                $course->students()->attach($studentId, [
                    'enrollment_date' => $enrollmentDate
                ]);
            }
        }

        return redirect()->route('courses.show', $course)
            ->with('success', 'Students enrolled successfully.');
    }

    /**
     * Remove a student from the course.
     */
    public function removeStudent(Course $course, Student $student)
    {
        if ($course->hasStudent($student->id)) {
            $course->students()->detach($student->id);
            return redirect()->route('courses.show', $course)
            
                ->with('success', 'Student removed from course successfully.');
        }

        return redirect()->route('courses.show', $course)
            ->with('error', 'Student is not enrolled in this course.');
    }

    /**
     * Remove the grade for a specific student in the course.
     */
    public function deleteGrade(Course $course, Student $student)
    {
        // Find the pivot record and update the grade to null
        $course->students()->updateExistingPivot($student->id, ['grade' => null]);

        return redirect()->back()->with('success', 'Grade has been removed successfully.');
    }

    /**
     * Update grades for multiple students in the course.
     */
    public function updateGrades(Request $request, Course $course)
    {
        $validated = $request->validate([
            'grades.*' => 'nullable|numeric|min:0|max:100'
        ]);

        foreach ($request->grades ?? [] as $studentId => $grade) {
            $course->students()->updateExistingPivot($studentId, [
                'grade' => $grade
            ]);
        }

        return redirect()->back()->with('success', 'Grades have been updated successfully.');
    }

    public function updateStudentGrade(Request $request, Course $course, Student $student)
    {
        $validated = $request->validate([
            'grade' => 'nullable|numeric|min:0|max:100'
        ]);

        $course->students()->updateExistingPivot($student->id, [
            'grade' => $validated['grade']
        ]);

        return redirect()->back()->with('success', 'Grade updated successfully.');
    }
} 