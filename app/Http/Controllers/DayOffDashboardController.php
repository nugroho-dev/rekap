<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Dayoff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DayOffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Data Hari Libur Nasional';

        $search = $request->query('search', null);
        $date_start = $request->query('date_start', null);
        $date_end = $request->query('date_end', null);
        $month = $request->query('month', null);
        $year = $request->query('year', null);
        $perPage = (int) $request->query('perPage', 50);
        $perPage = $perPage > 0 ? $perPage : 50;

        $query = Dayoff::query();

        if (!empty($search)) {
            $query->where('keterangan', 'LIKE', "%{$search}%");
        }

        if (!empty($date_start) && !empty($date_end)) {
            if ($date_start > $date_end) {
                return redirect('/dayoff')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
            }
            $query->whereBetween('tanggal', [$date_start, $date_end]);
        }

        if (!empty($month) && !empty($year)) {
            $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        } elseif (!empty($year)) {
            $query->whereYear('tanggal', $year);
        }

        $items = $query->orderBy('tanggal', 'desc')
                       ->paginate($perPage)
                       ->appends($request->except('page'));

        return view('admin.konfigurasi.dayoff.index', compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    public function handle(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $response = Http::retry(3, 1000)->timeout(10)->get('https://dayoffapi.vercel.app/api', ['year' => $year]);

        if (! $response->ok()) {
            return redirect('/dayoff')->with('error', 'Gagal mengambil data hari libur dari API.');
        }

        $data = $response->json();
        if (! is_array($data) || empty($data)) {
            return redirect('/dayoff')->with('error', 'Data API kosong.');
        }

        DB::transaction(function () use ($data) {

            // Option A — simple: updateOrCreate (one query per row)
            foreach ($data as $val) {
                if (empty($val['tanggal'])) continue;
                try {
                    $dt = Carbon::parse($val['tanggal'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    continue;
                }

                Dayoff::updateOrCreate(
                    ['tanggal' => $dt],
                    [
                        'keterangan' => isset($val['keterangan']) ? trim($val['keterangan']) : null,
                        'is_cuti'    => isset($val['is_cuti']) ? (int) $val['is_cuti'] : 0,
                    ]
                );
            }

            // Option B — more efficient: batch check then insert/create only when needed
            // (uncomment and use instead of Option A for fewer writes)
            /*
            $dates = array_values(array_filter(array_map(fn($v) => $v['tanggal'] ?? null, $data)));
            $existing = Dayoff::whereIn('tanggal', $dates)->get()->keyBy('tanggal');

            $toInsert = [];
            foreach ($data as $val) {
                if (empty($val['tanggal'])) continue;
                try {
                    $dt = Carbon::parse($val['tanggal'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    continue;
                }

                $keterangan = isset($val['keterangan']) ? trim($val['keterangan']) : null;
                $is_cuti = isset($val['is_cuti']) ? (int) $val['is_cuti'] : 0;

                if (! isset($existing[$dt])) {
                    // not exists -> collect for bulk insert
                    $toInsert[] = [
                        'tanggal' => $dt,
                        'keterangan' => $keterangan,
                        'is_cuti' => $is_cuti,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    // exists -> update only if changed
                    $old = $existing[$dt];
                    if ($old->keterangan !== $keterangan || (int)$old->is_cuti !== $is_cuti) {
                        $old->keterangan = $keterangan;
                        $old->is_cuti = $is_cuti;
                        $old->save();
                    }
                }
            }

            if (! empty($toInsert)) {
                Dayoff::insert($toInsert);
            }
            */
        });

        return redirect('/dayoff')->with('success', 'Hari libur berhasil disinkronkan.');
    }
}
