<?php

namespace App\Http\Controllers;

use App\Models\Bisnis;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class DashboardBusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Business Meeting';
		$query = Bisnis::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('nama_bisnis', 'LIKE', "%{$search}%")
				   ->orWhere('tempat', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/business')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/business')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/business')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/business')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal', [$month])
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('tanggal', [$year])
				   ->orderBy('tanggal', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->where('del', 0)->orderBy('tanggal', 'asc')->paginate($perPage);
		$items->withPath(url('/business'));
		return view('admin.promosi.business.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data Business Meeting';
        return view('admin.promosi.business.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'nama_bisnis' => 'required', 
            'slug' => 'required|unique:loi', 
            'tempat' => 'required',
            'file' => 'nullable|file|mimes:pdf']);
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/bisnis-files');
        }
        $validatedData['del'] = 0;
        
        Bisnis::create($validatedData);
        return redirect('/business')->with('success', 'Data Pameran Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bisnis $business)
    {
        if ($business) {
            return response()->json($business);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bisnis $business)
    {
        $judul = 'Edit Business Meeting';
        return view('admin.promosi.business.edit', compact('judul','business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bisnis $business)
    {
        $rules=[
            'tanggal' => 'required',
            'nama_bisnis' => 'required', 
            'tempat' => 'required',
            'file' => 'nullable|file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            if ($request->slug != $business->slug) {
                $rules['slug'] = 'required|unique:bisnis';
            }
            if ($request->file('file')) {
                if ($request->oldFile) {
                    Storage::delete($request->oldFile);
                }
                $validatedData['file'] = $request->file('file')->store('public/bisnis-files');
            }
            $validatedData['del'] = 0;
            Bisnis::where('id', $business->id)->update($validatedData);
            return redirect('/business/'.$business->slug.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bisnis $business)
    {
         $business->delete();
        return redirect('/business')->with('success', 'Business Meeting Berhasil di hapus!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Bisnis::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    public function statistik(Request $request)
    {
        $judul = 'Statistik Business Meeting';
        $years = Bisnis::selectRaw('YEAR(tanggal) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');
        $year = $request->input('year', $years->first() ?? date('Y'));

        // Total business meeting
        $total = Bisnis::whereYear('tanggal', $year)->count();

       

        // Tren bulanan (jumlah business meeting per bulan di tahun berjalan)
        $trend = Bisnis::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->whereYear('tanggal', $year)
            ->groupByRaw('MONTH(tanggal)')
            ->orderBy('bulan')
            ->pluck('jumlah', 'bulan');

        return view('admin.promosi.business.statistik', compact(
            'judul',
            'total',
           
            'trend',
            'year',
            'years'
        ));
    }
}
