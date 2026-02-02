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

            // Matches: `name` varchar(255) NOT NULL
            $table->string('name');

            // Matches: `username` varchar(255) NOT NULL + UNIQUE KEY
            // Note: Your SQL dump named the index 'users_email_unique', but the column is 'username'
            $table->string('username')->unique();

            // Matches: `password` varchar(255) NOT NULL
            $table->string('password');

            // Matches: `remember_token` varchar(100) DEFAULT NULL
            $table->rememberToken();

            // Matches: `created_at` & `updated_at`
            $table->timestamps();

            // --- New Columns based on SQL ---

            // Matches: `first_name` varchar(100) DEFAULT NULL
            $table->string('first_name', 100)->nullable();

            // Matches: `last_name` varchar(100) DEFAULT NULL
            $table->string('last_name', 100)->nullable();

            // Matches: `occupation` varchar(100) DEFAULT NULL
            $table->string('occupation', 100)->nullable();

            // Matches: `is_admin` tinyint(1) NOT NULL DEFAULT 0
            $table->boolean('is_admin')->default(0);

            // Matches: `is_doctor` tinyint(1) DEFAULT 0 (SQL allows NULL implicitly here)
            $table->boolean('is_doctor')->nullable()->default(0);

            // Matches: `ai_access` tinyint(1) NOT NULL DEFAULT 0
            $table->boolean('ai_access')->default(0);

            // Matches: `openai_api_key` text DEFAULT NULL
            $table->text('openai_api_key')->nullable();

            // Matches: `ai_prompt` text DEFAULT NULL
            $table->text('ai_prompt')->nullable();
        });

        // Default Laravel tables (kept as provided in your original file)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
