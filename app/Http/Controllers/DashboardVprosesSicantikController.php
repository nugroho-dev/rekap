<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\Pegawai;
use App\Models\Vproses;
use App\Models\Vpstatistik;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Proses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardVprosesSicantikController extends Controller
{
	public function index(Request $request)
	{
	// Prepare query for listing; read directly from Proses model
	$query = Proses::query();

		// Filters expected by the view
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		$group = $request->input('group');

		// Apply search
		if ($search) {
			$query->where(function ($q) use ($search) {
				$q->where('no_permohonan', 'LIKE', "%{$search}%")
				  ->orWhere('nama', 'LIKE', "%{$search}%")
				  ->orWhere('jenis_izin', 'LIKE', "%{$search}%");
			});
		}

		// Date range filter (tgl_penetapan)
		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('tgl_penetapan', [$date_start, $date_end]);
		}

		// Month/year filters
		if ($month && $year) {
			$query->whereMonth('tgl_penetapan', $month)->whereYear('tgl_penetapan', $year);
		} elseif ($year) {
			$query->whereYear('tgl_penetapan', $year);
		}

		// Only show items that are still in process
		$query->where('status', 'Proses');

		// Pagination
		$perPage = (int) $request->input('perPage', 10);
		if ($perPage <= 0) $perPage = 10;

		// Grouped toggle: when grouped, we may want to show one row per no_permohonan
		$grouped = $request->boolean('group');
		// Add a computed column start_date_awal: earliest end_date for jenis_proses_id = 2 per no_permohonan
		$items = $query->selectRaw("proses.*,
			(SELECT MIN(p2.end_date) FROM proses p2 WHERE p2.no_permohonan = proses.no_permohonan AND p2.jenis_proses_id = 2) AS start_date_awal,
			(SELECT MAX(p3.end_date) FROM proses p3 WHERE p3.no_permohonan = proses.no_permohonan AND p3.jenis_proses_id = 40) AS end_date_akhir,
			(SELECT p4.status FROM proses p4 WHERE p4.no_permohonan = proses.no_permohonan AND p4.jenis_proses_id = 40 ORDER BY p4.id ASC LIMIT 1) AS status_jenis_40
		")
		->orderByRaw("COALESCE(tgl_pengajuan, tgl_pengajuan_time, created_at) DESC")
		->orderBy('no_permohonan', 'ASC')
		->paginate($perPage);

		$items->withPath(url('/sicantik'));

		// Calendar helpers used by the view
		$namaBulan = [
			'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
		];
		$currentYear = Carbon::now()->year;
		$startYear = max(2019, $currentYear - 5);

		$judul = 'Data Izin SiCantik';

		return view('admin.nonberusaha.sicantik.index', compact(
			'items', 'perPage', 'grouped', 'search', 'month', 'year', 'date_start', 'date_end', 'namaBulan', 'startYear', 'currentYear', 'judul'
		));
	}

	/**
	 * Return JSON detail for a given proses record (by id or no_permohonan)
	 * Expected to be called via AJAX for the detail modal.
	 */
public function show(Request $request, $id)
	{
		// Try to interpret $id as slug (id_proses_permohonan) first, then as numeric id, then as no_permohonan
		$prosesQuery = Proses::query();
		$record = $prosesQuery->where('id_proses_permohonan', $id)
			->orWhere('id', $id)
			->orWhere('no_permohonan', $id)
			->first();

		if (!$record) {
			return response()->json(['error' => 'Not found'], 404);
		}

		// Get all steps for the same no_permohonan ordered by start_date
		// don't select computed columns that may not exist in the table (durasi, jumlah_hari_kerja)
		$steps = Proses::where('no_permohonan', $record->no_permohonan)
			->orderByRaw("COALESCE(start_date, created_at) ASC")
			->get(['id', 'id_proses_permohonan', 'no_permohonan', 'jenis_proses_id', 'nama_proses', 'start_date', 'end_date', 'status']);

		// Debug: if requested, return diagnostics about duplicated step rows
		if ($request->boolean('debug_dupes')) {
			$collection = $steps;
			$total = $collection->count();
			$uniqueIds = $collection->pluck('id')->unique()->count();
			// group by combination likely to indicate duplicate rows
			$groups = $collection->groupBy(function ($s) {
				return $s->jenis_proses_id . '|' . ($s->start_date ?? '') . '|' . ($s->end_date ?? '') . '|' . ($s->nama_proses ?? '');
			});
			$duplicateGroups = $groups->filter(function ($g) {
				return $g->count() > 1;
			})->map(function ($g) {
				return [
					'count' => $g->count(),
					'samples' => $g->map(function ($r) {
						return [
							'id' => $r->id,
							'id_proses_permohonan' => $r->id_proses_permohonan,
							'start_date' => $r->start_date,
							'end_date' => $r->end_date,
							'nama_proses' => $r->nama_proses,
							'status' => $r->status,
						];
					})->values()->all(),
				];
			})->values()->all();

			return response()->json([
				'record_no_permohonan' => $record->no_permohonan,
				'total_steps' => $total,
				'unique_step_ids' => $uniqueIds,
				'duplicate_count' => max(0, $total - $uniqueIds),
				'duplicate_groups' => $duplicateGroups,
			]);
		}

		// Deduplicate steps by a stable signature so the UI doesn't show repeated identical rows.
		$steps = $steps->unique(function ($s) {
			return implode('|', [
				(string) $s->jenis_proses_id,
				(string) ($s->start_date ?? ''),
				(string) ($s->end_date ?? ''),
				(string) ($s->nama_proses ?? ''),
				(string) ($s->status ?? ''),
			]);
		})->values();

		// Compute a friendly label for each step (defensive parsing)
		$steps->transform(function ($s) {
			// Safe parse for start_date
			$start = null;
			if (!empty($s->start_date) && is_scalar($s->start_date)) {
				try {
					if (!in_array($s->start_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) {
						$start = Carbon::parse($s->start_date);
					}
				} catch (\Throwable $e) {
					$start = null;
				}
			}
			$s->start = $start ? $start->translatedFormat('d F Y H:i') : null;

			// Safe parse for end_date, ignoring sentinel zero dates
			$end = null;
			if (!empty($s->end_date) && is_scalar($s->end_date)) {
				if (!in_array($s->end_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) {
					try {
						$end = Carbon::parse($s->end_date);
					} catch (\Throwable $e) {
						$end = null;
					}
				}
			}
			$s->end = $end ? $end->translatedFormat('d F Y H:i') : null;

			// Compute durasi (inclusive days) and jumlah_hari_kerja (business days Mon-Fri) for the step
			try {
				if ($start && $end) {
					$s->durasi = $start->diffInDays($end) + 1;
					$workDays = 0;
					$cursor = $start->copy();
					while ($cursor->lte($end)) {
						$wd = $cursor->dayOfWeekIso; // 1..7
						if ($wd >= 1 && $wd <= 5) $workDays++;
						$cursor->addDay();
					}
					$s->jumlah_hari_kerja = $workDays;
				} else {
					// If either date missing, leave as null for the caller to decide fallback
					$s->durasi = null;
					$s->jumlah_hari_kerja = null;
				}
			} catch (\Throwable $e) {
				$s->durasi = null;
				$s->jumlah_hari_kerja = null;
			}

			return $s;
		});

		return response()->json(['record' => $record, 'steps' => $steps]);
	}
	public function print(Request $request)
	{
		
		$judul = 'Data Izin SiCantik';
		$query = Vproses::query();
		$instansi = Instansi::where('slug', '=', 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang')->first();
		$pegawai = Pegawai::where('ttd', 1)->first();
		$nama = $pegawai->nama;
		$nip = $pegawai->nip;
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		$jenis_izin = $request->input('jenis_izin');
		$logo = $instansi->logo;
		
		if ($search) {
			$query->where(function ($q) use ($search) {
			$q->where('no_permohonan', 'LIKE', "%{$search}%")
			  ->orWhere('nama', 'LIKE', "%{$search}%")
			  ->orWhere('jenis_izin', 'LIKE', "%{$search}%");
			});
		}

		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
			return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			$query->whereBetween('tgl_penetapan', [$date_start, $date_end]);
		}

		if ($jenis_izin && $month && $year) {
			$query->where('jenis_izin', $jenis_izin)
			  ->whereMonth('tgl_penetapan', $month)
			  ->whereYear('tgl_penetapan', $year);
		} elseif ($month && $year) {
			$query->whereMonth('tgl_penetapan', $month)
			  ->whereYear('tgl_penetapan', $year);
		} elseif ($year) {
			$query->whereYear('tgl_penetapan', $year);
		}

		$perPage = $request->input('perPage', 111);
	$items = $query->orderByRaw("COALESCE(tgl_pengajuan, tgl_pengajuan_time, created_at) ASC")
		   ->orderBy('no_permohonan', 'ASC')
		   ->paginate($perPage);
		$items->withPath(url('/sicantik'));
		return Pdf::loadView('admin.nonberusaha.sicantik.print.print', compact('items','search','logo', 'month', 'year','nama','nip'))
			->setPaper('A4', 'landscape')
			->stream('sicantik.pdf');
	}

	public function statistik(Request $request)
    {
		$judul='Statistik Izin SiCantik';
		$query = Proses::query();
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$now = Carbon::now();
		$year = $request->input('year');
		if ($request->has('year')) {
            $year = $request->input('year');
           
            $jumlah_permohonan = Proses::where('jenis_proses_id', 18)
                ->whereYear('start_date', $year)
                ->count();
            
            $terbit = DB::table('sicantik.sicantik_proses_statistik')
                ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data, sum(jumlah_hari_kerja) - IFNULL(sum(jumlah_rekom),0) - IFNULL(sum(jumlah_cetak_rekom),0) - IFNULL(sum(jumlah_tte_rekom),0) - IFNULL(sum(jumlah_verif_rekom),0) - IFNULL(sum(jumlah_proses_bayar),0) as jumlah_hari')
                ->whereNotNull('tgl_penetapan')
                ->whereYear('tgl_penetapan', $year)
                ->groupByRaw('month(tgl_penetapan)')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalJumlahData = $terbit->sum('jumlah_data');
            $totalJumlahHari = $terbit->sum('jumlah_hari');
            $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $totalJumlahData = $terbit->sum('jumlah_data');
                $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                    $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_data;
					return $item;
				});
				$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
		} else {
			$year = $now->year;
			$jumlah_permohonan = Proses::where('jenis_proses_id', 18)->whereYear('start_date', [$year])->count();
            $terbit = DB::table('sicantik.sicantik_proses_statistik')
                ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, count(tgl_penetapan) as jumlah_data, sum(jumlah_hari_kerja - COALESCE(jumlah_rekom,0) - COALESCE(jumlah_cetak_rekom,0) - COALESCE(jumlah_tte_rekom,0) - COALESCE(jumlah_verif_rekom,0) - COALESCE(jumlah_proses_bayar,0)) as jumlah_hari')
                ->whereNotNull('tgl_penetapan')
                ->whereYear('tgl_penetapan', $year)
                ->groupByRaw('month(tgl_penetapan)')
                ->orderBy('bulan', 'asc')
                ->get();
                $totalJumlahData = $terbit->sum('jumlah_data');
                $totalJumlahHari = $terbit->sum('jumlah_hari');
                $rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;

            $rataRataJumlahHariPerBulan = $terbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_data;
                return $item;
            });
      
			$coverse = $jumlah_permohonan ? number_format($totalJumlahData / $jumlah_permohonan * 100, 2) : 0;
			
		};
		
		return view('admin.nonberusaha.sicantik.statistik',compact('judul','jumlah_permohonan','date_start','date_end','month','year','rataRataJumlahHariPerBulan', 'rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse'));
	}
    public function rincian(Request $request)
    {
        $judul='Statistik Izin SiCantik';
        if ($request->has('year') && $request->has('month')) {
            $year = $request->input('year');
            $month = $request->input('month');
            $rincianterbit = DB::table('sicantik.sicantik_proses_statistik')
            ->selectRaw('month(tgl_penetapan) AS bulan, year(tgl_penetapan) AS tahun, jenis_izin, jenis_izin_id, count(jenis_izin) as jumlah_izin, sum(jumlah_hari_kerja) - COALESCE(sum(jumlah_rekom),0) - COALESCE(sum(jumlah_cetak_rekom),0) - COALESCE(sum(jumlah_tte_rekom),0) - COALESCE(sum(jumlah_verif_rekom),0) - COALESCE(sum(jumlah_proses_bayar),0) as jumlah_hari')
            ->whereNotNull('tgl_penetapan')
            ->whereYear('tgl_penetapan', $year)
            ->whereMonth('tgl_penetapan', $month)
            ->groupBy('jenis_izin')
            ->orderBy('jumlah_izin', 'desc')
            ->get();

            $totalJumlahHari = $rincianterbit->sum('jumlah_hari');
            $total_izin = $rincianterbit->sum('jumlah_izin');
            $rataRataJumlahHari = $total_izin ? $totalJumlahHari / $total_izin : 0;

            $rataRataJumlahHariPerJenisIzin = $rincianterbit->map(function ($item) {
                $item->rata_rata_jumlah_hari = $item->jumlah_hari / $item->jumlah_izin;
                return $item;
            });
        }
		return view('admin.nonberusaha.sicantik.rincian',compact('judul','month','year','rataRataJumlahHariPerJenisIzin', 'rataRataJumlahHari','total_izin','totalJumlahHari'));
    }
	public function sync(Request $request)
	{
		$request->validate([
			'date_start' => 'required|date_format:Y-m-d',
			'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start',
			'async' => 'nullable|boolean',
			'statistik' => 'nullable',
			'force_sync' => 'nullable|boolean',
		]);

		$date1 = $request->input('date_start');
		$date2 = $request->input('date_end');

		// If the date range is large, split into smaller subranges so each job stays small.
		$maxDaysPerJob = (int) config('sicantik.sync_max_days', 7); // default 7 days per job, configurable
		$start = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
		$end = \Carbon\Carbon::createFromFormat('Y-m-d', $date2);
		$diffDays = $start->diffInDays($end) + 1;
		$jobsDispatched = 0;

		// If force_sync is provided and true, run synchronous small-range sync (not recommended for large ranges)
		if ($request->boolean('force_sync')) {
			// call the job handler synchronously
			$job = new \App\Jobs\SyncSicantikProsesJob($date1, $date2, Auth::id() ?? null);
			$job->handle();
			$msg = 'Sinkronisasi selesai (sinkron).';
			return $request->input('statistik') ? redirect('/statistik')->with('success', $msg) : redirect('/sicantik')->with('success', $msg);
		}

		// If range is small enough, dispatch single job. Otherwise split into subranges.
		if ($diffDays <= $maxDaysPerJob) {
			try {
				\App\Jobs\SyncSicantikProsesJob::dispatch($date1, $date2, Auth::id() ?? null);
				$jobsDispatched = 1;
			} catch (\Throwable $e) {
				Log::error('Failed to dispatch SyncSicantikProsesJob', ['error' => $e->getMessage()]);
				return redirect('/sicantik')->with('error', 'Gagal menjadwalkan sinkronisasi.');
			}
		} else {
			$cursor = $start->copy();
			while ($cursor->lte($end)) {
				$chunkStart = $cursor->copy();
				$chunkEnd = $cursor->copy()->addDays($maxDaysPerJob - 1);
				if ($chunkEnd->gt($end)) $chunkEnd = $end->copy();
				try {
					\App\Jobs\SyncSicantikProsesJob::dispatch($chunkStart->toDateString(), $chunkEnd->toDateString(), Auth::id() ?? null);
					$jobsDispatched++;
				} catch (\Throwable $e) {
					Log::error('Failed to dispatch SyncSicantikProsesJob for subrange', ['error' => $e->getMessage(), 'start' => $chunkStart->toDateString(), 'end' => $chunkEnd->toDateString()]);
				}
				$cursor->addDays($maxDaysPerJob);
			}
		}

		if ($jobsDispatched === 0) {
			return redirect('/sicantik')->with('error', 'Gagal menjadwalkan sinkronisasi.');
		}

		$msg = "Sinkronisasi telah dijadwalkan. {$jobsDispatched} job(s) dijadwalkan dan akan berjalan di background.";
		return $request->input('statistik') ? redirect('/statistik')->with('success', $msg) : redirect('/sicantik')->with('success', $msg);
	}

}
