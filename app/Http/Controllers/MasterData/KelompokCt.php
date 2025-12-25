<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\KelompokModel;
use Illuminate\Http\Request;
use Datatables;

class KelompokCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(KelompokModel::
            get())
            ->addColumn('id_kl', function ($data) {
                return $data->id_kl; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Kelompok.index');
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        if($request->id_kl == "")
        {
            $jumlah = KelompokModel::where('kd_kl2', $request->kode_lama)->orwhere('kd_kl', $request->kode_baru)->orwhere('nm_kl', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new KelompokModel();
                $data->kd_kl = $request->kode_baru;
                $data->kd_kl2 = $request->kode_lama;
                $data->nm_kl = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = KelompokModel::where('id_kl', $request->id_kl)->first();
            if($cekData->kd_kl == $request->kode_baru and $cekData->kd_kl2 == $request->kode_lama and $cekData->nm_kl == $request->nama)
            {
                return response()->json(['status' => 2]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->kd_kl != $request->kode_baru)
                {
                    $jumlah = KelompokModel::where('kd_kl', $request->kode_baru)->count();
                    if($jumlah>0)
                    {                        
                        return response()->json(['status' => 11]);
                    }
                }
                if($cekData->kd_kl2 != $request->kode_lama)
                {
                    $jumlah = KelompokModel::where('kd_kl2', $request->kode_lama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($cekData->nm_kl != $request->nama)
                {
                    $jumlah = KelompokModel::where('nm_kl', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                { 
                    $data = KelompokModel::where('id_kl', $request->id_kl)->first();                   
                    $data->kd_kl = $request->kode_baru;
                    $data->kd_kl2 = $request->kode_lama;
                    $data->nm_kl = $request->nama;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
        
    }

    public function edit(Request $request)
    {   
        $data = KelompokModel::where('id_kl', $request->id_kl)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = KelompokModel::where('id_kl', $request->id_kl)->first();   
        $data->delete();
        return Response()->json(0);
    }
}