<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiTokenAbility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        if (! $user->can('api.login')) {
            throw new HttpResponseException(response()->json([
                'message' => 'Akun ini tidak diizinkan untuk login API.',
            ], 403));
        }

        $tokenName = trim($validated['device_name']);

        return $this->issueTokenResponse($user, $tokenName, ApiTokenAbility::defaultAbilities(), 'Login API berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->transformUser($request->user()),
            'current_token' => [
                'id' => $request->user()->currentAccessToken()?->id,
                'name' => $request->user()->currentAccessToken()?->name,
                'last_used_at' => optional($request->user()->currentAccessToken()?->last_used_at)->toIso8601String(),
                'expires_at' => optional($request->user()->currentAccessToken()?->expires_at)->toIso8601String(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'message' => 'Logout API berhasil.',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();

        if (! $currentToken) {
            throw new HttpResponseException(response()->json([
                'message' => 'Token aktif tidak ditemukan.',
            ], 401));
        }

        $response = $this->issueTokenResponse(
            $request->user(),
            (string) ($currentToken->name ?: 'refreshed-token'),
            $currentToken->abilities ?? ApiTokenAbility::defaultAbilities(),
            'Refresh token berhasil.'
        );

        $currentToken->delete();

        return $response;
    }

    private function issueTokenResponse(User $user, string $tokenName, array $abilities, string $message): JsonResponse
    {
        $expiresAt = $this->resolveExpiration();
        $token = $user->createToken($tokenName, $abilities, $expiresAt);

        return response()->json([
            'message' => $message,
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'abilities' => array_values($abilities),
            'expires_at' => optional($token->accessToken->expires_at)->toIso8601String(),
            'refresh_before_seconds' => $this->resolveRefreshBeforeSeconds($expiresAt),
            'user' => $this->transformUser($user),
        ]);
    }

    private function resolveExpiration()
    {
        $configuredMinutes = config('sanctum.expiration');

        if (is_numeric($configuredMinutes) && (int) $configuredMinutes > 0) {
            return now()->addMinutes((int) $configuredMinutes);
        }

        return now()->addMinutes(120);
    }

    private function resolveRefreshBeforeSeconds($expiresAt): ?int
    {
        if (! $expiresAt) {
            return null;
        }

        $ttlSeconds = (int) now()->diffInSeconds($expiresAt, false);

        if ($ttlSeconds <= 0) {
            return 0;
        }

        $refreshLeadSeconds = min(300, max(60, (int) floor($ttlSeconds / 10)));

        return max($ttlSeconds - $refreshLeadSeconds, 0);
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'id_pegawai' => $user->id_pegawai,
        ];
    }
}