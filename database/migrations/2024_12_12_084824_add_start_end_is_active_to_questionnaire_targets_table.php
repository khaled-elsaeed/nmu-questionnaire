<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaire_targets', function (Blueprint $table) {
            $table->date('start')->nullable()->after('scope_type'); // Adding the start column
            $table->date('end')->nullable()->after('start'); // Adding the end column
            $table->boolean('is_active')->default(true)->after('end'); // Adding the is_active column
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_targets', function (Blueprint $table) {
            $table->dropColumn(['start', 'end', 'is_active']);
        });
    }
};
