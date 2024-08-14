<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CekSicantikResource;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;

class ApiCekSicantikController extends Controller
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
        $posts = Proses::where('no_permohonan','=', $no_permohonan)->where('email','=', $email)->select('jenis_izin','nama','nama_proses','no_permohonan','email')->orderBy('id_proses_permohonan', 'asc')->get();
    
        //return collection of posts as a resource
        return new CekSicantikResource(true, 'List Data Posts', $posts);
    }
}
