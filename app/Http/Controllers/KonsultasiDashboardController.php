<?php

namespace App\Http\Controllers;

use App\Imports\KonsultasiImport;
use App\Models\Konsultasi;
use Illuminate\Http\Request;
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
			$query ->where('nama_pemohon', 'LIKE', "%{$search}%")
				   ->orWhere('nama_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('email', 'LIKE', "%{$search}%")
                   ->orWhere('perihal', 'LIKE', "%{$search}%")
                   ->orWhere('jenis', 'LIKE', "%{$search}%")
                   ->orWhere('no_hp', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/konsultasi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/konsultasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/konsultasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/konsultasi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal', [$month])
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal', 'desc')->paginate($perPage);
		$items->withPath(url('/konsultasi'));
		return view('admin.pelayananpm.konsultasi.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }
  

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
       
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
        //$sbuitems=Sbu::where('del', 0)->get();
        //$jenislayananitems=Jenislayanan::where('del', 0)->get();
        //$atasnamaitems=Atasnama::where('del', 0)->get();
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
        $konsultasi->delete();
        return redirect('/konsultasi')->with('success', 'Data Berhasil Dihapus!');
    }
    
    public function statistik(Request $request)
    {
        $judul = 'Statistik Konsultasi';
        
        // Ambil filter dari request
        $month = $request->input('month');
        $year = $request->input('year');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        
        // Query dasar
        $query = Konsultasi::query();
        
        // Filter berdasarkan periode
        if ($date_start && $date_end) {
            $query->whereBetween('tanggal', [$date_start, $date_end]);
        } elseif ($month && $year) {
            $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        } elseif ($year) {
            $query->whereYear('tanggal', $year);
        }
        
        // Total konsultasi
        $totalKonsultasi = (clone $query)->count();
        
        // Total per jenis
        $totalJenisKonsultasi = (clone $query)->where('jenis', 'Konsultasi')->count();
        $totalJenisInformasi = (clone $query)->where('jenis', 'Informasi')->count();
        
        // Konsultasi per bulan (tahun ini) - Terpisah per jenis
        $currentYear = $year ?: date('Y');
        
        // Data Konsultasi per bulan
        $konsultasiPerBulan = Konsultasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal', $currentYear)
            ->where('jenis', 'Konsultasi')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('jumlah', 'bulan')
            ->toArray();
        
        // Data Informasi per bulan
        $informasiPerBulan = Konsultasi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal', $currentYear)
            ->where('jenis', 'Informasi')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('jumlah', 'bulan')
            ->toArray();
        
        // Isi bulan yang kosong dengan 0
        $chartDataKonsultasi = [];
        $chartDataInformasi = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartDataKonsultasi[] = $konsultasiPerBulan[$i] ?? 0;
            $chartDataInformasi[] = $informasiPerBulan[$i] ?? 0;
        }
        
        // Top 10 perihal konsultasi
        $topPerihal = (clone $query)
            ->selectRaw('perihal, COUNT(*) as jumlah')
            ->whereNotNull('perihal')
            ->where('perihal', '!=', '')
            ->groupBy('perihal')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();
        
        // Konsultasi per jenis
        $konsultasiPerJenis = (clone $query)
            ->selectRaw('jenis, COUNT(*) as jumlah')
            ->groupBy('jenis')
            ->get();
        
        // Tren konsultasi (6 bulan terakhir)
        $trenKonsultasi = Konsultasi::selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as periode, COUNT(*) as jumlah')
            ->where('tanggal', '>=', now()->subMonths(6))
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
        
        return view('admin.pelayananpm.konsultasi.statistik', compact(
            'judul',
            'totalKonsultasi',
            'totalJenisKonsultasi',
            'totalJenisInformasi',
            'chartDataKonsultasi',
            'chartDataInformasi',
            'topPerihal',
            'konsultasiPerJenis',
            'trenKonsultasi',
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

		// upload ke folder file_siswa di dalam folder public
		$file->move(base_path('storage/app/public/file_konsultasi'), $nama_file);

		// import data
		$import = new KonsultasiImport;
		
		try {
			Excel::import($import, base_path('storage/app/public/file_konsultasi/' . $nama_file));
			
			$imported = $import->getImportedCount();
			$updated = $import->getUpdatedCount();
			$errors = $import->getErrorCount();
			
			// Buat pesan notifikasi
			$message = '';
			if ($imported > 0) {
				$message .= "$imported data baru berhasil diimport. ";
			}
			if ($updated > 0) {
				$message .= "$updated data berhasil diperbarui. ";
			}
			if ($errors > 0) {
				$errorDetails = $import->getErrors();
				$errorMsg = "$errors baris gagal diimport. ";
				
				// Tampilkan detail error (maksimal 5 baris pertama)
				$errorList = [];
				foreach (array_slice($errorDetails, 0, 5) as $error) {
					if (isset($error['row'])) {
						$errorList[] = "Baris {$error['row']}: " . implode(', ', $error['errors']);
					} elseif (isset($error['message'])) {
						$errorList[] = $error['message'];
					}
				}
				
				if (!empty($errorList)) {
					$errorMsg .= "Detail: " . implode(' | ', $errorList);
				}
				
				return redirect('/konsultasi')->with('warning', $message . $errorMsg);
			}
			
			if ($imported == 0 && $updated == 0) {
				return redirect('/konsultasi')->with('info', 'Tidak ada data yang diimport atau diperbarui.');
			}
			
			return redirect('/konsultasi')->with('success', $message ?: 'Data Berhasil Diimport!');
			
		} catch (\Exception $e) {
			return redirect('/konsultasi')->with('error', 'Gagal import data: ' . $e->getMessage());
		}
	}
    
}
