<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id');
            $table->float('cholesterol')->nullable();
            $table->float('hdl_cholesterol')->nullable();
            $table->float('systolic_bp')->nullable();
            $table->float('fbs')->nullable();
            $table->float('hba1c')->nullable();
            $table->boolean('hypertension')->nullable();
            $table->boolean('diabetes')->nullable();
            $table->boolean('smoking')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
