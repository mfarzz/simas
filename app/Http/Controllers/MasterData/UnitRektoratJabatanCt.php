<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\RolePenggunaModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class UnitRektoratJabatanCt extends Controller
{
    public function index($encripted_id)
    {
        
        $id_ur = Crypt::decryptString($encripted_id);        
        $data_unitrektorat = UnitRektoratModel::where('id_ur', $id_ur)->first();
        
        if(request()->ajax()) {
            return datatables()->of(UnitRektoratJabatanModel::
            join('role_pengguna','unit_rektorat_jabatan.role_id','=','role_pengguna.id_rp')
            ->where('unit_rektorat_jabatan.id_ur', $id_ur)
            ->orderBy('nama_rp')
            ->get())     
            ->addColumn('id_urj', function ($data) {
                return $data->id_urj; 
            })     
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_role = RolePenggunaModel::where('id_rp','=','5')->orwhere('id_rp','=','6')->orwhere('id_rp','=','9')->orwhere('id_rp','=','12')->orwhere('id_rp','=','13')->orwhere('id_rp','=','14')->orderby('id_rp')->get();
        return view('MasterData.UnitRektorat.Jabatan.index',['data_unitrektorat'=>$data_unitrektorat, 'daftar_role'=>$daftar_role, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ur = Crypt::decryptString($request->encripted_id);
        $data_kategori = UnitRektoratModel::where('id_ur', $id_ur)->first();
        if($request->id_urj == "")
        {
            $jumlah = UnitRektoratJabatanModel::where('id_ur', $id_ur)->where('role_id', $request->role)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 11]);
            }
            else
            {   
                $jumlah = UnitRektoratJabatanModel::where('id_ur', $id_ur)->where('role_id', $request->role)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 12]);
                }
                else
                {
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();
                    
                    $data = new UnitRektoratJabatanModel();
                    $data->id_ur = $id_ur;
                    $data->nm_urj = $baris->nama_rp;
                    $data->role_id = $baris->id_rp;            
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            } 
        }
        else
        {
            $cekData = UnitRektoratJabatanModel::where('id_urj', $request->id_urj)->first();
            if($cekData->role_id == $request->role)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;               
                if($cekData->role_id != $request->role)
                {
                    $jumlah = UnitRektoratJabatanModel::where('id_ur', $id_ur)->where('role_id', $request->role)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {            
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();

                    $data = UnitRektoratJabatanModel::where('id_urj', $request->id_urj)->first();                   
                    $data->id_ur = $id_ur;
                    $data->nm_urj = $baris->nama_rp;
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
        $data = UnitRektoratJabatanModel::where('id_urj',$request->id_urj)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = UnitRektoratJabatanModel::where('id_urj', $request->id_urj)->first();      
        $data->delete();  
        return Response()->json(0);
    }
}
