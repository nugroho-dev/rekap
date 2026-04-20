<?php

namespace Tests\Feature;

use App\Models\Mppd;
use App\Models\User;
use App\Support\ApiTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NonBerusahaStatistikApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mppd_statistik_requires_authentication(): void
    {
        $response = $this->getJson('/api/statistik/non-berusaha/mppd');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_fetch_mppd_statistik_payload(): void
    {
        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Uji',
            'slug' => 'instansi-uji',
            'alamat' => 'Alamat Instansi Uji',
            'del' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pegawaiId = DB::table('pegawai')->insertGetId([
            'pegawai_token' => 'pegawai-token-uji',
            'nama' => 'Pegawai Uji',
            'id_instansi' => $instansiId,
            'slug' => 'pegawai-uji',
            'nip' => '1234567890',
            'no_hp' => '08123456789',
            'del' => false,
            'user_status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'email' => 'api-user@example.test',
            'id_pegawai' => $pegawaiId,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => 'remember-token-uji',
        ]);

        Sanctum::actingAs($user, [ApiTokenAbility::STATISTIK_NON_BERUSAHA_READ], 'sanctum');

        Mppd::create([
            'nik' => '3173000000000001',
            'nama' => 'Dokter A',
            'alamat' => 'Alamat Uji',
            'email' => 'dokter-a@example.test',
            'nomor_telp' => '08123456789',
            'nomor_str' => 'STR-001',
            'masa_berlaku_str' => '2027-12-31',
            'nomor_register' => 'REG-001',
            'profesi' => 'Dokter',
            'tempat_praktik' => 'Klinik Uji',
            'alamat_tempat_praktik' => 'Alamat Klinik',
            'nomor_sip' => 'SIP-001',
            'tanggal_sip' => '2026-02-15',
            'tanggal_akhir_sip' => '2027-02-15',
            'keterangan' => 'Aktif',
            'del' => false,
        ]);

        $response = $this->getJson('/api/statistik/non-berusaha/mppd?year=2026&semester=1');

        $response->assertOk()
            ->assertJsonPath('module', 'mppd')
            ->assertJsonPath('data.filters.year', 2026)
            ->assertJsonPath('data.filters.semester', '1')
            ->assertJsonPath('data.summary.total', 1)
            ->assertJsonPath('data.summary.total_terbit', 1)
            ->assertJsonPath('data.stats.0.profesi', 'Dokter')
            ->assertJsonPath('data.stats.0.jumlah', 1);
    }
}