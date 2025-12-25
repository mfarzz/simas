<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabursModel;
use Illuminate\Http\Request;
use Datatables;

class JabursCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(JabursModel::
            get())            
            ->addColumn('id_jaburs', function ($data) {
                return $data->id_jaburs; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Jabatan.RumahSakit.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;        
        if($request->id_jaburs == "")
        {
            $jumlah = JabursModel::where('nm_jaburs', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabursModel();
                $data->nm_jaburs = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabursModel::where('id_jaburs', $request->id_jaburs)->first();
            if($cekData->nm_jaburs == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_jaburs != $request->nama)
                {
                    $jumlah = JabursModel::where('nm_jaburs', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabursModel::where('id_jaburs', $request->id_jaburs)->first();                   
                    $data->nm_jaburs = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabursModel::where('id_jaburs',$request->id_jaburs)->first();
        return Response()->json($data);
    }
}