<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CekSicantikResource;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

class TteController extends Controller
{
   /**
     * index
     *
     * @return void
     */
    public function index(Request $request)
    {
        //get all posts
        $no_permohonan= request('no_permohonan');
        $email= request('email');
        $posts = Proses::where('no_permohonan','=', $no_permohonan)->where('no_hp','=', $email)->whereNotIn('status',['Drop'])->where('jenis_proses_id','=', 40)->select(DB::raw('COALESCE(CONCAT("https://sicantik.go.id/webroot/files/signed/",file_signed_report)) as tte'),'jenis_izin','nama','nama_proses','no_permohonan','email','status')->get();
    
        //return collection of posts as a resource
        return new CekSicantikResource(true, 'List Data Posts', $posts);
    }
}
