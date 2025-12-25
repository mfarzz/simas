<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JabfkModel;
use Illuminate\Http\Request;
use Datatables;

class JabfkCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(JabfkModel::
            get())            
            ->addColumn('id_jabfk', function ($data) {
                return $data->id_jabfk; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Jabatan.Fakultas.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;        
        if($request->id_jabfk == "")
        {
            $jumlah = JabfkModel::where('nm_jabfk', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new JabfkModel();
                $data->nm_jabfk = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = JabfkModel::where('id_jabfk', $request->id_jabfk)->first();
            if($cekData->nm_jabfk == $request->nama)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->nm_jabfk != $request->nama)
                {
                    $jumlah = JabfkModel::where('nm_jabfk', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = JabfkModel::where('id_jabfk', $request->id_jabfk)->first();                   
                    $data->nm_jabfk = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = JabfkModel::where('id_jabfk',$request->id_jabfk)->first();
        return Response()->json($data);
    }
}