<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CekSicantikResource;
use App\Models\Proses;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

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
        $posts = Proses::where('no_permohonan','=', $no_permohonan)->where('no_hp','=', $email)->whereNotIn('status',['Drop'])->select(DB::raw('(@row_number:=@row_number + 1)AS no,COALESCE(DATE_FORMAT(end_date,"%d-%m-%Y %H:%i:%S"),DATE_FORMAT(start_date,"%d-%m-%Y %H:%i:%S"), "Menunggu Diproses") as date'),'jenis_izin','nama','nama_proses','no_permohonan','email','status')->orderBy('id_proses_permohonan', 'asc')->get();
    
        //return collection of posts as a resource
        return new CekSicantikResource(true, 'List Data Posts', $posts);
    }
}
