<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class KategoriCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            /*return datatables()->of(KategoriModel::
            leftjoin('v_lab_nilai4_kategori', 'kategori.kd_kt','=','v_lab_nilai4_kategori.v_kd_kt')
            ->orderBy('no_kt')
            ->get())*/
            return datatables()->of(KategoriModel::
            orderBy('no_kt')
            ->get())
            ->addColumn('id_kt', function ($data) {
                return $data->id_kt; 
            })
            ->addColumn('id_kt_en', function ($data) {
                $id_kt_en = Crypt::encryptString($data->id_kt);
                return $id_kt_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Kategori.index');
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        if($request->id_kt == "")
        {
            $jumlah = KategoriModel::where('nm_kt', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 2]);
            }
            else
            {
                $jumlah = KategoriModel::orderBy('no_kt','desc')->count();
                if($jumlah==0)
                {
                    $no_kt_baru_cek = "01";
                }
                else
                {
                    $baris = KategoriModel::orderBy('no_kt','desc')->first();
                    $no_kt_baru = $baris->no_kt + 1;
                    if (strlen($no_kt_baru) == 1) {
                        $no_kt_baru_cek = "0$no_kt_baru";
                    }
                    else{
                        $no_kt_baru_cek = "$no_kt_baru";
                    }
                }
                
                $data = new KategoriModel();            
                $data->kd_kt = "1181$no_kt_baru_cek";
                $data->no_kt = $no_kt_baru_cek;
                $data->nm_kt = $request->nama;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }
        }
        else
        {
            $cekData = KategoriModel::where('id_kt', $request->id_kt)->first();
            if($cekData->nm_kt == $request->nama )
            {
                return response()->json(['status' => 3]);
            }  
            else
            {
                $jumlah=0;               
                if($cekData->nm_kt != $request->nama)
                {
                    $jumlah = KategoriModel::where('nm_kt', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                { 
                    $data = KategoriModel::where('id_kt', $request->id_kt)->first(); 
                    $data->nm_kt = $request->nama;
                    $data->user_id = $user_id;             
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = KategoriModel::where('id_kt',$request->id_kt)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = KategoriModel::where('id_kt', $request->id_kt)->first();
        $data->delete();
        return response()->json(['status' => 1]);
        /*$kd_kt = $data->kd_kt;
        $jumlah = SubKategoriModel::where('kd_kt', $kd_kt)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $data->delete();
            return response()->json(['status' => 1]);
        }*/
    }
}