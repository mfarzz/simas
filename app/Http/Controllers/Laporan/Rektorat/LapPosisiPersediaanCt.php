<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\LokasiModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapPosisiPersediaanCt extends Controller
{
    public function index()
    {
        $daftar_lokasi = LokasiModel::orderby('kd_lks')->get();
        return view('Laporan.Rektorat.PosisiPersediaan.index',['daftar_lokasi'=>$daftar_lokasi]);
    }
    public function cek(Request $request)
    {
        $tgl = Crypt::encryptString($request->tgl);
        $lokasi = Crypt::encryptString($request->lokasi);        
        return response()->json(['tgl' => $tgl, 'lokasi'=>$lokasi]);
    }
}