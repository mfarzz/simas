<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\FakultasJabatanModel;
use App\Models\FakultasModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PenggunaFakultasCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(User::       
            join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->join('role_pengguna','fakultas_jabatan.role_id','=','role_pengguna.id_rp')     
            ->get())            
            ->addColumn('id', function ($data) {
                return $data->id; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_fakultas = FakultasModel::orderby('nm_fk')->get();
        $daftar_fakultas_jabatan = FakultasJabatanModel::orderby('nm_fkj')->get();
        return view('MasterData.PenggunaFakultas.index',['daftar_fakultas'=> $daftar_fakultas,'daftar_fakultas_jabatan'=> $daftar_fakultas_jabatan]);
        /*$data = User::get();     
        foreach($data as $baris)
        {
            $data2 = User::where('id', $baris->id)->first();
            $data2->password_text = $baris->username;
            $data2->save();
        }*/
        
    }

    public function getFakultasjabatan(Request $request){
        $idFakultasjabatan = FakultasJabatanModel::where('id_fk', $request->fakultasjabatan)->pluck('id_fkj','nm_fkj');
        return response()->json($idFakultasjabatan);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id == "")
        {
            $jumlah = User::where('username', $request->username)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $baris = FakultasJabatanModel::where('id_fkj', $request->idFakultasjabatan)->first();
                $password = Hash::make($request->username);
                $data = new User();
                $data->username = $request->username;
                $data->password = $password;
                $data->password_text = $request->username;
                $data->name = $request->nama;
                $data->jk = $request->jk;
                $data->nowa = $request->no_wa;
                $data->role_id = $baris->role_id;
                $data->email = $request->email;
                $data->id_urj = 0;
                $data->id_fkj = $request->idFakultasjabatan;
                $data->user_id = $user_id;
                $data->pengguna = $request->pengguna;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $baris = FakultasJabatanModel::where('id_fkj', $request->idFakultasjabatan)->first();
            //$cekData = User::where('id', $request->id)->first();
            /*if($cekData->nm_js == $request->nama )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_js != $request->nama)
                {
                    $jumlah = User::where('nm_js', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                {*/ 
                    $data = User::where('id', $request->id)->first();                   
                    $data->name = $request->nama;
                    $data->jk = $request->jk;
                    $data->nowa = $request->no_wa;
                    $data->role_id = $baris->role_id;
                    $data->email = $request->email;
                    $data->id_urj = 0;
                    $data->id_fkj = $request->idFakultasjabatan;
                    $data->user_id = $user_id;        
                    $data->pengguna = $request->pengguna;   
                    $data->save();
                    return response()->json(['status' => 4]);
                //}
            //}            
        } 
    }

    public function edit(Request $request)
    {   
        $data = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('id',$request->id)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = User::where('id', $request->id)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function reset(Request $request)
    {     
        $data = User::where('id', $request->id)->first();     
        $password = Hash::make($data->username);              
        $data->password = $password;
        $data->password_text = $data->username;
        $data->save();
        return Response()->json(0);
    }
}