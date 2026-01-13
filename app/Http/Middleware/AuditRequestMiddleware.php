<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Generate a per-request ID and capture start time
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $request->attributes->set('audit_start', microtime(true));

        $response = $next($request);

        // Propagate request id to client for correlation
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }

    public function terminate($request, $response): void
    {
        try {
            $start = $request->attributes->get('audit_start');
            $latencyMs = is_numeric($start)
                ? (int) ((microtime(true) - (float) $start) * 1000)
                : null;

            $route = $request->route();
            $routeName = $route?->getName();
            $routeUri = method_exists($route, 'uri') ? $route->uri() : null;
            $controller = optional($route)->getActionName();
            $middlewareList = method_exists($route, 'gatherMiddleware') ? $route->gatherMiddleware() : [];
            $metadata = [
               'controller' => $controller,
               'middleware' => $middlewareList,
               'locale' => app()->getLocale(),
            ];

            DB::table('audit_logs')->insert([
                'occurred_at' => now(),
                'event_type' => 'route.hit',
                'user_id' => optional($request->user())->id,
                'session_id' => $request->session()?->getId(),
                'request_id' => $request->attributes->get('request_id'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'http_method' => $request->method(),
                'route_name' => $routeName,
                'route_uri' => $routeUri,
                'url' => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
                'latency_ms' => $latencyMs,
                'subject_type' => null,
                'subject_id' => null,
                'action' => null,
                'outcome' => (isset($response) && method_exists($response, 'getStatusCode') && $response->getStatusCode() < 400) ? 'success' : 'failure',
                'failure_reason' => null,
                'metadata_json' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Swallow errors to avoid impacting the response lifecycle
        }
    }
}
