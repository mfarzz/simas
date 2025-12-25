<?php

namespace App\Http\Controllers\PermintaanBarang;

use App\Http\Controllers\Controller;
use App\Models\PbrdModel;
use App\Models\PbrlModel;
use App\Models\PbrModel;
use App\Models\PbsModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PbrCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbrModel::
            join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')
            ->where('permintaan_barang_rektorat.proses_pbr','1')
            ->where('permintaan_barang_rektorat.id_ur',$datarektorat->id_ur)
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
        return view('PermintaanBarang.Rektorat.index');
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
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;  
        $thn_permintaan = substr($request->tgl_permintaan,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_permintaan)
        {               
            $cekDataStatus = PbsModel::where('untuk_pbs', 2)->where('level_pbs', 1)->where('urutan_pbs', 1)->first();            
            if($request->id_pbr == "")
            {                
                $data = new PbrModel();
                $data->id_ur = $id_ur;
                $data->butuh_pbr = $request->butuh;
                $data->tgl_pbr = $request->tgl_permintaan;
                $data->id_pbs = $cekDataStatus->id_pbs;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
            else
            {
                $cekData = PbrModel::where('id_ur', $id_ur)->where('id_pbr', $request->id_pbr)->first();
                if($cekData->tgl_pbr == $request->tgl_permintaan and $cekData->butuh_pbr == $request->butuh)
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $data = PbrModel::where('id_pbr', $request->id_pbr)->first();                   
                    $data->butuh_pbr = $request->butuh;
                    $data->tgl_pbr = $request->tgl_permintaan;
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
        $data = PbrModel::where('id_pbr',$request->id_pbr)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = PbrModel::where('id_pbr', $request->id_pbr)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;  

        $cekDataStatus = PbsModel::where('untuk_pbs', 2)->where('level_pbs', 2)->where('urutan_pbs', 1)->first();

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