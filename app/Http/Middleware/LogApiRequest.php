<?php

namespace App\Http\Middleware;

use App\Models\ApiAuditLog;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
            $this->storeLog($request, $response, $startedAt, null);

            return $response;
        } catch (Throwable $exception) {
            $this->storeLog($request, null, $startedAt, $exception);
            throw $exception;
        }
    }

    private function storeLog(Request $request, ?Response $response, float $startedAt, ?Throwable $exception): void
    {
        try {
            $token = $this->resolveToken($request);
            $user = $request->user();
            $route = $request->route();
            $isSensitiveRoute = $this->isSensitiveRoute($request, $route?->getName());

            ApiAuditLog::query()->create([
                'user_id' => $user?->id ?? $token?->tokenable_id,
                'token_id' => $token?->id,
                'token_name' => $token?->name,
                'client_name' => $token?->name ?? $request->input('device_name'),
                'route_name' => $route?->getName(),
                'method' => $request->method(),
                'path' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $this->truncate((string) $request->userAgent(), 1000),
                'status_code' => $response?->getStatusCode() ?? 500,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'authenticated' => (bool) ($user || $token),
                'api_group' => $this->resolveApiGroup($request),
                'query_params' => $this->sanitizePayload($request->query(), $isSensitiveRoute),
                'request_payload' => $this->sanitizePayload($request->except(['password', 'password_confirmation']), $isSensitiveRoute),
                'response_excerpt' => $this->resolveResponseExcerpt($request, $response, $exception, $isSensitiveRoute),
                'error_message' => $exception ? $this->truncate($exception->getMessage(), 2000) : null,
            ]);
        } catch (Throwable $loggingException) {
            // Never fail the API request because audit logging fails.
        }
    }

    private function resolveToken(Request $request): ?PersonalAccessToken
    {
        $currentToken = $request->user()?->currentAccessToken();
        if ($currentToken instanceof PersonalAccessToken) {
            return $currentToken;
        }

        $plainTextToken = $request->bearerToken();
        if (! $plainTextToken) {
            return null;
        }

        return PersonalAccessToken::findToken($plainTextToken);
    }

    private function resolveApiGroup(Request $request): string
    {
        $segments = $request->segments();

        if (($segments[0] ?? null) !== 'api') {
            return 'unknown';
        }

        if (isset($segments[1], $segments[2])) {
            return $segments[1].'.'.$segments[2];
        }

        return $segments[1] ?? 'root';
    }

    private function sanitizePayload(array $payload, bool $strict = false): array
    {
        $sanitized = [];
        $redactedKeys = ['password', 'password_confirmation', 'token', 'access_token'];

        if ($strict) {
            $redactedKeys = array_merge($redactedKeys, ['email', 'device_name']);
        }

        foreach ($payload as $key => $value) {
            if (in_array($key, $redactedKeys, true)) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizePayload($value, $strict);
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = $this->truncate($value, 1000);
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function resolveResponseExcerpt(Request $request, ?Response $response, ?Throwable $exception, bool $isSensitiveRoute): ?string
    {
        if ($isSensitiveRoute) {
            return null;
        }

        if ($exception) {
            return $this->truncate($exception->getMessage(), 2000);
        }

        if (! $response || $response->getStatusCode() < 400) {
            return null;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return null;
        }

        return $this->truncate($content, 4000);
    }

    private function isSensitiveRoute(Request $request, ?string $routeName): bool
    {
        if ($routeName && str_starts_with($routeName, 'api.auth.')) {
            return true;
        }

        return $request->is('api/auth/*') || $request->is('api/auth');
    }

    private function truncate(string $value, int $limit): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit).'...' : $value;
    }
}