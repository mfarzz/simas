<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\LokasiModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\UnitRektoratModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UnitRektoratCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(UnitRektoratModel::       
            join('lokasi','unit_rektorat.kd_lks','=','lokasi.kd_lks')
            ->get())            
            ->addColumn('id_ur', function ($data) {
                return $data->id_ur; 
            })
            ->addColumn('id_ur_en', function ($data) {
                $id_ur_en = Crypt::encryptString($data->id_ur);
                return $id_ur_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }     
        return view('MasterData.UnitRektorat.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id_ur == "")
        {
            $jumlah = UnitRektoratModel::where('nm_ur', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new UnitRektoratModel();
                $data->nm_ur = $request->nama;
                $data->kd_lks = "690522009KD";
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = UnitRektoratModel::where('id_ur', $request->id_ur)->first();
            if($cekData->nm_ur == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_ur != $request->nama)
                {
                    $jumlah = UnitRektoratModel::where('nm_ur', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = UnitRektoratModel::where('id_ur', $request->id_ur)->first();                   
                    $data->nm_ur = $request->nama;
                    $data->kd_lks = "690522009KD";
                    $data->user_id = $user_id;           
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        } 
    }

    public function edit(Request $request)
    {   
        $data = UnitRektoratModel::
        where('id_ur',$request->id_ur)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = UnitRektoratJabatanModel::where('id_ur', $request->id_ur)->count();
        if($jumlah >0)
        {
            return response()->json(['status' => 2]);    
        }
        else
        {
            $data = UnitRektoratModel::where('id_ur', $request->id_ur)->first();
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}