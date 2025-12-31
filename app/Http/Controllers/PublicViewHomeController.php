<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicViewHomeController extends Controller
{
    public function index()
    {
        $judul = 'Data Hub DPMPTSP';
        $kategori = \App\Models\KategoriInformasi::with(['jenisInformasi' => function($q){ $q->orderBy('urutan'); }])->orderBy('urutan')->get();

        // Set jumlah for each jenisInformasi using model name from DB
        foreach ($kategori as $kat) {
            foreach ($kat->jenisInformasi as $jenis) {
                if ($jenis->model && class_exists($jenis->model)) {
                    $modelClass = $jenis->model;
                    $jenis->jumlah = app($modelClass)::count();
                    // Ambil updated_at terbaru dari model terkait
                    $latest = app($modelClass)::orderByDesc('updated_at')->first();
                    $jenis->updated_model_at = $latest ? $latest->updated_at : null;
                } else {
                    $jenis->jumlah = 0;
                    $jenis->updated_model_at = null;
                }
            }
        }

        return view('publicviews.home.index', compact('judul', 'kategori'));
    }
}
