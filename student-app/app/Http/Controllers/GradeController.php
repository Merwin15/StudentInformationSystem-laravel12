<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function update(Request $request, Course $course, Student $student)
    {
        $validated = $request->validate([
            'grade' => 'nullable|numeric|min:0|max:100'
        ]);

        $course->students()->updateExistingPivot($student->id, [
            'grade' => $validated['grade']
        ]);

        return redirect()->back()->with('success', 'Grade updated successfully');
    }

    public function updateBulk(Request $request, Course $course)
    {
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'nullable|numeric|min:0|max:100'
        ]);

        foreach ($validated['grades'] as $studentId => $grade) {
            $course->students()->updateExistingPivot($studentId, [
                'grade' => $grade
            ]);
        }

        return redirect()->back()->with('success', 'Grades updated successfully');
    }

    public function destroy(Course $course, Student $student)
    {
        $course->students()->updateExistingPivot($student->id, [
            'grade' => null
        ]);

        return redirect()->back()->with('success', 'Grade removed successfully');
    }
} 