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
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Title of the questionnaire
            $table->text('description'); // Description of the questionnaire
            $table->date('start_date'); // Start date for the questionnaire
            $table->date('end_date'); // End date for the questionnaire
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};

