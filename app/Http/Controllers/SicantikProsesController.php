<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;

class SicantikProsesController extends Controller
{
    public function index()
    {
        $date1 = date("H:i");
        $date2 = date("H:i", strtotime('-400 minutes', strtotime($date1)));
        $proses = Proses::orderBy('tgl_pengajuan','desc')->paginate(50);//DB::table('proses')->whereIn('jenis_proses_id', [2,18,30,40])->whereRaw('DATE(end_date) = DATE(NOW())')->whereRaw('TIME_FORMAT(end_date, "%H:%i") >= TIME_FORMAT("'.$date2.'", "%H:%i")')->whereRaw('TIME_FORMAT(end_date, "%H:%i") <= TIME_FORMAT("'.$date1.'","%H:%i")')->paginate(50);
        
        //$proses = DB::table('proses')->where('jenis_proses_id', '=','18')->whereDate('end_date', '=', 'DATE(NOW())')->whereRaw('TIME_FORMAT(end_date, "%H:%i") >= TIME_FORMAT(SUBTIME(NOW(), "100000"), "%H:%i")')->whereRaw('TIME_FORMAT(end_date, "%H:%i") <= TIME_FORMAT(NOW(), "%H:%i")')->paginate(50);
      
        //$proses = DB::table('proses')->where('status', '=','Proses')->where('del', '=', '0')->orderBy('tgl_pengajuan')->whereNotIn('jenis_proses_id',[13,115])->paginate(50);
        return view('proses', compact('proses'));
    }
}
