<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JenisSatuanModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class JenisSatuanCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(JenisSatuanModel::
            get())            
            ->addColumn('id_js', function ($data) {
                return $data->id_js; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.JenisSatuan.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id_js == "")
        {
            $jumlah = JenisSatuanModel::where('nm_js', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JenisSatuanModel();
                $data->nm_js = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JenisSatuanModel::where('id_js', $request->id_js)->first();
            if($cekData->nm_js == $request->nama )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_js != $request->nama)
                {
                    $jumlah = JenisSatuanModel::where('nm_js', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JenisSatuanModel::where('id_js', $request->id_js)->first();                   
                    $data->nm_js = $request->nama;  
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JenisSatuanModel::where('id_js',$request->id_js)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = JenisSatuanModel::where('id_js', $request->id_js)->first();   
        $data->delete();         
        return Response()->json(0);
    }
}