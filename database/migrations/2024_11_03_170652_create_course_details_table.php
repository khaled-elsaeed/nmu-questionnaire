<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('term', ['fall', 'spring', 'summer']);
            $table->integer('academic_year');
            $table->string('teaching_assistant_name')->nullable(); // Store TA name directly
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_details');
    }
}
