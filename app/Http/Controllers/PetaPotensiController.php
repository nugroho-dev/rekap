<?php

namespace App\Http\Controllers;

use App\Models\Potensi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class PetaPotensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Peta Potensi';
		$query = Potensi::query()->where('del', 0);
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('judul', 'LIKE', "%{$search}%")
				   ->orWhere('tahun', 'LIKE', "%{$search}%")
				   ->orWhere('desc', 'LIKE', "%{$search}%")
				   ->orderBy('created_at', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/potensi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('created_at', [$date_start,$date_end])
				   ->orderBy('created_at', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/potensi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/potensi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/potensi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('created_at', [$month])
				   ->whereYear('created_at', [$year])
				   ->orderBy('created_at', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('created_at', [$year])
				   ->orderBy('created_at', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('created_at', 'desc')->paginate($perPage);
		$items->withPath(url('/potensi'));
        return view('admin.petapotensi.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Tambah Data Peta Potensi';
        return view('admin.petapotensi.create',compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required',
            'slug' => 'required|unique:potensi', 
            'tahun' => 'required', 
            'desc' => 'nullable',
            'file' => 'file|mimes:pdf|required'
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/potensi-files');
        }
        
        
        Potensi::create($validatedData);
        return redirect('/potensi')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Potensi $potensi)
    {
        if ($potensi) {
            return response()->json($potensi);
        }

        return response()->json(['message' => 'Data not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Potensi $potensi)
    {
        $judul = 'Edit Peta Potensi';
        return view('admin.petapotensi..edit', compact('judul', 'potensi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Potensi $potensi)
    {
        $rules = [
            'judul' => 'required',
            'tahun' => 'required', 
            'desc' => 'nullable',
            'file' => 'file|mimes:pdf'
        ];
        
        if ($request->slug != $potensi->slug) {
            $rules['slug'] = 'required|unique:potensi';
        }
        $validatedData = $request->validate($rules);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            if ($request->oldFile) {
                Storage::delete($request->oldFile);
            }
            $validatedData['file'] = $request->file('file')->store('public/potensi-files');
        }
       
        Potensi::where('id', $potensi->id)->update($validatedData);
        return redirect('/potensi')->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Potensi $potensi)
    {
        $validatedData['del'] = 1;
        
        Potensi::where('id', $potensi->id)->update($validatedData);
         return redirect('/potensi')->with('success', 'Data  Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Potensi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
