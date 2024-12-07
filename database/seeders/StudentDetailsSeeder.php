<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\StudentDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentDetailsSeeder extends Seeder
{
    public function run()
    {
        // Create 3 students
        $students = [
            [
                'email' => 'student1@example.com',
                'username_en' => 'student_en1',
                'username_ar' => 'student_ar1',
                'password' => 'password123', // Set a default password
            ],
            [
                'email' => 'student2@example.com',
                'username_en' => 'student_en2',
                'username_ar' => 'student_ar2',
                'password' => 'password123',
            ],
            [
                'email' => 'student3@example.com',
                'username_en' => 'student_en3',
                'username_ar' => 'student_ar3',
                'password' => 'password123',
            ],
        ];

        foreach ($students as $studentData) {
            // Create user for each student
            $user = User::create([
                'email' => $studentData['email'],
                'username_en' => $studentData['username_en'],
                'username_ar' => $studentData['username_ar'],
                'password' => Hash::make($studentData['password']),
                'is_active' => 1,
                'profile_picture' => null, // Add a path to a profile picture if needed
                'last_login' => now(),
            ]);

            // Assign 'student' role to the user
            $studentRole = Role::findByName('student'); // Assumes 'student' role exists
            $user->assignRole($studentRole);

            // Create student-specific details
            StudentDetail::create([
                'user_id' => $user->id,
                'faculty_id' => 1, // Assign a faculty ID that exists in your faculties table
                'department_id' => 1, // Assign a department ID that exists in your departments table
                'program_id' => 1, // Assign a program ID that exists in your programs table
                'level' => 1, // The level (e.g., 1 for first year)
            ]);
        }
    }
}
