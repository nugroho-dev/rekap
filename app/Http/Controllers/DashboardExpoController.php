<?php

namespace App\Http\Controllers;

use App\Models\Expo;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class DashboardExpoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Pameran';
		$query = Expo::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama_expo', 'LIKE', "%{$search}%")
				   ->orWhere('tempat', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_mulai', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/expo')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal_mulai', [$date_start,$date_end])
				   ->orderBy('tanggal_mulai', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/expo')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/expo')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/expo')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal_mulai', [$month])
				   ->whereYear('tanggal_mulai', [$year])
				   ->orderBy('tanggal_mulai', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_mulai', [$year])
				   ->orderBy('tanggal_mulai', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_mulai', 'asc')->paginate($perPage);
		$items->withPath(url('/expo'));
		return view('admin.promosi.pameran.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Pameran';
        return view('admin.promosi.pameran.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required', 
            'nama_expo' => 'required', 
            'slug' => 'required|unique:loi', 
            'tempat' => 'required',
            'file' => 'nullable|file|mimes:pdf']);
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/expo-files');
        }
        $validatedData['del'] = 0;
        
        Expo::create($validatedData);
        return redirect('/expo')->with('success', 'Data Pameran Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expo $expo)
    {
        if ($expo) {
            return response()->json($expo);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expo $expo)
    {
        $judul = 'Edit Data Pameran';
        return view('admin.promosi.pameran.edit', compact('judul','expo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expo $expo)
    {
        $rules=[
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required', 
            'nama_expo' => 'required',
            'tempat' => 'required',
            'file' => 'nullable|file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            if ($request->slug != $expo->slug) {
                $rules['slug'] = 'required|unique:expo';
            }
            if ($request->file('file')) {
                if ($request->oldFile) {
                    Storage::delete($request->oldFile);
                }
                $validatedData['file'] = $request->file('file')->store('public/expo-files');
            }
            $validatedData['del'] = 0;
            Expo::where('id', $expo->id)->update($validatedData);
            return redirect('/expo/'.$expo->slug.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expo $expo)
    {
        $expo->delete();
        return redirect('/expo')->with('success', 'Pameran Berhasil di hapus!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Expo::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    /**
     * Statistik Pameran
     */
    public function statistik(Request $request)
    {
        $judul = 'Statistik Pameran';
        $year = $request->input('year', date('Y'));
        $years = Expo::selectRaw('YEAR(tanggal_mulai) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

        // Trend per bulan
        $trend = [];
        for ($m = 1; $m <= 12; $m++) {
            $trend[$m] = Expo::whereYear('tanggal_mulai', $year)
                ->whereMonth('tanggal_mulai', $m)
                ->count();
        }

        // Total
        $total = Expo::whereYear('tanggal_mulai', $year)->count();

        // Rekap per tempat
        $tempatCounts = Expo::select('tempat')
            ->whereYear('tanggal_mulai', $year)
            ->groupBy('tempat')
            ->selectRaw('tempat, COUNT(*) as jumlah')
            ->get()->pluck('jumlah', 'tempat');

        // Rekap per nama expo
        $namaCounts = Expo::select('nama_expo')
            ->whereYear('tanggal_mulai', $year)
            ->groupBy('nama_expo')
            ->selectRaw('nama_expo, COUNT(*) as jumlah')
            ->get()->pluck('jumlah', 'nama_expo');

        return view('admin.promosi.pameran.statistik', compact(
            'judul', 'year', 'years', 'trend', 'total', 'tempatCounts', 'namaCounts'
        ));
    }
}
