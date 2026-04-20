<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WebLoginPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_only_user_cannot_login_to_web_panel(): void
    {
        Permission::findOrCreate('api.login', 'web');
        Permission::findOrCreate('web.login', 'web');

        $user = $this->createUser('api-only@example.test', 'secret123');
        $user->givePermissionTo('api.login');

        config()->set('services.recaptcha.secret', 'test-secret');
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'g-recaptcha-response' => 'test-token',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('loginError', 'Akun ini tidak diizinkan untuk login ke panel web.');
        $this->assertGuest();
    }

    private function createUser(string $email, string $password): User
    {
        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Web Login',
            'slug' => 'instansi-web-login-'.md5($email),
            'alamat' => 'Alamat Instansi Web Login',
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