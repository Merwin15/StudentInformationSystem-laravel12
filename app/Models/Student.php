<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public function availableCourses()
    {
        return Course::whereDoesntHave('students', function($query) {
            // Specify the table name to avoid ambiguity
            $query->where('students.id', $this->id);
        })->get();
    }
} 