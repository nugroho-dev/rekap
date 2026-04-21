<?php

namespace Tests\Feature;

use App\Support\ApiTokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserApiTokenManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_generate_and_revoke_token_for_api_user(): void
    {
        Permission::findOrCreate('user.access.manage', 'web');
        Permission::findOrCreate('api.token.manage', 'web');
        Permission::findOrCreate('api.login', 'web');
        Permission::findOrCreate('konfigurasi.view', 'web');

        $admin = $this->createUser('admin-token@example.test', 'secret123');
        $admin->givePermissionTo(['konfigurasi.view', 'user.access.manage', 'api.token.manage']);

        $apiUser = $this->createUser('api-target@example.test', 'secret123');
        $apiUser->givePermissionTo('api.login');

        $createResponse = $this->actingAs($admin)->post(route('konfigurasi.user.api-tokens.store', $apiUser), [
            'token_name' => 'integrasi-server',
            'abilities' => [
                ApiTokenAbility::AUTH_SESSION,
                ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ,
            ],
        ]);

        $createResponse->assertRedirect(route('konfigurasi.user.access', $apiUser));
        $generatedApiToken = $createResponse->getSession()->get('generatedApiToken');

        $this->assertIsArray($generatedApiToken);
        $this->assertNotEmpty($generatedApiToken['id'] ?? null);
        $this->assertNotEmpty($generatedApiToken['plain_text'] ?? null);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $apiUser->id,
            'name' => 'integrasi-server',
        ]);

        $token = PersonalAccessToken::query()
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $apiUser->id)
            ->firstOrFail();

        $this->assertSame([
            ApiTokenAbility::AUTH_SESSION,
            ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ,
        ], $token->abilities ?? []);

        $this->assertSame([
            ApiTokenAbility::AUTH_SESSION,
            ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ,
        ], $generatedApiToken['abilities'] ?? []);

        $pageResponse = $this->withSession(['generatedApiToken' => $generatedApiToken])
            ->actingAs($admin)
            ->get(route('konfigurasi.user.access', $apiUser));

        $pageResponse->assertOk()
            ->assertSee('Token API')
            ->assertSee('integrasi-server')
            ->assertSee($generatedApiToken['plain_text'])
            ->assertSee('Copy Token')
            ->assertSee('Download TXT')
            ->assertSee('BARU')
            ->assertSee('Ability')
            ->assertSee('Status')
            ->assertSee('AKTIF')
            ->assertSee(ApiTokenAbility::AUTH_SESSION)
            ->assertSee(ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ)
            ->assertSee('new-api-token-row');

        $deleteResponse = $this->actingAs($admin)->delete(route('konfigurasi.user.api-tokens.destroy', [$apiUser, $token->id]));
        $deleteResponse->assertRedirect(route('konfigurasi.user.access', $apiUser));
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
    }

    public function test_user_index_can_filter_api_accounts(): void
    {
        Permission::findOrCreate('user.view', 'web');
        Permission::findOrCreate('konfigurasi.view', 'web');
        Permission::findOrCreate('api.login', 'web');
        Permission::findOrCreate('web.login', 'web');

        $admin = $this->createUser('admin-filter@example.test', 'secret123');
        $admin->givePermissionTo(['konfigurasi.view', 'user.view']);

        $apiOnlyUser = $this->createUser('api-only-filter@example.test', 'secret123');
        $apiOnlyUser->givePermissionTo('api.login');

        $webOnlyUser = $this->createUser('web-only-filter@example.test', 'secret123');
        $webOnlyUser->givePermissionTo('web.login');

        $response = $this->actingAs($admin)->get('/konfigurasi/user?access_type=api-only');

        $response->assertOk()
            ->assertSee('api-only-filter@example.test')
            ->assertDontSee('web-only-filter@example.test')
            ->assertSee('Akun API')
            ->assertSee('API SAJA');
    }

    public function test_user_without_api_token_manage_permission_cannot_create_or_revoke_token(): void
    {
        Permission::findOrCreate('user.access.manage', 'web');
        Permission::findOrCreate('konfigurasi.view', 'web');
        Permission::findOrCreate('api.login', 'web');

        $operator = $this->createUser('operator-no-token@example.test', 'secret123');
        $operator->givePermissionTo(['konfigurasi.view', 'user.access.manage']);

        $apiUser = $this->createUser('api-no-manage@example.test', 'secret123');
        $apiUser->givePermissionTo('api.login');

        $createResponse = $this->actingAs($operator)->post(route('konfigurasi.user.api-tokens.store', $apiUser), [
            'token_name' => 'blocked-token',
        ]);

        $createResponse->assertForbidden();
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $apiUser->id,
            'name' => 'blocked-token',
        ]);

        $token = $apiUser->createToken('existing-token', ['*']);

        $deleteResponse = $this->actingAs($operator)->delete(route('konfigurasi.user.api-tokens.destroy', [$apiUser, $token->accessToken->id]));

        $deleteResponse->assertForbidden();
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $token->accessToken->id]);
    }

    public function test_admin_can_open_api_accounts_page_and_generate_token_automatically(): void
    {
        Permission::findOrCreate('user.view', 'web');
        Permission::findOrCreate('konfigurasi.view', 'web');
        Permission::findOrCreate('api.token.manage', 'web');
        Permission::findOrCreate('api.login', 'web');

        $admin = $this->createUser('admin-api-page@example.test', 'secret123');
        $admin->givePermissionTo(['konfigurasi.view', 'user.view', 'api.token.manage']);

        $apiUser = $this->createUser('api-page@example.test', 'secret123');
        $apiUser->givePermissionTo('api.login');

        $pageResponse = $this->actingAs($admin)->get(route('konfigurasi.user.api-accounts'));

        $pageResponse->assertOk()
            ->assertSee('Daftar Akun API')
            ->assertSee('api-page@example.test')
            ->assertSee('Generate Token Otomatis')
            ->assertDontSee('Semua Akses');

        $createResponse = $this->actingAs($admin)->post(route('konfigurasi.user.api-tokens.quick-store', $apiUser));

        $createResponse->assertRedirect();
        $createResponse->assertSessionHas('generatedApiToken');
        $generatedApiToken = $createResponse->getSession()->get('generatedApiToken');

        $this->assertIsArray($generatedApiToken);
        $this->assertNotEmpty($generatedApiToken['id'] ?? null);
        $this->assertNotEmpty($generatedApiToken['plain_text'] ?? null);

        $pageResponse = $this->withSession(['generatedApiToken' => $generatedApiToken])
            ->actingAs($admin)
            ->get(route('konfigurasi.user.api-accounts'));

        $pageResponse->assertOk()->assertSee($generatedApiToken['plain_text']);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $apiUser->id,
        ]);
    }

    public function test_admin_can_filter_active_and_expired_tokens_on_access_page(): void
    {
        Permission::findOrCreate('user.access.manage', 'web');
        Permission::findOrCreate('api.token.manage', 'web');
        Permission::findOrCreate('api.login', 'web');
        Permission::findOrCreate('konfigurasi.view', 'web');

        $admin = $this->createUser('admin-filter-token@example.test', 'secret123');
        $admin->givePermissionTo(['konfigurasi.view', 'user.access.manage', 'api.token.manage']);

        $apiUser = $this->createUser('api-filter-token@example.test', 'secret123');
        $apiUser->givePermissionTo('api.login');

        $apiUser->createToken('token-aktif', ['*'], now()->addDay());
        $apiUser->createToken('token-expired', ['*'], now()->subDay());

        $activeResponse = $this->actingAs($admin)->get(route('konfigurasi.user.access', ['user' => $apiUser, 'token_status' => 'active']));

        $activeResponse->assertOk()
            ->assertSee('token-aktif')
            ->assertDontSee('token-expired')
            ->assertSee('AKTIF');

        $expiredResponse = $this->actingAs($admin)->get(route('konfigurasi.user.access', ['user' => $apiUser, 'token_status' => 'expired']));

        $expiredResponse->assertOk()
            ->assertSee('token-expired')
            ->assertDontSee('token-aktif')
            ->assertSee('KEDALUWARSA');
    }

    private function createUser(string $email, string $password): User
    {
        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Token '.substr(md5($email), 0, 5),
            'slug' => 'instansi-token-'.substr(md5($email), 0, 8),
            'alamat' => 'Alamat Instansi Token',
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

        return User::query()->create([
            'email' => $email,
            'id_pegawai' => $pegawaiId,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'remember_token' => 'remember-'.substr(md5($email), 0, 12),
        ]);
    }
}