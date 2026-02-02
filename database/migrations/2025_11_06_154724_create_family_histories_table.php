<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_histories', function (Blueprint $table) {
            $table->id();

            // Boolean columns matching the SQL 'tinyint(1)' and capitalized naming
            $table->boolean('Hypertension');
            $table->boolean('Diabetes');
            $table->boolean('Heart_Attack');
            $table->boolean('Cholesterol');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_histories');
    }
};
