<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index()
    {
        $teachers = Teacher::withCount('courses')
            ->latest()
            ->get();

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'employee_id' => 'required|string|max:20|unique:teachers',
            'department' => 'required|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        Teacher::create($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['courses' => function($query) {
            $query->withCount('students');
        }]);

        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'employee_id' => 'required|string|max:20|unique:teachers,employee_id,' . $teacher->id,
            'department' => 'required|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher)
    {
        // Check if teacher is assigned to any courses
        if ($teacher->courses()->exists()) {
            return redirect()->route('teachers.index')
                ->with('error', 'Cannot delete teacher: Currently assigned to ' . 
                       $teacher->courses()->count() . ' course(s). Please reassign courses first.');
        }

        try {
            DB::beginTransaction();
            $teacher->delete();
            DB::commit();
            
            return redirect()->route('teachers.index')
                ->with('success', 'Teacher deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('teachers.index')
                ->with('error', 'Error deleting teacher. Please try again.');
        }
    }

    /**
     * Display the teacher's courses.
     */
    public function courses(Teacher $teacher)
    {
        $teacher->load(['courses' => function($query) {
            $query->withCount('students');
        }]);

        return view('teachers.courses', compact('teacher'));
    }
} 