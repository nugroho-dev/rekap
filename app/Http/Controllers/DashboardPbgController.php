<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PbgImport;
use App\Models\Pbg;
use Maatwebsite\Excel\Facades\Excel;

class DashboardPbgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Persetujuan Bangunan Gedung';
		$query = Pbg::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama_pemilik', 'LIKE', "%{$search}%")
				   ->orWhere('jenis_permohonan', 'LIKE', "%{$search}%")
				   ->orWhere('nomor_dokumen', 'LIKE', "%{$search}%")
                   ->orWhere('nomor_registrasi', 'LIKE', "%{$search}%")
                   ->orWhere('kota_kabupaten_bangunan', 'LIKE', "%{$search}%")
                   ->orWhere('kecamatan_bangunan', 'LIKE', "%{$search}%")
                   ->orWhere('kelurahan_bangunan', 'LIKE', "%{$search}%")
                   ->orWhere('status', 'LIKE', "%{$search}%")
                   ->orWhere('status_slf', 'LIKE', "%{$search}%")
                   ->orWhere('fungsi', 'LIKE', "%{$search}%")
                   ->orWhere('tipe_konsultasi', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/pbg')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/pbg')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/pbg')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/pbg')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal', [$month])
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal', 'desc')->paginate($perPage);
		$items->withPath(url('/pbg'));
		return view('admin.nonberusaha.simbg.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
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
		$file->move(base_path('storage/app/public/file_pbg'), $nama_file);

		// import data
		Excel::import(new PbgImport, base_path('storage/app/public/file_pbg/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/pbg')->with('success', 'Data Berhasil Diimport !');
	}
}
