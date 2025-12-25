<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapOprPersediaanCt extends Controller
{
    public function index()
    {        
        return view('Laporan.Rektorat.Unit.Persediaan.index');
    }
    public function cek(Request $request)
    {
        $tgl = Crypt::encryptString($request->tgl);        
        return response()->json(['tgl' => $tgl]);
    }
}