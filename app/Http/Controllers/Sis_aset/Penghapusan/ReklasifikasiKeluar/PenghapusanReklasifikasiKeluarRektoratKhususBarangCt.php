<?php

namespace App\Http\Controllers\Sis_aset\Penghapusan\ReklasifikasiKeluar;

use App\Http\Controllers\Controller;
use App\Models\AsetHapusItemModel;
use App\Models\AsetHapusModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetUbahItemModel;
use App\Models\AsetUbahModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PenghapusanReklasifikasiKeluarRektoratKhususBarangCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_id_ah = Crypt::decryptString($encripted_id);        
        $data_aset = AsetHapusModel::
        join('aset_barang', 'aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->where('a_id_ah', $a_id_ah)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetHapusItemModel::
            join('aset_hapus','aset_hapus_item.a_id_ah','=','aset_hapus.a_id_ah')
            ->join('aset_barang','aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
            ->where('aset_hapus_item.a_id_ah', $a_id_ah)
            ->orderBy('a_no_api')
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Penghapusan.ReklasifikasiKeluar.Khusus.Rektorat.Barang.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
}
