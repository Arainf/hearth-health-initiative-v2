<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->integer('id');
            $table->string('unit_code');
            $table->string('unit_name');
            $table->string('unit_abbr');
            $table->string('unit_group_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
