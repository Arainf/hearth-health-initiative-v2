<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        $metadata = ['channel' => 'email'];
        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'auth.password.reset',
                'user_id' => $event->user?->id,
                'session_id' => session()->getId(),
                'request_id' => request()->attributes->get('request_id'),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 1024),
                'http_method' => request()->method(),
                'route_name' => optional(request()->route())->getName(),
                'route_uri' => optional(request()->route())->uri(),
                'url' => request()->fullUrl(),
                'status_code' => null,
                'latency_ms' => null,
                'action' => 'password.reset',
                'outcome' => 'success',
                'failure_reason' => null,
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
