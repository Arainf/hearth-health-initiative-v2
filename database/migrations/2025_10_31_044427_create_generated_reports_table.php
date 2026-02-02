<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();

            // SQL: `generated_text` longtext NOT NULL
            // 'longText' is required here; 'string' would truncate data at 255 chars
            $table->longText('generated_text');

            // SQL: `staff_generated` int(11) NOT NULL
            $table->integer('staff_generated');

            // SQL: `staff_updates` varchar(255) NOT NULL
            $table->string('staff_updates');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
