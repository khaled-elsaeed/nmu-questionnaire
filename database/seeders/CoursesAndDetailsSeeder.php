<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseDetail;
use Illuminate\Database\Seeder;

class CoursesAndDetailsSeeder extends Seeder
{
    public function run()
    {
        // Define the courses
        $courses = [
            ['name' => 'Introduction to Programming', 'course_code' => 'CS101'],
            ['name' => 'Data Structures and Algorithms', 'course_code' => 'CS102'],
            ['name' => 'Discrete Mathematics', 'course_code' => 'CS103'],
            ['name' => 'Computer Networks', 'course_code' => 'CS201'],
            ['name' => 'Operating Systems', 'course_code' => 'CS202'],
            ['name' => 'Database Systems', 'course_code' => 'CS203'],
            ['name' => 'Software Engineering', 'course_code' => 'CS301'],
            ['name' => 'Artificial Intelligence', 'course_code' => 'CS302'],
            ['name' => 'Machine Learning', 'course_code' => 'CS303'],
            ['name' => 'Computer Graphics', 'course_code' => 'CS304'],
        ];

        // Define the course details
        $courseDetails = [
            ['course_code' => 'CS101', 'term' => 'fall', 'academic_year' => 2024, 'teaching_assistant_name' => 'Mohamed Ali'],
            ['course_code' => 'CS102', 'term' => 'spring', 'academic_year' => 2024, 'teaching_assistant_name' => 'Ahmed Mostafa'],
            ['course_code' => 'CS103', 'term' => 'summer', 'academic_year' => 2024, 'teaching_assistant_name' => 'Sara Ahmed'],
            ['course_code' => 'CS201', 'term' => 'fall', 'academic_year' => 2024, 'teaching_assistant_name' => 'Mohamed Ibrahim'],
            ['course_code' => 'CS202', 'term' => 'spring', 'academic_year' => 2024, 'teaching_assistant_name' => 'Ali Hassan'],
            ['course_code' => 'CS203', 'term' => 'summer', 'academic_year' => 2024, 'teaching_assistant_name' => 'Laila Youssef'],
            ['course_code' => 'CS301', 'term' => 'fall', 'academic_year' => 2024, 'teaching_assistant_name' => 'Tamer Hossam'],
            ['course_code' => 'CS302', 'term' => 'spring', 'academic_year' => 2024, 'teaching_assistant_name' => 'Nadia Fawzy'],
            ['course_code' => 'CS303', 'term' => 'summer', 'academic_year' => 2024, 'teaching_assistant_name' => 'Hassan El-Banna'],
            ['course_code' => 'CS304', 'term' => 'fall', 'academic_year' => 2024, 'teaching_assistant_name' => 'Dalia Ibrahim'],
        ];

        // Seed courses and their details
        foreach ($courses as $courseData) {
            // Create a course
            $course = Course::create([
                'name' => $courseData['name'],
                'course_code' => $courseData['course_code'],
            ]);

            // Find corresponding course details
            foreach ($courseDetails as $detailData) {
                if ($detailData['course_code'] === $courseData['course_code']) {
                    CourseDetail::create([
                        'course_id' => $course->id,
                        'term' => $detailData['term'],
                        'academic_year' => $detailData['academic_year'],
                        'teaching_assistant_name' => $detailData['teaching_assistant_name'],
                    ]);
                }
            }
        }
    }
}
