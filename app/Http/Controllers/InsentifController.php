<?php

namespace App\Http\Controllers;

use App\Models\Insentif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class InsentifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = 'Data Insentif';
		$query = Insentif::query()->where('del', 0);
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('penerima', 'LIKE', "%{$search}%")
				   ->orWhere('nama_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('alamat_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('alamat_penerima', 'LIKE', "%{$search}%")
				  
				   ->orderBy('tanggal_permohonan', 'desc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/insentif')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->whereBetween('tanggal_permohonan', [$date_start,$date_end])
				   ->orderBy('tanggal_permohonan', 'desc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/insentif')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/insentif')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/insentif')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->whereMonth('tanggal_permohonan', [$month])
				   ->whereYear('tanggal_permohonan', [$year])
				   ->orderBy('tanggal_permohonan', 'desc');
				}
		}
		if ($request->has('year')) {
			$year = $request->input('year');
			$query ->whereYear('tanggal_permohonan', [$year])
				   ->orderBy('tanggal_permohonan', 'desc');
		}
		$perPage = $request->input('perPage', 50);
		$items=$query->orderBy('tanggal_permohonan', 'desc')->paginate($perPage);
		$items->withPath(url('/insentif'));
		return view('admin.insentif.permohonan.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Data Insentif';
        $nama=auth()->user()->pegawai->nama;
        
        return view('admin.insentif.permohonan.create',compact('judul','nama'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tahun_pemberian' => 'required',
            'penerima' => 'required',
            'slug' => 'required|unique:insentif', 
            'jenis_perusahaan' => 'required', 
            'no_sk' => 'nullable',
            'no_rekomendasi' => 'nullable',
            'bentuk_pemberian' => 'required',
            'pemberian_insentif' => 'required',
            'persentase_insentif' => 'required',
            'file' => 'file|mimes:pdf|nullable',
            'tanggal_permohonan'=>'date|nullable',
            'tanggal_sk'=>'date|nullable',
            'alamat_penerima'=>'nullable',
            'nama_perusahaan'=>'nullable',
            'alamat_perusahaan'=>'nullable',
            'file_sk'=>'file|mimes:pdf|nullable',
        ]);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/insentif-files');
        }
        if ($request->file('file_sk')) {
            $validatedData['file_sk'] = $request->file('file_sk')->store('public/insentif-files');
        }
        
        Insentif::create($validatedData);
        return redirect('/insentif')->with('success', 'Data Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Insentif $insentif)
    {
       
        if ($insentif) {
            return response()->json($insentif);
        }

        return response()->json(['message' => 'Data not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insentif $insentif)
    {
        $judul = 'Edit Data Insentif';
        return view('admin.insentif.permohonan.edit', compact('judul', 'insentif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insentif $insentif)
    {
        $rules = [
            'tahun_pemberian' => 'required',
            'penerima' => 'required',
            'jenis_perusahaan' => 'required', 
            'no_sk' => 'nullable',
            'no_rekomendasi' => 'nullable',
            'bentuk_pemberian' => 'required',
            'pemberian_insentif' => 'required',
            'persentase_insentif' => 'required',
            'file' => 'file|mimes:pdf|nullable',
            'tanggal_permohonan'=>'date|nullable',
            'tanggal_sk'=>'date|nullable',
            'alamat_penerima'=>'nullable',
            'nama_perusahaan'=>'nullable',
            'alamat_perusahaan'=>'nullable',
            'file_sk'=>'file|mimes:pdf|nullable', 
        ];
        
        if ($request->slug != $insentif->slug) {
            $rules['slug'] = 'required|unique:insentif';
        }
        $validatedData = $request->validate($rules);
        $validatedData['del'] = 0;
        if ($request->file('file')) {
            if ($request->oldFilePermohonan) {
                Storage::delete($request->oldFilePermohonan);
            }
            $validatedData['file'] = $request->file('file')->store('public/insentif-files');
        }
        if ($request->file('file_sk')) {
            if ($request->oldFileSk) {
                Storage::delete($request->oldFileSk);
            }
            $validatedData['file_sk'] = $request->file('file_sk')->store('public/insentif-files');
        }
        Insentif::where('id', $insentif->id)->update($validatedData);
        return redirect('/insentif')->with('success', 'Data  Berhasil di Perbaharui !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insentif $insentif)
    {
        $validatedData['del'] = 1;
        
        Insentif::where('id', $insentif->id)->update($validatedData);
         return redirect('/insentif')->with('success', 'Data  Berhasil di Hapus !');
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Insentif::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
