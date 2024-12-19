<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BimtekImport;
use Illuminate\Support\Facades\Storage;

class DashboardBimtekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Bimbingan Teknis OSS RBA & LKPM';
		$query = Bimtek::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('del', 0)
				   ->where('jumlah_peserta', 'LIKE', "%{$search}%")
				   ->orWhere('acara', 'LIKE', "%{$search}%")
				   ->orWhere('tempat', 'LIKE', "%{$search}%")
                   ->orWhere('keterangan', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_pelaksanaan', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/bimtek')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('tanggal_pelaksanaan', [$date_start,$date_end])
				   ->orderBy('tanggal_pelaksanaan', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/bimtek')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/bimtek')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/bimtek')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('tanggal_pelaksanaan', [$month])
				   ->whereYear('tanggal_pelaksanaan', [$year])
				   ->orderBy('tanggal_pelaksanaan', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('tanggal_pelaksanaan', [$year])
				   ->orderBy('tanggal_pelaksanaan', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_pelaksanaan', 'asc')->paginate($perPage);
		$items->withPath(url('/bimtek'));
		return view('admin.pembinaan.bimtek.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
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
    public function show(Bimtek $bimtek)
    {
        

        if ($bimtek) {
            return response()->json($bimtek);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bimtek $bimtek)
    {
        $judul = 'Edit Data Bimbingan Teknis OSS RBA & LKPM';
        return view('admin.pembinaan.bimtek.edit', compact('judul','bimtek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bimtek $bimtek)
    {
        $rules=[
            'tanggal_pelaksanaan'=>'required',
            'jumlah_peserta'=>'required',
            'satuan_peserta'=>'required',
            'acara'=>'required',
            'tempat'=>'required',
            'keterangan'=>'required',
            'file'=>'file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            if ($request->file('file')) {
                if ($request->oldFile) {
                    Storage::delete($request->oldFile);
                }
                $validatedData['file'] = $request->file('file')->store('public/bimtek-files');
            }
            
            $validatedData['del'] = 0;
            Bimtek::where('id', $bimtek->id)->update($validatedData);
            return redirect('/bimtek/'.$bimtek->id.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bimtek $bimtek)
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
		$file->move(base_path('storage/app/public/file_bimtek'), $nama_file);

		// import data
		Excel::import(new BimtekImport, base_path('storage/app/public/file_bimtek/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/bimtek')->with('success', 'Data Berhasil Diimport !');
	}
}
