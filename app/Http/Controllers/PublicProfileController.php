<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function index()
    {
        $judul = 'My Profil';
        return view('publicviews.setting.index', compact('judul'));
    }
    public function token()
    {
        $judul = 'Setting Token';
        return view('publicviews.setting.token', compact('judul'));
    }
}
