<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status', function (Blueprint $table) {
            // Matches: id tinyint(1) NOT NULL AUTO_INCREMENT
            $table->tinyIncrements('id');

            // Matches: status_name varchar(20) DEFAULT NULL
            $table->string('status_name', 20)->nullable();

            // Note: Timestamps (created_at, updated_at) are omitted
            // because they are not present in the provided SQL schema.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};
