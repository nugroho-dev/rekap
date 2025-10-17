<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\Pegawai;
use App\Models\Vproses;
use App\Models\Vpstatistik;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Proses;
use App\Models\Dayoff;
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
			(SELECT MIN(p2.end_date) FROM proses p2 WHERE p2.permohonan_izin_id = proses.permohonan_izin_id AND p2.jenis_proses_id = 2) AS start_date_awal,
			(SELECT MAX(p3.end_date) FROM proses p3 WHERE p3.permohonan_izin_id = proses.permohonan_izin_id AND p3.jenis_proses_id = 40) AS end_date_akhir,
			(SELECT p4.status FROM proses p4 WHERE p4.permohonan_izin_id = proses.permohonan_izin_id AND p4.jenis_proses_id = 40 ORDER BY p4.id ASC LIMIT 1) AS status_jenis_40
		")
		->orderByRaw("COALESCE(tgl_pengajuan, tgl_pengajuan_time, created_at) DESC")
		->orderBy('no_permohonan', 'ASC')
		->paginate($perPage);

		$items->withPath(url('/sicantik'));

        // Hitung lama_proses & jumlah_hari_kerja per item (start = end_date jenis=2, end = end_date jenis=40 or fallback)
        $items->getCollection()->transform(function ($item) {
            try {
                $no = $item->permohonan_izin_id;

                $invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];

                // Start: earliest end_date for jenis_proses_id = 2 (if any and valid)
                $startDate = Proses::where('permohonan_izin_id', $no)
                    ->where('jenis_proses_id', 2)
                    ->whereNotNull('end_date')
                    ->whereNotIn('end_date', $invalidDates)
                    ->min('end_date');

                // Preferred end: latest end_date for jenis_proses_id = 40 (valid)
                $endDate40 = Proses::where('permohonan_izin_id', $no)
                    ->where('jenis_proses_id', 40)
                    ->whereNotNull('end_date')
                    ->whereNotIn('end_date', $invalidDates)
                    ->max('end_date');

                // Fallback end: latest valid end_date in the group
                $lastEndDate = Proses::where('permohonan_izin_id', $no)
                    ->whereNotNull('end_date')
                    ->whereNotIn('end_date', $invalidDates)
                    ->max('end_date');

                $usedEnd = $endDate40 ?: $lastEndDate;

				// If any proses in this permohonan has jenis_proses_id = 226, we skip counting kerja days
				$hasJenis226 = Proses::where('permohonan_izin_id', $no)->where('jenis_proses_id', 226)->exists();

				if ($startDate && $usedEnd) {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($usedEnd);

					// inclusive days (allow fractional days) -> compute seconds difference and convert to days
					$diffSeconds = $end->timestamp - $start->timestamp;
					$daysFloat = ($diffSeconds / 86400) + 1;
					$item->lama_proses = round($daysFloat, 2);

					// business days Mon-Fri excluding national holidays from dayoff.tanggal
					// Fetch holidays in the inclusive range once to avoid per-day queries
					try {
						$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) {
							return Carbon::parse($d)->toDateString();
						})->unique()->toArray();
					} catch (\Throwable $e) {
						$holidays = [];
					}

					// compute total work days in the overall range
					$workDays = 0;
					$cursor = $start->copy();
					while ($cursor->lte($end)) {
						$iso = $cursor->dayOfWeekIso; // 1..7
						$ds = $cursor->toDateString();
						if ($iso >= 1 && $iso <= 5 && !in_array($ds, $holidays)) $workDays++;
						$cursor->addDay();
					}

					// If there are steps with jenis_proses_id = 226, subtract their workdays (intersected with overall range)
					$reduction = 0;
					if ($hasJenis226) {
						$steps226 = Proses::where('permohonan_izin_id', $no)
							->where('jenis_proses_id', 226)
							->whereNotNull('start_date')
							->whereNotIn('start_date', $invalidDates)
							->whereNotNull('end_date')
							->whereNotIn('end_date', $invalidDates)
							->get(['start_date', 'end_date']);

						foreach ($steps226 as $st) {
							try {
								$sStart = Carbon::parse($st->start_date);
								$sEnd = Carbon::parse($st->end_date);
							} catch (\Throwable $e) {
								continue;
							}
							// intersect with overall [start, end]
							if ($sEnd->lt($start) || $sStart->gt($end)) {
								continue; // no overlap
							}
							$is = $sStart->lt($start) ? $start->copy() : $sStart->copy();
							$ie = $sEnd->gt($end) ? $end->copy() : $sEnd->copy();

							$cursor2 = $is->copy();
							while ($cursor2->lte($ie)) {
								$iso2 = $cursor2->dayOfWeekIso;
								$ds2 = $cursor2->toDateString();
								if ($iso2 >= 1 && $iso2 <= 5 && !in_array($ds2, $holidays)) $reduction++;
								$cursor2->addDay();
							}
						}
					}

					$final = max(0, $workDays - $reduction);
					$item->jumlah_hari_kerja = $final;
                } else {
                    $item->lama_proses = null;
                    $item->jumlah_hari_kerja = null;
                }
            } catch (\Throwable $e) {
                $item->lama_proses = null;
                $item->jumlah_hari_kerja = null;
            }
            return $item;
        });
        
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
				$record = $prosesQuery->where(function($q) use ($id) {
								$q->where('id_proses_permohonan', $id)
									->orWhere('id', $id)
									->orWhere('no_permohonan', $id)
									->orWhere('permohonan_izin_id', $id);
						})->first();

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
						// durasi as fractional inclusive days, rounded to 2 decimals
						$diffSecondsStep = $end->timestamp - $start->timestamp;
						$daysFloatStep = ($diffSecondsStep / 86400) + 1;
						$s->durasi = round($daysFloatStep, 2);
						// If this step is jenis_proses_id 226, per requirement do not count kerja days for this step
						if ((int) $s->jenis_proses_id === 226) {
							$s->jumlah_hari_kerja = null;
						} else {
							// fetch holidays for the step range and exclude them from Mon-Fri counts
							try {
								$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) {
									return Carbon::parse($d)->toDateString();
								})->unique()->toArray();
							} catch (\Throwable $e) {
								$holidays = [];
							}
							$workDays = 0;
							$cursor = $start->copy();
							while ($cursor->lte($end)) {
								$wd = $cursor->dayOfWeekIso; // 1..7
								$ds = $cursor->toDateString();
								if ($wd >= 1 && $wd <= 5 && !in_array($ds, $holidays)) $workDays++;
								$cursor->addDay();
							}
							$s->jumlah_hari_kerja = $workDays;
						}
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
		$statError = null;
		$judul = 'Statistik Izin SiCantik';
		$year = (int) $request->input('year', Carbon::now()->year);
		$date_start = $request->input('date_start') ?? null;
		$date_end = $request->input('date_end') ?? null;
		$month = $request->input('month') ?? null;
		
		try {
			$invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
			// Get one row per permohonan_izin_id representing an issued permit (jenis 40, selesai) in the year
			$issued = Proses::selectRaw('permohonan_izin_id, MAX(end_date) AS end_date_40')
				->where('jenis_proses_id', 40)
				->whereRaw("LOWER(TRIM(status)) = 'selesai'")
				->whereNotNull('end_date')
				->whereYear('end_date', $year)
				->groupBy('permohonan_izin_id')
				->get();

			// initialize buckets
			$monthly = [];
			for ($m = 1; $m <= 12; $m++) {
				$monthly[$m] = ['jumlah_data' => 0, 'jumlah_hari' => 0];
			}

			foreach ($issued as $row) {
				$no = $row->permohonan_izin_id;
				$usedEndRaw = $row->end_date_40;
				if (empty($usedEndRaw)) continue;
				try {
					$usedEnd = Carbon::parse($usedEndRaw);
				} catch (\Throwable $e) {
					continue;
				}
				// increment issued count for the month of usedEnd
				$bulan = (int) $usedEnd->month;
				$monthly[$bulan]['jumlah_data']++;

				// compute start date same as index: min end_date where jenis_proses_id = 2
				$startDate = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 2)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->min('end_date');

				// fallback: latest valid end_date in the group
				$lastEndDate = Proses::where('permohonan_izin_id', $no)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->max('end_date');

				$usedEndFinalRaw = $usedEndRaw ?: $lastEndDate;
				if (empty($startDate) || empty($usedEndFinalRaw)) {
					// no start or no end -> jumlah_hari not computable; skip adding days
					continue;
				}
				try {
					$start = Carbon::parse($startDate);
					$end = Carbon::parse($usedEndFinalRaw);
				} catch (\Throwable $e) {
					continue;
				}

				// fetch holidays in range
				try {
					$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) { return Carbon::parse($d)->toDateString(); })->unique()->toArray();
				} catch (\Throwable $e) {
					$holidays = [];
				}

				// compute work days Mon-Fri excluding holidays
				$workDays = 0;
				$cursor = $start->copy();
				while ($cursor->lte($end)) {
					$iso = $cursor->dayOfWeekIso;
					$ds = $cursor->toDateString();
					if ($iso >= 1 && $iso <= 5 && !in_array($ds, $holidays)) $workDays++;
					$cursor->addDay();
				}

				// subtract days covered by jenis_proses_id = 226 overlapping with [start,end]
				$reduction = 0;
				$steps226 = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 226)
					->whereNotNull('start_date')
					->whereNotIn('start_date', $invalidDates)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->get(['start_date', 'end_date']);

				foreach ($steps226 as $st) {
					try {
						$sStart = Carbon::parse($st->start_date);
						$sEnd = Carbon::parse($st->end_date);
					} catch (\Throwable $e) {
						continue;
					}
					if ($sEnd->lt($start) || $sStart->gt($end)) continue;
					$is = $sStart->lt($start) ? $start->copy() : $sStart->copy();
					$ie = $sEnd->gt($end) ? $end->copy() : $sEnd->copy();
					$cursor2 = $is->copy();
					while ($cursor2->lte($ie)) {
						$iso2 = $cursor2->dayOfWeekIso;
						$ds2 = $cursor2->toDateString();
						if ($iso2 >= 1 && $iso2 <= 5 && !in_array($ds2, $holidays)) $reduction++;
						$cursor2->addDay();
					}
				}

				$final = max(0, $workDays - $reduction);
				$monthly[$bulan]['jumlah_hari'] += $final;
			}

			// build result collection for 12 months
			$rataRataJumlahHariPerBulan = collect(range(1,12))->map(function($m) use ($monthly, $year) {
				$jumlah_data = $monthly[$m]['jumlah_data'] ?? 0;
				$jumlah_hari = $monthly[$m]['jumlah_hari'] ?? 0;
				$rata = $jumlah_data ? ($jumlah_hari / $jumlah_data) : 0;
				return (object) ['bulan' => $m, 'tahun' => $year, 'jumlah_data' => $jumlah_data, 'jumlah_hari' => $jumlah_hari, 'rata_rata_jumlah_hari' => $rata];
			});

			$totalJumlahData = $rataRataJumlahHariPerBulan->sum('jumlah_data');
			$totalJumlahHari = $rataRataJumlahHariPerBulan->sum('jumlah_hari');
			$rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;
			$jumlah_permohonan = $totalJumlahData;
			$coverse = 0;
		} catch (\Throwable $e) {
			Log::error('DashboardVprosesSicantikController::statistik failed', ['error' => $e->getMessage()]);
			$statError = $e->getMessage();
			$jumlah_permohonan = 0;
			$rataRataJumlahHariPerBulan = collect(range(1,12))->map(function($m) use ($year) {
				return (object) ['bulan' => $m, 'tahun' => $year, 'jumlah_data' => 0, 'jumlah_hari' => 0, 'rata_rata_jumlah_hari' => 0];
			});
			$totalJumlahData = 0;
			$totalJumlahHari = 0;
			$rataRataJumlahHari = 0;
			$coverse = 0;
		}

		return view('admin.nonberusaha.sicantik.statistik', compact('judul','jumlah_permohonan','date_start','date_end','month','year','rataRataJumlahHariPerBulan', 'rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse','statError'));
	}

	/**
	 * Show create form for a new Proses (Tambah Data)
	 */
	public function create()
	{
		$judul = 'Tambah Data Izin SiCantik';
		return view('admin.nonberusaha.sicantik.create', compact('judul'));
	}

	/**
	 * Store a newly created Proses record.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'no_permohonan' => 'required|string|max:191',
			'nama' => 'required|string|max:191',
			'jenis_izin' => 'nullable|string|max:191',
			'tgl_pengajuan' => 'nullable|date',
		]);

		// Create a minimal Proses record. Other fields can be set later by sync jobs or edits.
		$proses = new Proses();
		$proses->no_permohonan = $validated['no_permohonan'];
		$proses->nama = $validated['nama'];
		$proses->jenis_izin = $validated['jenis_izin'] ?? null;
		if (!empty($validated['tgl_pengajuan'])) {
			$proses->tgl_pengajuan = $validated['tgl_pengajuan'];
		}
		$proses->status = 'Proses';
		$proses->save();

		return redirect('/sicantik')->with('success', 'Data berhasil ditambahkan');
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
