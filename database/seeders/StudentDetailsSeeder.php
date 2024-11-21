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
        // Create a new user
        $user = User::create([
            'email' => 'student@example.com', // Replace with an email
            'username_en' => 'student_en',    // English username
            'username_ar' => 'student_ar',    // Arabic username
            'password' => Hash::make('password123'), // Set a default password
            'is_active' => 1,
            'profile_picture' => null, // You can add a path to a profile picture if needed
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