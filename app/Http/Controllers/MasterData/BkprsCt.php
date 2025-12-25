<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BkprsModel;
use App\Models\BkrsnModel;
use App\Models\UnitRumahSakitJabatanModel;
use Illuminate\Http\Request;
use Datatables;

class BkprsCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(BkprsModel::
            get())            
            ->addColumn('id_bkprs', function ($data) {
                return $data->id_bkprs; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Penerima.RumahSakit.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;
        if($request->id_bkprs == "")
        {
            $jumlah = BkprsModel::where('id_urs', $id_urs)->where('nm_bkprs', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new BkprsModel();
                $data->id_urs = $id_urs;
                $data->nm_bkprs = $request->nama;
                $data->status_bkprs = $request->status;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = BkprsModel::where('id_bkprs', $request->id_bkprs)->first();
            if($cekData->nm_bkprs == $request->nama and $cekData->status_bkprs == $request->status )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_bkprs != $request->nama)
                {
                    $jumlah = BkprsModel::where('id_urs', $id_urs)->where('nm_bkprs', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BkprsModel::where('id_bkprs', $request->id_bkprs)->first();                   
                    $data->nm_bkprs = $request->nama;
                    $data->status_bkprs = $request->status;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = BkprsModel::where('id_bkprs',$request->id_bkprs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = BkrsnModel::where('id_bkprs',$request->id_bkprs)->count();
        if($jumlah==0)
        {
            $data = BkprsModel::where('id_bkprs', $request->id_bkprs)->first();   
            $data->delete();         
            return response()->json(['status' => 1]);
        }
        else
        {
            return response()->json(['status' => 2]);
        }
        
    }
}