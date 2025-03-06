<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Course;
use Faker\Factory as Faker;
use Carbon\Carbon;

class StudentCourseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create or ensure courses exist
        $courses = [
            [
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'description' => 'A program that focuses on IT and computer systems.',
                'credits' => 4,
                'status' => 'active'
            ],
            [
                'code' => 'BSEMC',
                'name' => 'Bachelor of Science in Entertainment and Multimedia Computing',
                'description' => 'A program that focuses on multimedia and digital entertainment.',
                'credits' => 4,
                'status' => 'active'
            ],
            [
                'code' => 'BSET',
                'name' => 'Bachelor of Science in Electronics Technology',
                'description' => 'A program that focuses on electronics and technology.',
                'credits' => 4,
                'status' => 'active'
            ]
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }

        // Get all courses
        $courseIds = Course::whereIn('code', ['BSIT', 'BSEMC', 'BSET'])->pluck('id');

        // Create 50 students
        for ($i = 0; $i < 50; $i++) {
            $student = Student::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-25 years', '-18 years'),
                'student_id' => $faker->unique()->numerify('STU####'),
                'enrollment_date' => Carbon::now(),
                'status' => 'active'
            ]);

            // Randomly assign 1-3 courses to each student
            $numberOfCourses = rand(1, 3);
            $selectedCourses = $courseIds->random($numberOfCourses);

            foreach ($selectedCourses as $courseId) {
                $student->courses()->attach($courseId, [
                    'enrollment_date' => Carbon::now()->subDays(rand(0, 30)),
                    'grade' => rand(0, 1) ? rand(75, 100) : null // 50% chance of having a grade
                ]);
            }
        }
    }
} 