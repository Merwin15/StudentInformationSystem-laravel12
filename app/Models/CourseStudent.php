<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseStudent extends Pivot
{
    protected $table = 'course_student';

    protected $casts = [
        'enrollment_date' => 'datetime',
        'grade' => 'float'
    ];
} 