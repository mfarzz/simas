<?php

namespace App\Http\Controllers\PermintaanBarang;

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

class PbfKgCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbfModel::
            join('permintaan_barang_status','permintaan_barang_fakultas.id_pbs','=','permintaan_barang_status.id_pbs')
            ->join('fakultas','permintaan_barang_fakultas.id_fk','=','fakultas.id_fk')
            ->where('permintaan_barang_fakultas.proses_pbf',1)
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
        return view('PermintaanBarang.KepalaGudang.Fakultas.index');
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $cekDataStatus = PbsModel::where('untuk_pbs', 1)->where('level_pbs', 4)->where('urutan_pbs', 1)->first();

        $jumlah_pbf = PbfdModel::where('id_pbf', $request->id_pbf)->count();
        if($jumlah_pbf == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = PbfModel::where('id_pbf', $request->id_pbf)->first();
            $cek_data->id_pbs = $cekDataStatus->id_pbs;
            $cek_data->proses_pbf = 2;
            $cek_data->user_id = $user_id;
            $cek_data->save();

            $log = new PbflModel();
            $log->id_pbf = $request->id_pbf;
            $log->id_pbs = $cekDataStatus->id_pbs;
            $log->tgl_pbfl = $tgl;
            $log->user_id = $user_id;
            $log->save();
            return response()->json(['status' => 1]);
        }
    }
}