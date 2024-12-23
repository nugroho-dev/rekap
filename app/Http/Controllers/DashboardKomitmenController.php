<?php

namespace App\Http\Controllers;

use App\Imports\KomitmenImport;
use App\Models\Komitmen;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardKomitmenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Komitmen';
		$query = Komitmen::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('del', 0)
				   ->where('nama_pelaku_usaha', 'LIKE', "%{$search}%")
				   ->orWhere('alamat_pelaku_usaha', 'LIKE', "%{$search}%")
				   ->orWhere('nama_proyek', 'LIKE', "%{$search}%")
                   ->orWhere('jenis_izin', 'LIKE', "%{$search}%")
                   ->orWhere('status', 'LIKE', "%{$search}%")
                   ->orWhere('nib', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_izin_terbit', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('tanggal_izin_terbit', [$date_start,$date_end])
				   ->orderBy('tanggal_izin_terbit', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('tanggal_izin_terbit', [$month])
				   ->whereYear('tanggal_izin_terbit', [$year])
				   ->orderBy('tanggal_izin_terbit', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('tanggal_izin_terbit', [$year])
				   ->orderBy('tanggal_izin_terbit', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->where('del', 0)->orderBy('tanggal_izin_terbit', 'asc')->paginate($perPage);
		$items->withPath(url('/commitment'));
		return view('admin.pelayananpm.komitmen.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
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
    public function show(Komitmen $komitmen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Komitmen $commitment)
    {
        $judul = 'Edit Data Komitmen';
        return view('admin.pelayananpm.komitmen.edit', compact('judul','commitment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Komitmen $commitment)
    {
		$rules=[
            'nama_pelaku_usaha'=>'required',
            'alamat_pelaku_usaha'=>'required',
            'nib'=>'required',
            'nama_proyek'=>'required',
            'jenis_izin'=>'required',
            'status'=>'required',
			'tanggal_izin_terbit'=>'required',
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
            Komitmen::where('id', $commitment->id)->update($validatedData);
            return redirect('/commitment/'.$commitment->id_rule.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komitmen $komitmen)
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
		$file->move(base_path('storage/app/public/file_komitmen'), $nama_file);

		// import data
		Excel::import(new KomitmenImport, base_path('storage/app/public/file_komitmen/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/komitmen')->with('success', 'Data Berhasil Diimport !');
	}
}
