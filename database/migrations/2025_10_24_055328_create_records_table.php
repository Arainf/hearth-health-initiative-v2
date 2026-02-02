<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();

            // Note: The SQL has a KEY for patient_id, but the provided CONSTRAINT list
            // did not strictly define a foreign key relation for it.
            // If you need the constraint, append ->constrained()
            $table->foreignId('patient_id')->index();

            // SQL uses 'double', which offers better precision than 'float'
            $table->double('cholesterol')->nullable();
            $table->double('hdl_cholesterol')->nullable();
            $table->double('systolic_bp')->nullable();
            $table->double('fbs')->nullable();
            $table->double('hba1c')->nullable();

            $table->boolean('hypertension')->nullable();
            $table->boolean('diabetes')->nullable();
            $table->boolean('smoking')->nullable();

            // New columns based on SQL Schema
            $table->unsignedTinyInteger('status_id')->default(3);
            $table->foreignId('generated_id')->nullable();
            $table->boolean('is_archived')->default(0);
            $table->string('staff_id');
            $table->string('approved_by')->nullable();

            $table->timestamps();

            // Foreign Key Constraints
            // Matches: CONSTRAINT records_ibfk_2 FOREIGN KEY (status_id)...
            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('cascade');

            // Matches: CONSTRAINT records_generated_fk FOREIGN KEY (generated_id)...
            $table->foreign('generated_id', 'records_generated_fk')
                ->references('id')
                ->on('generated_reports')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Composite Index
            // Matches: KEY `Patient Details` ...
            $table->index([
                'cholesterol',
                'hdl_cholesterol',
                'systolic_bp',
                'fbs',
                'hba1c',
                'hypertension',
                'diabetes',
                'smoking',
                'status_id'
            ], 'Patient Details');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
