<?php

namespace App\Http\Controllers;

use App\Models\Loi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class DashboardLoiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul='Data Latter Of Intent (Pernyataan Kepeminatan)';
		$query = Loi::query();
		$search = $request->input('search');
		$date_start = $request->input('date_start');
		$date_end = $request->input('date_end');
		$month = $request->input('month');
		$year = $request->input('year');
		if ($request->has('search')) {
			$search = $request->input('search');
			$query ->where('del', 0)
				   ->where('nama_perusahaan', 'LIKE', "%{$search}%")
				   ->orWhere('alamat', 'LIKE', "%{$search}%")
				   ->orWhere('bidang_usaha', 'LIKE', "%{$search}%")
                   ->orWhere('nama', 'LIKE', "%{$search}%")
                   ->orWhere('lokasi', 'LIKE', "%{$search}%")
				   ->orderBy('tanggal', 'asc');
		}
		if ($request->has('date_start')&&$request->has('date_end')) {
			$date_start = $request->input('date_start');
			$date_end = $request->input('date_end');
			if($date_start>$date_end ){
				return redirect('/loi')->with('error', 'Silakan Cek Kembali Pilihan Range Tanggal Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereBetween('tanggal', [$date_start,$date_end])
				   ->orderBy('tanggal', 'asc');
			}
		}
		if ($request->has('month')&&$request->has('year')) {
			$month = $request->input('month');
			$year = $request->input('year');
			if(empty($month)&&empty($year)){
				return redirect('/loi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($year)){
				return redirect('/loi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}if(empty($month)){
				return redirect('/loi')->with('error', 'Silakan Cek Kembali Pilihan Bulan dan Tahun Anda ');
			}else{
			$query ->where('del', 0)
				   ->whereMonth('tanggal', [$month])
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
		$items=$query->orderBy('tanggal', 'asc')->paginate($perPage);
		$items->withPath(url('/loi'));
		return view('admin.promosi.Loi.index',compact('judul','items','perPage','search','date_start','date_end','month','year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $judul = 'Buat Data LOI';
        return view('admin.promosi.loi.create', compact('judul'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'nama_perusahaan' => 'required', 
            'slug' => 'required|unique:loi', 
            'alamat' => 'required', 
            'bidang_usaha' => 'required', 
            'negara' => 'nullable', 
            'nama' => 'required', 
            'jabatan' => 'required',
            'telp' => 'required|numeric',
            'peminatan_bidang_usaha' => 'required',
            'lokasi' => 'required',
            'status_investasi' => 'required',
            'nilai_investasi_dolar' => 'nullable|numeric',
            'nilai_investasi_rupiah' => 'nullable|numeric',
            'tki' => 'nullable|numeric',
            'tka' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'file' => 'file|mimes:pdf']);
        if ($request->file('file')) {
            $validatedData['file'] = $request->file('file')->store('public/loi-files');
        }
        $validatedData['del'] = 0;
        
        Loi::create($validatedData);
        return redirect('/loi')->with('success', 'Data LOI Baru Berhasil di Tambahkan !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loi $loi)
    {
        if ($loi) {
            return response()->json($loi);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loi $loi)
    {
        $judul = 'Edit Data Latter Of Intent (Pernyataan Kepeminatan)';
        return view('admin.promosi.loi.edit', compact('judul','loi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loi $loi)
    {
        $rules=[
            'tanggal' => 'required',
            'nama_perusahaan' => 'required', 
            'alamat' => 'required', 
            'bidang_usaha' => 'required', 
            'negara' => 'nullable', 
            'nama' => 'required', 
            'jabatan' => 'required',
            'telp' => 'required|numeric',
            'peminatan_bidang_usaha' => 'required',
            'lokasi' => 'required',
            'status_investasi' => 'required',
            'nilai_investasi_dolar' => 'nullable|numeric',
            'nilai_investasi_rupiah' => 'nullable|numeric',
            'tki' => 'nullable|numeric',
            'tka' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'file' => 'file|mimes:pdf'];
            $validatedData = $request->validate($rules);
            if ($request->slug != $loi->slug) {
                $rules['slug'] = 'required|unique:loi';
            }
            if ($request->file('file')) {
                if ($request->oldFile) {
                    Storage::delete($request->oldFile);
                }
                $validatedData['file'] = $request->file('file')->store('public/loi-files');
            }
            $validatedData['del'] = 0;
            Loi::where('id', $loi->id)->update($validatedData);
            return redirect('/loi/'.$loi->slug.'/edit')->with('success', 'Berhasil di Ubah !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loi $loi)
    {
        //
    }
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Loi::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
