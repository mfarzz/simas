<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\FakultasJabatanModel;
use App\Models\FakultasModel;
use App\Models\LokasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class FakultasCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(FakultasModel::       
            join('lokasi','fakultas.kd_lks','=','lokasi.kd_lks')
            ->get())            
            ->addColumn('id_fk', function ($data) {
                return $data->id_fk; 
            })
            ->addColumn('id_fk_en', function ($data) {
                $id_fk_en = Crypt::encryptString($data->id_fk);
                return $id_fk_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_lokasi = LokasiModel::orderby('nm_lks')->get();        
        return view('MasterData.Fakultas.index',['daftar_lokasi'=> $daftar_lokasi]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        if($request->id_fk == "")
        {
            $jumlah = FakultasModel::where('kd_lks', $request->lokasi)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new FakultasModel();
                $data->nm_fk = $request->nama;
                $data->kd_lks = $request->lokasi;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = FakultasModel::where('id_fk', $request->id_fk)->first();
            if($cekData->nm_fk == $request->nama and $cekData->kd_lks == $request->lokasi )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->kd_lks != $request->lokasi)
                {
                    $jumlah = FakultasModel::where('nm_js', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = FakultasModel::where('id_fk', $request->id_fk)->first();                   
                    $data->nm_fk = $request->nama;
                    $data->kd_lks = $request->lokasi;
                    $data->user_id = $user_id;           
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        } 
    }

    public function edit(Request $request)
    {   
        $data = FakultasModel::
        where('id_fk',$request->id_fk)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $jumlah = FakultasJabatanModel::where('id_fk', $request->id_fk)->count();
        if($jumlah >0)
        {
            return response()->json(['status' => 2]);    
        }
        else
        {
            $data = FakultasModel::where('id_fk', $request->id_fk)->first();
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}