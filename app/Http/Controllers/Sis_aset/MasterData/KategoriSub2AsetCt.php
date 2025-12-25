<?php

namespace App\Http\Controllers\Sis_aset\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class KategoriSub2AsetCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_kd_kt_sub = Crypt::decryptString($encripted_id);        
        $data_kategori_sub = AsetKategoriSubModel::
        join('aset_kategori','aset_kategori_sub.a_kd_kt','=','aset_kategori.a_kd_kt')
        ->where('a_kd_kt_sub', $a_kd_kt_sub)->first();
        $a_kd_kt_sub=$data_kategori_sub->a_kd_kt_sub;

        $a_kd_kt = Crypt::encryptString($data_kategori_sub->a_kd_kt);
        
        if(request()->ajax()) {
            return datatables()->of(AsetKategoriSub2Model::
            where('aset_kategori_sub_2.a_kd_kt_sub', $a_kd_kt_sub)
            ->orderBy('a_no_kt_sub_2')
            ->get())     
            ->addColumn('a_kd_kt_sub_2_en', function ($data) {
                $a_kd_kt_sub_2_en = Crypt::encryptString($data->a_kd_kt_sub_2);
                return $a_kd_kt_sub_2_en;
            })       
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.MasterData.Kategori.SubKategori.Sub2Kategori.index',['data_kategori_sub'=>$data_kategori_sub, 'encripted_id'=>$encripted_id, 'a_kd_kt'=>$a_kd_kt]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_kd_kt_sub = Crypt::decryptString($request->encripted_id);
        $data_kategori_sub = AsetKategoriSubModel::where('a_kd_kt_sub', $a_kd_kt_sub)->first();        
        if($request->a_id_kt_sub_2 == "")
        {   
            $jumlah = AsetKategoriSub2Model::where('a_kd_kt_sub', $data_kategori_sub->a_kd_kt_sub)->where('a_nm_kt_sub_2', $request->nama)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {                
                $jumlah = AsetKategoriSub2Model::where('a_kd_kt_sub', $data_kategori_sub->a_kd_kt_sub)->orderBy('a_no_kt_sub_2','desc')->count();
                if($jumlah==0)
                {
                    $a_no_kt_sub_2_baru_cek = "01";
                }
                else
                {
                    $baris = AsetKategoriSub2Model::where('a_kd_kt_sub', $data_kategori_sub->a_kd_kt_sub)->orderBy('a_no_kt_sub_2','desc')->first();
                    $a_no_kt_sub_2_baru = $baris->a_no_kt_sub_2 + 1;
                    if (strlen($a_no_kt_sub_2_baru) == 1) {
                        $a_no_kt_sub_2_baru_cek = "0$a_no_kt_sub_2_baru";
                    }
                    else{
                        $a_no_kt_sub_2_baru_cek = "$a_no_kt_sub_2_baru";
                    }
                }
                $data = new AsetKategoriSub2Model();
                $data->a_kd_kt_sub = $data_kategori_sub->a_kd_kt_sub;
                $data->a_kd_kt_sub_2 = "$data_kategori_sub->a_kd_kt_sub$a_no_kt_sub_2_baru_cek";
                $data->a_no_kt_sub_2 = $a_no_kt_sub_2_baru_cek;
                $data->a_nm_kt_sub_2 = $request->nama;            
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = AsetKategoriSub2Model::where('a_id_kt_sub_2', $request->a_id_kt_sub_2)->first();
            if($cekData->a_nm_kt_sub_2 == $request->nama)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->a_nm_kt_sub_2 != $request->nama)
                {
                    $jumlah = AsetKategoriSub2Model::where('a_kd_kt_sub', $data_kategori_sub->a_kd_kt_sub)->where('a_nm_kt_sub_2', $request->nama)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = AsetKategoriSub2Model::where('a_id_kt_sub_2', $request->a_id_kt_sub_2)->first();                   
                    $data->a_kd_kt_sub = $data_kategori_sub->a_kd_kt_sub;
                    $data->a_nm_kt_sub_2 = $request->nama;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetKategoriSub2Model::where('a_id_kt_sub_2',$request->a_id_kt_sub_2)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetKategoriSub2Model::where('a_id_kt_sub_2', $request->a_id_kt_sub_2)->first();  
        $a_kd_kt_sub_2 = $data->a_kd_kt_sub_2;
        
        $jumlah = AsetKategoriSub3Model::where('a_kd_kt_sub_2', $a_kd_kt_sub_2)->count();
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
