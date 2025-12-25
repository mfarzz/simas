<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\Pembelian;

use App\Http\Controllers\Controller;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanPembelianKhususRincianCt extends Controller
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
        return view('Sis_aset.Perolehan.Pembelian.Khusus.Rincian.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
}
