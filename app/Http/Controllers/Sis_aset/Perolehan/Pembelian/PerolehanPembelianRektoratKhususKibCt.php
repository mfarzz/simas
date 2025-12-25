<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\Pembelian;

use App\Http\Controllers\Controller;
use App\Models\AsetMilikModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianKibModel;
use App\Models\AsetPerolehanRincianModel;
use App\Models\AsetStatusDigunakanModel;
use App\Models\RefKotaModel;
use App\Models\RefProvinsiModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanPembelianRektoratKhususKibCt extends Controller
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
            return datatables()->of(AsetPerolehanRincianKibModel::
            join('aset_perolehan_item','aset_perolehan_rincian_kib.a_kd_brg_api','=','aset_perolehan_item.a_kd_brg_api')
            ->join('aset_perolehan','aset_perolehan_item.a_id_ap','=','aset_perolehan.a_id_ap')
            ->where('aset_perolehan_rincian_kib.a_id_ap', $a_id_ap)
            ->orderBy('a_no_aprk')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $data_provinsi = RefProvinsiModel::get();
        $data_dok_kepemilikan = AsetMilikModel::get();
        $data_status_digunakan = AsetStatusDigunakanModel::get();
        return view('Sis_aset.Perolehan.Pembelian.Khusus.Rektorat.Kib.index',['data_aset'=>$data_aset, 'data_provinsi'=>$data_provinsi, 'data_dok_kepemilikan' => $data_dok_kepemilikan, 'data_status_digunakan' => $data_status_digunakan, 'encripted_id'=>$encripted_id]);
    }

    public function getSubkategori(Request $request){
        $idKota = RefKotaModel::where('kd_rprov', $request->kota)->pluck('kd_rkot','nm_rkot');
        return response()->json($idKota);
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
        }
        else
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
    public function edit(Request $request)
    {   
        $data = AsetPerolehanRincianKibModel::where('a_id_aprk',$request->a_id_aprk)->first();
        return Response()->json($data);
    }
}
