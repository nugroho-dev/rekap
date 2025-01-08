<?php

namespace App\Http\Controllers;

use App\Imports\KonsultasiImport;
use App\Models\Atasnama;
use App\Models\Jenislayanan;
use App\Models\Konsultasi;
use App\Models\Sbu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Laravel\Prompts\Key;
use Maatwebsite\Excel\Facades\Excel;

class KonsultasiDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Konsultasi';
		$query = Konsultasi::query();
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
				   ->orderBy('tanggal', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
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
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/commitment')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
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
		$items=$query->where('del', 0)->orderBy('tanggal', 'asc')->paginate($perPage);
		$items->withPath(url('/konsultasi'));
		return view('admin.pelayananpm.konsultasi.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
    public function cari(Request $request)
    {
        $judul = 'Daftar Konsultansi';
        $cari=$request->cari;
       
        $nama=auth()->user()->pegawai->nama;
        $items = Konsultasi::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->whereAny(['nama', 'no_tlp', 'email','alamat','nib','nama_perusahaan','lokasi_layanan',], 'LIKE', '%'.$cari.'%')->paginate(25);
        return view('admin.pelayananpm.konsultasi.index', compact('judul','items','nama'));
    }
    public function display(Request $request)
    {
        $judul = 'Daftar Konsultansi';
        $tgl_awal=$request->tanggal_awal;
        $tgl_akhir=$request->tanggal_akhir;
        $nama=auth()->user()->pegawai->nama;
        $items = Konsultasi::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->where('tanggal','>=',$tgl_awal)->where('tanggal','<=',$tgl_akhir)->paginate(25);
        if($tgl_awal>$tgl_akhir){
            return view('admin.pelayananpm.konsultasi.index', compact('judul','items','nama'));
        }if($tgl_awal<=$tgl_akhir){
            return view('admin.pelayananpm.konsultasi.display', compact('judul','items', 'nama'));
        }
        
    }
    public function print(Request $request)
    {
        $judul = 'Daftar Konsultansi';
        $tgl_awal=$request->tanggal_awal;
        $tgl_akhir=$request->tanggal_akhir;
        //dd($tgl_awal, $tgl_akhir);
        $nama=auth()->user()->pegawai->nama;
        $item = Konsultasi::all()->where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->where('tanggal','>=',$tgl_awal)->where('tanggal','<=',$tgl_akhir);
        $data = [
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'nama' => $nama,
            'items' => $item,
            'judul' => $judul
        ];
        
        $pdf= PDF::loadView('admin.pelayananpm.konsultasi.print', $data);
        $customPaper = array(0,0,1299,827);
        $pdf->setPaper($customPaper);
        return $pdf->download('konsultasi.pdf');
        
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Konsultansi';
        $sbuitems=Sbu::where('del', 0)->get();
        $jenislayananitems=Jenislayanan::where('del', 0)->get();
        $atasnamaitems=Atasnama::where('del', 0)->get();
        return view('admin.pelayananpm.konsultasi.create', compact('judul','sbuitems','jenislayananitems','atasnamaitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'id_pegawai' => 'required',
            'id_an' => 'required',
            'id_sbu' => 'required',
            'id_jenis_layanan' => 'required',
            'tanggal' => 'required|date',
            'nama' => 'required|max:255', 
            'slug' => 'required|unique:konsultasi', 
            'no_tlp' => 'required', 
            'nama_perusahaan' => 'required', 
            'email' => 'required', 
            'nib' => 'nullable',
            'alamat' => 'required',
            'lokasi_layanan' => 'required',
            'kendala' => 'required']);
        $validatedData['del'] = 0;
        
       Konsultasi::create($validatedData);
        return redirect('/pelayanan/konsultasi')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Konsultasi $konsultasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Konsultasi $konsultasi)
    {
        $judul = 'Edit Konsultansi';
        $sbuitems=Sbu::where('del', 0)->get();
        $jenislayananitems=Jenislayanan::where('del', 0)->get();
        $atasnamaitems=Atasnama::where('del', 0)->get();
        return view('admin.pelayananpm.konsultasi.edit', compact('judul','sbuitems','jenislayananitems','atasnamaitems', 'konsultasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Konsultasi $konsultasi)
    {
        $validatedData = $request->validate([
            'id_pegawai' => 'required',
            'id_an' => 'required',
            'id_sbu' => 'required',
            'id_jenis_layanan' => 'required',
            'tanggal' => 'required|date',
            'nama' => 'required|max:255', 
            'slug' => 'required|unique:konsultasi', 
            'no_tlp' => 'required', 
            'nama_perusahaan' => 'required', 
            'email' => 'required', 
            'nib' => 'nullable',
            'alamat' => 'required',
            'lokasi_layanan' => 'required',
            'kendala' => 'required']);
        $validatedData['del'] = 0;
        
       Konsultasi::where('id', $konsultasi->id)->update($validatedData);
        return redirect('/pelayanan/konsultasi')->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Konsultasi $konsultasi)
    {
        $validatedData['del'] = 1;
        
        Konsultasi::where('id', $konsultasi->id)->update($validatedData);
         return redirect('/pelayanan/konsultasi')->with('success', 'Data  Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Konsultasi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
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
		$file->move(base_path('storage/app/public/file_konsultasi'), $nama_file);

		// import data
		Excel::import(new KonsultasiImport, base_path('storage/app/public/file_konsultasi/' . $nama_file));
        
		// notifikasi dengan session
		//Session::flash('sukses','Data  Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/konsultas')->with('success', 'Data Berhasil Diimport !');
	}
    
}
