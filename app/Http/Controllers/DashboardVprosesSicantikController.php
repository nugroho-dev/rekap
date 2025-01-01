<?php

namespace App\Http\Controllers;

use App\Models\Vproses;
use Illuminate\Http\Request;

class DashboardVprosesSicantikController extends Controller
{
    public function index(Request $request)
    {
        $judul='Data Izin SiCantik';
		$query = Vproses::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('no_permohonan', 'LIKE', "%{$search}%")
				   ->where('nama', 'LIKE', "%{$search}%")
				   ->orWhere('jenis_izin', 'LIKE', "%{$search}%")
				   ->orderBy('no_permohonan', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('end_date_akhir', [$date_start,$date_end])
				   ->orderBy('end_date_akhir', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('end_date_akhir', [$month])
				   ->whereYear('end_date_akhir', [$year])
				   ->orderBy('end_date_akhir', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('end_date_akhir', [$year])
				   ->orderBy('no_permohonan', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('no_permohonan', 'desc')->paginate($perPage);
		$items->withPath(url('/sicantik'));
		return view('admin.nonberusaha.sicantik.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
}
