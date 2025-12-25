<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\FakultasJabatanModel;
use App\Models\FakultasModel;
use App\Models\RolePenggunaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class FakultasJabatanCt extends Controller
{
    public function index($encripted_id)
    {
        
        $id_fk = Crypt::decryptString($encripted_id);        
        $data_fakultas = FakultasModel::where('id_fk', $id_fk)->first();
        
        if(request()->ajax()) {
            return datatables()->of(FakultasJabatanModel::
            join('role_pengguna','fakultas_jabatan.role_id','=','role_pengguna.id_rp')
            ->where('fakultas_jabatan.id_fk', $id_fk)
            ->orderBy('nama_rp')
            ->get())     
            ->addColumn('id_fkj', function ($data) {
                return $data->id_fkj; 
            })     
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_role = RolePenggunaModel::where('id_rp','=','7')->orwhere('id_rp','=','8')->orderby('id_rp')->get();
        return view('MasterData.Fakultas.Jabatan.index',['data_fakultas'=>$data_fakultas, 'daftar_role'=>$daftar_role, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fk = Crypt::decryptString($request->encripted_id);
        $data_kategori = FakultasModel::where('id_fk', $id_fk)->first();
        if($request->id_fkj == "")
        {
            $jumlah = FakultasJabatanModel::where('id_fk', $id_fk)->where('role_id', $request->role)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 11]);
            }
            else
            {   
                $jumlah = FakultasJabatanModel::where('id_fk', $id_fk)->where('role_id', $request->role)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 12]);
                }
                else
                {
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();
                    
                    $data = new FakultasJabatanModel();
                    $data->id_fk = $id_fk;
                    $data->nm_fkj = $baris->nama_rp;
                    $data->role_id = $baris->id_rp;            
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            } 
        }
        else
        {
            $cekData = FakultasJabatanModel::where('id_fkj', $request->id_fkj)->first();
            if($cekData->role_id == $request->role)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;               
                if($cekData->role_id != $request->role)
                {
                    $jumlah = FakultasJabatanModel::where('id_fk', $id_fk)->where('role_id', $request->role)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {            
                    $baris = RolePenggunaModel::where('id_rp', $request->role)->first();

                    $data = FakultasJabatanModel::where('id_fkj', $request->id_fkj)->first();                   
                    $data->id_fk = $id_fk;
                    $data->nm_fkj = $baris->nama_rp;
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
        $data = FakultasJabatanModel::where('id_fkj',$request->id_fkj)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = FakultasJabatanModel::where('id_fkj', $request->id_fkj)->first();      
        $data->delete();  
        return Response()->json(0);
    }
}
