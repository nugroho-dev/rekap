<?php

namespace App\Http\Controllers;

use App\Models\Mppd;
use App\Http\Requests\StoreMppdRequest;
use App\Http\Requests\UpdateMppdRequest;
use Illuminate\Http\Request;
use App\Imports\MppdImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MppdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Izin MPP Digital';
		$query = Mppd::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama', 'LIKE', "%{$search}%")
				   ->orWhere('nik', 'LIKE', "%{$search}%")
				   ->orWhere('nomor_register', 'LIKE', "%{$search}%")
				   ->orWhere('profesi', 'LIKE', "%{$search}%")
				   ->orWhere('tempat_praktik', 'LIKE', "%{$search}%")
				   ->orWhere('nomor_sip', 'LIKE', "%{$search}%")
				   ->orWhere('keterangan', 'LIKE', "%{$search}%")
				   ->orderBy('nomor_register', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/mppdsort')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal_sip', [$date_start,$date_end])
				   ->orderBy('nomor_register', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/mppdsort')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/mppdsort')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/mppdsort')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal_sip', [$month])
				   ->whereYear('tanggal_sip', [$year])
				   ->orderBy('nomor_register', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_sip', [$year])
				   ->orderBy('nomor_register', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('nomor_register', 'desc')->paginate($perPage);
		$items->withPath(url('/mppd'));
		return view('admin.nonberusaha.mppd.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMppdRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Mppd $mppd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mppd $mppd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMppdRequest $request, Mppd $mppd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mppd $mppd)
    {
        //
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
		$file->move(base_path('storage/app/public/file_mppd'), $nama_file);

		// import data
		Excel::import(new MppdImport, base_path('storage/app/public/file_mppd/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/mppd')->with('success', 'Data Berhasil Diimport !');
	}
}
