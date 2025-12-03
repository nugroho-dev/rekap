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
use Illuminate\Support\Facades\Schema;
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
		// Add a computed column start_date_awal: earliest end_date for jenis_proses_id in (2,14) per no_permohonan
		$items = $query->selectRaw("proses.*,
			(SELECT MIN(p2.end_date) FROM proses p2 WHERE p2.permohonan_izin_id = proses.permohonan_izin_id AND p2.jenis_proses_id IN (2,14)) AS start_date_awal,
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
					->whereIn('jenis_proses_id', [2,14])
					->whereNotNull('end_date')
					->whereNotIn('end_date', $invalidDates)
					->min('end_date');
				// Ambil langkah jenis 40 terbaru lalu gunakan tanggal akhir = min(end_date, tgl_signed_report)
				$latest40 = Proses::where('permohonan_izin_id', $no)
					->where('jenis_proses_id', 40)
					->where(function($q){ $q->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
					->orderByRaw('COALESCE(end_date, tgl_signed_report) DESC')
					->first(['end_date','tgl_signed_report']);
				$endDate40 = null;
				if ($latest40) {
					$e = $latest40->end_date;
					$s = $latest40->tgl_signed_report;
					$eValid = $e && !in_array((string)$e, $invalidDates);
					$sValid = $s && !in_array((string)$s, $invalidDates);
					if ($eValid && $sValid) {
						$endDate40 = Carbon::parse($e)->lt(Carbon::parse($s)) ? $e : $s; // min(e,s)
					} elseif ($eValid) {
						$endDate40 = $e;
					} elseif ($sValid) {
						$endDate40 = $s;
					}
				}
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
					// Ambil semua langkah untuk no_permohonan yang relevan statusnya (ditambah status tambahan)
					$steps = Proses::where('no_permohonan', $item->no_permohonan)
						->whereNotNull('status')
						->where(function ($q) {
							$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu','verifikasi','validasi','upload','tte','terbit','diterbitkan')")
							  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
						})
						->orderBy('id_proses_permohonan', 'ASC')
						->get(['jenis_proses_id', 'start_date', 'end_date', 'tgl_signed_report']);

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
					if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date, $invalidDates)) {
						$s->start_date = $s->end_date; // fallback same-day
					}
					$jp = (int) $s->jenis_proses_id;
						// Untuk jenis 40, gunakan end_date = min(end_date, tgl_signed_report) bila keduanya valid
						if ($jp === 40 && !empty($s->tgl_signed_report) && !in_array((string)$s->tgl_signed_report, $invalidDates) && !empty($s->end_date) && !in_array((string)$s->end_date, $invalidDates)) {
							$ed = Carbon::parse($s->end_date);
							$sr = Carbon::parse($s->tgl_signed_report);
							if ($ed->gt($sr)) { $s->end_date = $sr->toDateTimeString(); }
						}
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
		$steps = Proses::where('no_permohonan', $record->no_permohonan)
			->whereNotNull('status')
			->where(function ($q) {
				$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu','verifikasi','validasi','upload','tte','terbit','diterbitkan')")
				  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
			})
			->orderBy('id_proses_permohonan', 'ASC')
			->get(['id', 'id_proses_permohonan', 'no_permohonan', 'jenis_proses_id', 'nama_proses', 'start_date', 'end_date', 'tgl_signed_report', 'status', 'file_signed_report']);

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
			if (empty($step->start_date) && !empty($step->end_date) && !in_array($step->end_date, ['0001-01-01 00:00:00','0001-01-01 00:00:00.000'])) {
				$step->start_date = $step->end_date; // fallback
			}
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
			// Untuk jenis proses 40, gunakan end_date = min(end_date, tgl_signed_report) bila keduanya valid
			if ((int)($step->jenis_proses_id) === 40 && !empty($step->tgl_signed_report) && !in_array((string)$step->tgl_signed_report, ['0001-01-01 00:00:00','0001-01-01 00:00:00.000']) && !empty($step->end_date) && !in_array((string)$step->end_date, ['0001-01-01 00:00:00','0001-01-01 00:00:00.000'])) {
				try {
					$ed = Carbon::parse($step->end_date);
					$sr = Carbon::parse($step->tgl_signed_report);
					if ($ed->gt($sr)) { $step->end_date = $sr->toDateTimeString(); }
				} catch (\Throwable $e) { /* ignore */ }
			}
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
			// SLA classification: force jenis_proses_id 14 into DPMPTSP per request
			$nonSlaIds = [2,13,18,33,115];
			$dinasTeknisIds = [7,108,185,192,212,226,234,293,420];
			$forceDpmIds = [14,403]; // explicit override
			$jp = (int) $step->jenis_proses_id;
			if (in_array($jp, $forceDpmIds, true)) {
				$step->sla_klasifikasi = 'DPMPTSP';
			} elseif (in_array($jp, $nonSlaIds, true)) {
				$step->sla_klasifikasi = 'Non-SLA';
			} elseif (in_array($jp, $dinasTeknisIds, true)) {
				$step->sla_klasifikasi = 'Dinas Teknis';
			} else {
				$step->sla_klasifikasi = 'DPMPTSP';
			}
			return $step;
		});

		return response()->json(['record' => $record, 'steps' => $steps]);
	}
	public function print(Request $request)
	{

		$judul = 'Data Izin SiCantik';
		// Legacy view/table 'sicantik_proses' (Vproses) no longer available; switch to core 'proses' data
		// Use finalized steps (jenis_proses_id=40, status selesai) as issued permits for print report
		$query = Proses::query()
			->where('jenis_proses_id', 40)
			->whereRaw("LOWER(TRIM(status)) = 'selesai'")
			->whereNotNull('end_date');
		$instansi = Instansi::where('slug', '=', 'dinas-penanaman-modal-dan-pelayanan-terpadu-satu-pintu-kota-magelang')->first();
		// Safely resolve penandatangan without assuming a 'ttd' column exists
		$pegawai = null;
		try {
			$table = (new Pegawai)->getTable();
			if (Schema::hasColumn($table, 'ttd')) {
				$pegawai = Pegawai::where('ttd', 1)->first();
			}
		} catch (\Throwable $e) {
			$pegawai = null;
		}
		if (!$pegawai) {
			$pegawai = Pegawai::first();
		}
		$nama = $pegawai->nama ?? '';
		$nip = $pegawai->nip ?? '';
		$hasSigner = (trim((string)$nama) !== '' || trim((string)$nip) !== '');
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		$jenis_izin = $request->input('jenis_izin');
		$logo = $instansi->logo ?? null;
		
		if ($search) {
			$query->where(function ($q) use ($search) {
				$q->where('no_permohonan', 'LIKE', "%{$search}%")
				  ->orWhere('nama', 'LIKE', "%{$search}%")
				  ->orWhere('jenis_izin', 'LIKE', "%{$search}%")
				  ->orWhere('no_izin', 'LIKE', "%{$search}%");
			});
		}

		if ($date_start && $date_end) {
			if ($date_start > $date_end) {
				return redirect('/sicantik')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda');
			}
			// Prefer tgl_penetapan range; fallback to end_date if tgl_penetapan null
			$query->where(function($q) use ($date_start,$date_end){
				$q->whereBetween('tgl_penetapan', [$date_start, $date_end])
				  ->orWhere(function($qq) use ($date_start,$date_end){
					  $qq->whereNull('tgl_penetapan')->whereBetween('end_date', [$date_start, $date_end]);
				  });
			});
		}

		if ($jenis_izin && $month && $year) {
			$query->where('jenis_izin', $jenis_izin)
				->where(function($q) use ($month,$year){
					$q->whereMonth('tgl_penetapan', $month)->whereYear('tgl_penetapan',$year)
					  ->orWhere(function($qq) use ($month,$year){
						  $qq->whereNull('tgl_penetapan')->whereMonth('end_date',$month)->whereYear('end_date',$year);
					  });
				});
		} elseif ($month && $year) {
			$query->where(function($q) use ($month,$year){
				$q->whereMonth('tgl_penetapan',$month)->whereYear('tgl_penetapan',$year)
				  ->orWhere(function($qq) use ($month,$year){
					  $qq->whereNull('tgl_penetapan')->whereMonth('end_date',$month)->whereYear('end_date',$year);
				  });
			});
		} elseif ($year) {
			$query->where(function($q) use ($year){
				$q->whereYear('tgl_penetapan',$year)
				  ->orWhere(function($qq) use ($year){
					  $qq->whereNull('tgl_penetapan')->whereYear('end_date',$year);
				  });
			});
		}

		$perPage = $request->input('perPage', 111);
	$items = $query->orderByRaw("COALESCE(tgl_penetapan, end_date, created_at) ASC")
		   ->orderBy('no_permohonan', 'ASC')
		   ->paginate($perPage);
		$items->withPath(url('/sicantik'));
		return Pdf::loadView('admin.nonberusaha.sicantik.print.print', compact('items','search','logo', 'month', 'year','nama','nip','hasSigner'))
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

		// Bump versi cache karena refactor performa
		$cacheKey = 'sicantik_stat_v4_'.$year.'_'.($month ?: 'all');
		// Default TTL dipendekkan menjadi 2 jam untuk keseimbangan antara fresh data dan performa.
			$cached = Cache::remember($cacheKey, now()->addHours(2), function () use ($year, $month) {
			$baseQuery = Proses::query()
				->where('jenis_proses_id', 40)
				->whereRaw("LOWER(TRIM(status)) = 'selesai'")
				->whereNotNull('end_date')
				->whereYear('end_date', $year);
			if ($month) { $baseQuery->whereMonth('end_date', $month); }
			$items = $baseQuery->get();
			$invalidDates = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];

			// Ambil semua permohonan id & no_permohonan dari items
			$permIds = $items->pluck('permohonan_izin_id')->filter()->unique()->values();
			$noPerms = $items->pluck('no_permohonan')->filter()->unique()->values();

			// Preload semua proses terkait untuk window dan SLA (minimalkan N+1)
			$allRelated = Proses::query()
				->whereIn('permohonan_izin_id', $permIds)
				->where(function($q){ $q->whereNotNull('start_date')->orWhereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
				->get(['permohonan_izin_id','no_permohonan','jenis_proses_id','start_date','end_date','tgl_signed_report','status','id_proses_permohonan']);

			$relatedByPerm = $allRelated->groupBy('permohonan_izin_id');
			$relatedByNoPerm = $allRelated->groupBy('no_permohonan');

			// Hitung window (start earliest jenis 2/14, end latest end_date) untuk tiap item
			$windows = [];
			foreach ($items as $it) {
				$pid = $it->permohonan_izin_id;
				$group = $relatedByPerm->get($pid, collect());
				// Ambil startDate utama dari start_date proses jenis 2/14
				$startDate = $group->filter(function($p) use ($invalidDates){
					return in_array($p->jenis_proses_id, [2,14], true)
						&& !empty($p->start_date)
						&& !in_array((string)$p->start_date, $invalidDates);
				})->min('start_date');
				// Fallback: bila tidak ada start_date valid, gunakan end_date paling awal dari jenis 2/14
				if (!$startDate) {
					$startDate = $group->filter(function($p) use ($invalidDates){
						return in_array($p->jenis_proses_id, [2,14], true)
							&& !empty($p->end_date)
							&& !in_array((string)$p->end_date, $invalidDates);
					})->min('end_date');
				}
				// Fallback kedua: jika masih null, ambil start_date paling awal dari SEMUA proses permohonan
				if (!$startDate) {
					$startDate = $group->filter(function($p) use ($invalidDates){
						return !empty($p->start_date) && !in_array((string)$p->start_date, $invalidDates);
					})->min('start_date');
				}
				// Ambil endDate terakhir dengan aturan jenis 40: end = min(end_date, tgl_signed_report)
				$effectiveEnds = $group->map(function($p) use ($invalidDates){
					$end = null;
					$eValid = !empty($p->end_date) && !in_array((string)$p->end_date, $invalidDates);
					$sValid = !empty($p->tgl_signed_report) && !in_array((string)$p->tgl_signed_report, $invalidDates);
					if ((int)$p->jenis_proses_id === 40) {
						if ($eValid && $sValid) {
							$end = Carbon::parse($p->end_date)->lt(Carbon::parse($p->tgl_signed_report)) ? $p->end_date : $p->tgl_signed_report;
						} elseif ($eValid) { $end = $p->end_date; }
						elseif ($sValid) { $end = $p->tgl_signed_report; }
					} else {
						if ($eValid) { $end = $p->end_date; }
					}
					return $end;
				})->filter()->values();
				$endDate = $effectiveEnds->max();
				// Jika startDate masih kosong namun endDate ada, set startDate=endDate (konsisten fallback step)
				if (!$startDate && $endDate) { $startDate = $endDate; }
				if ($startDate && $endDate) {
					$windows[$pid] = [Carbon::parse($startDate), Carbon::parse($endDate)];
				}
			}

			// Holiday global range sekali (optimisasi) lalu nanti difilter per window
			$globalStart = collect($windows)->map(fn($w)=>$w[0])->min();
			$globalEnd = collect($windows)->map(fn($w)=>$w[1])->max();
			$holidayGlobal = [];
			if ($globalStart && $globalEnd) {
				try {
					$holidayGlobal = Dayoff::whereBetween('tanggal', [$globalStart->toDateString(), $globalEnd->toDateString()])
						->pluck('tanggal')
						->map(fn($d)=>Carbon::parse($d)->toDateString())
						->unique()->values()->toArray();
				} catch (\Throwable $e) { $holidayGlobal = []; }
			}

			// Helper perhitungan business day sama seperti logika lama
			$calcBusinessDays = function(Carbon $start, Carbon $end, array $holidays){
				$businessDaysDecimal = 0.0; $businessDates = []; $cursor = $start->copy();
				while ($cursor->lte($end)) {
					$isBusiness = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
					if ($isBusiness) {
						$businessDates[] = $cursor->toDateString();
						if ($cursor->isSameDay($start)) {
							$endOfDay = $cursor->copy()->endOfDay();
							$minutes = min($end->diffInMinutes($cursor), $endOfDay->diffInMinutes($cursor));
							$businessDaysDecimal += $minutes / (24*60);
						} elseif ($cursor->isSameDay($end)) {
							$startOfDay = $cursor->copy()->startOfDay();
							$minutes = $end->diffInMinutes($startOfDay);
							$businessDaysDecimal += $minutes / (24*60);
						} else {
							$businessDaysDecimal += 1.0;
						}
					}
					$cursor->addDay()->startOfDay();
				}
				$bizCount = count($businessDates); $durHours = $end->diffInHours($start);
				if ($bizCount > 1 && $durHours < ($bizCount * 24)) { $jumlahHariKerja = $bizCount - 1; }
				elseif ($bizCount == 2 && $durHours < 24) { $jumlahHariKerja = 1; }
				else { $jumlahHariKerja = $bizCount; }
				return [max(0,$jumlahHariKerja), $businessDaysDecimal > 0 ? round($businessDaysDecimal,2) : 0];
			};

			$nonSlaIds = [2, 13, 18, 33, 115];
			$dinasTeknisIds = [7, 108, 185, 192, 212, 226, 234, 293, 420];
			$forceDpmIds = [14, 403];

			// Proses setiap item memakai data yang sudah dipreload
			foreach ($items as $item) {
				try {
					// Normalisasi end_date untuk langkah 40 pada item (min(end_date, tgl_signed_report))
					if ((int)($item->jenis_proses_id ?? 0) === 40) {
						$e = $item->end_date ?? null; $s = $item->tgl_signed_report ?? null;
						if ($e && $s && !in_array((string)$e,$invalidDates) && !in_array((string)$s,$invalidDates)) {
							$item->end_date = Carbon::parse($e)->lt(Carbon::parse($s)) ? $e : $s;
						}
					}
					$pid = $item->permohonan_izin_id;
					if (!isset($windows[$pid])) {
						$item->lama_proses = null;
						$item->jumlah_hari_kerja = null;
						$item->business_days_decimal = null;
						$item->total_hours = null;
						$item->total_days = null;
						continue;
					}
					[$start,$end] = $windows[$pid];
					$diffSeconds = $end->timestamp - $start->timestamp;
					$item->total_hours = round($diffSeconds / 3600, 2);
					$item->total_days = round($item->total_hours / 24, 2);
					if ($start->isSameDay($end) && $diffSeconds > 0) {
						$item->lama_proses = 0; $item->durasi_jam = round($diffSeconds/3600,2);
					} elseif ($diffSeconds <= 0) {
						$item->lama_proses = 0; $item->durasi_jam = 0;
					} else {
						$item->lama_proses = ceil($diffSeconds / 86400); $item->durasi_jam = null;
					}
					// Filter holiday untuk window ini
					$holidays = array_values(array_filter($holidayGlobal, function($h) use ($start,$end){ return $h >= $start->toDateString() && $h <= $end->toDateString(); }));
					list($jumlahHariKerja,$businessDaysDecimal) = $calcBusinessDays($start,$end,$holidays);
					$item->jumlah_hari_kerja = $jumlahHariKerja;
					$item->business_days_decimal = $businessDaysDecimal;

					// Hitung SLA per langkah (replikasi logika lama)
					$steps = $relatedByNoPerm->get($item->no_permohonan, collect())->filter(function($s){ return !empty($s->status); });
					$steps = $steps->unique(function ($s) {
						return implode('|', [ (string)$s->jenis_proses_id, (string)($s->start_date ?? ''), (string)($s->end_date ?? '') ]);
					})->values();
					$holidaySet = $holidays; // gunakan holiday window yang sama
					$sumDpm=0; $sumDinas=0; $sumNon=0; $computedAny=false;
					foreach ($steps as $s) {
						if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalidDates)) { $s->start_date = $s->end_date; }
						$jp = (int)$s->jenis_proses_id;
						// Jenis 40: gunakan end_date efektif = min(end_date, tgl_signed_report)
						if ($jp === 40 && !empty($s->tgl_signed_report) && !in_array((string)$s->tgl_signed_report,$invalidDates) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalidDates)) {
							try { $ed = Carbon::parse($s->end_date); $sr = Carbon::parse($s->tgl_signed_report); if ($ed->gt($sr)) { $s->end_date = $sr->toDateTimeString(); } } catch (\Throwable $e) {}
						}
						if (empty($s->start_date) || empty($s->end_date)) { continue; }
						if (in_array((string)$s->start_date,$invalidDates) || in_array((string)$s->end_date,$invalidDates)) { continue; }
						try { $st = Carbon::parse($s->start_date); $ed = Carbon::parse($s->end_date); } catch (\Throwable $e) { continue; }
						if ($st->lt($start)) { $st = $start->copy(); }
						if ($ed->gt($end)) { $ed = $end->copy(); }
						$diffSecStep = $ed->timestamp - $st->timestamp; $jumlahHariStep = 0;
						if ($st->isSameDay($ed) && $diffSecStep > 0) { $jumlahHariStep = 0; }
						elseif ($diffSecStep <= 0) { $jumlahHariStep = 0; }
						else {
							$businessDays = []; $cur = $st->copy();
							while ($cur->lte($ed)) { $isBiz = $cur->dayOfWeekIso >=1 && $cur->dayOfWeekIso <=5 && !in_array($cur->toDateString(), $holidaySet); if ($isBiz) { $businessDays[] = $cur->toDateString(); } $cur->addDay()->startOfDay(); }
							$bizCount = count($businessDays); $durHours = $ed->diffInHours($st);
							if ($bizCount === 0) { continue; }
							if ($bizCount > 1 && $durHours < ($bizCount*24)) { $jumlahHariStep = $bizCount - 1; }
							elseif ($bizCount == 2 && $durHours < 24) { $jumlahHariStep = 1; }
							else { $jumlahHariStep = $bizCount; }
						}
						$jumlahHariStep = max(0,(int)$jumlahHariStep); $computedAny = true;
						if (in_array($jp,$forceDpmIds,true)) { $sumDpm += $jumlahHariStep; }
						else {
							$isNon = in_array($jp,$nonSlaIds,true); $isDinas = in_array($jp,$dinasTeknisIds,true); $isDpm = (!$isNon && !$isDinas);
							if ($isDpm) { $sumDpm += $jumlahHariStep; }
							if ($isDinas) { $sumDinas += $jumlahHariStep; }
							if ($isNon) { $sumNon += $jumlahHariStep; }
						}
					}
					if ($computedAny) {
						$item->jumlah_hari_kerja_sla_dpmptsp = $sumDpm;
						$item->jumlah_hari_kerja_sla_dinas_teknis = $sumDinas;
						$item->jumlah_hari_kerja_sla_gabungan = $sumDpm + $sumDinas;
						$item->jumlah_hari_kerja_sla_non_sla = $sumNon;
						$item->jumlah_hari_kerja_sla_selisih = ($item->jumlah_hari_kerja ?? 0) - ($sumDpm + $sumDinas + $sumNon);
					} else {
						$item->jumlah_hari_kerja_sla_dpmptsp = null;
						$item->jumlah_hari_kerja_sla_dinas_teknis = null;
						$item->jumlah_hari_kerja_sla_gabungan = null;
						$item->jumlah_hari_kerja_sla_non_sla = null;
						$item->jumlah_hari_kerja_sla_selisih = null;
					}
				} catch (\Throwable $e) {
					$item->lama_proses = null;
					$item->jumlah_hari_kerja = null;
					$item->business_days_decimal = null;
					$item->total_hours = null;
					$item->total_days = null;
					$item->jumlah_hari_kerja_sla_dpmptsp = null;
					$item->jumlah_hari_kerja_sla_dinas_teknis = null;
					$item->jumlah_hari_kerja_sla_gabungan = null;
					$item->jumlah_hari_kerja_sla_non_sla = null;
					$item->jumlah_hari_kerja_sla_selisih = null;
				}
			}

			$totalJumlahData = $items->count(); // jumlah izin terbit (selesai)
			$totalJumlahHari = $items->sum('jumlah_hari_kerja');
			$rataRataJumlahHari = $totalJumlahData ? $totalJumlahHari / $totalJumlahData : 0;
			// Jumlah pengajuan = semua permohonan (jenis proses 2/14) yang mulai (start_date) atau fallback end_date di tahun (dan bulan jika dipilih)
			$pengajuanQuery = Proses::query()->whereIn('jenis_proses_id',[2,14])
				->where(function($q) use ($year,$month){
					$q->whereYear('start_date',$year);
					if($month){ $q->whereMonth('start_date',$month); }
				});
			// Fallback tambahan: jika start_date null, pakai end_date di tahun/bulan
			$pengajuanQuery->orWhere(function($q) use ($year,$month){
				$q->whereIn('jenis_proses_id',[2,14])
				  ->whereNull('start_date')
				  ->whereYear('end_date',$year);
				if($month){ $q->whereMonth('end_date',$month); }
			});
			$jumlah_permohonan = $pengajuanQuery->distinct()->count('permohonan_izin_id');
			$coverse = $jumlah_permohonan ? round(($totalJumlahData / $jumlah_permohonan)*100,2) : 0;

			$rekapPerBulan = collect();
			$items->groupBy(function($item){ return Carbon::parse($item->end_date)->format('Y-m'); })
				->each(function($group,$bulan) use (&$rekapPerBulan){
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
		$cacheKey = 'sicantik_stat_detail_v2_'.$year.'_'.str_pad($month,2,'0',STR_PAD_LEFT);
		// Detail per-bulan juga pakai TTL 2 jam.
		$items = Cache::remember($cacheKey, now()->addHours(2), function () use ($year, $month) {
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
					// Normalisasi end_date untuk langkah 40 pada item (min(end_date, tgl_signed_report))
					if ((int)($item->jenis_proses_id ?? 0) === 40) {
						$e = $item->end_date ?? null; $s = $item->tgl_signed_report ?? null;
						if ($e && $s && !in_array((string)$e,$invalid) && !in_array((string)$s,$invalid)) {
							try { $ed = \Carbon\Carbon::parse($e); $sr = \Carbon\Carbon::parse($s); $row['end_date'] = $ed->lt($sr) ? $e : $s; } catch (\Throwable $ex) {}
						}
					}
					// Window start: earliest valid start_date of step in (2,14)
					$startDate = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
						->whereIn('jenis_proses_id', [2,14])
						->whereNotNull('start_date')
						->whereNotIn('start_date', $invalid)
						->min('start_date');
					// Window end: latest effective end across all steps for that permohonan
					$stepsForWindow = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
						->where(function($q){ $q->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
						->get(['jenis_proses_id','end_date','tgl_signed_report']);
					$effectiveEnds = $stepsForWindow->map(function($p) use ($invalid) {
						$end = null;
						$eValid = !empty($p->end_date) && !in_array((string)$p->end_date, $invalid);
						$sValid = !empty($p->tgl_signed_report) && !in_array((string)$p->tgl_signed_report, $invalid);
						if ((int)$p->jenis_proses_id === 40) {
							if ($eValid && $sValid) {
								try { $end = (\Carbon\Carbon::parse($p->end_date)->lt(\Carbon\Carbon::parse($p->tgl_signed_report))) ? $p->end_date : $p->tgl_signed_report; } catch (\Throwable $ex) { $end = $p->end_date; }
							} elseif ($eValid) { $end = $p->end_date; }
							elseif ($sValid) { $end = $p->tgl_signed_report; }
						} else {
							if ($eValid) { $end = $p->end_date; }
						}
						return $end;
					})->filter()->values();
					$latestEnd = $effectiveEnds->max();
					// Fallback: jika startDate kosong tapi latestEnd ada, set startDate = latestEnd
					if (!$startDate && $latestEnd) { $startDate = $latestEnd; }
					if ($startDate && $latestEnd) {
						$start = Carbon::parse($startDate); $end = Carbon::parse($latestEnd);
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
								$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu','verifikasi','validasi','upload','tte','terbit','diterbitkan')")
								  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
							})
								->orderBy('id_proses_permohonan', 'ASC')
								->get(['jenis_proses_id', 'start_date', 'end_date', 'tgl_signed_report']);
                        $steps = $steps->unique(function ($s) { return implode('|', [(string)$s->jenis_proses_id,(string)($s->start_date ?? ''),(string)($s->end_date ?? '')]); })->values();
						$holidaySet = $holidays; // reuse unified set for window
						$nonSlaIds = [2, 13, 18, 33, 115];
						$dinasIds = [7,108,185,192,212,226,234,293,420];
						$forceDpmIds = [14,403]; // explicitly count as DPMPTSP per request
                        $sumDpm=0; $sumDinas=0; $sumNon=0;
						foreach ($steps as $s) {
							if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date, $invalid)) {
								$s->start_date = $s->end_date;
							}
                            // Jenis 40: gunakan end_date efektif = min(end_date, tgl_signed_report)
                            if ((int)$s->jenis_proses_id === 40 && !empty($s->tgl_signed_report) && !in_array((string)$s->tgl_signed_report,$invalid) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalid)) {
                                try { $ed = Carbon::parse($s->end_date); $sr = Carbon::parse($s->tgl_signed_report); if ($ed->gt($sr)) { $s->end_date = $sr->toDateTimeString(); } } catch (\Throwable $e) {}
                            }
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
							$jp=(int)$s->jenis_proses_id;
							if (in_array($jp, $forceDpmIds, true)) {
								$sumDpm += $val;
							} else {
								$isNon=in_array($jp,$nonSlaIds,true);
								$isDin=in_array($jp,$dinasIds,true);
								$isDpm=(!$isNon&&!$isDin);
								if($isDpm){$sumDpm+=$val;}
								if($isDin){$sumDinas+=$val;}
								if($isNon){$sumNon+=$val;}
							}
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
	 * Clear cache statistik (summary + detail) untuk tahun tertentu, opsional bulan.
	 * Jika bulan tidak diberikan akan membersihkan summary dan semua detail bulan tahun tsb.
	 */
	public function clearStatistikCache(Request $request)
	{
		$year = (int) $request->input('year', Carbon::now()->year);
		$month = $request->input('month');
		$keys = [];
		if ($month) {
			$keys[] = 'sicantik_stat_v4_' . $year . '_' . $month;
			$keys[] = 'sicantik_stat_detail_v2_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
		} else {
			// summary all
			$keys[] = 'sicantik_stat_v4_' . $year . '_all';
			// all month details
			for ($m = 1; $m <= 12; $m++) {
				$keys[] = 'sicantik_stat_detail_v2_' . $year . '_' . str_pad($m, 2, '0', STR_PAD_LEFT);
			}
		}
		foreach ($keys as $k) { Cache::forget($k); }
		return redirect()->back()->with('success', 'Cache statistik untuk tahun ' . $year . ($month ? (' bulan ' . $month) : '') . ' dibersihkan.');
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
		// Tentukan window keseluruhan:
		// - Start: earliest valid start_date untuk langkah jenis_proses_id=2 (Entri Data)
		// - End: latest valid end_date dari SEMUA langkah pada permohonan (agar langkah pasca-40 tetap terhitung)
		$overallStart = Proses::where('no_permohonan',$no_permohonan)
			->whereIn('jenis_proses_id',[2,14])
			->whereNotNull('start_date')
			->whereNotIn('start_date',$invalid)
			->min('start_date');
		$overallEnd = Proses::where('no_permohonan',$no_permohonan)
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
				'durasi_hari' => null,
				'sla_klasifikasi' => null,
				'lama_hari_kerja' => null,
				'lama_jam' => null,
			];
			if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalid)) {
				$s->start_date = $s->end_date; // fallback same-day
				$row['start_date'] = $s->start_date;
			}
			if (empty($s->end_date)) { return $row; }
			if (empty($s->start_date)) { return $row; }
			if (in_array((string)$s->start_date,$invalid) || in_array((string)$s->end_date,$invalid)) { return $row; }
			try { $st = Carbon::parse($s->start_date); $ed = Carbon::parse($s->end_date); } catch(\Throwable $e){ return $row; }
			if ($overallStartCarbon && $st->lt($overallStartCarbon)) { $st = $overallStartCarbon->copy(); }
			if ($overallEndCarbon && $ed->gt($overallEndCarbon)) { $ed = $overallEndCarbon->copy(); }
			$diff = $ed->timestamp - $st->timestamp; if ($diff <= 0) {
				// Non-positive duration: normalize to zeros instead of leaving blanks
				$row['lama_jam'] = 0;
				$row['lama_menit'] = 0;
				$row['durasi_hari'] = 0;
				$row['lama_hari_kerja'] = 0;
				$jp = (int)$s->jenis_proses_id;
				if (in_array($jp,$nonSlaIds,true)) { $row['sla_klasifikasi'] = 'Non-SLA'; }
				elseif (in_array($jp,$dinasIds,true)) { $row['sla_klasifikasi'] = 'Dinas Teknis'; }
				else { $row['sla_klasifikasi'] = 'DPMPTSP'; }
				return $row;
			}
			// Robust duration in minutes to prevent negative values
			$totalMinutes = $ed->greaterThan($st) ? $ed->diffInMinutes($st) : 0;
			$hours = intdiv($totalMinutes, 60);
			$minutes = $totalMinutes % 60;
			$row['lama_jam'] = $hours;
			$row['lama_menit'] = $minutes;
			if ($st->isSameDay($ed) && $diff > 0) {
				$val = 0; // Same-day dianggap 0 hari kerja (logika konsisten)
				$row['durasi_hari'] = 0;
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
				// durasi hari kalender (bukan hari kerja)
				$row['durasi_hari'] = ($ed->greaterThan($st)) ? (int) ceil(($ed->timestamp - $st->timestamp)/86400) : 0;
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
		$judul = 'Statistik Izin SiCantik';
		$year = (int) $request->input('year', \Carbon\Carbon::now()->year);
		$month = (int) $request->input('month', \Carbon\Carbon::now()->month);
		if ($year <= 0) { $year = (int) \Carbon\Carbon::now()->year; }
		if ($month < 1 || $month > 12) { $month = (int) \Carbon\Carbon::now()->month; }

		$invalid = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
		$base = Proses::query()
			->where('jenis_proses_id', 40)
			->whereRaw("LOWER(TRIM(status)) = 'selesai'")
			->whereNotNull('end_date')
			->whereYear('end_date', $year)
			->whereMonth('end_date', $month)
			->get(['permohonan_izin_id','no_permohonan','jenis_izin','end_date','tgl_signed_report','jenis_proses_id']);

		$mapped = $base->map(function($item) use ($invalid) {
			$row = (object) [
				'jenis_izin' => $item->jenis_izin,
				'jumlah_hari_kerja' => 0,
				'sla_dpmptsp' => 0,
				'sla_dinas_teknis' => 0,
				'sla_gabungan' => 0,
			];
			try {
				// Window start: earliest valid start_date of step in (2,14)
				$startDate = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
					->whereIn('jenis_proses_id', [2,14])
					->whereNotNull('start_date')
					->whereNotIn('start_date', $invalid)
					->min('start_date');
				// Window end: latest effective end across all steps (jenis 40 uses min(end_date, tgl_signed_report))
				$stepsForWindow = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
					->where(function($q){ $q->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
					->get(['jenis_proses_id','end_date','tgl_signed_report']);
				$effectiveEnds = $stepsForWindow->map(function($p) use ($invalid){
					$end = null;
					$eValid = !empty($p->end_date) && !in_array((string)$p->end_date, $invalid);
					$sValid = !empty($p->tgl_signed_report) && !in_array((string)$p->tgl_signed_report, $invalid);
					if ((int)$p->jenis_proses_id === 40) {
						if ($eValid && $sValid) {
							try { $end = (\Carbon\Carbon::parse($p->end_date)->lt(\Carbon\Carbon::parse($p->tgl_signed_report))) ? $p->end_date : $p->tgl_signed_report; } catch (\Throwable $ex) { $end = $p->end_date; }
						} elseif ($eValid) { $end = $p->end_date; }
						elseif ($sValid) { $end = $p->tgl_signed_report; }
					} else {
						if ($eValid) { $end = $p->end_date; }
					}
					return $end;
				})->filter()->values();
				$latestEnd = $effectiveEnds->max();
				// Fallback: if no start but has end, set start=end
				if (!$startDate && $latestEnd) { $startDate = $latestEnd; }
				if ($startDate && $latestEnd) {
					$start = \Carbon\Carbon::parse($startDate); $end = \Carbon\Carbon::parse($latestEnd);
					try {
						$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
							->pluck('tanggal')->map(fn($d)=>\Carbon\Carbon::parse($d)->toDateString())->unique()->toArray();
					} catch (\Throwable $e) { $holidays = []; }
					$businessDays = []; $cursor = $start->copy();
					while ($cursor->lte($end)) {
						$isBiz = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
						if ($isBiz) { $businessDays[] = $cursor->toDateString(); }
						$cursor->addDay()->startOfDay();
					}
					$bizCount = count($businessDays); $durHours = $end->diffInHours($start);
					if ($bizCount > 1 && $durHours < ($bizCount * 24)) { $row->jumlah_hari_kerja = $bizCount - 1; }
					elseif ($bizCount == 2 && $durHours < 24) { $row->jumlah_hari_kerja = 1; }
					else { $row->jumlah_hari_kerja = $bizCount; }

					// SLA subtotals per permohonan
					$steps = Proses::where('no_permohonan', $item->no_permohonan)
						->whereNotNull('status')
						->where(function ($q) {
							$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu','verifikasi','validasi','upload','tte','terbit','diterbitkan')")
							  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
						})
						->orderBy('id_proses_permohonan', 'ASC')
						->get(['jenis_proses_id','start_date','end_date','tgl_signed_report']);
					$steps = $steps->unique(function ($s) { return implode('|', [(string)$s->jenis_proses_id,(string)($s->start_date ?? ''),(string)($s->end_date ?? '')]); })->values();
					$nonSlaIds = [2, 13, 18, 33, 115];
					$dinasIds = [7,108,185,192,212,226,234,293,420];
					$forceDpmIds = [14,403];
					$sumDpm=0; $sumDinas=0;
					foreach ($steps as $s) {
						if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date, $invalid)) {
							$s->start_date = $s->end_date; // fallback same-day
						}
                        // Jenis 40: gunakan end_date efektif = min(end_date, tgl_signed_report)
                        if ((int)$s->jenis_proses_id === 40 && !empty($s->tgl_signed_report) && !in_array((string)$s->tgl_signed_report,$invalid) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalid)) {
                            try { $ed = \Carbon\Carbon::parse($s->end_date); $sr = \Carbon\Carbon::parse($s->tgl_signed_report); if ($ed->gt($sr)) { $s->end_date = $sr->toDateTimeString(); } } catch (\Throwable $e) {}
                        }
						if (empty($s->start_date) || empty($s->end_date)) { continue; }
						if (in_array((string)$s->start_date,$invalid) || in_array((string)$s->end_date,$invalid)) { continue; }
						try { $st = \Carbon\Carbon::parse($s->start_date); $ed = \Carbon\Carbon::parse($s->end_date); } catch (\Throwable $e) { continue; }
						if ($st->lt($start)) { $st=$start->copy(); } if ($ed->gt($end)) { $ed=$end->copy(); }
						$diff = $ed->timestamp - $st->timestamp; if ($diff <= 0) { continue; }
						$val = 0;
						if ($st->isSameDay($ed) && $diff > 0) { $val = 0; }
						else {
							$days = []; $cur = $st->copy();
							while ($cur->lte($ed)) {
								$isBiz = $cur->dayOfWeekIso >= 1 && $cur->dayOfWeekIso <= 5 && !in_array($cur->toDateString(), $holidays);
								if ($isBiz) { $days[] = $cur->toDateString(); }
								$cur->addDay()->startOfDay();
							}
							$n = count($days); $hrs = $ed->diffInHours($st);
							if ($n > 1 && $hrs < ($n*24)) { $val = $n - 1; }
							elseif ($n == 2 && $hrs < 24) { $val = 1; }
							else { $val = $n; }
						}
						$jp=(int)$s->jenis_proses_id;
						if (in_array($jp, $forceDpmIds, true)) { $sumDpm += $val; }
						else if (in_array($jp, $nonSlaIds, true)) { /* exclude from SLA subtotals */ }
						else if (in_array($jp, $dinasIds, true)) { $sumDinas += $val; }
						else { $sumDpm += $val; }
					}
					$row->sla_dpmptsp = $sumDpm;
					$row->sla_dinas_teknis = $sumDinas;
					$row->sla_gabungan = $sumDpm + $sumDinas;
				}
			} catch (\Throwable $e) { /* ignore per item */ }
			return $row;
		})->values();

		$grouped = $mapped->groupBy(function($r){ return (string) ($r->jenis_izin ?? ''); });
		$rincianterbit = $grouped->map(function($grp, $jenis){
			$jumlah_izin = $grp->count();
			$jumlah_hari = $grp->sum('jumlah_hari_kerja');
			$jumlah_sla_dpm = $grp->sum('sla_dpmptsp');
			$jumlah_sla_dinas = $grp->sum('sla_dinas_teknis');
			$avg = $jumlah_izin ? round($jumlah_hari / $jumlah_izin, 2) : 0;
			return (object) [
				'jenis_izin' => $jenis,
				'jumlah_izin' => $jumlah_izin,
				'jumlah_hari' => $jumlah_hari,
				'jumlah_sla_dpmptsp' => $jumlah_sla_dpm,
				'jumlah_sla_dinas_teknis' => $jumlah_sla_dinas,
				'jumlah_sla_gabungan' => $jumlah_sla_dpm + $jumlah_sla_dinas,
				'rata_rata_jumlah_hari' => $avg,
			];
		})->sortByDesc('jumlah_izin')->values();

		$totalJumlahHari = (int) $rincianterbit->sum('jumlah_hari');
		$total_izin = (int) $rincianterbit->sum('jumlah_izin');
		$rataRataJumlahHari = $total_izin ? round($totalJumlahHari / $total_izin, 2) : 0;
		$totalSlaDpm = (int) $rincianterbit->sum('jumlah_sla_dpmptsp');
		$totalSlaDinas = (int) $rincianterbit->sum('jumlah_sla_dinas_teknis');
		$totalSlaGab = (int) $rincianterbit->sum('jumlah_sla_gabungan');

		$rataRataJumlahHariPerJenisIzin = $rincianterbit; // already contains per-jenis metrics

		return view(
			'admin.nonberusaha.sicantik.rincian',
			compact('judul', 'month', 'year', 'rataRataJumlahHariPerJenisIzin', 'rataRataJumlahHari', 'total_izin', 'totalJumlahHari', 'totalSlaDpm', 'totalSlaDinas', 'totalSlaGab')
		);
	}

	public function printRincian(Request $request)
	{
		$judul = 'Rincian Izin SiCantik';
		$year = (int) $request->input('year', \Carbon\Carbon::now()->year);
		$month = (int) $request->input('month', \Carbon\Carbon::now()->month);
		if ($year <= 0) { $year = (int) \Carbon\Carbon::now()->year; }
		if ($month < 1 || $month > 12) { $month = (int) \Carbon\Carbon::now()->month; }

		$invalid = ['0001-01-01 00:00:00', '0001-01-01 00:00:00.000'];
		$base = Proses::query()
			->where('jenis_proses_id', 40)
			->whereRaw("LOWER(TRIM(status)) = 'selesai'")
			->whereNotNull('end_date')
			->whereYear('end_date', $year)
			->whereMonth('end_date', $month)
			->get(['permohonan_izin_id','no_permohonan','jenis_izin','end_date','tgl_signed_report','jenis_proses_id']);

		$mapped = $base->map(function($item) use ($invalid) {
			$row = (object) [
				'jenis_izin' => $item->jenis_izin,
				'jumlah_hari_kerja' => 0,
				'sla_dpmptsp' => 0,
				'sla_dinas_teknis' => 0,
				'sla_gabungan' => 0,
			];
			try {
				$startDate = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
					->whereIn('jenis_proses_id', [2,14])
					->whereNotNull('start_date')
					->whereNotIn('start_date', $invalid)
					->min('start_date');
				$stepsForWindow = Proses::where('permohonan_izin_id', $item->permohonan_izin_id)
					->where(function($q){ $q->whereNotNull('end_date')->orWhereNotNull('tgl_signed_report'); })
					->get(['jenis_proses_id','end_date','tgl_signed_report']);
				$effectiveEnds = $stepsForWindow->map(function($p) use ($invalid){
					$end = null;
					$eValid = !empty($p->end_date) && !in_array((string)$p->end_date, $invalid);
					$sValid = !empty($p->tgl_signed_report) && !in_array((string)$p->tgl_signed_report, $invalid);
					if ((int)$p->jenis_proses_id === 40) {
						if ($eValid && $sValid) {
							try { $end = (\Carbon\Carbon::parse($p->end_date)->lt(\Carbon\Carbon::parse($p->tgl_signed_report))) ? $p->end_date : $p->tgl_signed_report; } catch (\Throwable $ex) { $end = $p->end_date; }
						} elseif ($eValid) { $end = $p->end_date; }
						elseif ($sValid) { $end = $p->tgl_signed_report; }
					} else {
						if ($eValid) { $end = $p->end_date; }
					}
					return $end;
				})->filter()->values();
				$latestEnd = $effectiveEnds->max();
				if (!$startDate && $latestEnd) { $startDate = $latestEnd; }
				if ($startDate && $latestEnd) {
					$start = \Carbon\Carbon::parse($startDate); $end = \Carbon\Carbon::parse($latestEnd);
					try {
						$holidays = Dayoff::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
							->pluck('tanggal')->map(fn($d)=>\Carbon\Carbon::parse($d)->toDateString())->unique()->toArray();
					} catch (\Throwable $e) { $holidays = []; }
					$businessDays = []; $cursor = $start->copy();
					while ($cursor->lte($end)) {
						$isBiz = $cursor->dayOfWeekIso >= 1 && $cursor->dayOfWeekIso <= 5 && !in_array($cursor->toDateString(), $holidays);
						if ($isBiz) { $businessDays[] = $cursor->toDateString(); }
						$cursor->addDay()->startOfDay();
					}
					$bizCount = count($businessDays); $durHours = $end->diffInHours($start);
					if ($bizCount > 1 && $durHours < ($bizCount * 24)) { $row->jumlah_hari_kerja = $bizCount - 1; }
					elseif ($bizCount == 2 && $durHours < 24) { $row->jumlah_hari_kerja = 1; }
					else { $row->jumlah_hari_kerja = $bizCount; }

					$steps = Proses::where('no_permohonan', $item->no_permohonan)
						->whereNotNull('status')
						->where(function ($q) {
							$q->whereRaw("LOWER(TRIM(status)) IN ('proses','selesai','menunggu','verifikasi','validasi','upload','tte','terbit','diterbitkan')")
							  ->orWhereRaw("LOWER(status) LIKE '%nunggu%'");
						})
						->orderBy('id_proses_permohonan', 'ASC')
							->get(['jenis_proses_id','start_date','end_date','tgl_signed_report']);
					$steps = $steps->unique(function ($s) { return implode('|', [(string)$s->jenis_proses_id,(string)($s->start_date ?? ''),(string)($s->end_date ?? '')]); })->values();
					$nonSlaIds = [2, 13, 18, 33, 115]; $dinasIds = [7,108,185,192,212,226,234,293,420]; $forceDpmIds = [14,403];
					$sumDpm=0; $sumDinas=0;
					foreach ($steps as $s) {
						if (empty($s->start_date) && !empty($s->end_date) && !in_array((string)$s->end_date, $invalid)) { $s->start_date = $s->end_date; }
						// Jenis 40: gunakan end_date efektif = min(end_date, tgl_signed_report)
						if ((int)$s->jenis_proses_id === 40 && !empty($s->tgl_signed_report) && !in_array((string)$s->tgl_signed_report,$invalid) && !empty($s->end_date) && !in_array((string)$s->end_date,$invalid)) {
							try { $ed = \Carbon\Carbon::parse($s->end_date); $sr = \Carbon\Carbon::parse($s->tgl_signed_report); if ($ed->gt($sr)) { $s->end_date = $sr->toDateTimeString(); } } catch (\Throwable $e) {}
						}
						if (empty($s->start_date) || empty($s->end_date)) { continue; }
						if (in_array((string)$s->start_date,$invalid) || in_array((string)$s->end_date,$invalid)) { continue; }
						try { $st = \Carbon\Carbon::parse($s->start_date); $ed = \Carbon\Carbon::parse($s->end_date); } catch (\Throwable $e) { continue; }
						if ($st->lt($start)) { $st=$start->copy(); } if ($ed->gt($end)) { $ed=$end->copy(); }
						$diff = $ed->timestamp - $st->timestamp; if ($diff <= 0) { continue; }
						$val = 0;
						if ($st->isSameDay($ed) && $diff > 0) { $val = 0; }
						else {
							$days = []; $cur = $st->copy();
							while ($cur->lte($ed)) {
								$isBiz = $cur->dayOfWeekIso >= 1 && $cur->dayOfWeekIso <= 5 && !in_array($cur->toDateString(), $holidays);
								if ($isBiz) { $days[] = $cur->toDateString(); }
								$cur->addDay()->startOfDay();
							}
							$n = count($days); $hrs = $ed->diffInHours($st);
							if ($n > 1 && $hrs < ($n*24)) { $val = $n - 1; }
							elseif ($n == 2 && $hrs < 24) { $val = 1; }
							else { $val = $n; }
						}
						$jp=(int)$s->jenis_proses_id;
						if (in_array($jp, $forceDpmIds, true)) { $sumDpm += $val; }
						else if (in_array($jp, $nonSlaIds, true)) { /* exclude */ }
						else if (in_array($jp, $dinasIds, true)) { $sumDinas += $val; }
						else { $sumDpm += $val; }
					}
					$row->sla_dpmptsp = $sumDpm; $row->sla_dinas_teknis = $sumDinas; $row->sla_gabungan = $sumDpm + $sumDinas;
				}
			} catch (\Throwable $e) { /* ignore per item */ }
			return $row;
		})->values();

		$grouped = $mapped->groupBy(function($r){ return (string) ($r->jenis_izin ?? ''); });
		$rincianterbit = $grouped->map(function($grp, $jenis){
			$jumlah_izin = $grp->count();
			$jumlah_hari = $grp->sum('jumlah_hari_kerja');
			$jumlah_sla_dpm = $grp->sum('sla_dpmptsp');
			$jumlah_sla_dinas = $grp->sum('sla_dinas_teknis');
			$avg = $jumlah_izin ? round($jumlah_hari / $jumlah_izin, 2) : 0;
			return (object) [
				'jenis_izin' => $jenis,
				'jumlah_izin' => $jumlah_izin,
				'jumlah_hari' => $jumlah_hari,
				'jumlah_sla_dpmptsp' => $jumlah_sla_dpm,
				'jumlah_sla_dinas_teknis' => $jumlah_sla_dinas,
				'jumlah_sla_gabungan' => $jumlah_sla_dpm + $jumlah_sla_dinas,
				'rata_rata_jumlah_hari' => $avg,
			];
		})->sortByDesc('jumlah_izin')->values();

		$totalJumlahHari = (int) $rincianterbit->sum('jumlah_hari');
		$total_izin = (int) $rincianterbit->sum('jumlah_izin');
		$rataRataJumlahHari = $total_izin ? round($totalJumlahHari / $total_izin, 2) : 0;
		$totalSlaDpm = (int) $rincianterbit->sum('jumlah_sla_dpmptsp');
		$totalSlaDinas = (int) $rincianterbit->sum('jumlah_sla_dinas_teknis');
		$totalSlaGab = (int) $rincianterbit->sum('jumlah_sla_gabungan');

		$rataRataJumlahHariPerJenisIzin = $rincianterbit;

		return Pdf::loadView('admin.nonberusaha.sicantik.print.rincian', compact(
			'judul','year','month','rataRataJumlahHariPerJenisIzin','rataRataJumlahHari','total_izin','totalJumlahHari','totalSlaDpm','totalSlaDinas','totalSlaGab'
		))
		->setPaper('A4','landscape')
		->stream('sicantik-rincian-'.str_pad((string)$year,4,'0',STR_PAD_LEFT).'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT).'.pdf');
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
