<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the 'teaching' table
        Schema::create('teaching', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('instructor_name'); // Instructor's name
            $table->string('instructor_email')->unique(); // Instructor's email, ensure it is unique
            $table->timestamps(); // Timestamps: created_at and updated_at
        });

        // Create the 'course_details' table
        Schema::create('course_details', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Foreign key to the 'courses' table
            $table->enum('term', ['fall', 'spring', 'summer']); // Term: fall, spring, or summer
            $table->string('academic_year'); // Academic year, e.g., '2023-2024'
            $table->foreignId('teaching_id')->nullable()->constrained('teaching')->onDelete('set null'); // Foreign key to the 'teaching' table, nullable in case no instructor assigned
            $table->timestamps(); // Timestamps: created_at and updated_at
        });
    }

    public function down(): void
    {
        // Drop the 'course_details' and 'teaching' tables if this migration is rolled back
        Schema::dropIfExists('course_details');
        Schema::dropIfExists('teaching');
    }
};
