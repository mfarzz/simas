<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\FakultasJabatanModel;
use App\Models\PbfdModel;
use App\Models\PbflModel;
use App\Models\PbfModel;
use App\Models\PbsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PbfKgSpCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbfModel::
            join('permintaan_barang_status','permintaan_barang_fakultas.id_pbs','=','permintaan_barang_status.id_pbs')
            ->join('fakultas','permintaan_barang_fakultas.id_fk','=','fakultas.id_fk')
            ->where('permintaan_barang_fakultas.proses_pbf',2)
            ->whereYear('permintaan_barang_fakultas.tgl_pbf',$tahun_anggaran)
            ->get())
            ->addColumn('id_pbf', function ($data) {
                return $data->id_pbf; 
            })
            ->addColumn('id_pbf_en', function ($data) {
                $id_pbf_en = Crypt::encryptString($data->id_pbf);
                return $id_pbf_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('PermintaanBarang.KepalaGudang.Fakultas.SelesaiProses.index');
    }
}