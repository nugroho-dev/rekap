<?php

namespace Tests\Feature;

use App\Models\ApiAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_and_authenticated_statistik_requests_are_audited(): void
    {
        $user = $this->createApiUser('audit@example.test', 'secret123');

        DB::table('mppd')->insert([
            'nik' => '3173000000000002',
            'nama' => 'Audit Dokter',
            'alamat' => 'Alamat Audit',
            'email' => 'audit-dokter@example.test',
            'nomor_telp' => '08123456780',
            'nomor_str' => 'STR-AUDIT',
            'masa_berlaku_str' => '2027-12-31',
            'nomor_register' => 'REG-AUDIT',
            'profesi' => 'Dokter',
            'tempat_praktik' => 'Klinik Audit',
            'alamat_tempat_praktik' => 'Alamat Klinik Audit',
            'nomor_sip' => 'SIP-AUDIT',
            'tanggal_sip' => '2026-03-15',
            'tanggal_akhir_sip' => '2027-03-15',
            'keterangan' => 'Aktif',
            'del' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'external-dashboard',
        ]);

        $loginResponse->assertOk();
        $token = $loginResponse->json('access_token');

        $statsResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/statistik/non-berusaha/mppd?year=2026&semester=1');

        $statsResponse->assertOk();

        $this->assertDatabaseHas('api_audit_logs', [
            'path' => 'api/auth/login',
            'method' => 'POST',
            'status_code' => 200,
            'client_name' => 'external-dashboard',
            'authenticated' => false,
        ]);

        $this->assertDatabaseHas('api_audit_logs', [
            'path' => 'api/statistik/non-berusaha/mppd',
            'method' => 'GET',
            'status_code' => 200,
            'client_name' => 'external-dashboard',
            'authenticated' => true,
            'user_id' => $user->id,
            'api_group' => 'statistik.non-berusaha',
        ]);
    }

    public function test_authorized_user_can_view_api_audit_log_detail_page(): void
    {
        $user = $this->createApiUser('audit-web@example.test', 'secret123');
        Permission::findOrCreate('api.audit.view', 'web');
        $user->givePermissionTo('api.audit.view');

        $log = ApiAuditLog::query()->create([
            'user_id' => $user->id,
            'client_name' => 'external-dashboard',
            'token_name' => 'external-dashboard',
            'route_name' => 'api.statistik.non-berusaha.mppd',
            'api_group' => 'statistik.non-berusaha',
            'method' => 'GET',
            'path' => 'api/statistik/non-berusaha/mppd',
            'ip_address' => '127.0.0.1',
            'status_code' => 200,
            'duration_ms' => 24,
            'authenticated' => true,
            'query_params' => ['year' => 2026],
            'request_payload' => [],
            'response_excerpt' => '{"message":"ok"}',
        ]);

        $response = $this->actingAs($user)->get(route('api.audits.show', $log));

        $response->assertOk()
            ->assertSee('Detail Audit Log API')
            ->assertSee('external-dashboard')
            ->assertSee('api/statistik/non-berusaha/mppd')
            ->assertSee('{"message":"ok"}');
    }

    public function test_audit_index_supports_date_filter(): void
    {
        $user = $this->createApiUser('audit-filter@example.test', 'secret123');
        Permission::findOrCreate('api.audit.view', 'web');
        $user->givePermissionTo('api.audit.view');

        ApiAuditLog::query()->create([
            'client_name' => 'older-client',
            'api_group' => 'auth.login',
            'method' => 'POST',
            'path' => 'api/auth/login',
            'status_code' => 200,
            'duration_ms' => 10,
            'authenticated' => false,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        ApiAuditLog::query()->create([
            'client_name' => 'recent-client',
            'api_group' => 'auth.login',
            'method' => 'POST',
            'path' => 'api/auth/login',
            'status_code' => 422,
            'duration_ms' => 12,
            'authenticated' => false,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('api.audits.index', [
            'date_from' => now()->subDays(2)->toDateString(),
            'date_to' => now()->toDateString(),
        ]));

        $response->assertOk()
            ->assertSee('recent-client')
            ->assertDontSee('older-client');
    }

    public function test_failed_login_request_stores_response_excerpt(): void
    {
        $user = $this->createApiUser('audit-failed-login@example.test', 'secret123');

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'external-dashboard',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('api_audit_logs', [
            'path' => 'api/auth/login',
            'method' => 'POST',
            'status_code' => 422,
            'client_name' => 'external-dashboard',
        ]);

        $this->assertStringContainsString(
            '[REDACTED]',
            json_encode(ApiAuditLog::query()->where('path', 'api/auth/login')->latest('id')->value('request_payload'))
        );

        $this->assertNull(
            ApiAuditLog::query()->where('path', 'api/auth/login')->latest('id')->value('response_excerpt')
        );
    }

    public function test_audit_index_shows_summary_and_export_csv_works(): void
    {
        $user = $this->createApiUser('audit-summary@example.test', 'secret123');
        Permission::findOrCreate('api.audit.view', 'web');
        Permission::findOrCreate('api.audit.export', 'web');
        $user->givePermissionTo(['api.audit.view', 'api.audit.export']);

        ApiAuditLog::query()->create([
            'client_name' => 'summary-client',
            'token_name' => 'summary-client',
            'api_group' => 'statistik.non-berusaha',
            'method' => 'GET',
            'path' => 'api/statistik/non-berusaha/mppd',
            'status_code' => 200,
            'duration_ms' => 22,
            'authenticated' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ApiAuditLog::query()->create([
            'client_name' => 'summary-client',
            'token_name' => 'summary-client',
            'api_group' => 'auth.login',
            'method' => 'POST',
            'path' => 'api/auth/login',
            'status_code' => 422,
            'duration_ms' => 40,
            'authenticated' => false,
            'error_message' => 'Email atau password tidak valid.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $indexResponse = $this->actingAs($user)->get(route('api.audits.index'));

        $indexResponse->assertOk()
            ->assertSee('Total Request')
            ->assertSee('Top Client')
            ->assertSee('Status Code')
            ->assertSee('summary-client');

        $exportResponse = $this->actingAs($user)->get(route('api.audits.export', ['client' => 'summary-client']));

        $exportResponse->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('summary-client', $exportResponse->streamedContent());
        $this->assertStringContainsString('api/statistik/non-berusaha/mppd', $exportResponse->streamedContent());
    }

    public function test_prune_command_removes_old_api_audit_logs(): void
    {
        ApiAuditLog::query()->create([
            'client_name' => 'old-client',
            'api_group' => 'auth.login',
            'method' => 'POST',
            'path' => 'api/auth/login',
            'status_code' => 200,
            'duration_ms' => 10,
            'authenticated' => false,
            'created_at' => now()->subDays(120),
            'updated_at' => now()->subDays(120),
        ]);

        ApiAuditLog::query()->create([
            'client_name' => 'new-client',
            'api_group' => 'auth.login',
            'method' => 'POST',
            'path' => 'api/auth/login',
            'status_code' => 200,
            'duration_ms' => 10,
            'authenticated' => false,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $this->artisan('api-audit:prune --days=90')
            ->expectsOutput('Berhasil menghapus 1 log audit API yang lebih lama dari 90 hari.')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('api_audit_logs', ['client_name' => 'old-client']);
        $this->assertDatabaseHas('api_audit_logs', ['client_name' => 'new-client']);
    }

    private function createApiUser(string $email, string $password): User
    {
        Permission::findOrCreate('api.login', 'web');

        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Audit Test',
            'slug' => 'instansi-audit-test-'.md5($email),
            'alamat' => 'Alamat Instansi Audit Test',
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

        $user->givePermissionTo('api.login');

        return $user;
    }
}