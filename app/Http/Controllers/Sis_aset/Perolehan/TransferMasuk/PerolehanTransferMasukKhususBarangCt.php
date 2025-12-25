<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk;

use App\Http\Controllers\Controller;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanTransferMasukKhususBarangCt extends Controller
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

        $data_asal = AsetPerolehanModel::
        join('aset_lokasi', 'aset_perolehan.a_kdsppa_asal','=','aset_lokasi.a_kd_al')
        ->where('a_id_ap', $a_id_ap)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetPerolehanItemModel::
            where('aset_perolehan_item.a_id_ap', $a_id_ap)
            ->orderBy('a_no_api')
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Perolehan.TransferMasuk.Khusus.Barang.index',['data_aset'=>$data_aset, 'data_asal'=>$data_asal, 'encripted_id'=>$encripted_id]);
    }
}
