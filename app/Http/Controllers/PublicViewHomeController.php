<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicViewHomeController extends Controller
{
    public function index()
    {
        $judul = 'Maintenance';
        return view('publicviews.home.index', compact('judul'));
    }
}
