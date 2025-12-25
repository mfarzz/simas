<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\PbflModel;
use App\Models\PbfModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbflKaSpCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_pbf = Crypt::decryptString($encripted_id);

        if(request()->ajax()) {
            return datatables()->of(PbflModel::
            join('permintaan_barang_status','permintaan_barang_fakultas_log.id_pbs','=','permintaan_barang_status.id_pbs')
            ->where('id_pbf', $id_pbf)
            ->orderby('id_pbfl')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $cek_pbf = PbfModel::
        join('permintaan_barang_status','permintaan_barang_fakultas.id_pbs','=','permintaan_barang_status.id_pbs')
        ->join('fakultas','permintaan_barang_fakultas.id_fk','=','fakultas.id_fk')
        ->where('id_pbf',$id_pbf)->first();
        return view('PermintaanBarang.KasiLogistik.Fakultas.SelesaiProses.Log.index',['encripted_id'=> $encripted_id, 'cek_pbf' =>$cek_pbf]);
    }
}
