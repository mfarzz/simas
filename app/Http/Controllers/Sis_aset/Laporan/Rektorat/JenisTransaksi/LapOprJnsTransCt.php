<?php

namespace App\Http\Controllers\Sis_aset\Laporan\Rektorat\JenisTransaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapOprJnsTransCt extends Controller
{
    public function index()
    {        
        return view('Sis_aset.Laporan.Rektorat.JenisTransaksi.index');
    }
    public function cek(Request $request)
    {
        $tercatat = Crypt::encryptString($request->tercatat);
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);     
        return response()->json(['tercatat' => $tercatat, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }
}