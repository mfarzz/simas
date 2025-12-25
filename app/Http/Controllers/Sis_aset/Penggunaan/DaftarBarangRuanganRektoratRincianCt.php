<?php

namespace App\Http\Controllers\Sis_aset\Penggunaan;

use App\Http\Controllers\Controller;
use App\Models\AsetDaftarRuanganRektoratDetailModel;
use App\Models\AsetDaftarRuanganRektoratModel;
use App\Models\AsetPembelianItemModel;
use App\Models\AsetPembelianModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class DaftarBarangRuanganRektoratRincianCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_id_adrr = Crypt::decryptString($encripted_id);        
        $data_aset = AsetDaftarRuanganRektoratModel::
        join('aset_barang', 'aset_daftar_ruangan_rektorat.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->join('aset_ruangan_rektorat', 'aset_daftar_ruangan_rektorat.a_id_arr','=','aset_ruangan_rektorat.a_id_arr')
        ->where('a_id_adrr', $a_id_adrr)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetDaftarRuanganRektoratDetailModel::
            join('aset_pembelian_item', 'aset_daftar_ruangan_rektorat_detail.a_kd_brg_api','=','aset_pembelian_item.a_kd_brg_api')
            ->where('aset_daftar_ruangan_rektorat_detail.a_id_adrr', $a_id_adrr)
            
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Penggunaan.DaftarRuangan.Rektorat.Barang.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
}
