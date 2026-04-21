<?php

namespace Tests\Feature;

use App\Models\Proyek;
use App\Models\User;
use App\Support\ApiTokenAbility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BerusahaStatistikApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_proyek_statistik_requires_authentication(): void
    {
        $response = $this->getJson('/api/statistik/berusaha/proyek');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_fetch_proyek_statistik_payload(): void
    {
        $instansiId = DB::table('instansi')->insertGetId([
            'nama_instansi' => 'Instansi Uji',
            'slug' => 'instansi-uji-berusaha',
            'alamat' => 'Alamat Instansi Uji',
            'del' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pegawaiId = DB::table('pegawai')->insertGetId([
            'pegawai_token' => 'pegawai-token-uji-berusaha',
            'nama' => 'Pegawai Uji Berusaha',
            'id_instansi' => $instansiId,
            'slug' => 'pegawai-uji-berusaha',
            'nip' => '123456789012',
            'no_hp' => '08123456780',
            'del' => false,
            'user_status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'email' => 'berusaha-api-user@example.test',
            'id_pegawai' => $pegawaiId,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => 'remember-token-berusaha-uji',
        ]);

        Sanctum::actingAs($user, [ApiTokenAbility::STATISTIK_BERUSAHA_READ], 'sanctum');

        Proyek::query()->create([
            'id_proyek' => 'PROYEK-001',
            'uraian_jenis_proyek' => 'Penanaman Modal Baru',
            'nib' => '9120000000001',
            'nama_perusahaan' => 'PT Uji Proyek',
            'tanggal_terbit_oss' => '2026-02-10',
            'uraian_status_penanaman_modal' => 'PMDN',
            'uraian_jenis_perusahaan' => 'PT',
            'uraian_risiko_proyek' => 'Menengah Tinggi',
            'nama_proyek' => 'Pabrik Uji',
            'uraian_skala_usaha' => 'Usaha Menengah',
            'alamat_usaha' => 'Jl. Uji No. 1',
            'kab_kota_usaha' => 'Kota Uji',
            'kecamatan_usaha' => 'Kecamatan Uji',
            'kelurahan_usaha' => 'Kelurahan Uji',
            'longitude' => '106.8',
            'latitude' => '-6.2',
            'day_of_tanggal_pengajuan_proyek' => '2026-02-08',
            'kbli' => '62010',
            'judul_kbli' => 'Aktivitas Pemrograman Komputer',
            'kl_sektor_pembina' => 'Komunikasi dan Informatika',
            'nama_user' => 'Tester',
            'email' => 'proyek@example.test',
            'nomor_telp' => '08123456781',
            'luas_tanah' => 100,
            'satuan_tanah' => 'm2',
            'jumlah_investasi' => 150000000,
            'tki' => 25,
        ]);

        $response = $this->getJson('/api/statistik/berusaha/proyek?year=2026&semester=1');

        $response->assertOk()
            ->assertJsonPath('module', 'proyek')
            ->assertJsonPath('data.filters.year', 2026)
            ->assertJsonPath('data.filters.semester', '1')
            ->assertJsonPath('data.summary.total', 1)
            ->assertJsonPath('data.summary.total_terbit', 1)
            ->assertJsonPath('data.summary.total_nib', 1)
            ->assertJsonPath('data.summary.total_tki', 25)
            ->assertJsonPath('data.stats.0.kategori', 'Usaha Menengah')
            ->assertJsonPath('data.stats.0.jumlah', 1);
    }
}