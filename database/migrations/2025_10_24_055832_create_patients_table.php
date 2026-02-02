<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('unit')->nullable();

            // Changed from float to double to match SQL schema precision
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->double('bmi')->nullable();

            // Missing column from your original file
            // Matches: history_id bigint(20) DEFAULT NULL
            $table->foreignId('history_id')->nullable();

            $table->timestamps();

            // Composite Index
            // Matches: KEY last_name (last_name, first_name, middle_name)
            $table->index(['last_name', 'first_name', 'middle_name'], 'last_name');

            // Foreign Key Constraint
            // Matches: CONSTRAINT patients_ibfk_1 FOREIGN KEY (history_id) REFERENCES family_histories...
            $table->foreign('history_id', 'patients_ibfk_1')
                ->references('id')
                ->on('family_histories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
