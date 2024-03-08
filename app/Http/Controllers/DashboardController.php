<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $judul='Data Hub DASHBOARD';
        return view('admin.dashboard.index', compact('judul'));
    }
}
