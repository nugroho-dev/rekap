<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Simpel;
use Carbon\Carbon;

class SimpelCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpel:cron';

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
        $response3 = Http::retry(10, 1000)->get('https://dlh.magelangkota.go.id/simpel/get-json.php');
        $data = $response3->json();
        $items = $data;
        foreach ($items as $val) {
            Simpel::updateOrCreate(
            ['token'=> $val['token']],
            ['pemohon'=> $val['pemohon'],
                    'daftar' => $val['daftar'] == "" ? is_null($val['daftar']) : Carbon::parse($val['daftar'])->translatedFormat('Y-m-d'),
                    'konfirm' => $val['konfirm'] == "" ? is_null($val['konfirm']) : Carbon::parse($val['konfirm'])->translatedFormat('Y-m-d'),
                    'validasi' => $val['validasi'] == "" ? is_null($val['validasi']) : Carbon::parse($val['validasi'])->translatedFormat('Y-m-d'),
                    'rekomendasi' => $val['rekomendasi'] == "" ? is_null($val['rekomendasi']) : Carbon::parse($val['rekomendasi'])->translatedFormat('Y-m-d'),
                    'review' => $val['review'] == "" ? is_null($val['review']) : Carbon::parse($val['review'])->translatedFormat('Y-m-d'),
                    'otorisasi' => $val['otorisasi'] == "" ? is_null($val['otorisasi']) : Carbon::parse($val['otorisasi'])->translatedFormat('Y-m-d'),
                    'tte' =>  $val['tte'] == "" ? is_null($val['tte']) : Carbon::parse($val['tte'])->translatedFormat('Y-m-d'),
            'nama'=>$val['nama'],
            'gender'=>$val['gender'],
            'agama'=>$val['agama'],
                    'lahir' => $val['lahir'] == "" ? is_null($val['lahir']) : Carbon::parse($val['lahir'])->translatedFormat('Y-m-d'),
                    'wafat' => $val['wafat'] == "" ? is_null($val['wafat']) : Carbon::parse($val['wafat'])->translatedFormat('Y-m-d'),
                    'kubur' => $val['kubur'] == "" ? is_null($val['kubur']) : Carbon::parse($val['kubur'])->translatedFormat('Y-m-d'),
            'blok' => $val['blok'],
            'waris'=>$val['waris'],
            'telp'=>$val['telp'],
            'alamat'=>$val['alamat'],
            'rt'=>$val['rt'],
            'rw'=>$val['rw'],
            'desa'=>$val['desa'],
            'kec'=>$val['kec'],
            'kota'=>$val['kota'],
            'asal'=>$val['asal'],
            'jasa'=>$val['jasa'],
            'retro'=>$val['retro'],
            'biaya'=>$val['biaya'],
            'status'=>$val['status'],
            'ijin'=>$val['ijin'],
            ]);
        }
    }
}
