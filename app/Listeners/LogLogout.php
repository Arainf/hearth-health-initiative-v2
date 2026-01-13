<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class LogLogout
{
    public function handle(Logout $event): void
    {
        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'auth.logout',
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
                'action' => 'logout',
                'outcome' => 'success',
                'failure_reason' => null,
                'metadata_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
