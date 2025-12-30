<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicViewHomeController extends Controller
{
    public function index()
    {
        $judul = 'Data Hub DPMPTSP';
        $investasiRows = [
            [
                'label' => 'Jumlah LKPM Non UMK',
                'jumlah' => \App\Models\LkpmNonUmk::count(),
                'updated' => optional(\App\Models\LkpmNonUmk::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah LKPM UMK',
                'jumlah' => \App\Models\LkpmUmk::count(),
                'updated' => optional(\App\Models\LkpmUmk::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah Berusaha',
                'jumlah' => \App\Models\Berusaha::count(),
                'updated' => optional(\App\Models\Berusaha::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah Bimtek',
                'jumlah' => \App\Models\Bimtek::count(),
                'updated' => optional(\App\Models\Bimtek::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah Fasilitasi',
                'jumlah' => \App\Models\Fasilitasi::count(),
                'updated' => optional(\App\Models\Fasilitasi::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah Proyek',
                'jumlah' => \App\Models\Proyek::count(),
                'updated' => optional(\App\Models\Proyek::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'Jumlah Izin (OSS)',
                'jumlah' => \App\Models\Izin::count(),
                'updated' => optional(\App\Models\Izin::latest('updated_at')->first())->updated_at,
            ],
        ];
        $nonBerusahaRows = [
            [
                'label' => 'MPPD',
                'jumlah' => \App\Models\Mppd::count(),
                'updated' => optional(\App\Models\Mppd::latest('updated_at')->first())->updated_at,
            ],
            [
                'label' => 'SiCantik',
                'jumlah' => class_exists('App\\Models\\Mediapengaduan') ? \App\Models\Mediapengaduan::count() : 0,
                'updated' => class_exists('App\\Models\\Mediapengaduan') ? optional(\App\Models\Mediapengaduan::latest('updated_at')->first())->updated_at : null,
            ],
            [
                'label' => 'Simpel',
                'jumlah' => class_exists('App\\Models\\Simpel') ? \App\Models\Simpel::count() : 0,
                'updated' => class_exists('App\\Models\\Simpel') ? optional(\App\Models\Simpel::latest('updated_at')->first())->updated_at : null,
            ],
            [
                'label' => 'PBG',
                'jumlah' => class_exists('App\\Models\\Pbg') ? \App\Models\Pbg::count() : 0,
                'updated' => class_exists('App\\Models\\Pbg') ? optional(\App\Models\Pbg::latest('updated_at')->first())->updated_at : null,
            ],
        ];
        return view('publicviews.home.index', compact('judul', 'investasiRows', 'nonBerusahaRows'));
    }
}
