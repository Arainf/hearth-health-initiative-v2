<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class LogLockout
{
    public function handle(Lockout $event): void
    {
        $key = method_exists(request(), 'ip') ? (string) request()->ip() : '';
        $metadata = [
            'throttle_key' => app(\App\Http\Requests\Auth\LoginRequest::class)->throttleKey() ?? null,
            'available_in_seconds' => RateLimiter::availableIn(app(\App\Http\Requests\Auth\LoginRequest::class)->throttleKey() ?? ''),
        ];

        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'auth.lockout',
                'user_id' => null,
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
                'action' => 'lockout',
                'outcome' => 'failure',
                'failure_reason' => 'locked_out',
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
