<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSubModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class KategoriSubAsetCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_kd_kt = Crypt::decryptString($encripted_id);        
        $data_kategori = AsetKategoriModel::where('a_kd_kt', $a_kd_kt)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetKategoriSubModel::
            where('aset_kategori_sub.a_kd_kt', $a_kd_kt)
            ->orderBy('a_no_kt_sub')
            ->get())     
            ->addColumn('a_kd_kt_sub_en', function ($data) {
                $a_kd_kt_sub_en = Crypt::encryptString($data->a_kd_kt_sub);
                return $a_kd_kt_sub_en; 
            })       
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Kategori.SubKategori.index',['data_kategori'=>$data_kategori, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_kd_kt = Crypt::decryptString($request->encripted_id);
        $data_kategori = AsetKategoriModel::where('a_kd_kt', $a_kd_kt)->first();        
        if($request->a_id_kt_sub == "")
        {      
            $jumlah = AsetKategoriSubModel::where('a_kd_kt', $a_kd_kt)->where('a_nm_kt_sub', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {
                $jumlah = AsetKategoriSubModel::where('a_kd_kt', $a_kd_kt)->orderBy('a_no_kt_sub','desc')->count();
                if($jumlah==0)
                {
                    $a_no_kt_sub_baru_cek = "01";
                }
                else
                {
                    $baris = AsetKategoriSubModel::where('a_kd_kt', $a_kd_kt)->orderBy('a_no_kt_sub','desc')->first();
                    $a_no_kt_sub_baru = $baris->a_no_kt_sub + 1;
                    if (strlen($a_no_kt_sub_baru) == 1) {
                        $a_no_kt_sub_baru_cek = "0$a_no_kt_sub_baru";
                    }
                    else{
                        $a_no_kt_sub_baru_cek = "$a_no_kt_sub_baru";
                    }
                }

                $data = new AsetKategoriSubModel();
                $data->a_kd_kt = $a_kd_kt;
                $data->a_kd_kt_sub = "$a_kd_kt$a_no_kt_sub_baru_cek";
                $data->a_no_kt_sub = $a_no_kt_sub_baru_cek;
                $data->a_nm_kt_sub = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = AsetKategoriSubModel::where('a_id_kt_sub', $request->a_id_kt_sub)->first();
            if($cekData->a_nm_kt_sub == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->a_nm_kt_sub != $request->nama)
                {
                    $jumlah = AsetKategoriSubModel::where('a_kd_kt', $a_kd_kt)->where('a_nm_kt_sub', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = AsetKategoriSubModel::where('a_id_kt_sub', $request->a_id_kt_sub)->first();                   
                    $data->a_kd_kt = $a_kd_kt;
                    $data->a_nm_kt_sub = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetKategoriSubModel::where('a_id_kt_sub',$request->a_id_kt_sub)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetKategoriSubModel::where('a_id_kt_sub', $request->a_id_kt_sub)->first();  
        $a_kd_kt_sub = $data->a_kd_kt_sub;
        
        $jumlah = AsetKategoriSub2Model::where('a_kd_kt_sub', $a_kd_kt_sub)->count();
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
