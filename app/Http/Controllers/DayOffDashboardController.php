<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Dayoff;
use Illuminate\Http\Request;

class DayOffDashboardController extends Controller
{
    public function index()
    {

    }
    public function handle()
    {
        $year=date("Y");
        $response2 = Http::retry(10, 1000)->get('https://dayoffapi.vercel.app/api?year='.$year.'');
        $data = $response2->json();
      
        $items = $data;
        foreach ($items as $val) {
            Dayoff::updateOrCreate(
            ['tanggal'=> $val['tanggal']],
            ['keterangan'=> $val['keterangan'],
            'is_cuti'=>$val['is_cuti'],
            ]);
        }
    }
}
