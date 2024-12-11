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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('username_en');
            $table->boolean('is_active')->default(1); // Default to 1 (active)
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft delete functionality
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id(); 
            $table->string('email')->unique(); // Unique email column
            $table->string('token'); // Token column
            $table->timestamp('token_expires_at'); // Expiration timestamp
            $table->timestamps(); // Created at and updated at timestamps
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
