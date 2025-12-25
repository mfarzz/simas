<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\JabursModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class JabpenursCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();    

        if(request()->ajax()) {            
            return datatables()->of(JabpenursModel::
            join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')
            ->where('jabatan_pengesahan_rumah_sakit.id_urs',$datarumahsakit->id_urs)
            ->get())
            ->addColumn('id_jabpenurs', function ($data) {
                return $data->id_jabpenurs; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_jabatan = JabursModel::orderby('nm_jaburs')->get();
        return view('MasterData.Jabatan.RumahSakit.Pengesahan.index', ['daftar_jabatan'=>$daftar_jabatan]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;
        if($request->id_jabpenurs == "")
        {
            $jumlah = JabpenursModel::where('id_urs', $id_urs)->where('id_jaburs', $request->jabatan)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabpenursModel();
                $data->id_urs = $id_urs;
                $data->id_jaburs = $request->jabatan;
                $data->nm_jabpenurs = $request->nama;
                $data->nik_jabpenurs = $request->nik;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabpenursModel::where('id_jabpenurs', $request->id_jabpenurs)->first();
            if($cekData->nm_jabpenurs == $request->nama and $cekData->nik_jabpenurs == $request->nik and $cekData->id_jaburs == $request->jabatan )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->id_jaburs != $request->jabatan)
                {
                    $jumlah = JabpenursModel::where('id_urs', $id_urs)->where('id_jaburs', $request->jabatan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabpenursModel::where('id_jabpenurs', $request->id_jabpenurs)->first();
                    $data->id_jaburs = $request->jabatan;
                    $data->nm_jabpenurs = $request->nama;
                    $data->nik_jabpenurs = $request->nik;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabpenursModel::where('id_jabpenurs',$request->id_jabpenurs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = JabpenursModel::where('id_jabpenurs', $request->id_jabpenurs)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}