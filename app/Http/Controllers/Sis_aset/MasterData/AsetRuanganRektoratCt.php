<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetRuanganRektoratModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class AsetRuanganRektoratCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(AsetRuanganRektoratModel::
            where('aset_ruangan_rektorat.id_ur', $datarektorat->id_ur)
            ->orderBy('a_kd_arr')
            ->get())
            ->addColumn('a_id_arr', function ($data) {
                return $data->a_id_arr; 
            })            
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Ruangan.Rektorat.index');
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();

        if($request->a_id_arr == "")
        {
            $jumlah = AsetRuanganRektoratModel::where('aset_ruangan_rektorat.id_ur', $datarektorat->id_ur)->where('a_kd_arr', $request->kode)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new AsetRuanganRektoratModel();
                $data->id_ur = $datarektorat->id_ur;
                $data->a_kd_arr = $request->kode;
                $data->a_nm_arr = $request->nama_ruangan;                
                $data->a_nm_pj_arr = $request->nama_pj;
                $data->a_nip_pj_arr = $request->nip_pj;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = AsetRuanganRektoratModel::where('aset_ruangan_rektorat.id_ur', $datarektorat->id_ur)->where('a_id_arr', $request->a_id_arr)->first();
           
            if($cekData->a_kd_arr == $request->kode and $cekData->a_nm_arr == $request->nama_ruangan and $cekData->a_nip_pj_arr == $request->nip_pj and $cekData->a_nm_pj_arr == $request->nama_pj)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->a_kd_arr != $request->kode)
                {
                    $jumlah = AsetRuanganRektoratModel::where('a_kd_arr', $request->kode)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 11]);
                    }
                }                
                if($jumlah==0)
                { 
                    
                    $data = AsetRuanganRektoratModel::where('a_id_arr', $request->a_id_arr)->first();
                    $data->a_kd_arr = $request->kode;
                    $data->a_nm_arr = $request->nama_ruangan;
                    $data->a_nip_pj_arr = $request->nip_pj;
                    $data->a_nm_pj_arr = $request->nama_pj;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }                
            }            
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetRuanganRektoratModel::where('a_id_arr',$request->a_id_arr)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetRuanganRektoratModel::where('a_id_arr', $request->a_id_arr)->first();
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