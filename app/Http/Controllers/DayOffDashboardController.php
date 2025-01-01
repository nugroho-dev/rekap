<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Dayoff;
use Illuminate\Http\Request;

class DayOffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $judul='Data Hari Libur Nasional';
		$query = Dayoff::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('Keterangan', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/dayoff')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/dayoff')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/dayoff')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/dayoff')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal', [$month])
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query  ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal', 'desc')->paginate($perPage);
		$items->withPath(url('/dayoff'));
        return view('admin.konfigurasi.dayoff.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
    public function handle(Request $request)
    {
        $year=$request->input('year');
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
        return redirect('/dayoff')->with('success', 'Hari libur Berhasil di Tambahkan !');
    }
}
