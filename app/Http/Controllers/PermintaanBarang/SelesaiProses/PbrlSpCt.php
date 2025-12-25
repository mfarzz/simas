<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\PbrlModel;
use App\Models\PbrModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbrlSpCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_pbr = Crypt::decryptString($encripted_id);

        if(request()->ajax()) {
            return datatables()->of(PbrlModel::
            join('permintaan_barang_status','permintaan_barang_rektorat_log.id_pbs','=','permintaan_barang_status.id_pbs')
            ->where('id_pbr', $id_pbr)
            ->orderby('id_pbrl')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $cek_pbr = PbrModel::
        join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')
        ->where('id_pbr',$id_pbr)->first();
        return view('PermintaanBarang.Rektorat.SelesaiProses.Log.index',['encripted_id'=> $encripted_id, 'cek_pbr' =>$cek_pbr]);
    }
}
