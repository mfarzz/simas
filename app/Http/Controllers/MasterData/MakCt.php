<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MataAnggaranModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class MakCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(MataAnggaranModel::
            where('sts_mak',1)
            ->get())            
            ->addColumn('id_mak', function ($data) {
                return $data->id_mak; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.MataAnggaran.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id_mak == "")
        {
            $jumlah = MataAnggaranModel::where('kd_mak', $request->kode)->where('nm_mak', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new MataAnggaranModel();
                $data->kd_mak = $request->kode;
                $data->nm_mak = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = MataAnggaranModel::where('id_mak', $request->id_mak)->first();
            if($cekData->kd_mak == $request->kode and $cekData->nm_mak == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0; 
                if($cekData->kd_mak != $request->kode)
                {
                    $jumlah = MataAnggaranModel::where('kd_mak', $request->kode)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }              
                if($cekData->nm_mak != $request->nama)
                {
                    $jumlah = MataAnggaranModel::where('nm_mak', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = MataAnggaranModel::where('id_mak', $request->id_mak)->first();                   
                    $data->kd_mak = $request->kode;
                    $data->nm_mak = $request->nama;  
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = MataAnggaranModel::where('id_mak',$request->id_mak)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = MataAnggaranModel::where('id_mak', $request->id_mak)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}