<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questionnaire_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained('questionnaires')->onDelete('cascade');
            $table->foreignId('dept_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade'); // New field for course_id
            $table->enum('role_name', ['student', 'staff', 'teaching_assistant'])->notNullable();
            $table->integer('level')->nullable(); // Made level nullable
            $table->enum('scope_type', ['local', 'global'])->notNullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_targets');
    }
};
