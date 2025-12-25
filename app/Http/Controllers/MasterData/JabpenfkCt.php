<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\FakultasJabatanModel;
use App\Models\JabfkModel;
use App\Models\JabpenfkModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class JabpenfkCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        if(request()->ajax()) {            
            return datatables()->of(JabpenfkModel::
            join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')
            ->where('jabatan_pengesahan_fakultas.id_fk',$datafakultas->id_fk)
            ->get())
            ->addColumn('id_jabpenfk', function ($data) {
                return $data->id_jabpenfk; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_jabatan = JabfkModel::orderby('nm_jabfk')->get();
        return view('MasterData.Jabatan.Fakultas.Pengesahan.index', ['daftar_jabatan'=>$daftar_jabatan]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($request->id_jabpenfk == "")
        {
            $jumlah = JabpenfkModel::where('id_fk', $id_fk)->where('id_jabfk', $request->jabatan)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabpenfkModel();
                $data->id_fk = $id_fk;
                $data->id_jabfk = $request->jabatan;
                $data->nm_jabpenfk = $request->nama;
                $data->nik_jabpenfk = $request->nik;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabpenfkModel::where('id_jabpenfk', $request->id_jabpenfk)->first();
            if($cekData->nm_jabpenfk == $request->nama and $cekData->nik_jabpenfk == $request->nik and $cekData->id_jabfk == $request->jabatan )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->id_jabfk != $request->jabatan)
                {
                    $jumlah = JabpenfkModel::where('id_fk', $id_fk)->where('id_jabfk', $request->jabatan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabpenfkModel::where('id_jabpenfk', $request->id_jabpenfk)->first();
                    $data->id_jabfk = $request->jabatan;
                    $data->nm_jabpenfk = $request->nama;
                    $data->nik_jabpenfk = $request->nik;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabpenfkModel::where('id_jabpenfk',$request->id_jabpenfk)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = JabpenfkModel::where('id_jabpenfk', $request->id_jabpenfk)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}