<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unit_group', function (Blueprint $table) {
            $table->id();
            $table->integer('id');
            $table->string('unit_group_code');
            $table->string('unit_group_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_group');
    }
};
