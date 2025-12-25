<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\RolePenggunaModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\UnitRumahSakitModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class UnitRumahSakitJabatanCt extends Controller
{
    public function index($encripted_id)
    {
        
        $id_urs = Crypt::decryptString($encripted_id);        
        $data_unitrumahsakit = UnitRumahSakitModel::where('id_urs', $id_urs)->first();
        
        if(request()->ajax()) {
            return datatables()->of(UnitRumahSakitJabatanModel::
            join('role_pengguna','unit_rumah_sakit_jabatan.role_id','=','role_pengguna.id_rp')
            ->where('unit_rumah_sakit_jabatan.id_urs', $id_urs)
            ->orderBy('nama_rp')
            ->get())     
            ->addColumn('id_ursj', function ($data) {
                return $data->id_ursj; 
            })     
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_role = RolePenggunaModel::where('id_rp','=','10')->orwhere('id_rp','=','11')->orderby('id_rp')->get();
        return view('MasterData.UnitRumahSakit.Jabatan.index',['data_unitrumahsakit'=>$data_unitrumahsakit, 'daftar_role'=>$daftar_role, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urs = Crypt::decryptString($request->encripted_id);
        $data_kategori = UnitRumahSakitModel::where('id_urs', $id_urs)->first();
        if($request->id_ursj == "")
        {            
            $jumlah = UnitRumahSakitJabatanModel::where('id_urs', $id_urs)->where('role_id', $request->role)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 11]);
            }
            else
            {   
                $jumlah = UnitRumahSakitJabatanModel::where('id_urs', $id_urs)->where('role_id', $request->role)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 12]);
                }
                else
                {
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();
                    
                    $data = new UnitRumahSakitJabatanModel();
                    $data->id_urs = $id_urs;
                    $data->nm_ursj = $baris->nama_rp;
                    $data->role_id = $baris->id_rp;            
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            } 
        }
        else
        {
            $cekData = UnitRumahSakitJabatanModel::where('id_ursj', $request->id_ursj)->first();
            if($cekData->role_id == $request->role)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;               
                if($cekData->role_id != $request->role)
                {
                    $jumlah = UnitRumahSakitJabatanModel::where('id_urs', $id_urs)->where('role_id', $request->role)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {            
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();

                    $data = UnitRumahSakitJabatanModel::where('id_ursj', $request->id_ursj)->first();                   
                    $data->id_urs = $id_urs;
                    $data->nm_ursj = $baris->nama_rp;
                    $data->role_id = $baris->id_rp;            
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = UnitRumahSakitJabatanModel::where('id_ursj',$request->id_ursj)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = UnitRumahSakitJabatanModel::where('id_ursj', $request->id_ursj)->first();      
        $data->delete();  
        return Response()->json(0);
    }
}
