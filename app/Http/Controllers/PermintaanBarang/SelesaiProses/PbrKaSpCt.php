<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\PbrdModel;
use App\Models\PbrlModel;
use App\Models\PbrModel;
use App\Models\PbsModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PbrKaSpCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbrModel::
            join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')            
            ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
            ->where('permintaan_barang_rektorat.proses_pbr','2')
            ->whereYear('permintaan_barang_rektorat.tgl_pbr',$tahun_anggaran)
            ->get())
            ->addColumn('id_pbr', function ($data) {
                return $data->id_pbr; 
            })
            ->addColumn('id_pbr_en', function ($data) {
                $id_pbr_en = Crypt::encryptString($data->id_pbr);
                return $id_pbr_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('PermintaanBarang.KasiLogistik.Rektorat.SelesaiProses.index');
    }
}