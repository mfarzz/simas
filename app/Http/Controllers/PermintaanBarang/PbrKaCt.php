<?php

namespace App\Http\Controllers\PermintaanBarang;

use App\Http\Controllers\Controller;
use App\Models\PbrdModel;
use App\Models\PbrlModel;
use App\Models\PbrModel;
use App\Models\PbsModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PbrKaCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbrModel::
            join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')            
            ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
            ->where('permintaan_barang_rektorat.proses_pbr','1')
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
        return view('PermintaanBarang.KasiLogistik.Rektorat.index');
    }

    public function cektolak(Request $request)
    {   
        $data = PbrModel::where('id_pbr',$request->id_pbr)->first();
        return Response()->json($data);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $cekDataStatus = PbsModel::where('untuk_pbs', 2)->where('level_pbs', 1)->where('urutan_pbs', 2)->first();

        $jumlah_pbr = PbrdModel::where('id_pbr', $request->id_pbr)->count();
        if($jumlah_pbr == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = PbrModel::where('id_pbr', $request->id_pbr)->first();
            $cek_data->id_pbs = $cekDataStatus->id_pbs;
            $cek_data->ket_pbr = $request->alasan;
            $cek_data->user_id = $user_id;
            $cek_data->save();

            $log = new PbrlModel();
            $log->id_pbr = $request->id_pbr;
            $log->id_pbs = $cekDataStatus->id_pbs;
            $log->ket_pbr = $request->alasan;
            $log->tgl_pbrl = $tgl;
            $log->user_id = $user_id;
            $log->save();
            return response()->json(['status' => 1]);
        }
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $cekDataStatus = PbsModel::where('untuk_pbs', 2)->where('level_pbs', 3)->where('urutan_pbs', 1)->first();

        $jumlah_pbr = PbrdModel::where('id_pbr', $request->id_pbr)->count();
        if($jumlah_pbr == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = PbrModel::where('id_pbr', $request->id_pbr)->first();
            $cek_data->id_pbs = $cekDataStatus->id_pbs;
            $cek_data->user_id = $user_id;
            $cek_data->save();

            $log = new PbrlModel();
            $log->id_pbr = $request->id_pbr;
            $log->id_pbs = $cekDataStatus->id_pbs;
            $log->tgl_pbrl = $tgl;
            $log->user_id = $user_id;
            $log->save();
            return response()->json(['status' => 1]);
        }
    }
}