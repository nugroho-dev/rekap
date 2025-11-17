<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Models\Proses;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SicantikSych48051 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sych:sicantik-48051 {--date1=} {--date2=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi dari API SiCantik (template 48051)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date2 = $this->option('date2') ?: Carbon::now()->format('Y-m-d');
        $date1 = $this->option('date1') ?: Carbon::now()->subMonth()->format('Y-m-d');
        $url = 'https://sicantik.go.id/api/TemplateData/keluaran/48051.json';

        $this->info("Fetching SiCantik 48051 from {$date1} to {$date2}...");

        try {
            $response = Http::retry(10, 1000)->get($url, [
                'date1' => $date1,
                'date2' => $date2,
            ]);
        } catch (\Throwable $e) {
            Log::error('SiCantik 48051 request failed', ['error' => $e->getMessage()]);
            $this->error('Gagal menghubungi API SiCantik 48051.');
            return self::FAILURE;
        }

        if (!$response->ok()) {
            $this->error('Response API tidak OK: ' . $response->status());
            return self::FAILURE;
        }

        $json = $response->json();
        if (!is_array($json) || !isset($json['data']['data']) || !is_array($json['data']['data'])) {
            $this->error('Struktur data API tidak sesuai (data.data tidak ditemukan).');
            return self::FAILURE;
        }

        $items = $json['data']['data'];
        $chunks = array_chunk($items, 100);

        $inserted = 0;
        $skipped = 0;
        foreach ($chunks as $chunk) {
            foreach ($chunk as $val) {
                if (!is_array($val)) { $skipped++; continue; }
                $payload = array_intersect_key($val, array_flip([
                    'id_proses_permohonan','alamat', 'data_status', 'default_active', 'del', 'dibuat_oleh', 'diproses_oleh', 'diubah_oleh',
                    'email', 'end_date', 'file_signed_report', 'instansi_id', 'jenis_izin', 'jenis_izin_id',
                    'jenis_kelamin', 'jenis_permohonan', 'jenis_proses_id', 'lokasi_izin', 'nama', 'nama_proses',
                    'no_hp', 'no_izin', 'no_permohonan', 'no_rekomendasi', 'no_tlp', 'permohonan_izin_id',
                    'start_date', 'status', 'tgl_dibuat', 'tgl_diubah', 'tgl_lahir', 'tgl_penetapan', 'tgl_pengajuan',
                    'tgl_pengajuan_time', 'tgl_rekomendasi', 'tgl_selesai', 'tgl_selesai_time', 'tgl_signed_report',
                ]));

                if (empty($payload['id_proses_permohonan'])) { $skipped++; continue; }

                try {
                    Proses::updateOrCreate(
                        ['id_proses_permohonan' => $payload['id_proses_permohonan']],
                        $payload
                    );
                    $inserted++;
                } catch (\Throwable $e) {
                    $skipped++;
                    Log::warning('Gagal upsert Proses 48051', ['error' => $e->getMessage(), 'payload' => $payload]);
                }
            }
        }

        $this->info("Selesai. Insert/Update: {$inserted}, Dilewati: {$skipped}");
        return self::SUCCESS;
    }
}
