<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Izin;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ProyekListExport;
use App\Exports\ProyekIzinQueryExport;

class ProyekIzinController extends Controller
{
    public function export(Request $request)
    {
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $year = $request->input('year');
        $search = trim((string) $request->input('search'));

        $export = new ProyekIzinQueryExport($date_start, $date_end, $year, $search);
        $filename = 'gabungan_proyek_izin_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download($export, $filename);
    }
}
