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
			$query ->where('nama_pelaku_usaha', 'LIKE', "%{$search}%")
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
			$query ->whereBetween('tanggal_izin_terbit', [$date_start,$date_end])
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
			$query ->whereMonth('tanggal_izin_terbit', [$month])
				   ->whereYear('tanggal_izin_terbit', [$year])
				   ->orderBy('tanggal_izin_terbit', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_izin_terbit', [$year])
				   ->orderBy('tanggal_izin_terbit', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_izin_terbit', 'asc')->paginate($perPage);
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
            
            Komitmen::where('id', $commitment->id)->update($validatedData);
            return redirect('/commitment/'.$commitment->id_rule.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komitmen $commitment)
    {
        $commitment->delete();
        return redirect('/commitment')->with('success', 'Data Berhasil Dihapus!');
    }
    public function statistik(Request $request)
    {
        $judul = 'Statistik Komitmen';
        
        // Filter parameters
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $month = $request->input('month');
        $year = $request->input('year');
        
        // Base query
        $query = Komitmen::query();
        
        // Apply filters
        if ($date_start && $date_end) {
            $query->whereBetween('tanggal_izin_terbit', [$date_start, $date_end]);
        } elseif ($month && $year) {
            $query->whereMonth('tanggal_izin_terbit', $month)
                  ->whereYear('tanggal_izin_terbit', $year);
        } elseif ($year) {
            $query->whereYear('tanggal_izin_terbit', $year);
        }
        
        // Total komitmen
        $totalKomitmen = (clone $query)->count();
        
        // Total per status (Baru & Perpanjangan)
        $totalStatusBaru = (clone $query)->where('status', 'Baru')->count();
        $totalStatusPerpanjangan = (clone $query)->where('status', 'Perpanjangan')->count();
        
        // Komitmen per bulan (tahun ini) - Terpisah per status
        $currentYear = $year ?: date('Y');
        
        // Base query untuk per-month data (menghormati filter yang sama)
        $monthQueryBase = Komitmen::query();
        if ($date_start && $date_end) {
            $monthQueryBase->whereBetween('tanggal_izin_terbit', [$date_start, $date_end]);
        } elseif ($month && $year) {
            // Jika filter spesifik bulan & tahun, chart tetap menampilkan 12 bulan untuk konteks
            $monthQueryBase->whereYear('tanggal_izin_terbit', $year);
        } elseif ($year) {
            $monthQueryBase->whereYear('tanggal_izin_terbit', $year);
        } else {
            $monthQueryBase->whereYear('tanggal_izin_terbit', $currentYear);
        }
        
        // Data Baru per bulan
        $komitmenBaruPerBulan = (clone $monthQueryBase)
            ->selectRaw('MONTH(tanggal_izin_terbit) as bulan, COUNT(*) as jumlah')
            ->where('status', 'Baru')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('jumlah', 'bulan')
            ->toArray();
        
        // Data Perpanjangan per bulan
        $komitmenPerpanjanganPerBulan = (clone $monthQueryBase)
            ->selectRaw('MONTH(tanggal_izin_terbit) as bulan, COUNT(*) as jumlah')
            ->where('status', 'Perpanjangan')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('jumlah', 'bulan')
            ->toArray();
        
        // Isi bulan yang kosong dengan 0
        $chartDataBaru = [];
        $chartDataPerpanjangan = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartDataBaru[] = $komitmenBaruPerBulan[$i] ?? 0;
            $chartDataPerpanjangan[] = $komitmenPerpanjanganPerBulan[$i] ?? 0;
        }
        
        // Top 10 Jenis Izin
        $topJenisIzin = (clone $query)
            ->selectRaw('jenis_izin, COUNT(*) as jumlah')
            ->groupBy('jenis_izin')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();
        
        // Komitmen per Status
        $komitmenPerStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();
        
        // Komitmen per Keterangan (Top 10)
        $byKeterangan = (clone $query)
            ->selectRaw('keterangan, COUNT(*) as jumlah')
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->groupBy('keterangan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();
        
        // Tren komitmen (3 bulan terakhir)
        $trenKomitmen = Komitmen::selectRaw('DATE_FORMAT(tanggal_izin_terbit, "%Y-%m") as bulan, COUNT(*) as jumlah')
            ->where('tanggal_izin_terbit', '>=', now()->subMonths(3))
            ->groupBy('bulan')
            ->orderBy('bulan', 'desc')
            ->get();
       
        return view('admin.pelayananpm.komitmen.statistik', compact(
            'judul',
            'totalKomitmen',
            'totalStatusBaru',
            'totalStatusPerpanjangan',
            'chartDataBaru',
            'chartDataPerpanjangan',
            'topJenisIzin',
            'komitmenPerStatus',
            'byKeterangan',
            'trenKomitmen',
            'month',
            'year',
            'date_start',
            'date_end',
            'currentYear'
        ));
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

		// upload ke folder file_komitmen di dalam folder public
		$file->move(base_path('storage/app/public/file_komitmen'), $nama_file);

        // import data
		$import = new KomitmenImport;
		Excel::import($import, base_path('storage/app/public/file_komitmen/' . $nama_file));
        
        // Notifikasi berdasarkan hasil import
        if ($import->errorCount > 0) {
            $message = "Import selesai dengan peringatan: ";
            $message .= "{$import->importedCount} ditambahkan, ";
            $message .= "{$import->updatedCount} diperbarui, ";
            $message .= "{$import->errorCount} gagal diimport.";

            return redirect('/commitment')
                ->with('warning', $message)
                ->with('import_failures', $import->failedRows);
        } elseif ($import->updatedCount > 0 && $import->importedCount == 0) {
            return redirect('/commitment')->with('info', "Semua data sudah ada. {$import->updatedCount} data diperbarui.");
        } else {
            $message = "Data Berhasil Diimport! ";
            $message .= "{$import->importedCount} data baru ditambahkan";
            if ($import->updatedCount > 0) {
                $message .= ", {$import->updatedCount} data diperbarui";
            }
            return redirect('/commitment')->with('success', $message);
        }
	}
}
