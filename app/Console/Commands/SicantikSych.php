<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Proses;

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
        $date1=date("Y-m-01");
        $date2=date("Y-m-d");
        $response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/42611.json?date1='.$date1.'&date2='. $date2.'');
        $data = $response->json();
        $items = $data['data']['data'];
        foreach ($items as $val) {
            Proses::updateOrCreate(
            ['id_proses_permohonan'=> $val['id']],
            ['alamat'=> $val['alamat'],
            'data_status'=>$val['data_status'],
            'default_active'=>$val['default_active'],
            'del'=>$val['del'],
            'dibuat_oleh'=>$val['dibuat_oleh'],
            'diproses_oleh'=>$val['diproses_oleh'],
            'diubah_oleh'=>$val['diubah_oleh'],
            'email' => $val['email'],
            'end_date'=>$val['end_date'],
            'file_signed_report'=>$val['file_signed_report'],
            'instansi_id'=>$val['instansi_id'],
            'jenis_izin'=>$val['jenis_izin'],
            'jenis_izin_id'=>$val['jenis_izin_id'],
            'jenis_kelamin'=>$val['jenis_kelamin'],
            'jenis_permohonan' => $val['jenis_permohonan'],
            'jenis_proses_id'=>$val['jenis_proses_id'],
            'lokasi_izin'=>$val['lokasi_izin'],
            'nama'=>$val['nama'],
            'nama_proses'=>$val['nama_proses'],
            'no_hp'=>$val['no_hp'],
            'no_izin'=>$val['no_izin'],
            'no_permohonan'=>$val['no_permohonan'],
            'no_rekomendasi'=>$val['no_rekomendasi'],
            'no_tlp'=>$val['no_tlp'],
            'start_date'=>$val['start_date'],
            'status'=>$val['status'],
            'tgl_dibuat'=>$val['tgl_dibuat'],
            'tgl_diubah'=>$val['tgl_diubah'],
            'tgl_lahir'=>$val['tgl_lahir'],
            'tgl_penetapan'=>$val['tgl_penetapan'],
            'tgl_pengajuan'=>$val['tgl_pengajuan'],
            'tgl_pengajuan_time'=>$val['tgl_pengajuan_time'],
            'tgl_rekomendasi'=>$val['tgl_rekomendasi'],
            'tgl_selesai'=>$val['tgl_selesai'],
            'tgl_selesai_time'=>$val['tgl_selesai_time'],
            'tgl_signed_report'=>$val['tgl_signed_report']
            ]);
        }
        $response1 = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/44216.json?date1='.$date1.'&date2='. $date2.'');
        $data = $response1->json();
        $items = $data['data']['data'];
        foreach ($items as $val) {
            Proses::updateOrCreate(
            ['id_proses_permohonan'=> $val['id']],
            ['alamat'=> $val['alamat'],
            'data_status'=>$val['data_status'],
            'default_active'=>$val['default_active'],
            'del'=>$val['del'],
            'dibuat_oleh'=>$val['dibuat_oleh'],
            'diproses_oleh'=>$val['diproses_oleh'],
            'diubah_oleh'=>$val['diubah_oleh'],
            'email' => $val['email'],
            'end_date'=>$val['end_date'],
            'file_signed_report'=>$val['file_signed_report'],
            'instansi_id'=>$val['instansi_id'],
            'jenis_izin'=>$val['jenis_izin'],
            'jenis_izin_id'=>$val['jenis_izin_id'],
            'jenis_kelamin'=>$val['jenis_kelamin'],
            'jenis_permohonan' => $val['jenis_permohonan'],
            'jenis_proses_id'=>$val['jenis_proses_id'],
            'lokasi_izin'=>$val['lokasi_izin'],
            'nama'=>$val['nama'],
            'nama_proses'=>$val['nama_proses'],
            'no_hp'=>$val['no_hp'],
            'no_izin'=>$val['no_izin'],
            'no_permohonan'=>$val['no_permohonan'],
            'no_rekomendasi'=>$val['no_rekomendasi'],
            'no_tlp'=>$val['no_tlp'],
            'start_date'=>$val['start_date'],
            'status'=>$val['status'],
            'tgl_dibuat'=>$val['tgl_dibuat'],
            'tgl_diubah'=>$val['tgl_diubah'],
            'tgl_lahir'=>$val['tgl_lahir'],
            'tgl_penetapan'=>$val['tgl_penetapan'],
            'tgl_pengajuan'=>$val['tgl_pengajuan'],
            'tgl_pengajuan_time'=>$val['tgl_pengajuan_time'],
            'tgl_rekomendasi'=>$val['tgl_rekomendasi'],
            'tgl_selesai'=>$val['tgl_selesai'],
            'tgl_selesai_time'=>$val['tgl_selesai_time'],
            'tgl_signed_report'=>$val['tgl_signed_report']
            ]);
        }
        $response2 = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/44217.json?date1='.$date1.'&date2='. $date2.'');
        $data = $response2->json();
        $items = $data['data']['data'];
        foreach ($items as $val) {
            Proses::updateOrCreate(
            ['id_proses_permohonan'=> $val['id']],
            ['alamat'=> $val['alamat'],
            'data_status'=>$val['data_status'],
            'default_active'=>$val['default_active'],
            'del'=>$val['del'],
            'dibuat_oleh'=>$val['dibuat_oleh'],
            'diproses_oleh'=>$val['diproses_oleh'],
            'diubah_oleh'=>$val['diubah_oleh'],
            'email' => $val['email'],
            'end_date'=>$val['end_date'],
            'file_signed_report'=>$val['file_signed_report'],
            'instansi_id'=>$val['instansi_id'],
            'jenis_izin'=>$val['jenis_izin'],
            'jenis_izin_id'=>$val['jenis_izin_id'],
            'jenis_kelamin'=>$val['jenis_kelamin'],
            'jenis_permohonan' => $val['jenis_permohonan'],
            'jenis_proses_id'=>$val['jenis_proses_id'],
            'lokasi_izin'=>$val['lokasi_izin'],
            'nama'=>$val['nama'],
            'nama_proses'=>$val['nama_proses'],
            'no_hp'=>$val['no_hp'],
            'no_izin'=>$val['no_izin'],
            'no_permohonan'=>$val['no_permohonan'],
            'no_rekomendasi'=>$val['no_rekomendasi'],
            'no_tlp'=>$val['no_tlp'],
            'start_date'=>$val['start_date'],
            'status'=>$val['status'],
            'tgl_dibuat'=>$val['tgl_dibuat'],
            'tgl_diubah'=>$val['tgl_diubah'],
            'tgl_lahir'=>$val['tgl_lahir'],
            'tgl_penetapan'=>$val['tgl_penetapan'],
            'tgl_pengajuan'=>$val['tgl_pengajuan'],
            'tgl_pengajuan_time'=>$val['tgl_pengajuan_time'],
            'tgl_rekomendasi'=>$val['tgl_rekomendasi'],
            'tgl_selesai'=>$val['tgl_selesai'],
            'tgl_selesai_time'=>$val['tgl_selesai_time'],
            'tgl_signed_report'=>$val['tgl_signed_report']
            ]);
        }
        
    }
}
