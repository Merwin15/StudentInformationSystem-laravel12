<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->unique(['student_id', 'course_id'], 'unique_student_course');
        });
    }

    public function down()
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropUnique('unique_student_course');
        });
    }
}; 