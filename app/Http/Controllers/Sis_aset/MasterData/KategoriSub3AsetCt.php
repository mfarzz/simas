<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSub4Model;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class KategoriSub3AsetCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_kd_kt_sub_2 = Crypt::decryptString($encripted_id);        
        $data_kategori_sub_2 = AsetKategoriSub2Model::
        join('aset_kategori_sub','aset_kategori_sub_2.a_kd_kt_sub','=','aset_kategori_sub.a_kd_kt_sub')
        ->join('aset_kategori','aset_kategori_sub.a_kd_kt','=','aset_kategori.a_kd_kt')
        ->where('aset_kategori_sub_2.a_kd_kt_sub_2', $a_kd_kt_sub_2)->first();
        $a_kd_kt_sub_2=$data_kategori_sub_2->a_kd_kt_sub_2;

        $a_kd_kt_sub = Crypt::encryptString($data_kategori_sub_2->a_kd_kt_sub);
        
        if(request()->ajax()) {
            return datatables()->of(AsetKategoriSub3Model::
            where('aset_kategori_sub_3.a_kd_kt_sub_2', $a_kd_kt_sub_2)
            ->orderBy('a_no_kt_sub_3')
            ->get())     
            ->addColumn('a_kd_kt_sub_3_en', function ($data) {
                $a_kd_kt_sub_3_en = Crypt::encryptString($data->a_kd_kt_sub_3);
                return $a_kd_kt_sub_3_en;
            })       
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Kategori.SubKategori.Sub3Kategori.index',['data_kategori_sub_2'=>$data_kategori_sub_2, 'encripted_id'=>$encripted_id, 'a_kd_kt_sub'=>$a_kd_kt_sub]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_kd_kt_sub_2 = Crypt::decryptString($request->encripted_id);
        $data_kategori_sub_2 = AsetKategoriSub2Model::where('a_kd_kt_sub_2', $a_kd_kt_sub_2)->first();        
        if($request->a_id_kt_sub_3 == "")
        {               
            $jumlah = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $data_kategori_sub_2->a_kd_kt_sub_2)->where('a_nm_kt_sub_3', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {        
                $jumlah = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $data_kategori_sub_2->a_kd_kt_sub_2)->orderBy('a_no_kt_sub_3','desc')->count();
                if($jumlah==0)
                {
                    $a_no_kt_sub_3_baru_cek = "01";
                }
                else
                {
                    $baris = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $data_kategori_sub_2->a_kd_kt_sub_2)->orderBy('a_no_kt_sub_3','desc')->first();
                    $a_no_kt_sub_3_baru = $baris->a_no_kt_sub_3 + 1;
                    if (strlen($a_no_kt_sub_3_baru) == 1) {
                        $a_no_kt_sub_3_baru_cek = "0$a_no_kt_sub_3_baru";
                    }
                    else{
                        $a_no_kt_sub_3_baru_cek = "$a_no_kt_sub_3_baru";
                    }
                }
                
                $data = new AsetKategoriSub3Model();
                $data->a_kd_kt_sub_2 = $data_kategori_sub_2->a_kd_kt_sub_2;
                $data->a_kd_kt_sub_3 = "$data_kategori_sub_2->a_kd_kt_sub_2$a_no_kt_sub_3_baru_cek";
                $data->a_no_kt_sub_3 = $a_no_kt_sub_3_baru_cek;
                $data->a_nm_kt_sub_3 = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = AsetKategoriSub3Model::where('a_id_kt_sub_3', $request->a_id_kt_sub_3)->first();
            if($cekData->a_nm_kt_sub_3 == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->a_nm_kt_sub_3 != $request->nama)
                {
                    $jumlah = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $data_kategori_sub_2->a_kd_kt_sub_2)->where('a_nm_kt_sub_3', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = AsetKategoriSub3Model::where('a_id_kt_sub_3', $request->a_id_kt_sub_3)->first();                   
                    $data->a_kd_kt_sub_2 = $data_kategori_sub_2->a_kd_kt_sub_2;
                    $data->a_nm_kt_sub_3 = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetKategoriSub3Model::where('a_id_kt_sub_3',$request->a_id_kt_sub_3)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetKategoriSub3Model::where('a_id_kt_sub_3', $request->a_id_kt_sub_3)->first();  
        $a_kd_kt_sub_3 = $data->a_kd_kt_sub_3;
        
        $jumlah = AsetKategoriSub4Model::where('a_kd_kt_sub_3', $a_kd_kt_sub_3)->count();
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
