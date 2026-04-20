<?php

namespace Tests\Feature;

use App\Support\ApiTokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_bearer_token_for_valid_credentials(): void
    {
        $user = $this->createApiUser('auth-login@example.test', 'secret123');

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'mobile-app-test',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'auth-login@example.test')
            ->assertJsonPath('user.id_pegawai', $user->id_pegawai);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertSame(ApiTokenAbility::defaultAbilities(), $response->json('abilities'));
        $this->assertIsInt($response->json('refresh_before_seconds'));
        $this->assertGreaterThan(0, $response->json('refresh_before_seconds'));
    }

    public function test_login_is_forbidden_for_user_without_api_login_permission(): void
    {
        $user = $this->createApiUser('auth-no-api@example.test', 'secret123', false);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'mobile-app-test',
        ]);

        $response->assertForbidden()
            ->assertJsonPath('message', 'Akun ini tidak diizinkan untuk login API.');
    }

    public function test_authenticated_user_can_fetch_profile_and_logout(): void
    {
        $user = $this->createApiUser('auth-me@example.test', 'secret123');
        Sanctum::actingAs($user, [ApiTokenAbility::AUTH_SESSION], 'sanctum');

        $meResponse = $this->getJson('/api/auth/me');
        $meResponse->assertOk()
            ->assertJsonPath('user.email', 'auth-me@example.test');

        $logoutResponse = $this->postJson('/api/auth/logout');
        $logoutResponse->assertOk()
            ->assertJsonPath('message', 'Logout API berhasil.');
    }

    public function test_authenticated_user_can_refresh_token_and_old_token_is_revoked(): void
    {
        $user = $this->createApiUser('auth-refresh@example.test', 'secret123');
        $token = $user->createToken('mobile-app-test', [
            ApiTokenAbility::AUTH_SESSION,
            ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ,
        ], now()->addMinutes(120));

        $oldTokenId = $token->accessToken->id;

        $refreshResponse = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
            ->postJson('/api/auth/refresh');

        $refreshResponse->assertOk()
            ->assertJsonPath('message', 'Refresh token berhasil.')
            ->assertJsonPath('abilities.0', ApiTokenAbility::AUTH_SESSION)
            ->assertJsonPath('abilities.1', ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ);

        $this->assertIsInt($refreshResponse->json('refresh_before_seconds'));
        $this->assertGreaterThan(0, $refreshResponse->json('refresh_before_seconds'));

        $newAccessToken = $refreshResponse->json('access_token');

        $this->assertNotEmpty($newAccessToken);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $oldTokenId]);

        $meResponse = $this->withHeader('Authorization', 'Bearer '.$newAccessToken)
            ->getJson('/api/auth/me');

        $meResponse->assertOk()
            ->assertJsonPath('user.email', 'auth-refresh@example.test');

        $this->assertNotNull(PersonalAccessToken::findToken($newAccessToken));
    }

    public function test_token_without_statistik_ability_cannot_access_statistik_endpoint(): void
    {
        $user = $this->createApiUser('auth-ability@example.test', 'secret123');
        Sanctum::actingAs($user, [ApiTokenAbility::AUTH_SESSION], 'sanctum');

        $response = $this->getJson('/api/statistik/non-berusaha/mppd');

        $response->assertForbidden();
    }

    public function test_token_with_only_non_berusaha_ability_cannot_access_berusaha_endpoint(): void
    {
        $user = $this->createApiUser('auth-ability-split@example.test', 'secret123');
        Sanctum::actingAs($user, [ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ], 'sanctum');

        $response = $this->getJson('/api/statistik/berusaha/proyek');

        $response->assertForbidden();
    }

    public function test_api_unauthenticated_error_is_returned_as_json_without_html_redirect(): void
    {
        $response = $this->get('/api/auth/me');

        $response->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'message' => 'Unauthenticated.',
                'code' => 'UNAUTHENTICATED',
                'status' => 401,
                'errors' => null,
            ]);

        $this->assertNull($response->headers->get('location'));
        $this->assertStringNotContainsString('<!DOCTYPE html>', $response->getContent());
    }

    public function test_api_validation_error_is_returned_as_json_without_html_redirect(): void
    {
        $response = $this->post('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonPath('status', 422)
            ->assertJsonStructure([
                'message',
                'code',
                'status',
                'errors' => ['email', 'password', 'device_name'],
            ]);

        $this->assertNull($response->headers->get('location'));
        $this->assertStringNotContainsString('<!DOCTYPE html>', $response->getContent());
    }

    public function test_missing_api_route_returns_json_not_html(): void
    {
        $response = $this->get('/api/route-yang-tidak-ada');

        $response->assertStatus(404)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'message' => 'Resource API tidak ditemukan.',
                'code' => 'NOT_FOUND',
                'status' => 404,
                'errors' => null,
            ]);

        $this->assertNull($response->headers->get('location'));
        $this->assertStringNotContainsString('<!DOCTYPE html>', $response->getContent());
    }

    private function createApiUser(string $email, string $password, bool $allowApiLogin = true): User
    {
        Permission::findOrCreate('api.login', 'web');

        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Auth Test',
            'slug' => 'instansi-auth-test-'.md5($email),
            'alamat' => 'Alamat Instansi Auth Test',
            'del' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pegawaiId = DB::table('pegawai')->insertGetId([
            'pegawai_token' => 'pegawai-token-'.substr(md5($email), 0, 12),
            'nama' => 'Pegawai '.substr($email, 0, 8),
            'id_instansi' => $instansiId,
            'slug' => 'pegawai-'.substr(md5($email), 0, 12),
            'nip' => substr(preg_replace('/\D+/', '', md5($email)), 0, 10) ?: '1234567890',
            'no_hp' => '08123456789',
            'del' => false,
            'user_status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'email' => $email,
            'id_pegawai' => $pegawaiId,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'remember_token' => 'remember-'.substr(md5($email), 0, 12),
        ]);

        if ($allowApiLogin) {
            $user->givePermissionTo('api.login');
        }

        return $user;
    }
}