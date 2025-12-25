<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\UnitRumahSakitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UnitRumahSakitCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(UnitRumahSakitModel::       
            join('lokasi','unit_rumah_sakit.kd_lks','=','lokasi.kd_lks')
            ->get())            
            ->addColumn('id_urs', function ($data) {
                return $data->id_urs; 
            })
            ->addColumn('id_urs_en', function ($data) {
                $id_urs_en = Crypt::encryptString($data->id_urs);
                return $id_urs_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }     
        return view('MasterData.UnitRumahSakit.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id_ur == "")
        {
            $jumlah = UnitRumahSakitModel::where('nm_urs', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new UnitRumahSakitModel();
                $data->nm_urs = $request->nama;
                $data->kd_lks = "690522020KD";
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = UnitRumahSakitModel::where('id_urs', $request->id_urs)->first();
            if($cekData->nm_urs == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_urs != $request->nama)
                {
                    $jumlah = UnitRumahSakitModel::where('nm_urs', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = UnitRumahSakitModel::where('id_urs', $request->id_urs)->first();                   
                    $data->nm_urs = $request->nama;
                    $data->kd_lks = "690522020KD";
                    $data->user_id = $user_id;           
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        } 
    }

    public function edit(Request $request)
    {   
        $data = UnitRumahSakitModel::
        where('id_urs',$request->id_urs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = UnitRumahSakitJabatanModel::where('id_urs', $request->id_urs)->count();
        if($jumlah >0)
        {
            return response()->json(['status' => 2]);    
        }
        else
        {
            $data = UnitRumahSakitModel::where('id_urs', $request->id_urs)->first();
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}