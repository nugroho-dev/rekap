<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berusaha;
use App\Imports\BerusahaImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardBerusahaController extends Controller
{
    public function index(Request $request)
	{
		$judul='Data Izin Berusaha';
		$query = Berusaha::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('del', 0)
				   ->where('nama_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('nib', 'LIKE', "%{$search}%")
				   ->orWhere('kbli', 'LIKE', "%{$search}%")
				   ->orderBy('day_of_tgl_izin', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/berusaha')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('day_of_tgl_izin', [$date_start,$date_end])
				   ->orderBy('day_of_tgl_izin', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/berusaha')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/berusaha')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/berusaha')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('day_of_tgl_izin', [$month])
				   ->whereYear('day_of_tgl_izin', [$year])
				   ->orderBy('day_of_tgl_izin', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('day_of_tgl_izin', [$year])
				   ->orderBy('day_of_tgl_izin', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->paginate($perPage);
		return view('admin.berusaha.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
	}
 
	public function export_excel()
	{
		//return Excel::download(new SiswaExport, 'siswa.xlsx');
	}
 
	public function import_excel(Request $request) 
	{
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

		// upload ke folder file_siswa di dalam folder public
		$file->move(public_path('file_berusaha'), $nama_file);

		// import data
		Excel::import(new BerusahaImport, public_path('/file_berusaha/' . $nama_file));
 
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/berusaha')->with('success', 'Data Berhasil Diimport !');
	}
	public function statistik()
	{
		$judul='Data Izin Berusaha';
		$query = Berusaha::query();
		$chartquery = "SELECT month(day_of_tgl_izin) as bulan,COUNT(*) AS jumlah_nib FROM (SELECT day_of_tgl_izin, count(nib) as s FROM berusaha GROUP BY nib) AS by_nib group by month(day_of_tgl_izin)";
		$itemsnib=$query->select(DB::raw('COUNT(*) as total'))->groupBy('nib')->get();
		$totalPerBulan = DB::select($chartquery);
		$totalAll = $itemsnib->count('total');
		return view('admin.berusaha.statistik',compact('judul','totalAll','totalPerBulan'));
	}
}
