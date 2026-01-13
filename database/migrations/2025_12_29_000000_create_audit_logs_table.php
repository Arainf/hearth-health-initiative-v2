<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestampTz('occurred_at')->index();
            $table->string('event_type')->index();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('request_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();

            $table->string('http_method', 10)->nullable();
            $table->string('route_name')->nullable()->index();
            $table->text('route_uri')->nullable();
            $table->text('url')->nullable();
            $table->text('referer')->nullable();

            $table->unsignedSmallInteger('status_code')->nullable()->index();
            $table->unsignedInteger('latency_ms')->nullable();

            $table->string('subject_type')->nullable()->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();

            $table->string('action')->nullable();
            $table->string('outcome')->nullable();
            $table->string('failure_reason')->nullable();

            $table->json('metadata_json')->nullable();

            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['subject_type', 'subject_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
