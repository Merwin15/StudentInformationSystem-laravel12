<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'credits',
        'status',
        'teacher_id'
    ];

    /**
     * Get the teacher that teaches the course.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student')
            ->withPivot('enrollment_date', 'grade')
            ->withTimestamps()
            ->using(CourseStudent::class);
    }

    /**
     * Check if a student is enrolled in the course.
     */
    public function hasStudent($studentId)
    {
        return $this->students()
            ->wherePivot('student_id', $studentId)
            ->exists();
    }

    /**
     * Get available courses for a student (courses they're not enrolled in).
     */
    public function scopeAvailableForStudent($query, $studentId)
    {
        return $query->whereDoesntHave('students', function($query) use ($studentId) {
            $query->where('students.id', $studentId);
        });
    }
} 