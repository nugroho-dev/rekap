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
			$query ->where('del', 0)
				   ->where('nama_expo', 'LIKE', "%{$search}%")
				   ->orWhere('tempat', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal_mulai', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/expo')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('tanggal_mulai', [$date_start,$date_end])
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
			$query ->where('del', 0)
				   ->whereMonth('tanggal_mulai', [$month])
				   ->whereYear('tanggal_mulai', [$year])
				   ->orderBy('tanggal_mulai', 'asc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->where('del', 0)
				   ->whereYear('tanggal_mulai', [$year])
				   ->orderBy('tanggal_mulai', 'asc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->where('del', 0)->orderBy('tanggal_mulai', 'asc')->paginate($perPage);
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
        $validatedData['del'] = 1;
        Expo::where('id', $expo->id)->update($validatedData);
        return redirect('/expo')->with('success', 'Pameran Berhasil di hapus!');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Expo::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
