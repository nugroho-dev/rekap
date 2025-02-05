<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Proses;
use App\Models\Simpel;
use Carbon\Carbon;

class SicantikSych extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sych:sicantik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sikronisasi Dari API SiCantik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date1 = date("Y-m-01");
        $date2 = date("Y-m-d");
        $urls = [
            'https://sicantik.go.id/api/TemplateData/keluaran/42611.json',
            'https://sicantik.go.id/api/TemplateData/keluaran/44216.json',
            'https://sicantik.go.id/api/TemplateData/keluaran/44217.json'
        ];

        foreach (array_chunk($urls, 1) as $chunk) {
            foreach ($chunk as $url) {
            $response = Http::retry(10, 1000)->get($url . '?date1=' . $date1 . '&date2=' . $date2);
            $data = $response->json();
            $items = $data['data']['data'];
            foreach (array_chunk($items, 100) as $itemChunk) {
                foreach ($itemChunk as $val) {
                Proses::upsert(
                    array_intersect_key($val, array_flip([
                    'id', 'alamat', 'data_status', 'default_active', 'del', 'dibuat_oleh', 'diproses_oleh', 
                    'diubah_oleh', 'email', 'end_date', 'file_signed_report', 'instansi_id', 'jenis_izin', 
                    'jenis_izin_id', 'jenis_kelamin', 'jenis_permohonan', 'jenis_proses_id', 'lokasi_izin', 
                    'nama', 'nama_proses', 'no_hp', 'no_izin', 'no_permohonan', 'no_rekomendasi', 'no_tlp', 
                    'start_date', 'status', 'tgl_dibuat', 'tgl_diubah', 'tgl_lahir', 'tgl_penetapan', 
                    'tgl_pengajuan', 'tgl_pengajuan_time', 'tgl_rekomendasi', 'tgl_selesai', 'tgl_selesai_time', 
                    'tgl_signed_report'
                    ])),
                    ['id_proses_permohonan']
                );
                }
            }
            }
        }
    }
}
