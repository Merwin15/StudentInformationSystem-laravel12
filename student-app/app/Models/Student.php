<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'student_id',
        'enrollment_date',
        'status',
        'course'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    /**
     * Get the courses the student is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
                    ->using(CourseStudent::class)
                    ->withPivot('enrollment_date', 'grade')
                    ->withTimestamps();
    }

    /**
     * Get courses grouped by enrollment year
     */
    public function getEnrolledCoursesByYear()
    {
        return $this->courses()
            ->with('teacher')
            ->get()
            ->groupBy(function($course) {
                // Check if enrollment_date is already a Carbon instance
                if ($course->pivot->enrollment_date instanceof \Carbon\Carbon) {
                    return $course->pivot->enrollment_date->format('Y');
                }
                // If it's a string, parse it into a Carbon instance
                return \Carbon\Carbon::parse($course->pivot->enrollment_date)->format('Y');
            })
            ->sortKeysDesc();
    }

    /**
     * Get available courses for the student (courses they're not enrolled in).
     */
    public function availableCourses()
    {
        return Course::availableForStudent($this->id)->get();
    }

    /**
     * Get the student's current GPA.
     */
    public function getGpaAttribute()
    {
        $courses = $this->courses()->wherePivotNotNull('grade')->get();
        
        if ($courses->isEmpty()) {
            return 0;
        }

        $totalPoints = $courses->sum(function ($course) {
            return $course->pivot->grade * $course->credits;
        });

        $totalCredits = $courses->sum('credits');

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }
}
