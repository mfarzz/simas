<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSubModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class KategoriAsetCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(AsetKategoriModel::
            orderBy('a_id_kt')
            ->get())
            ->addColumn('a_id_kt', function ($data) {
                return $data->a_id_kt; 
            })
            ->addColumn('a_kd_kt_en', function ($data) {
                $a_kd_kt_en = Crypt::encryptString($data->a_kd_kt);
                return $a_kd_kt_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Kategori.index');
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        if($request->a_id_kt == "")
        {
            $jumlah = AsetKategoriModel::where('a_kd_kt', $request->kode)->orwhere('a_nm_kt', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $data = new AsetKategoriModel();
                $data->a_kd_kt = $request->kode;
                $data->a_nm_kt = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = AsetKategoriModel::where('a_id_kt', $request->a_id_kt)->first();
           
            if($cekData->a_nm_kt == $request->nama and $cekData->a_kd_kt == $request->kode)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->a_kd_kt != $request->nama)
                {
                    $jumlah = AsetKategoriSubModel::where('a_kd_kt', $cekData->a_kd_kt)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 13]);
                    }
                    else
                    {
                        $jumlah = AsetKategoriModel::where('a_kd_kt', $request->kode)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 11]);
                        }
                    }                        
                }
                if($cekData->a_nm_kt != $request->nama)
                {
                    $jumlah = AsetKategoriModel::where('a_nm_kt', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                { 
                    
                    $data = AsetKategoriModel::where('a_id_kt', $request->a_id_kt)->first();
                    $data->a_kd_kt = $request->kode;
                    $data->a_nm_kt = $request->nama;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }                
            }            
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetKategoriModel::where('a_id_kt',$request->a_id_kt)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetKategoriModel::where('a_id_kt', $request->a_id_kt)->first();
        $jumlah = AsetKategoriSubModel::where('a_kd_kt', $data->a_kd_kt)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
              
            $data->delete();   
            return response()->json(['status' => 1]);
        }
    }
}