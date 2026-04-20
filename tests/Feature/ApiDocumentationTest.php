<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_view_rest_api_documentation_page(): void
    {
        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi API Docs',
            'slug' => 'instansi-api-docs',
            'alamat' => 'Alamat Instansi API Docs',
            'del' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pegawaiId = DB::table('pegawai')->insertGetId([
            'pegawai_token' => 'pegawai-token-api-docs',
            'nama' => 'Pegawai API Docs',
            'id_instansi' => $instansiId,
            'slug' => 'pegawai-api-docs',
            'nip' => '198765432100',
            'no_hp' => '08123456770',
            'del' => false,
            'user_status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'email' => 'api-docs@example.test',
            'id_pegawai' => $pegawaiId,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => 'remember-token-api-docs',
        ]);

        Permission::findOrCreate('api.docs.view', 'web');
        $user->givePermissionTo('api.docs.view');

        $response = $this->actingAs($user)->get(route('api.docs.index'));

        $response->assertOk()
            ->assertSee('REST API')
            ->assertSee('/auth/login')
            ->assertSee('/auth/refresh')
            ->assertSee('refresh_before_seconds')
            ->assertSee('Alur Refresh Token')
            ->assertSee('Contoh 401 dan Retry Otomatis')
            ->assertSee('Format Error API')
            ->assertSee('UNAUTHENTICATED')
            ->assertSee('VALIDATION_ERROR')
            ->assertSee('Contoh Axios Interceptor')
            ->assertSee('api.interceptors.response.use')
            ->assertSee('response.status !== 401 || hasRetried')
            ->assertSee('refreshAccessToken')
            ->assertSee('shared refresh promise')
            ->assertSee('request paralel')
            ->assertSee('curl --request POST')
            ->assertSee('Authorization: Bearer your-sanctum-token')
            ->assertSee('Header autentikasi')
            ->assertSee('fetch(')
            ->assertSee('/statistik/berusaha/proyek')
            ->assertSee('/statistik/non-berusaha/mppd')
            ->assertSee('Contoh response');
    }
}