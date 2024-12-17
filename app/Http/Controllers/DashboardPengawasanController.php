<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawasan;
use App\Imports\PengawasanImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardPengawasanController extends Controller
{
    public function index(Request $request)
	{
        $judul='Data Pengawasan';
		$query = Pengawasan::query();
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
				   ->orWhere('uraian_kbli', 'LIKE', "%{$search}%")
                   ->orWhere('kbli', 'LIKE', "%{$search}%")
				   ->orderBy('hari_penjadwalan', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('hari_penjadwalan', [$date_start,$date_end])
				   ->orderBy('hari_penjadwalan', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/pengawasan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('hari_penjadwalan', [$month])
				   ->whereYear('hari_penjadwalan', [$year])
				   ->orderBy('hari_penjadwalan', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('hari_penjadwalan', [$year])
				   ->orderBy('hari_penjadwalan', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('hari_penjadwalan', 'asc')->paginate($perPage);
		$items->withPath(url('/pengawasan'));
		return view('admin.pengawasanpm.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
	public function edit(Pengawasan $item)
    {
        $judul = 'Edit Data Pengawasan';
		
		return view('admin.pengawasanpm.edit', compact('judul','item'));
    }
	public function update(Request $request, Pengawasan $item)
    {
		$rules=[
		'nama_perusahaan'=>'required',
		'alamat_perusahaan'=>'required',
		'status_penanaman_modal'=>'required',
		'jenis_perusahaan'=>'required',
		'nib'=>'required',
		'kbli'=>'required',
		'uraian_kbli'=>'required',
		'sektor'=>'required',
		'alamat_proyek'=>'required',
		'propinsi_proyek'=>'required',
		'daerah_kabupaten_proyek'=>'required',
		'kecamatan_proyek'=>'required',
		'kelurahan_proyek'=>'required',
		'luas_tanah'=>'required',
		'satuan_luas_tanah'=>'required',
		'jumlah_tki_l'=>'required',
		'jumlah_tki_p'=>'required',
		'jumlah_tka_l'=>'required',
		'jumlah_tka_p'=>'required',
		'resiko'=>'required',
		'sumber_data'=>'required',
		'jumlah_investasi'=>'required',
		'skala_usaha_perusahaan'=>'required',
		'skala_usaha_proyek'=>'required',
		'hari_penjadwalan'=>'required' , 
		'kewenangan_koordinator'=>'required',
		'kewenangan_pengawasan'=>'required',
		'permasalahan'=>'required',
		'rekomendasi'=>'required',
		'file'=>'file|mimes:pdf|required',];
        if ($request->nomor_kode_proyek != $item->nomor_kode_proyek) {
            $rules['nomor_kode_proyek'] = 'required|unique:pengawasan';
        }
        $validatedData = $request->validate($rules);
        if ($request->file('file')) {
            if ($request->oldFile) {
                Storage::delete($request->oldFile);
            }
            $validatedData['file'] = $request->file('file')->store('public/pengawasan-files');
        }
		
        $validatedData['del'] = 0;
        Pengawasan::where('nomor_kode_proyek', $item->nomor_kode_proyek)->update($validatedData);
        return redirect('/pengawasan/'.$item->nomor_kode_proyek.'')->with('success', 'Berhasil di Ubah !');
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
		$file->move(base_path('storage/app/public/file_pengawasan'), $nama_file);

		// import data
		Excel::import(new PengawasanImport, base_path('storage/app/public/file_pengawasan/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/pengawasan')->with('success', 'Data Berhasil Diimport !');
	}
	
    
}
