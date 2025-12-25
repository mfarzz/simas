<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetKategoriSub3Model;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangAsetCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_kd_kt_sub_3 = Crypt::decryptString($encripted_id);        
        $data_kategori_sub_3 = AsetKategoriSub3Model::
        join('aset_kategori_sub_2','aset_kategori_sub_3.a_kd_kt_sub_2','=','aset_kategori_sub_2.a_kd_kt_sub_2')
        ->join('aset_kategori_sub','aset_kategori_sub_2.a_kd_kt_sub','=','aset_kategori_sub.a_kd_kt_sub')
        ->join('aset_kategori','aset_kategori_sub.a_kd_kt','=','aset_kategori.a_kd_kt')
        ->where('aset_kategori_sub_3.a_kd_kt_sub_3', $a_kd_kt_sub_3)->first();
        $a_kd_kt_sub_3=$data_kategori_sub_3->a_kd_kt_sub_3;

        $a_kd_kt_sub_2 = Crypt::encryptString($data_kategori_sub_3->a_kd_kt_sub_2);
        
        if(request()->ajax()) {
            return datatables()->of(AsetBarangModel::
            where('aset_barang.a_kd_kt_sub_3', $a_kd_kt_sub_3)
            ->orderBy('a_no_brg')
            ->get())     
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Kategori.SubKategori.Barang.index',['data_kategori_sub_3'=>$data_kategori_sub_3, 'encripted_id'=>$encripted_id, 'a_kd_kt_sub_2'=>$a_kd_kt_sub_2]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_kd_kt_sub_3 = Crypt::decryptString($request->encripted_id);
        $data_kategori_sub_3 = AsetKategoriSub3Model::where('a_kd_kt_sub_3', $a_kd_kt_sub_3)->first();        
        if($request->a_id_brg == "")
        {               
            $jumlah = AsetBarangModel::where('a_kd_kt_sub_3', $data_kategori_sub_3->a_kd_kt_sub_3)->where('a_nm_brg', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {        
                $jumlah = AsetBarangModel::where('a_kd_kt_sub_3', $data_kategori_sub_3->a_kd_kt_sub_3)->orderBy('a_no_brg','desc')->count();
                if($jumlah==0)
                {
                    $a_no_brg_baru_cek = "01";
                }
                else
                {
                    $baris = AsetBarangModel::where('a_kd_kt_sub_3', $data_kategori_sub_3->a_kd_kt_sub_3)->orderBy('a_no_brg','desc')->first();
                    $a_no_brg_baru = $baris->a_no_brg + 1;
                    if (strlen($a_no_brg_baru) == 1) {
                        $a_no_brg_baru_cek = "0$a_no_brg_baru";
                    }
                    else{
                        $a_no_brg_baru_cek = "$a_no_brg_baru";
                    }
                }
                
                $data = new AsetBarangModel();
                $data->a_kd_kt_sub_3 = $data_kategori_sub_3->a_kd_kt_sub_3;
                $data->a_kd_brg = "$data_kategori_sub_3->a_kd_kt_sub_3$a_no_brg_baru_cek";
                $data->a_no_brg = $a_no_brg_baru_cek;
                $data->a_nm_brg = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = AsetBarangModel::where('a_id_brg', $request->a_id_brg)->first();
            if($cekData->a_nm_brg == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->a_nm_brg != $request->nama)
                {
                    $jumlah = AsetBarangModel::where('a_kd_kt_sub_3', $data_kategori_sub_3->a_kd_kt_sub_3)->where('a_nm_brg', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = AsetBarangModel::where('a_id_brg', $request->a_id_brg)->first();                   
                    $data->a_kd_kt_sub_3 = $data_kategori_sub_3->a_kd_kt_sub_3;
                    $data->a_nm_brg = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetBarangModel::where('a_id_brg',$request->a_id_brg)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetBarangModel::where('a_id_brg', $request->a_id_brg)->first();  
        $a_kd_brg = $data->a_kd_brg;

        $data->delete();  
        return response()->json(['status' => 1]);
        
        /*$jumlah = AsetKategoriSub4Model::where('a_kd_kt_sub_3', $a_kd_kt_sub_3)->count();
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
