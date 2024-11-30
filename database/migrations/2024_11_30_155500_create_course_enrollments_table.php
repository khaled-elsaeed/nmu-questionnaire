<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_course_enrollments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseEnrollmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_details')->onDelete('cascade');
            $table->foreignId('course_detail_id')->constrained('course_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
}
