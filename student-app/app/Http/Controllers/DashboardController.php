<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all students for the table
        $students = Student::latest()->get();

        // Get counts for cards
        $totalStudents = $students->count();
        $totalTeachers = Teacher::count();
        $totalCourses = Course::count();
        $activeEnrollments = DB::table('course_student')->count();

        // Get student status distribution
        $studentStatusDistribution = Student::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all status types are represented
        $allStatuses = ['active', 'inactive', 'graduated', 'suspended'];
        foreach ($allStatuses as $status) {
            if (!isset($studentStatusDistribution[$status])) {
                $studentStatusDistribution[$status] = 0;
            }
        }

        // Get monthly enrollments for the current year
        $monthlyEnrollments = DB::table('course_student')
            ->select(DB::raw('MONTH(enrollment_date) as month'), DB::raw('count(*) as count'))
            ->whereYear('enrollment_date', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months with zero
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyEnrollments[$i])) {
                $monthlyEnrollments[$i] = 0;
            }
        }
        ksort($monthlyEnrollments);

        // Convert month numbers to names
        $monthlyEnrollments = collect($monthlyEnrollments)->mapWithKeys(function ($value, $key) {
            return [Carbon::create()->month($key)->format('M') => $value];
        })->toArray();

        // Get recent activities (enrollments)
        $recentActivities = DB::table('course_student')
            ->join('students', 'course_student.student_id', '=', 'students.id')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->select(
                'students.name as student_name',
                'courses.name as course_name',
                'course_student.enrollment_date'
            )
            ->orderBy('course_student.enrollment_date', 'desc')
            ->limit(5)
            ->get();

        // Get top courses by enrollment
        $topCourses = Course::withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'students',
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'activeEnrollments',
            'studentStatusDistribution',
            'monthlyEnrollments',
            'recentActivities',
            'topCourses'
        ));
    }
} 