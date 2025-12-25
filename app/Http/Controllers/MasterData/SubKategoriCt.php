<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\SubSubKategoriModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class SubKategoriCt extends Controller
{
    public function index($encripted_id)
    {
        
        $id_kt = Crypt::decryptString($encripted_id);        
        $data_kategori = KategoriModel::where('id_kt', $id_kt)->first();
        $kd_kt=$data_kategori->kd_kt;
        
        if(request()->ajax()) {
            return datatables()->of(SubKategoriModel::
            leftjoin('v_lab_nilai3_subkategori','subkategori.kd_skt','=','v_lab_nilai3_subkategori.v_kd_skt')
            ->where('subkategori.kd_kt', $kd_kt)
            ->orderBy('no_skt')
            ->get())     
            ->addColumn('id_skt_en', function ($data) {
                $id_skt_en = Crypt::encryptString($data->id_skt);
                return $id_skt_en; 
            })       
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('MasterData.Kategori.SubKategori.index',['data_kategori'=>$data_kategori, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_kt = Crypt::decryptString($request->encripted_id);
        $data_kategori = KategoriModel::where('id_kt', $id_kt)->first();
        $kd_kt=$data_kategori->kd_kt;
        if($request->id_skt == "")
        {      
            $jumlah = SubKategoriModel::where('kd_kt', $kd_kt)->where('nm_skt', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {
                $jumlah = SubKategoriModel::where('kd_kt', $kd_kt)->orderBy('no_skt','desc')->count();
                if($jumlah==0)
                {
                    $no_skt_baru_cek = "01";
                }
                else
                {
                    $baris = SubKategoriModel::where('kd_kt', $kd_kt)->orderBy('no_skt','desc')->first();
                    $no_skt_baru = $baris->no_skt + 1;
                    if (strlen($no_skt_baru) == 1) {
                        $no_skt_baru_cek = "0$no_skt_baru";
                    }
                    else{
                        $no_skt_baru_cek = "$no_skt_baru";
                    }
                }

                $data = new SubKategoriModel();
                $data->kd_kt = $kd_kt;
                $data->kd_skt = "$kd_kt$no_skt_baru_cek";
                $data->no_skt = $no_skt_baru_cek;
                $data->nm_skt = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = SubKategoriModel::where('id_skt', $request->id_skt)->first();
            if($cekData->nm_skt == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->nm_skt != $request->nama)
                {
                    $jumlah = SubKategoriModel::where('kd_kt', $kd_kt)->where('nm_skt', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = SubKategoriModel::where('id_skt', $request->id_skt)->first();                   
                    $data->kd_kt = $kd_kt;
                    $data->nm_skt = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = SubKategoriModel::where('id_skt',$request->id_skt)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = SubKategoriModel::where('id_skt', $request->id_skt)->first();  
        $kd_skt = $data->kd_skt;
        $jumlah = SubSubKategoriModel::where('kd_skt', $kd_skt)->count();
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
