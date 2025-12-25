<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabpenurModel;
use App\Models\JaburModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class JabpenurCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();    

        if(request()->ajax()) {            
            return datatables()->of(JabpenurModel::
            join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')
            ->where('jabatan_pengesahan_rektorat.id_ur',$datarektorat->id_ur)
            ->get())
            ->addColumn('id_jabpenur', function ($data) {
                return $data->id_jabpenur; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_jabatan = JaburModel::orderby('nm_jabur')->get();
        return view('MasterData.Jabatan.Rektorat.Pengesahan.index', ['daftar_jabatan'=>$daftar_jabatan]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;
        if($request->id_jabpenur == "")
        {
            $jumlah = JabpenurModel::where('id_ur', $id_ur)->where('id_jabur', $request->jabatan)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabpenurModel();
                $data->id_ur = $id_ur;
                $data->id_jabur = $request->jabatan;
                $data->nm_jabpenur = $request->nama;
                $data->nik_jabpenur = $request->nik;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabpenurModel::where('id_jabpenur', $request->id_jabpenur)->first();
            if($cekData->nm_jabpenur == $request->nama and $cekData->nik_jabpenur == $request->nik and $cekData->id_jabur == $request->jabatan )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->id_jabur != $request->jabatan)
                {
                    $jumlah = JabpenurModel::where('id_ur', $id_ur)->where('id_jabur', $request->jabatan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabpenurModel::where('id_jabpenur', $request->id_jabpenur)->first();
                    $data->id_jabur = $request->jabatan;
                    $data->nm_jabpenur = $request->nama;
                    $data->nik_jabpenur = $request->nik;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabpenurModel::where('id_jabpenur',$request->id_jabpenur)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = JabpenurModel::where('id_jabpenur', $request->id_jabpenur)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}