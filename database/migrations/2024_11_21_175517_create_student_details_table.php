<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key referencing the users table
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->onDelete('set null'); // Faculty relation
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('set null'); // Program relation
            $table->integer('level')->nullable(); // Level of the student (e.g., Year 1, Year 2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
