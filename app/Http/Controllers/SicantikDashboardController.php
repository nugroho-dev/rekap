<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SicantikDashboardController extends Controller
{
    public function index()
    {
        $judul='Sicantik Data Dashboard';
        return view('admin.nonberusaha.sicantik.index', compact('judul'));
    }
}
