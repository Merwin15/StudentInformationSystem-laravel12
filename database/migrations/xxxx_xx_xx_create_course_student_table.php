<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamps();

            // Add a unique constraint to prevent duplicate enrollments
            $table->unique(['course_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_student');
    }
}; 