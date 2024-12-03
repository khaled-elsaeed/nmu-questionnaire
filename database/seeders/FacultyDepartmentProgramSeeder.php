<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultyDepartmentProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            'Faculty of Engineering' => [
                'Mechanical Engineering' => ['Automotive Engineering', 'Production Engineering'],
                'Electrical Engineering' => ['Power Systems', 'Communication Systems'],
                'Civil Engineering' => ['Structural Engineering', 'Environmental Engineering'],
            ],
            'Faculty of Medicine' => [
                'General Medicine' => ['Surgery', 'Pediatrics'],
                'Clinical Medicine' => ['Dermatology', 'Radiology'],
            ],
            'Faculty of Law' => [
                'Public Law' => ['Constitutional Law', 'Administrative Law'],
                'Private Law' => ['Contract Law', 'Family Law'],
            ],
            'Faculty of Commerce' => [
                'Accounting' => ['Financial Accounting', 'Auditing'],
                'Management' => ['Marketing', 'Human Resources'],
            ],
        ];

        foreach ($faculties as $facultyName => $departments) {
            $facultyId = DB::table('faculties')->insertGetId([
                'name' => $facultyName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($departments as $departmentName => $programs) {
                $departmentId = DB::table('departments')->insertGetId([
                    'faculty_id' => $facultyId,
                    'name' => $departmentName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($programs as $programName) {
                    DB::table('programs')->insert([
                        'department_id' => $departmentId,
                        'name' => $programName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
