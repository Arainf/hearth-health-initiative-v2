<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
      $metadata = [
         'guard' => $event->guard ?? 'web',
         'remember' => (bool) request()->boolean('remember'),
         'username_sha256' => hash('sha256', (string) request()->input('username', '')),
      ];

        try {
            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'auth.login',
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
                'action' => 'login',
                'outcome' => 'success',
                'failure_reason' => null,
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
