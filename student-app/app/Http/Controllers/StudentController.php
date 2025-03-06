<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'student_id' => 'required|string|unique:students',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'course' => 'required|string|in:BSIT,BSFT,BSEMC',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load('courses');
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'student_id' => 'required|string|unique:students,student_id,' . $student->id,
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'course' => 'required|string|in:BSIT,BSFT,BSEMC',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Check if student is enrolled in any courses
        if ($student->courses()->exists()) {
            return redirect()->route('students.index')
                ->with('error', 'Cannot delete student: Currently enrolled in ' . 
                       $student->courses()->count() . ' course(s). Please remove from all courses first.');
        }

        try {
            DB::beginTransaction();
            $student->delete();
            DB::commit();
            
            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('students.index')
                ->with('error', 'Error deleting student. Please try again.');
        }
    }

    /**
     * Display the course management page for a student.
     */
    public function courses(Student $student)
    {
        $enrolledCoursesByYear = $student->getEnrolledCoursesByYear();
        
        $availableCourses = Course::whereDoesntHave('students', function($query) use ($student) {
            $query->where('students.id', $student->id);
        })->paginate(10);

        return view('students.courses', compact('student', 'enrolledCoursesByYear', 'availableCourses'));
    }

    /**
     * Add a course to a student's enrollment.
     */
    public function addCourse(Request $request, Student $student, Course $course)
    {
        try {
            if (!$student->courses->contains($course->id)) {
                $student->courses()->attach($course->id, [
                    'enrollment_date' => now(),
                ]);
                return redirect()->route('students.courses', $student)
                    ->with('success', 'Course added successfully.');
            }
            
            return redirect()->route('students.courses', $student)
                ->with('error', 'Student is already enrolled in this course.');
        } catch (\Exception $e) {
            return redirect()->route('students.courses', $student)
                ->with('error', 'Error adding course. Please try again.');
        }
    }

    /**
     * Remove a course from a student's enrollment.
     */
    public function removeCourse(Request $request, Student $student, Course $course)
    {
        try {
            // Check if the student is enrolled in the course
            $enrollment = $student->courses()
                ->wherePivot('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('students.courses', $student)
                    ->with('error', 'Student is not enrolled in this course.');
            }

            // Check if the course has a grade
            if (!is_null($enrollment->pivot->grade)) {
                return redirect()->route('students.courses', $student)
                    ->with('error', 'Cannot remove course: Student has a grade in this course.');
            }

            // Check if the enrollment is from a past semester
            $enrollmentDate = $enrollment->pivot->enrollment_date instanceof \Carbon\Carbon 
                ? $enrollment->pivot->enrollment_date 
                : \Carbon\Carbon::parse($enrollment->pivot->enrollment_date);

            if (!$enrollmentDate->isCurrentQuarter()) {
                return redirect()->route('students.courses', $student)
                    ->with('error', 'Cannot remove course: Past semester enrollment.');
            }

            // If all checks pass, remove the course
            $student->courses()->detach($course->id);
            
            return redirect()->route('students.courses', $student)
                ->with('success', 'Course removed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('students.courses', $student)
                ->with('error', 'Error removing course. Please try again.');
        }
    }

    /**
     * Enroll a student in a course.
     */
    public function enroll(Request $request, Student $student)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'enrollment_date' => 'required|date',
        ]);

        try {
            if (!$student->courses->contains($validated['course_id'])) {
                $student->courses()->attach($validated['course_id'], [
                    'enrollment_date' => $validated['enrollment_date'],
                ]);
                return redirect()->route('students.courses', $student)
                    ->with('success', 'Student enrolled in course successfully.');
            }
            
            return redirect()->route('students.courses', $student)
                ->with('error', 'Student is already enrolled in this course.');
        } catch (\Exception $e) {
            return redirect()->route('students.courses', $student)
                ->with('error', 'Error enrolling student. Please try again.');
        }
    }

    /**
     * Delete all students that are not enrolled in any courses.
     */
    public function deleteAll()
    {
        try {
            DB::beginTransaction();
            
            // Get students with no courses
            $studentsToDelete = Student::whereDoesntHave('courses')->get();
            $totalStudents = Student::count();
            $deletableCount = $studentsToDelete->count();
            
            if ($deletableCount === 0) {
                return redirect()->route('students.index')
                    ->with('error', 'No students can be deleted: All students are enrolled in courses.');
            }

            // Delete students with no courses
            Student::whereDoesntHave('courses')->delete();
            
            DB::commit();
            
            if ($deletableCount < $totalStudents) {
                return redirect()->route('students.index')
                    ->with('warning', "Deleted {$deletableCount} students. " . 
                           ($totalStudents - $deletableCount) . " students could not be deleted because they are enrolled in courses.");
            }
            
            return redirect()->route('students.index')
                ->with('success', "Successfully deleted all {$deletableCount} students.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('students.index')
                ->with('error', 'Error deleting students: ' . $e->getMessage());
        }
    }
}
