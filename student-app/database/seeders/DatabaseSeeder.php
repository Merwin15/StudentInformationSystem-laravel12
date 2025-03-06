<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Course;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Teachers first
        $teachers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@school.com',
                'phone' => '1234567890',
                'employee_id' => 'T001',
                'department' => 'Mathematics',
                'status' => 'active'
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@school.com',
                'phone' => '0987654321',
                'employee_id' => 'T002',
                'department' => 'Science',
                'status' => 'active'
            ]
        ];

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }

        // Create Courses
        $courses = [
            [
                'code' => 'MATH101',
                'name' => 'Basic Mathematics',
                'description' => 'Introduction to basic mathematics concepts',
                'credits' => 3,
                'status' => 'active',
                'teacher_id' => 1
            ],
            [
                'code' => 'SCI101',
                'name' => 'General Science',
                'description' => 'Introduction to scientific principles',
                'credits' => 4,
                'status' => 'active',
                'teacher_id' => 2
            ]
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        // Call StudentSeeder to create 50 students
        $this->call([
            StudentSeeder::class,
        ]);

        // Randomly enroll students in courses
        $this->command->info('Enrolling students in courses...');
        
        // Get all students and courses
        $students = \App\Models\Student::all();
        $courseIds = Course::pluck('id')->toArray();
        
        foreach ($students as $student) {
            // Randomly enroll each student in 1-2 courses
            $numCourses = rand(1, 2);
            $selectedCourses = array_rand($courseIds, $numCourses);
            
            if (!is_array($selectedCourses)) {
                $selectedCourses = [$selectedCourses];
            }
            
            foreach ($selectedCourses as $courseId) {
                $student->courses()->attach($courseIds[$courseId], [
                    'enrollment_date' => Carbon::now(),
                    'grade' => rand(70, 100)
                ]);
            }
        }
    }
}
