<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $judul = 'Daftar Pengaduan';
        
        $nama=auth()->user()->pegawai->nama;
        $items = Pengaduan::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->paginate(25);
        return view('admin.pengaduan.pengaduan.index', compact('judul','items','nama'));
    }
    public function cari(Request $request)
    {
        $judul = 'Daftar Pengaduan';
        $cari=$request->cari;
       
        $nama=auth()->user()->pegawai->nama;
        $items = Pengaduan::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->whereAny(['nama', 'no_tlp', 'email','alamat','nib','nama_perusahaan','lokasi_layanan',], 'LIKE', '%'.$cari.'%')->paginate(25);
        return view('admin.pengaduan.pengaduan.index', compact('judul','items','nama'));
    }
    public function display(Request $request)
    {
        $judul = 'Daftar Konsultansi';
        $tgl_awal=$request->tanggal_awal;
        $tgl_akhir=$request->tanggal_akhir;
        $nama=auth()->user()->pegawai->nama;
        $items = Pengaduan::where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->where('tanggal','>=',$tgl_awal)->where('tanggal','<=',$tgl_akhir)->paginate(25);
        if($tgl_awal>$tgl_akhir){
            return view('admin.pengaduan.pengaduan.index', compact('judul','items','nama'));
        }if($tgl_awal<=$tgl_akhir){
            return view('admin.pengaduan.pengaduan.display', compact('judul','items', 'nama'));
        }
        
    }

    public function printtandaterima(Request $request)
    {
        
        
        $pdf= PDF::loadView('admin.pengaduan.pengaduan.tandaterima');
        
        $pdf->setPaper(array(0,0,609.4488,935.433), 'portrait');
        return $pdf->download('pengaduan.pdf');
        
    }
    public function print(Request $request)
    {
        $judul = 'Daftar Konsultansi';
        $tgl_awal=$request->tanggal_awal;
        $tgl_akhir=$request->tanggal_akhir;
        //dd($tgl_awal, $tgl_akhir);
        $nama=auth()->user()->pegawai->nama;
        $item = Pengaduan::all()->where('del', 0)->where('id_pegawai', auth()->user()->pegawai->id)->where('tanggal','>=',$tgl_awal)->where('tanggal','<=',$tgl_akhir);
        $data = [
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'nama' => $nama,
            'items' => $item,
            'judul' => $judul
        ];
        
        $pdf= PDF::loadView('admin.pengaduan.pengaduan.print', $data);
        $customPaper = array(0,0,1299,827);
        $pdf->setPaper($customPaper);
        return $pdf->download('pengaduan.pdf');
        
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Pengaduan';
        $current = Carbon::now();
        $year = $current->year;
        $nomor = Pengaduan::where('del', 0)->where('tahun', $year)->count();
        $number=$nomor+1;
        return view('admin.pengaduan.pengaduan.create', compact('judul', 'current', 'year', 'number'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_pegawai' => 'required',
            'tanggal' => 'required|date',
            'nama' => 'required|max:255', 
            'slug' => 'required|unique:pengaduan', 
            'nomor' => 'required',
            'tahun' => 'required',
            'no_hp' => 'required', 
            'alamat' => 'required',
            'keluhan' => 'required', 
            'perbaikan' => 'required',
            'media' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5000',
            'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/pengaduan-files');
        }
        if ($request->file('file_identitas')) {
            $validatedData['file_identitas'] = $request->file('file_identitas')->store('public/pengaduan-files');
        }
        
        Pengaduan::create($validatedData);
        return redirect('/pengaduan/pengaduan')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengaduan $pengaduan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengaduan $pengaduan)
    {
        $judul = 'Edit Pengaduan';
        return view('admin.pengaduan.pengaduan.edit', compact('judul', 'pengaduan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        $rules = [
            'id_pegawai' => 'required',
            'tanggal' => 'required|date',
            'nama' => 'required|max:255', 
            'no_hp' => 'required', 
            'alamat' => 'required',
            'nomor' => 'required',
            'tahun' => 'required',
            'keluhan' => 'required', 
            'perbaikan' => 'required',
            'media' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5000',
            'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'];
        
        if ($request->slug != $pengaduan->slug) {
            $rules['slug'] = 'required|unique:pengaduan';
        }
        $validatedData = $request->validate($rules);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            if ($request->oldImageFile) {
                Storage::delete($request->oldImageFile);
            }
            $validatedData['file'] = $request->file('file')->store('public/pengaduan-files');
        }
        if ($request->file('file_identitas')) {
            if ($request->oldImageFileIdentitas) {
                Storage::delete($request->oldImageFileIdentitas);
            }
            $validatedData['file_identitas'] = $request->file('file_identitas')->store('public/pengaduan-files');
        }
        Pengaduan::where('id', $pengaduan->id)->update($validatedData);
        return redirect('/pengaduan/pengaduan')->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        $validatedData['del'] = 1;
        
        Pengaduan::where('id', $pengaduan->id)->update($validatedData);
         return redirect('/pengaduan/pengaduan')->with('success', 'Data  Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Pengaduan::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
