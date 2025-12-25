<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetRuanganFakultasModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class AsetRuanganFakultasCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(AsetRuanganFakultasModel::
            where('aset_ruangan_fakultas.id_fk', $datafakultas->id_fk)
            ->orderBy('a_kd_arf')
            ->get())
            ->addColumn('a_id_arf', function ($data) {
                return $data->a_id_arf; 
            })            
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Ruangan.Fakultas.index');
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first(); 

        if($request->a_id_arf == "")
        {
            $jumlah = AsetRuanganFakultasModel::where('aset_ruangan_fakultas.id_fk', $datafakultas->id_fk)->where('a_kd_arf', $request->kode)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new AsetRuanganFakultasModel();
                $data->id_fk = $datafakultas->id_fk;
                $data->a_kd_arf = $request->kode;
                $data->a_nm_arf = $request->nama_ruangan;                
                $data->a_nm_pj_arf = $request->nama_pj;
                $data->a_nip_pj_arf = $request->nip_pj;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = AsetRuanganFakultasModel::where('aset_ruangan_fakultas.id_fk', $datafakultas->id_fk)->where('a_id_arf', $request->a_id_arf)->first();
           
            if($cekData->a_kd_arf == $request->kode and $cekData->a_nm_arf == $request->nama_ruangan and $cekData->a_nip_pj_arf == $request->nip_pj and $cekData->a_nm_pj_arf == $request->nama_pj)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->a_kd_arf != $request->kode)
                {
                    $jumlah = AsetRuanganFakultasModel::where('a_kd_arf', $request->kode)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 11]);
                    }
                }                
                if($jumlah==0)
                { 
                    
                    $data = AsetRuanganFakultasModel::where('a_id_arf', $request->a_id_arf)->first();
                    $data->a_kd_arf = $request->kode;
                    $data->a_nm_arf = $request->nama_ruangan;
                    $data->a_nip_pj_arf = $request->nip_pj;
                    $data->a_nm_pj_arf = $request->nama_pj;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }                
            }            
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetRuanganFakultasModel::where('a_id_arf',$request->a_id_arf)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetRuanganFakultasModel::where('a_id_arf', $request->a_id_arf)->first();
        $data->delete();   
        return response()->json(['status' => 1]);
        /*$jumlah = AsetKategoriSubModel::where('a_kd_kt', $data->a_kd_kt)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
              
            $data->delete();   
            return response()->json(['status' => 1]);
        }*/
    }
}