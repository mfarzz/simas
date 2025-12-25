<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\PenyelesaianLangsung;

use App\Http\Controllers\Controller;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanPenyelesaianLangsungFakultasKhususRincianCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_id_ap = Crypt::decryptString($encripted_id);        
        $data_aset = AsetPerolehanModel::
        join('aset_barang', 'aset_perolehan.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->where('a_id_ap', $a_id_ap)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetPerolehanRincianModel::
            where('aset_perolehan_rincian.a_id_ap', $a_id_ap)
            ->orderBy('a_tgl_sp2d')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Perolehan.PenyelesaianLangsung.Khusus.Fakultas.Rincian.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $a_id_ap = Crypt::decryptString($request->encripted_id);
        $data_aset = AsetPerolehanModel::where('a_id_ap', $a_id_ap)->first();        
        if($request->a_id_apr == "")
        {   
            $jumlah = AsetPerolehanRincianModel::where('a_id_ap', $data_aset->a_id_ap)->where('a_no_sp2d', $request->no_sp2d)->count();
            if($jumlah>0)
            {
                return response()->json(['status' => 12]);
            }
            else
            {                
                $data = new AsetPerolehanRincianModel();
                $data->a_id_ap = $a_id_ap;
                $data->a_no_sp2d    = $request->no_sp2d;
                $data->a_tgl_sp2d   = $request->tgl_sp2d;
                $data->a_kl_belanja_apr = $request->kel_belanja;
                $data->a_nilai_spm = $request->nilai_spm;
                $data->user_id = $user_id;
                $data->save();
                return response()->json(['status' => 1]);
            }            
        }
        else
        {
            $cekData = AsetPerolehanRincianModel::where('a_id_apr', $request->a_id_apr)->first();
            if($cekData->a_no_sp2d == $request->no_sp2d and $cekData->a_tgl_sp2d == $request->tgl_sp2d and $cekData->a_kl_belanja_apr == $request->kel_belanja and $cekData->a_nilai_spm == $request->nilai_spm)
            {
                return response()->json(['status' => 3]);
            }  
            else
            {           
                $jumlah=0;
                if($cekData->a_no_sp2d != $request->no_sp2d)
                {
                    $jumlah = AsetPerolehanRincianModel::where('a_id_ap', $data_aset->a_id_ap)->where('a_no_sp2d', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 12]);
                    }
                }
                if($jumlah==0)
                {                    
                    $data = AsetPerolehanRincianModel::where('a_id_apr', $request->a_id_apr)->first();                   
                    $data->a_no_sp2d    = $request->no_sp2d;
                    $data->a_tgl_sp2d   = $request->tgl_sp2d;
                    $data->a_kl_belanja_apr = $request->kel_belanja;
                    $data->a_nilai_spm = $request->nilai_spm;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }
    public function edit(Request $request)
    {   
        $data = AsetPerolehanRincianModel::where('a_id_apr',$request->a_id_apr)->first();
        return Response()->json($data);
    }
    public function destroy(Request $request)
    {
        $data = AsetPerolehanRincianModel::where('a_id_apr', $request->a_id_apr)->first();  
        $data->delete();  
        return response()->json(['status' => 1]);
    }
}
