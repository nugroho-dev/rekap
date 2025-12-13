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
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Pengaduan';
		$query = Pengaduan::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($search) {
			$query->where(function ($q) use ($search) {
			$q      ->where('nama', 'LIKE', "%{$search}%")
				   ->orWhere('alamat', 'LIKE', "%{$search}%")
				   ->orWhere('keluhan', 'LIKE', "%{$search}%")
				   ->orWhere('perbaikan', 'LIKE', "%{$search}%")
				   ->orWhere('nomor', 'LIKE', "%{$search}%")
				   ->orWhere('tahun', 'LIKE', "%{$search}%")
				   ->orWhere('catatan', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'desc');
		    });
		}
        

        if ($date_start && $date_end) {
			if ($date_start > $date_end) {
			return redirect('/pengaduan')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('tanggal', [$date_start, $date_end]) 
                  ->orderBy('tanggal', 'desc');
		}
        if ($month && $year) {
			$query->whereMonth('tanggal', $month)
			  ->whereYear('tanggal', $year);
		} elseif ($year) {
			$query->whereYear('tanggal', $year);
		}
        $query->orderBy('tanggal', 'desc');
		$perPage = $request->input('perPage', 50);
		$items=$query->paginate($perPage);
		$items->withPath(url('/pengaduan'));
        return view('admin.pengaduan.pengaduan.index', compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
   
    
    public function klasifikasi(Pengaduan $item)
    {
        $judul = 'Klasifikasi Pengaduan';
        $klasifikasi = Klasifikasipengaduan::all();
        $media = Mediapengaduan::all();
        return view('admin.pengaduan.pengaduan.klasifikasi', compact('judul','media','klasifikasi','item'));
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
        $pegawai = Auth::user()->pegawai->id;
        $number=$nomor+1;
        return view('admin.pengaduan.pengaduan.create', compact('judul', 'current', 'year', 'number','klasifikasi','media','pegawai'));
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
            'id_pegawai' => 'required',
        ]);
                
        
        $validatedData['tanggal'] = $request->tanggal_terima ? str_replace('T', ' ', $request->tanggal_terima) . ':00' : null;
        $validatedData['tanggal_respon'] = $request->tanggal_respon ? str_replace('T', ' ', $request->tanggal_respon) . ':00' : null;
        $validatedData['tanggal_selesai'] = $request->tanggal_selesai ? str_replace('T', ' ', $request->tanggal_selesai) . ':00' : null;
        // lakukan juga untuk tanggal_respon dan tanggal_selesai
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
    
        $validatedData['tanggal'] = $request->tanggal_terima ? str_replace('T', ' ', $request->tanggal_terima) . ':00' : null;
        $validatedData['tanggal_respon'] = $request->tanggal_respon ? str_replace('T', ' ', $request->tanggal_respon) . ':00' : null;
        $validatedData['tanggal_selesai'] = $request->tanggal_selesai ? str_replace('T', ' ', $request->tanggal_selesai) . ':00' : null;
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
        $pengaduan->delete(); // Soft delete, akan mengisi kolom deleted_at
        return redirect('/pengaduan')->with('success', 'Data Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Pengaduan::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    public function statistik(Request $request)
    {
        $judul = 'Statistik Pengaduan';
        $year = $request->input('year', date('Y'));
        // Total pengaduan
        $total = Pengaduan::whereYear('tanggal_terima', $year)->count();

        
        // Status pengaduan (misal: baru, diproses, selesai)
        $statusCounts = Pengaduan::select('catatan as status')
            ->selectRaw('count(*) as jumlah')
            ->whereYear('tanggal_terima', $year)
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // Rata-rata waktu respon & selesai (dalam jam)
        $avgRespon = Pengaduan::whereNotNull('tanggal_respon')
            ->whereYear('tanggal_terima', $year)
            ->whereNotNull('tanggal_terima')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tanggal_terima, tanggal_respon)) as avg_respon')
            ->value('avg_respon');

        $avgSelesai = Pengaduan::whereNotNull('tanggal_selesai')
            ->whereYear('tanggal_terima', $year)
            ->whereNotNull('tanggal_terima')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tanggal_terima, tanggal_selesai)) as avg_selesai')
            ->value('avg_selesai');

        // Tren bulanan (jumlah pengaduan per bulan di tahun berjalan)
        
        $trend = Pengaduan::selectRaw('MONTH(tanggal_terima) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal_terima', $year)
            ->groupByRaw('MONTH(tanggal_terima)')
            ->orderBy('bulan')
            ->pluck('jumlah', 'bulan');
    

        // Statistik klasifikasi berdasarkan relasi
        $klasifikasiCounts = Pengaduan::with('klasifikasi')
            ->whereYear('tanggal_terima', $year)
            ->get()
            ->groupBy(function($item) {
                return $item->klasifikasi ? $item->klasifikasi->klasifikasi : 'Tanpa Klasifikasi';
            })
            ->map(function($group) {
                return $group->count();
            });

        // Statistik media berdasarkan relasi
        $mediaCounts = Pengaduan::with('media')
            ->whereYear('tanggal_terima', $year)
            ->get()
            ->groupBy(function($item) {
                return $item->media ? $item->media->media : 'Tanpa Media';
            })
            ->map(function($group) {
                return $group->count();
            });

        return view('admin.pengaduan.pengaduan.statistik', compact(
            'judul',
            'total',
            'statusCounts',
            'avgRespon',
            'avgSelesai',
            'trend',
            'year',
            'klasifikasiCounts',
            'mediaCounts'
        ));
    }
}
