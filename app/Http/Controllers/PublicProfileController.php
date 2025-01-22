<?php

namespace App\Http\Controllers;

use App\Models\Personalaccesstoken;
use App\Models\User;
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
        $id=auth()->user()->id;
        $tokenuser = Personalaccesstoken::where('tokenable_id', $id)->first();
        //$datauser->createToken('api-access')->plainTextToken;
        return view('publicviews.setting.token', compact('judul','tokenuser'));
    }
    public function token_refresh(Request $request)
    {
        $datauser = User::where('email', $request->email)->first();
        //dd($datauser->id);
        $datauser->tokens()->where('tokenable_id', $datauser->id)->delete();
        $datauser->createToken('api-access', ['*'],now()->addYears(5))->plainTextToken;
        return redirect('/token')->with('success', 'Berhasil di Ubah !');
    }
}
