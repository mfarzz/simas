<?php

namespace App\Http\Controllers\Sis_aset\Laporan\Rektorat\Kondisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapOprKondisiBrgCt extends Controller
{
    public function index()
    {        
        return view('Sis_aset.Laporan.Rektorat.Kondisi.index');
    }
    public function cek(Request $request)
    {
        $kondisi = Crypt::encryptString($request->kondisi);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);     
        return response()->json(['kondisi' => $kondisi, 'tgl_akhir' => $tgl_akhir]);
    }
}