<?php

namespace App\Http\Controllers\Sis_aset\Perubahan\Koreksi;

use App\Http\Controllers\Controller;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetUbahItemModel;
use App\Models\AsetUbahModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerubahanKoreksiRektoratKhususBarangCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_id_au = Crypt::decryptString($encripted_id);        
        $data_aset = AsetUbahModel::
        join('aset_barang', 'aset_ubah.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->where('a_id_au', $a_id_au)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetUbahItemModel::
            where('aset_ubah_item.a_id_au', $a_id_au)
            ->orderBy('a_no_api')
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Perubahan.Koreksi.Khusus.Rektorat.Barang.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
}
