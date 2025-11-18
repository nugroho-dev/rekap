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
use Illuminate\Support\Facades\Cache;

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
			(SELECT p4.status FROM proses p4 WHERE p4.permohonan_izin_id = proses.permohonan_izin_id AND p4.jenis_proses_id = 40 ORDER BY p4.id ASC LIMIT 1) AS status_jenis_40,
			(SELECT p5.file_signed_report FROM proses p5 WHERE p5.permohonan_izin_id = proses.permohonan_izin_id AND p5.jenis_proses_id = 40 AND p5.file_signed_report IS NOT NULL ORDER BY p5.id DESC LIMIT 1) AS file_signed_report_40
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
			// Jika baris index tidak memiliki file_signed_report sendiri (biasanya status=Proses), gunakan hasil subquery dari langkah 40
			if (empty($item->file_signed_report) && !empty($item->file_signed_report_40)) {
				$item->file_signed_report = $item->file_signed_report_40;
			}
			// Tambahkan subtotal SLA untuk tabel index: DPMPTSP, Dinas Teknis, Gabungan
			try {
				// Ambil semua langkah untuk no_permohonan yang relevan statusnya
				$steps = Proses::where('no_permohonan', $item->no_permohonan)
					->whereNotNull('status')
					->where(function ($q) {
						$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu')")
						  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
					})
					->orderBy('id_proses_permohonan', 'ASC')
					->get(['jenis_proses_id', 'start_date', 'end_date']);

				// Tentukan rentang tanggal keseluruhan untuk efisiensi pengambilan hari libur
				$validStepDates = $steps->filter(function ($s) {
					return !empty($s->start_date) && !empty($s->end_date)
						&& !in_array((string) $s->start_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])
						&& !in_array((string) $s->end_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000']);
				});

				$minStart = $validStepDates->min('start_date');
				$maxEnd = $validStepDates->max('end_date');

				$holidaySet = [];
				if ($minStart && $maxEnd) {
					try {
						$holidaySet = Dayoff::whereBetween('tanggal', [Carbon::parse($minStart)->toDateString(), Carbon::parse($maxEnd)->toDateString()])
							->pluck('tanggal')
							->map(function ($d) { return Carbon::parse($d)->toDateString(); })
							->unique()
							->values()
							->toArray();
					} catch (\Throwable $e) {
						$holidaySet = [];
					}
				}

				$nonSlaIds = [2, 13, 18, 33, 115];
				$dinasTeknisIds = [7, 108, 185, 192, 212, 226, 234, 293, 420];

				$sumDpm = 0; // SLA DPMPTSP
				$sumDinas = 0; // SLA Dinas Teknis
				$computedAny = false;

				foreach ($steps as $s) {
					$jp = (int) $s->jenis_proses_id;
					// Validasi tanggal
					if (empty($s->start_date) || empty($s->end_date)) { continue; }
					if (in_array((string) $s->start_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) { continue; }
					if (in_array((string) $s->end_date, ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'])) { continue; }

					try {
						$start = Carbon::parse($s->start_date);
						$end = Carbon::parse($s->end_date);
					} catch (\Throwable $e) {
						continue;
					}

					// 226 juga dihitung hari kerjanya (permintaan terbaru)

					// Selaraskan dengan logika detail modal (show()):
					// - Jika start dan end di hari yang sama dan durasi > 0, maka 0 hari kerja
					// - Jika durasi <= 0, maka 0
					// - Selain itu, hitung hari kerja seperti pada modal
					$diffSecondsStep = $end->timestamp - $start->timestamp;
					$jumlahHariStep = 0;
					if ($start->isSameDay($end) && $diffSecondsStep > 0) {
						$jumlahHariStep = 0;
					} elseif ($diffSecondsStep <= 0) {
						$jumlahHariStep = 0;
					} else {
						// Hitung jumlah_hari_kerja per langkah
						$businessDays = [];
						$cursor = $start->copy();
						while ($cursor->lte($end)) {
							$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidaySet);
							if ($isBusiness) {
								$businessDays[] = $cursor->toDateString();
							}
							$cursor->addDay()->startOfDay();
						}
						$businessDayCount = count($businessDays);
						$durationHours = $end->diffInHours($start);
						if ($businessDayCount === 0) { continue; }
						if ($businessDayCount > 1 && $durationHours < ($businessDayCount * 24)) {
							$jumlahHariStep = $businessDayCount - 1;
						} elseif ($businessDayCount == 2 && $durationHours < 24) {
							$jumlahHariStep = 1;
						} else {
							$jumlahHariStep = $businessDayCount;
						}
					}
					$jumlahHariStep = max(0, (int) $jumlahHariStep);

					$computedAny = true;
					$isNonSla = in_array($jp, $nonSlaIds, true);
					$isDinas = in_array($jp, $dinasTeknisIds, true);
					$isDpm = (!$isNonSla && !$isDinas);

					if ($isDpm) { $sumDpm += $jumlahHariStep; }
					if ($isDinas) { $sumDinas += $jumlahHariStep; }
				}

				if ($computedAny) {
					$item->jumlah_hari_kerja_sla_dpmptsp = $sumDpm;
					$item->jumlah_hari_kerja_sla_dinas_teknis = $sumDinas;
					$item->jumlah_hari_kerja_sla_gabungan = $sumDpm + $sumDinas;
				} else {
					$item->jumlah_hari_kerja_sla_dpmptsp = null;
					$item->jumlah_hari_kerja_sla_dinas_teknis = null;
					$item->jumlah_hari_kerja_sla_gabungan = null;
				}
			} catch (\Throwable $e) {
				$item->jumlah_hari_kerja_sla_dpmptsp = null;
				$item->jumlah_hari_kerja_sla_dinas_teknis = null;
				$item->jumlah_hari_kerja_sla_gabungan = null;
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
		// Filter statuses to include 'Proses', 'Selesai', and 'Menunggu' (case-insensitive, including variants like 'menunggu proses')
		$steps = Proses::where('no_permohonan', $record->no_permohonan)
			->whereNotNull('status')
			->where(function ($q) {
				$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu')")
				  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
			})
			->orderBy('id_proses_permohonan', 'ASC')
			->get(['id', 'id_proses_permohonan', 'no_permohonan', 'jenis_proses_id', 'nama_proses', 'start_date', 'end_date', 'status', 'file_signed_report']);

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

		$cacheKey = 'sicantik_stat_v2_'.$year.'_'.($month ?: 'all');
		$cached = Cache::remember($cacheKey, now()->addHours(6), function () use ($year, $month) {
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
			$items = $items->map(function ($item) use ($invalidDates) {
				try {
					$no = $item->permohonan_izin_id;
					$startDate = Proses::where('permohonan_izin_id', $no)
						->where('jenis_proses_id', 2)
						->whereNotNull('end_date')
						->whereNotIn('end_date', $invalidDates)
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
						$businessDays = [];
						$cursor = $start->copy();
						while ($cursor->lte($end)) {
							$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
							if ($isBusiness) {
								$businessDays[] = $cursor->toDateString();
								if ($cursor->isSameDay($start)) {
									$endOfDay = $cursor->copy()->endOfDay();
									$minutes = min($end->diffInMinutes($cursor), $endOfDay->diffInMinutes($cursor));
									$businessDaysDecimal += $minutes / (24 * 60);
								} elseif ($cursor->isSameDay($end)) {
									$startOfDay = $cursor->copy()->startOfDay();
									$minutes = $end->diffInMinutes($startOfDay);
									$businessDaysDecimal += $minutes / (24 * 60);
								} else {
									$businessDaysDecimal += 1.0;
								}
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
						$item->jumlah_hari_kerja = max(0, $jumlahHariKerja);
						$item->business_days_decimal = $businessDaysDecimal > 0 ? round($businessDaysDecimal, 2) : 0;

						// SLA subtotals within process window using unified holidays
						try {
							$steps = Proses::where('no_permohonan', $item->no_permohonan)
								->whereNotNull('status')
								->where(function ($q) {
									$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu')")
									  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
								})
								->orderBy('id_proses_permohonan', 'ASC')
								->get(['jenis_proses_id', 'start_date', 'end_date']);

							$steps = $steps->unique(function ($s) {
								return implode('|', [
									(string) $s->jenis_proses_id,
									(string) ($s->start_date ?? ''),
									(string) ($s->end_date ?? ''),
								]);
							})->values();

							try {
								$holidaySet = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
									->pluck('tanggal')
									->map(function ($d) { return Carbon::parse($d)->toDateString(); })
									->unique()->values()->toArray();
							} catch (\Throwable $e) { $holidaySet = []; }

							$nonSlaIds = [2, 13, 18, 33, 115];
							$dinasTeknisIds = [7, 108, 185, 192, 212, 226, 234, 293, 420];
							$sumDpm = 0; $sumDinas = 0; $sumNon = 0; $computedAny = false;
							foreach ($steps as $s) {
								$jp = (int) $s->jenis_proses_id;
								if (empty($s->start_date) || empty($s->end_date)) { continue; }
								if (in_array((string) $s->start_date, $invalidDates) || in_array((string) $s->end_date, $invalidDates)) { continue; }
								try { $st = Carbon::parse($s->start_date); $ed = Carbon::parse($s->end_date); } catch (\Throwable $e) { continue; }
								if ($st->lt($start)) { $st = $start->copy(); }
								if ($ed->gt($end)) { $ed = $end->copy(); }
								$diffSecondsStep = $ed->timestamp - $st->timestamp; $jumlahHariStep = 0;
								if ($st->isSameDay($ed) && $diffSecondsStep > 0) { $jumlahHariStep = 0; }
								elseif ($diffSecondsStep <= 0) { $jumlahHariStep = 0; }
								else {
									$businessDays = []; $cur = $st->copy();
									while ($cur->lte($ed)) { $isBiz = $cur->dayOfWeekIso >= 1 && $cur->dayOfWeekIso <= 5 && !in_array($cur->toDateString(), $holidaySet); if ($isBiz) { $businessDays[] = $cur->toDateString(); } $cur->addDay()->startOfDay(); }
									$bizCount = count($businessDays); $durHours = $ed->diffInHours($st);
									if ($bizCount === 0) { continue; }
									if ($bizCount > 1 && $durHours < ($bizCount * 24)) { $jumlahHariStep = $bizCount - 1; }
									elseif ($bizCount == 2 && $durHours < 24) { $jumlahHariStep = 1; }
									else { $jumlahHariStep = $bizCount; }
								}
								$jumlahHariStep = max(0, (int) $jumlahHariStep);
								$computedAny = true;
								$isNonSla = in_array($jp, $nonSlaIds, true); $isDinas = in_array($jp, $dinasTeknisIds, true); $isDpm = (!$isNonSla && !$isDinas);
								if ($isDpm) { $sumDpm += $jumlahHariStep; }
								if ($isDinas) { $sumDinas += $jumlahHariStep; }
								if ($isNonSla) { $sumNon += $jumlahHariStep; }
							}
							if ($computedAny) {
								$item->jumlah_hari_kerja_sla_dpmptsp = $sumDpm;
								$item->jumlah_hari_kerja_sla_dinas_teknis = $sumDinas;
								$item->jumlah_hari_kerja_sla_gabungan = $sumDpm + $sumDinas;
								$item->jumlah_hari_kerja_sla_non_sla = $sumNon;
								$item->jumlah_hari_kerja_sla_selisih = ($item->jumlah_hari_kerja ?? 0) - (($sumDpm + $sumDinas + $sumNon));
							} else {
								$item->jumlah_hari_kerja_sla_dpmptsp = null;
								$item->jumlah_hari_kerja_sla_dinas_teknis = null;
								$item->jumlah_hari_kerja_sla_gabungan = null;
								$item->jumlah_hari_kerja_sla_non_sla = null;
								$item->jumlah_hari_kerja_sla_selisih = null;
							}
						} catch (\Throwable $e) {
							$item->jumlah_hari_kerja_sla_dpmptsp = null;
							$item->jumlah_hari_kerja_sla_dinas_teknis = null;
							$item->jumlah_hari_kerja_sla_gabungan = null;
							$item->jumlah_hari_kerja_sla_non_sla = null;
							$item->jumlah_hari_kerja_sla_selisih = null;
						}
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
			$jumlah_permohonan = $totalJumlahData; $coverse = 0;
			$rekapPerBulan = collect();
			$items->groupBy(function($item) { return Carbon::parse($item->end_date)->format('Y-m'); })
				->each(function($group, $bulan) use (&$rekapPerBulan) {
					$jumlah_izin_terbit = $group->count();
					$jumlah_lama_proses = $group->sum('lama_proses');
					$jumlah_hari_kerja = $group->sum('jumlah_hari_kerja');
					$jumlah_sla_dpmptsp = $group->sum('jumlah_hari_kerja_sla_dpmptsp');
					$jumlah_sla_dinas = $group->sum('jumlah_hari_kerja_sla_dinas_teknis');
					$jumlah_sla_gabungan = $group->sum('jumlah_hari_kerja_sla_gabungan');
					$jumlah_sla_non_sla = $group->sum('jumlah_hari_kerja_sla_non_sla');
					$jumlah_sla_selisih = $group->sum('jumlah_hari_kerja_sla_selisih');
					$rata_rata_hari_kerja = $jumlah_izin_terbit ? round($jumlah_hari_kerja / $jumlah_izin_terbit, 2) : 0;
					$rata_rata_sla_dpmptsp = $jumlah_izin_terbit ? round($jumlah_sla_dpmptsp / $jumlah_izin_terbit, 2) : 0;
					$rata_rata_sla_dinas = $jumlah_izin_terbit ? round($jumlah_sla_dinas / $jumlah_izin_terbit, 2) : 0;
					$rata_rata_sla_gabungan = $jumlah_izin_terbit ? round($jumlah_sla_gabungan / $jumlah_izin_terbit, 2) : 0;
					$rata_rata_sla_non_sla = $jumlah_izin_terbit ? round($jumlah_sla_non_sla / $jumlah_izin_terbit, 2) : 0;
					$rekapPerBulan->push([
						'bulan' => $bulan,
						'jumlah_izin_terbit' => $jumlah_izin_terbit,
						'jumlah_lama_proses' => $jumlah_lama_proses,
						'jumlah_hari_kerja' => $jumlah_hari_kerja,
						'jumlah_sla_dpmptsp' => $jumlah_sla_dpmptsp,
						'jumlah_sla_dinas_teknis' => $jumlah_sla_dinas,
						'jumlah_sla_gabungan' => $jumlah_sla_gabungan,
						'jumlah_sla_non_sla' => $jumlah_sla_non_sla,
						'selisih_hk' => $jumlah_sla_selisih,
						'rata_rata_hari_kerja' => $rata_rata_hari_kerja,
						'rata_rata_sla_dpmptsp' => $rata_rata_sla_dpmptsp,
						'rata_rata_sla_dinas_teknis' => $rata_rata_sla_dinas,
						'rata_rata_sla_gabungan' => $rata_rata_sla_gabungan,
						'rata_rata_sla_non_sla' => $rata_rata_sla_non_sla,
					]);
				});

			return compact('items','rekapPerBulan','totalJumlahData','totalJumlahHari','rataRataJumlahHari','jumlah_permohonan','coverse');
		});

		$items = $cached['items'];
		$rekapPerBulan = $cached['rekapPerBulan'];
		$totalJumlahData = $cached['totalJumlahData'];
		$totalJumlahHari = $cached['totalJumlahHari'];
		$rataRataJumlahHari = $cached['rataRataJumlahHari'];
		$jumlah_permohonan = $cached['jumlah_permohonan'];
		$coverse = $cached['coverse'];
		return view('admin.nonberusaha.sicantik.statistik', compact('judul','jumlah_permohonan','date_start','date_end','month','year','items','rataRataJumlahHari','totalJumlahData','totalJumlahHari','coverse','rekapPerBulan'));
	}

    // AJAX: detail items for a given year-month to avoid embedding huge JSON in Blade
    public function statistikDetail(Request $request)
    {
        $year = (int) $request->query('year');
        $month = (int) $request->query('month');
        if ($year <= 0 || $month <= 0 || $month > 12) {
            return response()->json(['error' => 'Parameter tidak valid'], 422);
        }
        $cacheKey = 'sicantik_stat_detail_v1_'.$year.'_'.str_pad($month,2,'0',STR_PAD_LEFT);
        $items = Cache::remember($cacheKey, now()->addHours(6), function () use ($year, $month) {
            $list = Proses::query()
                ->where('jenis_proses_id', 40)
                ->whereRaw("LOWER(TRIM(status)) = 'selesai'")
                ->whereNotNull('end_date')
                ->whereYear('end_date', $year)
                ->whereMonth('end_date', $month)
                ->get();
            $invalid = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
            return $list->map(function ($item) use ($invalid) {
                $row = [
                    'no_permohonan' => $item->no_permohonan,
                    'nama' => $item->nama,
                    'jenis_izin' => $item->jenis_izin,
                    'end_date' => $item->end_date,
                ];
                try {
                    $startDate = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
                        ->where('jenis_proses_id', 2)
                        ->whereNotNull('end_date')
                        ->whereNotIn('end_date', $invalid)
                        ->min('end_date');
                    if ($startDate && $item->end_date) {
                        $start = Carbon::parse($startDate); $end = Carbon::parse($item->end_date);
                        $diffSeconds = $end->timestamp - $start->timestamp;
                        if ($start->isSameDay($end) && $diffSeconds > 0) { $row['lama_proses'] = 0; }
                        elseif ($diffSeconds <= 0) { $row['lama_proses'] = 0; }
                        else { $row['lama_proses'] = ceil($diffSeconds / 86400); }
                        try {
                            $holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])->pluck('tanggal')->map(function ($d) { return Carbon::parse($d)->toDateString(); })->unique()->toArray();
                        } catch (\Throwable $e) { $holidays = []; }
                        $businessDays = []; $cursor = $start->copy();
                        while ($cursor->lte($end)) { $isBiz = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays); if ($isBiz) { $businessDays[] = $cursor->toDateString(); } $cursor->addDay()->startOfDay(); }
                        $bizCount = count($businessDays); $durHours = $end->diffInHours($start);
                        if ($bizCount > 1 && $durHours < ($bizCount * 24)) { $row['jumlah_hari_kerja'] = $bizCount - 1; }
                        elseif ($bizCount == 2 && $durHours < 24) { $row['jumlah_hari_kerja'] = 1; }
                        else { $row['jumlah_hari_kerja'] = $bizCount; }

                        // SLA subtotals
                        $steps = Proses::where('no_permohonan', $item->no_permohonan)
                            ->whereNotNull('status')
                            ->where(function ($q) {
                                $q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu')")
                                  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
                            })
                            ->orderBy('id_proses_permohonan', 'ASC')
                            ->get(['jenis_proses_id', 'start_date', 'end_date']);
                        $steps = $steps->unique(function ($s) { return implode('|', [(string)$s->jenis_proses_id,(string)($s->start_date ?? ''),(string)($s->end_date ?? '')]); })->values();
                        $holidaySet = $holidays; // reuse unified set for window
                        $nonSlaIds = [2, 13, 18, 33, 115]; $dinasIds = [7,108,185,192,212,226,234,293,420];
                        $sumDpm=0; $sumDinas=0; $sumNon=0;
						foreach ($steps as $s) {
                            if (empty($s->start_date) || empty($s->end_date)) { continue; }
                            if (in_array((string)$s->start_date,$invalid) || in_array((string)$s->end_date,$invalid)) { continue; }
                            try { $st = Carbon::parse($s->start_date); $ed = Carbon::parse($s->end_date); } catch (\Throwable $e) { continue; }
                            if ($st->lt($start)) { $st=$start->copy(); } if ($ed->gt($end)) { $ed=$end->copy(); }
							$diff = $ed->timestamp - $st->timestamp; if ($diff <= 0) { continue; }
							if ($st->isSameDay($ed) && $diff > 0) {
								$val = 0;
							} else {
								$days=[]; $cur=$st->copy();
								while($cur->lte($ed)){
									$isBiz=$cur->dayOfWeekIso>=1&&$cur->dayOfWeekIso<=5&&!in_array($cur->toDateString(),$holidaySet);
									if($isBiz){$days[]=$cur->toDateString();}
									$cur->addDay()->startOfDay();
								}
								$n=count($days); $hrs=$ed->diffInHours($st);
								if($n===0){continue;}
								$val=($n>1&&$hrs<($n*24))?($n-1):(($n==2&&$hrs<24)?1:$n);
							}
                            $jp=(int)$s->jenis_proses_id; $isNon=in_array($jp,$nonSlaIds,true); $isDin=in_array($jp,$dinasIds,true); $isDpm=(!$isNon&&!$isDin);
                            if($isDpm){$sumDpm+=$val;} if($isDin){$sumDinas+=$val;} if($isNon){$sumNon+=$val;}
                        }
                        $row['jumlah_hari_kerja_sla_dpmptsp'] = $sumDpm;
                        $row['jumlah_hari_kerja_sla_dinas_teknis'] = $sumDinas;
                        $row['jumlah_hari_kerja_sla_gabungan'] = $sumDpm + $sumDinas;
                        $row['jumlah_hari_kerja_sla_non_sla'] = $sumNon;
						// Delta reconciliation (should be 0 ideally): HK Total - (DPMPTSP + Dinas + Non-SLA)
						$row['selisih_hk'] = ($row['jumlah_hari_kerja'] ?? 0) - (($sumDpm + $sumDinas + $sumNon));
                    }
                } catch (\Throwable $e) { /* ignore per item error */ }
                return $row;
            })->values();
        });
        return response()->json(['items' => $items]);
    }

	/**
	 * Tampilkan detail proses per langkah berdasarkan no_permohonan.
	 * Menyajikan durasi hari kerja per langkah dengan klasifikasi SLA.
	 */
	public function showPermohonanProses($no_permohonan)
	{
		$judul = 'Detail Proses Izin';
		$invalid = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
		$steps = Proses::where('no_permohonan', $no_permohonan)
			->orderBy('id_proses_permohonan','ASC')
			->get(['id_proses_permohonan','jenis_proses_id','nama_proses','status','start_date','end_date']);
		$meta = Proses::where('no_permohonan', $no_permohonan)
			->orderBy('id_proses_permohonan','DESC')
			->first(['nama','jenis_izin']);
		if ($steps->isEmpty()) {
			return view('admin.nonberusaha.sicantik.detailproses', compact('judul','no_permohonan'))->with('detailError','Data proses tidak ditemukan');
		}
		// Tentukan window keseluruhan (start earliest valid jenis_proses_id=2 end latest end_date dari jenis_proses_id=40 kalau ada)
		$overallStart = Proses::where('no_permohonan',$no_permohonan)
			->where('jenis_proses_id',2)
			->whereNotNull('end_date')
			->whereNotIn('end_date',$invalid)
			->min('end_date');
		$overallEnd = Proses::where('no_permohonan',$no_permohonan)
			->where('jenis_proses_id',40)
			->whereNotNull('end_date')
			->whereNotIn('end_date',$invalid)
			->max('end_date');
		$overallStartCarbon = $overallStart ? Carbon::parse($overallStart) : null;
		$overallEndCarbon = $overallEnd ? Carbon::parse($overallEnd) : null;
		$holidays = [];
		if ($overallStartCarbon && $overallEndCarbon) {
			try {
				$holidays = Dayoff::whereBetween('tanggal', [$overallStartCarbon->toDateString(), $overallEndCarbon->toDateString()])
					->pluck('tanggal')
					->map(fn($d)=>Carbon::parse($d)->toDateString())
					->unique()->values()->toArray();
			} catch (\Throwable $e) { $holidays = []; }
		}
		$nonSlaIds = [2,13,18,33,115];
		$dinasIds = [7,108,185,192,212,226,234,293,420];
		$mapped = $steps->map(function($s) use($invalid,$holidays,$overallStartCarbon,$overallEndCarbon,$nonSlaIds,$dinasIds){
			$row = [
				'id' => $s->id_proses_permohonan,
				'jenis_proses_id' => $s->jenis_proses_id,
				'nama_proses' => $s->nama_proses,
				'status' => $s->status,
				'start_date' => $s->start_date,
				'end_date' => $s->end_date,
				'sla_klasifikasi' => null,
				'lama_hari_kerja' => null,
				'lama_jam' => null,
			];
			if (empty($s->start_date) || empty($s->end_date)) { return $row; }
			if (in_array((string)$s->start_date,$invalid) || in_array((string)$s->end_date,$invalid)) { return $row; }
			try { $st = Carbon::parse($s->start_date); $ed = Carbon::parse($s->end_date); } catch(\Throwable $e){ return $row; }
			if ($overallStartCarbon && $st->lt($overallStartCarbon)) { $st = $overallStartCarbon->copy(); }
			if ($overallEndCarbon && $ed->gt($overallEndCarbon)) { $ed = $overallEndCarbon->copy(); }
			$diff = $ed->timestamp - $st->timestamp; if ($diff <= 0) { return $row; }
			// Robust duration in minutes to prevent negative values
			$totalMinutes = $ed->greaterThan($st) ? $ed->diffInMinutes($st) : 0;
			$hours = intdiv($totalMinutes, 60);
			$minutes = $totalMinutes % 60;
			$row['lama_jam'] = $hours;
			$row['lama_menit'] = $minutes;
			if ($st->isSameDay($ed) && $diff > 0) {
				$val = 0; // Same-day dianggap 0 hari kerja (logika konsisten)
			} else {
				$days=[]; $cur=$st->copy();
				while($cur->lte($ed)){
					$isBiz=$cur->dayOfWeekIso>=1&&$cur->dayOfWeekIso<=5&&!in_array($cur->toDateString(),$holidays);
					if($isBiz){$days[]=$cur->toDateString();}
					$cur->addDay()->startOfDay();
				}
				$n=count($days); $hrs=$ed->diffInHours($st);
				if($n===0){ $val=null; }
				else { $val=($n>1&&$hrs<($n*24))?($n-1):(($n==2&&$hrs<24)?1:$n); }
			}
			$row['lama_hari_kerja'] = $val;
			$jp = (int)$s->jenis_proses_id;
			if (in_array($jp,$nonSlaIds,true)) { $row['sla_klasifikasi'] = 'Non-SLA'; }
			elseif (in_array($jp,$dinasIds,true)) { $row['sla_klasifikasi'] = 'Dinas Teknis'; }
			else { $row['sla_klasifikasi'] = 'DPMPTSP'; }
			return $row;
		})->values();
		$totalHari = $mapped->sum(function($r){ return is_numeric($r['lama_hari_kerja']) ? $r['lama_hari_kerja'] : 0; });
		$jumlahLangkah = $mapped->count();
		$rataHari = $jumlahLangkah ? round($totalHari / $jumlahLangkah,2) : 0;
		$jenisIzin = $meta->jenis_izin ?? null;
		$namaPemohon = $meta->nama ?? null;
		return view('admin.nonberusaha.sicantik.detailproses', compact('judul','no_permohonan','mapped','totalHari','rataHari','overallStartCarbon','overallEndCarbon','jenisIzin','namaPemohon'));
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
			return $request->boolean('statistik') ? redirect('/sicantik/statistik')->with('success', $msg) : redirect('/sicantik')->with('success', $msg);
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
		return $request->boolean('statistik') ? redirect('/sicantik/statistik')->with('success', $msg) : redirect('/sicantik')->with('success', $msg);
	}

}
