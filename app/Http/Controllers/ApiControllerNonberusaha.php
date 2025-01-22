<?php

namespace App\Http\Controllers;

use App\Models\Mppd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiControllerNonberusaha extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Mppd::query();
        $items = $query->select('profesi',DB::raw('COUNT(*) as total'))->groupBy('profesi')->where('keterangan',  'SK Diterbitkan')
        ->orWhere('keterangan', 'Survey IKM')->get();
		//$items->withPath(url('/api/agregat/nonberusaha'));
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data'=>$items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
