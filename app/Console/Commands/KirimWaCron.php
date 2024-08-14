<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class KirimWaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date1 = date("H:i");
        $date2 = date("H:i", strtotime('-400 minutes', strtotime($date1)));
        $proses = DB::table('proses')->whereIn('jenis_proses_id',[2,18,30,40])->whereRaw('DATE(end_date) = DATE(NOW())')->whereRaw('TIME_FORMAT(end_date, "%H:%i") >= TIME_FORMAT("'.$date2.'", "%H:%i")')->whereRaw('TIME_FORMAT(end_date, "%H:%i") <= TIME_FORMAT("'.$date1.'","%H:%i")')->get();
        function gantiformat($nomorhp)
        {
            //$proses = DB::table('proses')->where('status', '=','Proses')->where('del', '=', '0')->orderBy('tgl_pengajuan')->whereNotIn('jenis_proses_id',[13,115])->paginate(50);
            //Terlebih dahulu kita trim dl
            $nomorhp = trim($nomorhp);
            //bersihkan dari karakter yang tidak perlu
            $nomorhp = strip_tags($nomorhp);
            // Berishkan dari spasi
            $nomorhp = str_replace(" ", "", $nomorhp);
            // bersihkan dari bentuk seperti  (022) 66677788
            $nomorhp = str_replace("(", "", $nomorhp);
            // bersihkan dari format yang ada titik seperti 0811.222.333.4
            $nomorhp = str_replace(".", "", $nomorhp);

            //cek apakah mengandung karakter + dan 0-9
            if (!preg_match('/[^+0-9]/', trim($nomorhp))) {
                // cek apakah no hp karakter 1-3 adalah +62
                if (substr(trim($nomorhp), 0, 3) == '+62') {
                    $nomorhp = '' . substr($nomorhp, 3);
                }
                // cek apakah no hp karakter 1 adalah 0
                elseif (substr($nomorhp, 0, 1) == '0') {
                    $nomorhp = '' . substr($nomorhp, 1);
                } elseif (substr($nomorhp, 0, 1) == '8') {
                    $nomorhp = '' . substr($nomorhp, 0);
                } elseif (substr($nomorhp, 0, 2) == '62') {
                    $nomorhp = '' . substr($nomorhp, 2);
                }
            }
            return $nomorhp;
        }
        $nohp = '082135982533';
        foreach ($proses as $val){
            $tujuan = gantiformat($nohp);
            if($val->jenis_proses_id == 18){
                $pesan = 'Permohonan '.$val->jenis_izin.' An '.$val->nama.' dengan nomor izin '. $val->no_permohonan .' Berhasil didaftarkan';
                $kode = $val->jenis_proses_id;
                $link='';
            }
            elseif($val->jenis_proses_id == 2){
                $pesan = 'Permohonan '.$val->jenis_izin.' An '.$val->nama.' dengan nomor izin '. $val->no_permohonan .' Telah memenuhi persyaratan dan dinyatakan lengkap dan benar';
                $kode = $val->jenis_proses_id;
                $link='';
            }
            elseif($val->jenis_proses_id == 30){
                $pesan = 'Permohonan '.$val->jenis_izin.' An '.$val->nama.' dengan nomor izin '. $val->no_permohonan .' Sudah hampir selesai, dimohon berkenan untuk mengisi survei kepuasan masyarakat pada link berikut';
                $kode = $val->jenis_proses_id;
                $link='';
            } elseif($val->jenis_proses_id == 40){
                $pesan = 'Permohonan '.$val->jenis_izin.' An '.$val->nama.' dengan nomor izin '. $val->no_permohonan .' Sudah  selesai, silakan unduh surat izin anda dengan mengetik UNDUH#NOMOR_PERMOHONAN#NO_HP';
                $kode = $val->jenis_proses_id;
                $link='https://sicantik.go.id/'.$val->file_path;
            }
            $responsepesan = Http::retry(10, 1000)->get('http://172.18.185.247:3000/apipesan?tujuan='. $tujuan.'&pesan='. $pesan.'&kode='. $kode.'&link='.$link );
        }
        
        
        //$link = 'https://sicantik.go.id/'.$item['file_path'] ;
        
        
    }
}
