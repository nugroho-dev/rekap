<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $judul = 'Maintenance';
        return view('admin.maintenance.index', compact('judul'));
    }
}

