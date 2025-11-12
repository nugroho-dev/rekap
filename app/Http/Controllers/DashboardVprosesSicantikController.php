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
				$startDate = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 2)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->min('end_date');
				$endDate40 = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 40)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->max('end_date');
				$lastEndDate = Proses::where('permohonan_izin_id', $no)
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->max('end_date');
				$usedEnd = $endDate40 ?: $lastEndDate;
				$hasJenis226 = Proses::where('permohonan_izin_id', $no)->where('jenis_proses_id', 226)->exists();
				if ($startDate && $usedEnd) {
					$start = Carbon::parse($startDate);
					$end = Carbon::parse($usedEnd);
					$diffSeconds = $end->timestamp - $start->timestamp;
					$item->total_hours = round($diffSeconds / 3600, 2);
					$item->total_days = round($item->total_hours / 24, 2);
					if ($start->isSameDay($end) && $diffSeconds > 0) {
						$item->lama_proses = 0;
						$item->durasi_jam = round($diffSeconds / 3600, 2);
					} else if ($diffSeconds <= 0) {
						$item->lama_proses = 0;
						$item->durasi_jam = 0;
					} else {
						$item->lama_proses = ceil($diffSeconds / 86400);
						$item->durasi_jam = null;
					}
					try {
						$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) {
							return Carbon::parse($d)->toDateString();
						})->unique()->toArray();
					} catch (\Throwable $e) {
						$holidays = [];
					}
					// Business days as decimal
					$businessDaysDecimal = 0.0;
					$businessDays = [];
					$cursor = $start->copy();
					while ($cursor->lte($end)) {
						$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
						if ($isBusiness) {
							$businessDays[] = $cursor->toDateString();
							// First day
							if ($cursor->isSameDay($start)) {
								$endOfDay = $cursor->copy()->endOfDay();
								$minutes = min($end->diffInMinutes($cursor), $endOfDay->diffInMinutes($cursor));
								$businessDaysDecimal += $minutes / (24 * 60);
							}
							// Last day
							elseif ($cursor->isSameDay($end)) {
								$startOfDay = $cursor->copy()->startOfDay();
								$minutes = $end->diffInMinutes($startOfDay);
								$businessDaysDecimal += $minutes / (24 * 60);
							}
							// Full day
							else {
								$businessDaysDecimal += 1.0;
							}
						}
						$cursor->addDay()->startOfDay();
					}
					$businessDayCount = count($businessDays);
					$durationHours = $end->diffInHours($start);
					if ($businessDayCount > 1 && $durationHours < ($businessDayCount * 24)) {
						$item->jumlah_hari_kerja = $businessDayCount - 1;
					} elseif ($businessDayCount == 2 && $durationHours < 24) {
						$item->jumlah_hari_kerja = 1;
					} else {
						$item->jumlah_hari_kerja = $businessDayCount;
					}
					// business_days_decimal: represent fractional business days based on actual minutes
					$item->business_days_decimal = $businessDaysDecimal > 0 ? round($businessDaysDecimal, 2) : 0;
				} else {
					$item->lama_proses = null;
					$item->jumlah_hari_kerja = null;
					$item->business_days_decimal = null;
					$item->total_hours = null;
					$item->total_days = null;
				}
			} catch (\Throwable $e) {
				$item->lama_proses = null;
				$item->jumlah_hari_kerja = null;
				$item->business_days_decimal = null;
				$item->total_hours = null;
				$item->total_days = null;
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

		\Illuminate\Support\Facades\Log::info('Detail show called', ['id' => $id, 'record' => $record]);

		if (!$record) {
			\Illuminate\Support\Facades\Log::warning('Detail show not found', ['id' => $id]);
			return response()->json(['error' => 'Not found'], 404);
		}

		// Get all steps for the same no_permohonan ordered by id_proses_permohonan ASC
		$steps = Proses::where('no_permohonan', $record->no_permohonan)
			->orderBy('id_proses_permohonan', 'ASC')
			->get(['id', 'id_proses_permohonan', 'no_permohonan', 'jenis_proses_id', 'nama_proses', 'start_date', 'end_date', 'status']);

		\Illuminate\Support\Facades\Log::info('Detail show steps', ['no_permohonan' => $record->no_permohonan, 'steps_count' => $steps->count(), 'steps' => $steps]);

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
		$steps = $steps->transform(function ($step) {
			$start = null;
			if (!empty($step->start_date) && is_scalar($step->start_date)) {
				try {
					if (!in_array($step->start_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) {
						$start = Carbon::parse($step->start_date);
					}
				} catch (\Throwable $e) {
					$start = null;
				}
			}
			$step->start = $start ? $start->translatedFormat('d F Y H:i') : null;

			$end = null;
			if (!empty($step->end_date) && is_scalar($step->end_date)) {
				if (!in_array($step->end_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) {
					try {
						$end = Carbon::parse($step->end_date);
					} catch (\Throwable $e) {
						$end = null;
					}
				}
			}
			$step->end = $end ? $end->translatedFormat('d F Y H:i') : null;

			try {
				if ($start && $end) {
					$diffSecondsStep = $end->timestamp - $start->timestamp;
					$step->total_hours = round($diffSecondsStep / 3600, 2);
					$step->total_days = round($step->total_hours / 24, 2);
					$diffMinutesStep = $end->diffInMinutes($start);
					$step->durasi_jam = $diffMinutesStep > 0 ? intdiv($diffMinutesStep, 60) : 0;
					$step->durasi_menit = $diffMinutesStep > 0 ? ($diffMinutesStep % 60) : 0;
					if ($start->isSameDay($end) && $diffSecondsStep > 0) {
						$step->durasi = 0;
						$step->jumlah_hari_kerja = 0;
					} else if ($diffSecondsStep <= 0) {
						$step->durasi = 0;
						$step->jumlah_hari_kerja = 0;
					} else {
						$step->durasi = ceil($diffSecondsStep / 86400);
						if ((int) $step->jenis_proses_id === 226) {
							$step->jumlah_hari_kerja = null;
						} else {
							try {
								$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) {
									return Carbon::parse($d)->toDateString();
								})->unique()->toArray();
							} catch (\Throwable $e) {
								$holidays = [];
							}
							$jumlahHariKerja = 0;
							$cursor = $start->copy();
							$businessDays = [];
							while ($cursor->lte($end)) {
								$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
								if ($isBusiness) {
									$businessDays[] = $cursor->toDateString();
								}
								$cursor->addDay()->startOfDay();
							}
							$businessDayCount = count($businessDays);
							$durationHours = $end->diffInHours($start);
							if ($businessDayCount > 1 && $durationHours < ($businessDayCount * 24)) {
								$jumlahHariKerja = $businessDayCount - 1;
							} elseif ($businessDayCount == 2 && $durationHours < 24) {
								$jumlahHariKerja = 1;
							} else {
								$jumlahHariKerja = $businessDayCount;
							}
							$step->jumlah_hari_kerja = max(0, $jumlahHariKerja);
						}
					}
				} else {
					$step->durasi = null;
					$step->jumlah_hari_kerja = null;
					$step->total_hours = null;
					$step->total_days = null;
					$step->durasi_jam = null;
					$step->durasi_menit = null;
				}
			} catch (\Throwable $e) {
				$step->durasi = null;
				$step->jumlah_hari_kerja = null;
				$step->total_hours = null;
				$step->total_days = null;
				$step->durasi_jam = null;
				$step->durasi_menit = null;
			}
			return $step;
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
		$judul = 'Statistik Izin SiCantik';
		$year = (int) $request->input('year', Carbon::now()->year);
		$month = $request->input('month') ?? null;
		$date_start = $request->input('date_start') ?? null;
		$date_end = $request->input('date_end') ?? null;

		$query = Proses::query();
		$query->where('jenis_proses_id', 40)
			->whereRaw("LOWER(TRIM(status)) = 'selesai'")
			->whereNotNull('end_date')
			->whereYear('end_date', $year);
		if ($month) {
			$query->whereMonth('end_date', $month);
		}
		$items = $query->get();

		$invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
		$items = $items->map(function ($item) {
			try {
				$no = $item->permohonan_izin_id;
				$startDate = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 2)
					->whereNotNull('end_date')
					->whereNotIn('end_date', ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])
					->min('end_date');
				$endDate40 = $item->end_date;
				if ($startDate && $endDate40) {
					$start = Carbon::parse($startDate);
					$end = Carbon::parse($endDate40);
					$diffSeconds = $end->timestamp - $start->timestamp;
					$item->total_hours = round($diffSeconds / 3600, 2);
					$item->total_days = round($item->total_hours / 24, 2);
					if ($start->isSameDay($end) && $diffSeconds > 0) {
						$item->lama_proses = 0;
						$item->durasi_jam = round($diffSeconds / 3600, 2);
					} else if ($diffSeconds <= 0) {
						$item->lama_proses = 0;
						$item->durasi_jam = 0;
					} else {
						$item->lama_proses = ceil($diffSeconds / 86400);
						$item->durasi_jam = null;
					}
					try {
						$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) {
							return Carbon::parse($d)->toDateString();
						})->unique()->toArray();
					} catch (\Throwable $e) {
						$holidays = [];
					}
					$businessDaysDecimal = 0.0;
					$jumlahHariKerja = 0;
					$cursor = $start->copy();
					while ($cursor->lte($end)) {
						$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
						if ($isBusiness) {
							$jumlahHariKerja++;
							if ($cursor->isSameDay($start)) {
								$endOfDay = $cursor->copy()->endOfDay();
								$minutes = min($end->diffInMinutes($cursor), $endOfDay->diffInMinutes($cursor));
								$businessDaysDecimal += $minutes / (24 * 60);
							}
							elseif ($cursor->isSameDay($end)) {
								$startOfDay = $cursor->copy()->startOfDay();
								$minutes = $end->diffInMinutes($startOfDay);
								$businessDaysDecimal += $minutes / (24 * 60);
							}
							else {
								$businessDaysDecimal += 1.0;
							}
						}
						$cursor->addDay()->startOfDay();
					}
					$item->jumlah_hari_kerja = max(0, $jumlahHariKerja);
					$item->business_days_decimal = $businessDaysDecimal > 0 ? round($businessDaysDecimal, 2) : 0;
				} else {
					$item->lama_proses = null;
					$item->jumlah_hari_kerja = null;
					$item->business_days_decimal = null;
					$item->total_hours = null;
					$item->total_days = null;
				}
			} catch (\Throwable $e) {
				$item->lama_proses = null;
				$item->jumlah_hari_kerja = null;
				$item->business_days_decimal = null;
				$item->total_hours = null;
				$item->total_days = null;
			}
			return $item;
		});

		$totalJumlahData = $items->count();
		$totalJumlahHari = $items->sum('jumlah_hari_kerja');
		$rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;
		$jumlah_permohonan = $totalJumlahData;
		$coverse = 0;

		// Monthly aggregation
		$rekapPerBulan = collect();
		$items->groupBy(function($item) {
			return Carbon::parse($item->end_date)->format('Y-m');
		})->each(function($group, $bulan) use (&$rekapPerBulan) {
			$jumlah_izin_terbit = $group->count();
			$jumlah_lama_proses = $group->sum('lama_proses');
			$jumlah_hari_kerja = $group->sum('jumlah_hari_kerja');
			$rata_rata_hari_kerja = $jumlah_izin_terbit ? round($jumlah_hari_kerja / $jumlah_izin_terbit, 2) : 0;
			$rekapPerBulan->push([
				'bulan' => $bulan,
				'jumlah_izin_terbit' => $jumlah_izin_terbit,
				'jumlah_lama_proses' => $jumlah_lama_proses,
				'jumlah_hari_kerja' => $jumlah_hari_kerja,
				'rata_rata_hari_kerja' => $rata_rata_hari_kerja,
			]);
		});

		return view('admin.nonberusaha.sicantik.statistik', compact('judul','jumlah_permohonan','date_start','date_end','month','year','items','rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse','rekapPerBulan'));
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
