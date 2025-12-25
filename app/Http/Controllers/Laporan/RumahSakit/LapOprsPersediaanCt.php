<?php

namespace App\Http\Controllers\Laporan\RumahSakit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapOprsPersediaanCt extends Controller
{
    public function index()
    {        
        return view('Laporan.RumahSakit.Unit.Persediaan.index');
    }
    public function cek(Request $request)
    {
        $tgl = Crypt::encryptString($request->tgl);        
        return response()->json(['tgl' => $tgl]);
    }
}