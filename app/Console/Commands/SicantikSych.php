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
        $date2 = Carbon::now()->format('Y-m-d');
        $date1 = Carbon::now()->subDays(3)->format('Y-m-d');
        $url = 'https://sicantik.go.id/api/TemplateData/keluaran/42611.json';

        $response = Http::retry(10, 1000)->get($url, [
            'date1' => $date1,
            'date2' => $date2
        ]);

        $data = $response->json();
        $items = $data['data']['data'];
        $chunks = array_chunk($items, 100);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $val) {
            $data = array_intersect_key($val, array_flip([
                'id_proses_permohonan','alamat', 'data_status', 'default_active', 'del', 'dibuat_oleh', 'diproses_oleh', 'diubah_oleh', 
                'email', 'end_date', 'file_signed_report', 'instansi_id', 'jenis_izin', 'jenis_izin_id', 
                'jenis_kelamin', 'jenis_permohonan', 'jenis_proses_id', 'lokasi_izin', 'nama', 'nama_proses', 
                'no_hp', 'no_izin', 'no_permohonan', 'no_rekomendasi', 'no_tlp', 'permohonan_izin_id', 
                'start_date', 'status', 'tgl_dibuat', 'tgl_diubah', 'tgl_lahir', 'tgl_penetapan', 'tgl_pengajuan', 
                'tgl_pengajuan_time', 'tgl_rekomendasi', 'tgl_selesai', 'tgl_selesai_time', 'tgl_signed_report'
            ]));

            Proses::updateOrCreate(
                ['id_proses_permohonan' => $data['id_proses_permohonan']],
                $data
            );
            }
        }
        
    }
}
