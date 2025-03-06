<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating 50 students...');
        
        $courses = ['BSIT', 'BSFT', 'BSEMC'];
        
        foreach ($courses as $course) {
            // Create 17 students for BSIT, 17 for BSFT, and 16 for BSEMC (total 50)
            $count = ($course === 'BSEMC') ? 16 : 17;
            
            $this->command->info("Creating {$count} {$course} students...");
            
            for ($i = 1; $i <= $count; $i++) {
                $year = fake()->numberBetween(2020, 2023);
                Student::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'student_id' => $course . str_pad($i, 3, '0', STR_PAD_LEFT) . '-' . $year,
                    'course' => $course,
                    'phone' => fake()->phoneNumber(),
                    'address' => fake()->address(),
                    'status' => 'active',
                    'date_of_birth' => fake()->dateTimeBetween('-25 years', '-17 years')->format('Y-m-d'), // Generate birth dates for college-age students
                    'enrollment_date' => $year . '-06-' . fake()->numberBetween(1, 30), // Random day in June of enrollment year
                ]);
            }
        }
        
        $this->command->info('Students created successfully!');
    }

    /**
     * Generate a student ID based on course and random number
     */
    private function generateStudentId($course): string
    {
        $year = date('Y');
        $coursePrefix = match ($course) {
            'BSIT' => 'IT',
            'BSEMC' => 'EMC',
            'BSFT' => 'FT',
            default => 'ST'
        };
        
        return sprintf('%s-%s-%04d', 
            $coursePrefix,
            $year,
            fake()->unique()->numberBetween(1, 9999)
        );
    }
} 