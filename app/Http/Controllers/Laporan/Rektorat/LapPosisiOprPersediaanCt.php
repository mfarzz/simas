<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapPosisiOprPersediaanCt extends Controller
{
    public function index()
    {        
        return view('Laporan.Rektorat.Unit.PosisiPersediaan.index');
    }
    public function cek(Request $request)
    {
        $tgl = Crypt::encryptString($request->tgl);
        $lokasi = Crypt::encryptString($request->lokasi);        
        return response()->json(['tgl' => $tgl, 'lokasi'=>$lokasi]);
    }
}