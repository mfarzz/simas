<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabuuModel;
use Illuminate\Http\Request;
use Datatables;

class JabuuCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(JabuuModel::
            get())            
            ->addColumn('id_jabuni', function ($data) {
                return $data->id_jabuni; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Jabatan.Universitas.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;        
        if($request->id_jabuni == "")
        {
            $jumlah = JabuuModel::where('nm_jabuni', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabuuModel();
                $data->nm_jabuni = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabuuModel::where('id_jabuni', $request->id_jabuni)->first();
            if($cekData->nm_jabuni == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_jabuni != $request->nama)
                {
                    $jumlah = JabuuModel::where('nm_jabuni', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabuuModel::where('id_jabuni', $request->id_jabuni)->first();                   
                    $data->nm_jabuni = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabuuModel::where('id_jabuni',$request->id_jabuni)->first();
        return Response()->json($data);
    }
}