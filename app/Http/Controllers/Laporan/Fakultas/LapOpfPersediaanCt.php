<?php

namespace App\Http\Controllers\Laporan\Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapOpfPersediaanCt extends Controller
{
    public function index()
    {        
        return view('Laporan.Fakultas.Persediaan.index');
    }
    public function cek(Request $request)
    {
        $tgl = Crypt::encryptString($request->tgl);        
        return response()->json(['tgl' => $tgl]);
    }
}