<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BkprModel;
use App\Models\BkrnModel;
use App\Models\UnitRektoratJabatanModel;
use Illuminate\Http\Request;
use Datatables;

class BkprCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(BkprModel::
            get())            
            ->addColumn('id_bkpr', function ($data) {
                return $data->id_bkpr; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Penerima.Rektorat.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;
        if($request->id_bkpr == "")
        {
            $jumlah = BkprModel::where('id_ur', $id_ur)->where('nm_bkpr', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new BkprModel();
                $data->id_ur = $id_ur;
                $data->nm_bkpr = $request->nama;
                $data->status_bkpr = $request->status;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = BkprModel::where('id_bkpr', $request->id_bkpr)->first();
            if($cekData->nm_bkpr == $request->nama and $cekData->status_bkpr == $request->status )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_bkp != $request->nama)
                {
                    $jumlah = BkprModel::where('id_ur', $id_ur)->where('nm_bkpr', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BkprModel::where('id_bkpr', $request->id_bkpr)->first();                   
                    $data->nm_bkpr = $request->nama;
                    $data->status_bkpr = $request->status;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = BkprModel::where('id_bkpr',$request->id_bkpr)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = BkrnModel::where('id_bkpr',$request->id_bkpr)->count();
        if($jumlah==0)
        {
            $data = BkprModel::where('id_bkpr', $request->id_bkpr)->first();   
            $data->delete();
            return response()->json(['status' => 1]);
        }
        else
        {
            return response()->json(['status' => 2]);
        }
    }
}