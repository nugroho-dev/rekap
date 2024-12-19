<?php

namespace App\Http\Controllers;

use App\Imports\FasilitasiImport;
use App\Models\Fasilitasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardFasilitasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Fasilitasi';
		$query = Fasilitasi::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('del', 0)
				   ->where('tempat', 'LIKE', "%{$search}%")
				   ->orWhere('fasilitas', 'LIKE', "%{$search}%")
				   ->orWhere('permasalahan', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/fasilitasi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/fasilitasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/fasilitasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/fasilitasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('tanggal', [$month])
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal', 'asc')->paginate($perPage);
		$items->withPath(url('/fasilitasi'));
		return view('admin.pembinaan.fasilitasi.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Fasilitasi $fasilitasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fasilitasi $fasilitasi)
    {
        $judul = 'Edit Data Bimbingan Teknis OSS RBA & LKPM';
        return view('admin.pembinaan.fasilitasi.edit', compact('judul','fasilitasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fasilitasi $fasilitasi)
    {
        $rules=[
            'tanggal'=>'required',
            'keterangan'=>'required',
            'permasalahan'=>'required',
            'fasilitasi'=>'required',
            'tempat'=>'required',
            'keterangan'=>'required'];
            //'file'=>'file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            //if ($request->file('file')) {
                //if ($request->oldFile) {
                    //Storage::delete($request->oldFile);
                //}
                //$validatedData['file'] = $request->file('file')->store('public/fasilitasi-files');
            //}
            
            $validatedData['del'] = 0;
            Fasilitasi::where('id', $fasilitasi->id)->update($validatedData);
            return redirect('/fasilitasi/'.$fasilitasi->id.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fasilitasi $fasilitasi)
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
		$file->move(base_path('storage/app/public/file_fasilitasi'), $nama_file);

		// import data
		Excel::import(new FasilitasiImport, base_path('storage/app/public/file_fasilitasi/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/fasilitasi')->with('success', 'Data Berhasil Diimport !');
	}
}
