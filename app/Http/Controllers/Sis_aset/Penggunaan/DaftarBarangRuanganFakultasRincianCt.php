<?php

namespace App\Http\Controllers\Sis_aset\Penggunaan;

use App\Http\Controllers\Controller;
use App\Models\AsetDaftarRuanganFakultasDetailModel;
use App\Models\AsetDaftarRuanganFakultasModel;
use App\Models\AsetPembelianItemModel;
use App\Models\AsetPembelianModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class DaftarBarangRuanganFakultasRincianCt extends Controller
{
    public function index($encripted_id)
    {        
        $a_id_adrf = Crypt::decryptString($encripted_id);        
        $data_aset = AsetDaftarRuanganFakultasModel::
        join('aset_barang', 'aset_daftar_ruangan_fakultas.a_kd_brg','=','aset_barang.a_kd_brg')
        ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
        ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
        ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
        ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
        ->join('aset_ruangan_fakultas', 'aset_daftar_ruangan_fakultas.a_id_arf','=','aset_ruangan_fakultas.a_id_arf')
        ->where('a_id_adrf', $a_id_adrf)->first();
        
        if(request()->ajax()) {
            return datatables()->of(AsetDaftarRuanganFakultasDetailModel::
            join('aset_pembelian_item', 'aset_daftar_ruangan_fakultas_detail.a_kd_brg_api','=','aset_pembelian_item.a_kd_brg_api')
            ->where('aset_daftar_ruangan_fakultas_detail.a_id_adrf', $a_id_adrf)
            
            ->get())
            ->addIndexColumn()
            ->make(true);
        }
        return view('Sis_aset.Penggunaan.DaftarRuangan.Fakultas.Barang.index',['data_aset'=>$data_aset, 'encripted_id'=>$encripted_id]);
    }
}
