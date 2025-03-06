<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'employee_id',
        'department',
        'status'
    ];

    /**
     * Get the courses taught by the teacher.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the total number of students across all courses.
     */
    public function getTotalStudentsAttribute()
    {
        return $this->courses->sum(function ($course) {
            return $course->students_count ?? 0;
        });
    }

    /**
     * Get the average grade across all courses.
     */
    public function getAverageGradeAttribute()
    {
        $totalGrades = 0;
        $totalStudents = 0;

        foreach ($this->courses as $course) {
            $courseAvg = $course->students()->avg('grade');
            $studentCount = $course->students()->count();
            
            if ($courseAvg && $studentCount) {
                $totalGrades += $courseAvg * $studentCount;
                $totalStudents += $studentCount;
            }
        }

        return $totalStudents > 0 ? round($totalGrades / $totalStudents, 2) : null;
    }
} 