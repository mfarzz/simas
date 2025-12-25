<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PenggunaRektoratCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(User::       
            join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->join('role_pengguna','unit_rektorat_jabatan.role_id','=','role_pengguna.id_rp')     
            ->get())            
            ->addColumn('id', function ($data) {
                return $data->id; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_unit = UnitRektoratModel::orderby('nm_ur')->get();
        $daftar_unit_jabatan = UnitRektoratJabatanModel::orderby('nm_urj')->get();
        return view('MasterData.PenggunaRektorat.index',['daftar_unit'=> $daftar_unit,'daftar_unit_jabatan'=> $daftar_unit_jabatan]);
    }

    public function getUnitjabatan(Request $request){
        $idUnitjabatan = UnitRektoratJabatanModel::where('id_ur', $request->unitjabatan)->pluck('id_urj','nm_urj');
        return response()->json($idUnitjabatan);
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
                $baris = UnitRektoratJabatanModel::where('id_urj', $request->idUnitjabatan)->first();
                $password = Hash::make($request->username);
                $data = new User();
                $data->username = $request->username;
                $data->password_text = $request->username;
                $data->password = $password;
                $data->name = $request->nama;
                $data->jk = $request->jk;
                $data->nowa = $request->no_wa;
                $data->role_id = $baris->role_id;
                $data->email = $request->email;
                $data->id_urj = $request->idUnitjabatan;
                $data->id_fkj = 0;
                $data->user_id = $user_id;
                $data->pengguna = $request->pengguna; 
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $baris = UnitRektoratJabatanModel::where('id_urj', $request->idUnitjabatan)->first();
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
                    $data->id_urj = $request->idUnitjabatan;
                    $data->id_fkj = 0;
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
        $data = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
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