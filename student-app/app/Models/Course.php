<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
                    ->withPivot('enrollment_date', 'grade')
                    ->withTimestamps();
    }

    /**
     * Check if a student is enrolled in the course.
     */
    public function hasStudent($studentId): bool
    {
        return $this->students()->where('student_id', $studentId)->exists();
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