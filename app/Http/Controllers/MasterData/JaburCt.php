<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JaburModel;
use Illuminate\Http\Request;
use Datatables;

class JaburCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(JaburModel::
            get())            
            ->addColumn('id_jabur', function ($data) {
                return $data->id_jabur; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Jabatan.Rektorat.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;        
        if($request->id_jabur == "")
        {
            $jumlah = JaburModel::where('nm_jabur', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JaburModel();
                $data->nm_jabur = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JaburModel::where('id_jabur', $request->id_jabur)->first();
            if($cekData->nm_jabur == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_jabur != $request->nama)
                {
                    $jumlah = JaburModel::where('nm_jabur', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JaburModel::where('id_jabur', $request->id_jabur)->first();                   
                    $data->nm_jabur = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JaburModel::where('id_jabur',$request->id_jabur)->first();
        return Response()->json($data);
    }
}