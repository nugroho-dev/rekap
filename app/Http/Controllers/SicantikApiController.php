<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SicantikApiController extends Controller
{
    public function index(Request $request)
    {
        
        $cari=$request->cari;
        
        $page = request('page', 1);
        $per_page = request('per_page', 25);
        $dispage= $page * $per_page;
        $disfipage = ($dispage - $per_page)+1;
        $disfipagedata = ($page-1) * $per_page;
        if($disfipagedata==0){
            $disfipagedata='null';
        }
        else {
            $disfipagedata;
        }
        
        $previous = $page - 1;
        $next = $page + 1;
        $response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/38416.json?page='. $disfipagedata.'&per_page=' . $per_page . '&cari='.$cari.'');
        $data=$response->json();
        $items = $data['data']['data'];
        $count= $data['data']['count']['0']['data'];
        $nohp = $data['data']['data']['0']['no_hp'];
        $totalpage = ceil($count / $per_page);
        $secondlast = $totalpage  - 1;
        function format($nomorhp)
        {
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
                    $nomorhp = '0' . substr($nomorhp, 3);
                }
                // cek apakah no hp karakter 1 adalah 0
                elseif (substr($nomorhp, 0, 1) == '0') {
                    $nomorhp = '0' . substr($nomorhp, 1);
                } elseif (substr($nomorhp, 0, 1) == '8') {
                    $nomorhp = '0' . substr($nomorhp, 0);
                } elseif (substr($nomorhp, 0, 2) == '62') {
                    $nomorhp = '0' . substr($nomorhp, 2);
                }
            }
            return $nomorhp;
        }
        $userphonegsm = format($nohp);
        return view('home', compact('items','count','page','per_page','dispage', 'disfipage','totalpage', 'previous', 'next', 'secondlast' ,'userphonegsm'));
    }
    public function kirim()
    {
        $id = request('id');
        $response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/42533.json?key_id='. $id. '');
        $data = $response->json();
      
        $items = $data['data']['data']['0'];
        
        return view('kirim', compact('items','id'));
    }
    public function dokumen(Request $request)
    {
        $id = request('id');
        $pesan = $request->pesan;
        $tujuan = $request->tujuan;
        $link = $request->link;
        if(empty($pesan)&&empty($tujuan)&&empty($link)){
            $response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/42533.json?key_id=' . $id . '');
            $statuspesan = 'kosong';
        }else{
        $responsepesan = Http::retry(10, 1000)->get('http://172.18.185.247:3000/api?tujuan='. $tujuan.'&pesan='. $pesan.'&link='. $link.'');
        $response = Http::retry(10, 1000)->get('https://sicantik.go.id/api/TemplateData/keluaran/42533.json?key_id=' . $id . '');
        $datapesan = $responsepesan->json();
        $statuspesan = $datapesan['status'];
        }
        $data = $response->json();
        $items = $data['data']['data'];
        $nohp = $data['data']['data']['0']['no_hp'];
        $jenis_izin = $data['data']['data']['0']['jenis_izin'];
        $nama = $data['data']['data']['0']['nama'];
        $no_permohonan = $data['data']['data']['0']['no_izin'];

        function gantiformat($nomorhp)
        {
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
                    $nomorhp = '0' . substr($nomorhp, 3);
                }
                // cek apakah no hp karakter 1 adalah 0
                elseif (substr($nomorhp, 0, 1) == '0') {
                    $nomorhp = '0' . substr($nomorhp, 1);
                } elseif (substr($nomorhp, 0, 1) == '8') {
                    $nomorhp = '0' . substr($nomorhp, 0);
                } elseif (substr($nomorhp, 0, 2) == '62') {
                    $nomorhp = '0' . substr($nomorhp, 2);
                }
            }
            return $nomorhp;
        }
        $userphonegsm = gantiformat($nohp);
        return view('dokumen', compact('items','id', 'userphonegsm', 'jenis_izin', 'nama', 'no_permohonan','statuspesan'));
    }
}
