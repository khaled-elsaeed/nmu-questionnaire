<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use App\Models\Course;
use App\Models\CourseDetail;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\StudentDetail;
use App\Models\User;
use App\Models\CourseEnrollment;
use Carbon\Carbon;
use App\Notifications\SendLoginCredentials;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class CoursesAndStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'student' role exists
        Role::firstOrCreate(['name' => 'student']);

        // Path to the CSV file
        $filePath = database_path('data/students_enrollments.csv');

        // Load the spreadsheet
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Loop through each row in the sheet, starting from row 2 (skipping header)
        foreach ($sheet->getRowIterator(2) as $row) {
            // Get data from the current row
            $data = $this->getRowData($row);

            // Insert course if it doesn't exist
            $courseId = $this->insertCourses($data);

            // Insert course details (without the teacher ID for now)
            $courseDetailId = $this->insertCourseDetails($data, $courseId);

            // Insert faculty information if necessary
            $facultyId = $this->insertFaculty($data);

            // Insert program information if necessary
            $programId = $this->insertProgram($data, $facultyId);  // Now associates faculty ID

            // Insert student details
            $userId = $this->insertStudentDetails($data, $facultyId, $programId);

            // Enroll the student in the course detail
            $this->enrollStudentInCourse($userId, $courseDetailId);  // Using user_id to get student_id


        }
    }

    private function loadSpreadsheet(string $filePath)
    {
        return IOFactory::load($filePath);
    }

    private function getRowData($row): array
    {
        $cells = $row->getCellIterator();
        $cells->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cells as $cell) {
            $data[] = $cell->getValue();
        }

        return $data;
    }

    private function insertCourses(array $data)
    {
        $courseId = $data[9];  
        $courseName = $data[10]; 

        // Check if the course already exists
        $courseExist = Course::where('course_code', $courseId)->first();

        if ($courseExist) {
            return $courseExist->id;
        }

        $course = Course::create([
            'name' => $courseName,
            'course_code' => $courseId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $course->id;
    }

    private function insertCourseDetails(array $data, int $courseId)
    {
        $term = 'spring';  // Assuming fixed value for now
        $academicYear = '2024/2025'; // Assuming fixed value for now
    
        // Check if the course detail already exists
        $existingCourseDetail = CourseDetail::where('course_id', $courseId)
                                            ->where('term', $term)
                                            ->where('academic_year', $academicYear)
                                            ->first();
    
        if ($existingCourseDetail) {
            // If course detail already exists, do nothing
            return $existingCourseDetail->id;
        }
    
        // If course detail does not exist, create a new one
        $courseDetail = CourseDetail::create([
            'course_id' => $courseId,
            'term' => $term,
            'academic_year' => $academicYear,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $courseDetail->id;
    }

    private function insertFaculty(array $data)
    {
        $facultyName = $data[4];  // Assuming faculty name is in column 5

        $facultyExist = Faculty::where('name', $facultyName)->first();

        if ($facultyExist) {
            return $facultyExist->id;
        }

        $faculty = Faculty::create([
            'name' => $facultyName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $faculty->id;
    }

    private function insertProgram(array $data, int $faculty)
    {
        $programName = $data[6];  

        $programExist = Program::where('name', $programName)->first();

        if ($programExist) {
            return $programExist->id;
        }

        $program = Program::create([
            'name' => $programName,
            'faculty_id' => $faculty,  
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $program->id;
    }

    private function insertStudentDetails(array $data, int $facultyId, int $programId)
    {
        // Check if the user already exists
        $existingUser = User::where('email', $data[13])->first(); 

        if ($existingUser) {
            // If user exists, we just use the existing user
            $userId = $existingUser->id;
        } else {
            // If user does not exist, create a new user
            $userId = $this->createUser($data); 
        }

        $user = User::find($userId);

        // Assign 'student' role to the user if not already assigned
        if (!$user->hasRole('student')) {
            $user->assignRole('student');
        }

        // Check if the student detail already exists
        $existingStudentDetail = StudentDetail::where('user_id', $userId)
                                              ->where('faculty_id', $facultyId)
                                              ->where('program_id', $programId)
                                              ->first();

        if ($existingStudentDetail) {
            // If student details exist, do nothing
            return $userId;
        }

        // Assign the academic level (assuming academic ID is in column 8)
        $level = $this->getLevel($data[7]);

        // Insert the student details
        StudentDetail::create([
            'user_id' => $userId,
            'faculty_id' => $facultyId,
            'program_id' => $programId,
            'level' => $level,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $userId;
    }


    
    private function createUser(array $data)
    {
        $fullName = $data[8];  
    
        $nameParts = explode(' ', $fullName);
    
        $firstName = $nameParts[0]; 
        $lastName = end($nameParts);  
    
        // Create a username
        $username = strtolower($firstName . ' ' . $lastName);
    
        // Generate a strong password
        $password = $this->generatePassword();
    
        // Hash the password before storing it
        $hashedPassword = Hash::make($password['hashed']);
    
        // Create the user with hashed password
        $user = User::create([
            'email' => $data[13],  
            'username_en' => $username, 
            'password' => $hashedPassword,  
            'is_active' => 1,  
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    
        $user->notify(new SendLoginCredentials($user->username_en, $user->email, $password['not_hashed']));
    
        return $user->id;
    }
    
    private function generatePassword()
    {
        $password = Str::random(9); 
    
        
        $password = $this->ensureStrongPassword($password);
    
        return [
            'not_hashed' => $password, 
            'hashed' => $password,     
        ];
    }
    
    private function ensureStrongPassword($password)
    {
        if (!preg_match('/[A-Z]/', $password)) {
            $password .= Str::random(1); 
        }
        if (!preg_match('/[a-z]/', $password)) {
            $password .= Str::random(1); 
        }
        if (!preg_match('/[0-9]/', $password)) {
            $password .= Str::random(1);
        }
        if (!preg_match('/[\W_]/', $password)) {
            $password .= Str::random(1);
        }
    
        return $password;
    }
    
    private function getLevel(string $academicId): int
    {
        // Determine level based on the academic ID prefix
        switch (substr($academicId, 0, 3)) {
            case '224':
                return 1;  
            case '223':
                return 2;  
            case '222':
                return 3;  
            case '221':
                return 4;  
            default:
                return 0;  
        }
    }

    // This is the enrollStudentInCourse method that links a student to a course
    private function enrollStudentInCourse(int $userId, int $courseDetailId)
    {
        // Retrieve the student details using the user_id
        $studentDetail = StudentDetail::where('user_id', $userId)->first();

        if (!$studentDetail) {
            // If no student detail found, return or handle error
            return;
        }

        $studentId = $studentDetail->id;  // Use student_id from student_details

        // Check if the enrollment already exists
        $existingEnrollment = CourseEnrollment::where('student_id', $studentId)
                                              ->where('course_detail_id', $courseDetailId)
                                              ->first();

        if (!$existingEnrollment) {
            // Enroll the student in the course detail
            CourseEnrollment::create([
                'student_id' => $studentId,  // Correctly using student_id here
                'course_detail_id' => $courseDetailId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
