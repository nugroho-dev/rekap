<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;

class SicantikProsesController extends Controller
{
    public function index()
    {
        $date1 = date("Y-m-01");
        $date2 = date('Y-m-d', strtotime('-7 days', strtotime($date1)));
      
        $proses = DB::table('proses')->where('status', '=','Proses')->where('del', '=', '0')->orderBy('tgl_pengajuan')->whereNotIn('jenis_proses_id',[13,115])->paginate(50);
        return view('proses', compact('proses'));
    }
}
