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

class PbfCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbfModel::
            join('permintaan_barang_status','permintaan_barang_fakultas.id_pbs','=','permintaan_barang_status.id_pbs')
            ->where('permintaan_barang_fakultas.proses_pbf',1)
            ->where('permintaan_barang_fakultas.id_fk',$datafakultas->id_fk)
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
        return view('PermintaanBarang.Fakultas.index');
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        $thn_permintaan = substr($request->tgl_permintaan,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_permintaan)
        {               
            $cekDataStatus = PbsModel::where('untuk_pbs', 1)->where('level_pbs', 1)->where('urutan_pbs', 1)->first();            
            if($request->id_pbf == "")
            {                
                $data = new PbfModel();
                $data->id_fk = $id_fk;
                $data->butuh_pbf = $request->butuh;
                $data->tgl_pbf = $request->tgl_permintaan;
                $data->id_pbs = $cekDataStatus->id_pbs;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
            else
            {
                $cekData = PbfModel::where('id_fk', $id_fk)->where('id_pbf', $request->id_pbf)->first();
                if($cekData->tgl_pbf == $request->tgl_permintaan and $cekData->butuh_pbf == $request->butuh)
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $data = PbfModel::where('id_pbf', $request->id_pbf)->first();                   
                    $data->butuh_pbf = $request->butuh;
                    $data->tgl_pbf = $request->tgl_permintaan;
                    $data->id_pbs = $cekDataStatus->id_pbs;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }            
            }
        }
        else
        {
            return response()->json(['status' => 6]);
        }
    }

    public function edit(Request $request)
    {   
        $data = PbfModel::where('id_pbf',$request->id_pbf)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = PbfModel::where('id_pbf', $request->id_pbf)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;

        $cekDataStatus = PbsModel::where('untuk_pbs', 1)->where('level_pbs', 2)->where('urutan_pbs', 1)->first();

        $jumlah_pbf = PbfdModel::where('id_pbf', $request->id_pbf)->count();
        if($jumlah_pbf == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = PbfModel::where('id_pbf', $request->id_pbf)->first();
            $cek_data->id_pbs = $cekDataStatus->id_pbs;
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