<?php

namespace App\Http\Controllers\Sis_aset\Laporan\Kondisi;

use App\Http\Controllers\Controller;
use App\Models\AsetLokasiModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class LapKondisiBrgCt extends Controller
{
    public function index()
    {        
        $daftar_lokasi = AsetLokasiModel::orderby('a_kd_al')->get();
        return view('Sis_aset.Laporan.Kondisi.index', ['daftar_lokasi'=>$daftar_lokasi]);
    }
    public function cek(Request $request)
    {
        $kondisi = Crypt::encryptString($request->kondisi);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);     
        $id_lokasi = Crypt::encryptString($request->id_lokasi);
        return response()->json(['kondisi' => $kondisi, 'tgl_akhir' => $tgl_akhir, 'id_lokasi' => $id_lokasi]);
    }
}