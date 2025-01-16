<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\Klasifikasipengaduan;
use App\Models\Mediapengaduan;
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
    public function index(Request $request)
    {
        $judul='Data Pengaduan';
		$query = Pengaduan::query()->where('del', 0);
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama', 'LIKE', "%{$search}%")
				   ->orWhere('alamat', 'LIKE', "%{$search}%")
				   ->orWhere('keluhan', 'LIKE', "%{$search}%")
				   ->orWhere('perbaikan', 'LIKE', "%{$search}%")
				   ->orWhere('nomor', 'LIKE', "%{$search}%")
				   ->orWhere('tahun', 'LIKE', "%{$search}%")
				   ->orWhere('catatan', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_terima', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/pengaduan')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal_terima', [$date_start,$date_end])
				   ->orderBy('tanggal_terima', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/pengaduan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/pengaduan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/pengaduan')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal_terima', [$month])
				   ->whereYear('tanggal_terima', [$year])
				   ->orderBy('tanggal_terima', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_terima', [$year])
				   ->orderBy('tanggal_terima', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_terima', 'desc')->paginate($perPage);
		$items->withPath(url('/pengaduan'));
        return view('admin.pengaduan.pengaduan.index', compact('judul','items','perPage','search','date_start','date_end','month','year'));
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

    public function printtandaterima(Pengaduan $item)
    {
        $pick = [
            'items' => $item,
        ];
        
        $pdf= PDF::loadView('admin.pengaduan.pengaduan.tandaterima', $pick );
        
        $pdf->setPaper(array(0,0,609.4488,935.433), 'portrait');
        return $pdf->stream('pengaduan.pdf');
    }
    
    public function klasifikasi(Pengaduan $item)
    {
        $judul = 'Klasifikasi Pengaduan';
        $klasifikasi = Klasifikasipengaduan::all();
        $media = Mediapengaduan::all();
        return view('admin.pengaduan.pengaduan.klasifikasi', compact('judul','media','klasifikasi','item'));
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
        $klasifikasi = Klasifikasipengaduan::all();
        $media = Mediapengaduan::all();
        $number=$nomor+1;
        return view('admin.pengaduan.pengaduan.create', compact('judul', 'current', 'year', 'number','klasifikasi','media'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
         
            'tanggal_terima' => 'required|date',
            'nama' => 'required|max:255', 
            'slug' => 'required|unique:pengaduan', 
            'alamat' => 'required',
            'no_hp' => 'required',
            'keluhan' => 'required',
            'perbaikan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5000',
            'id_media' => 'required',
            'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nomor' => 'required',
            'tahun' => 'required',
            'id_klasifikasi' => 'required',
            'catatan' => 'required',
            'tanggal_respon' => 'required', 
            'tanggal_selesai' => 'required',  
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/pengaduan-files');
        }
        if ($request->file('file_identitas')) {
            $validatedData['file_identitas'] = $request->file('file_identitas')->store('public/pengaduan-files');
        }
       
        Pengaduan::create($validatedData);
        return redirect('/pengaduan')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengaduan $pengaduan)
    {
        if ($pengaduan) {
            return response()->json($pengaduan);
        }

        return response()->json(['message' => 'Data not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengaduan $pengaduan)
    {
        $judul = 'Edit Pengaduan';
        $media = Mediapengaduan::all();
        $klasifikasi = Klasifikasipengaduan::all();
        return view('admin.pengaduan.pengaduan.edit', compact('judul', 'pengaduan','media','klasifikasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengaduan $pengaduan)
    {
        $rules = [

            'tanggal_terima' => 'required|date',
            'nama' => 'required|max:255',  
            'alamat' => 'required',
            'no_hp' => 'required',
            'keluhan' => 'required',
            'perbaikan' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5000',
            'id_media' => 'required',
            'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nomor' => 'required',
            'tahun' => 'required',
            'id_klasifikasi' => 'required',
            'catatan' => 'required',
            'tanggal_respon' => 'required', 
            'tanggal_selesai' => 'required', 
        ];
        
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
        return redirect('/pengaduan')->with('success', 'Data  Berhasil di Perbaharui !');
    }
    public function updateklasifikasi(Request $request, Pengaduan $item)
    {
        
        $rules = [
            'tanggal_klasifikasi'=>'required|date',
            'id_klasifikasi'=>'required',
            'diteruskan'=>'required',
            'telaah'=>'required',
            'catatan'=>'required'];
        $validatedData = $request->validate($rules);
        
       
        Pengaduan::where('id', $item->id)->update($validatedData);
        return redirect('/pengaduan/pengaduan/klasifikasi/'.$item->slug)->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        $validatedData['del'] = 1;
        
        Pengaduan::where('id', $pengaduan->id)->update($validatedData);
         return redirect('/pengaduan')->with('success', 'Data  Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Pengaduan::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
