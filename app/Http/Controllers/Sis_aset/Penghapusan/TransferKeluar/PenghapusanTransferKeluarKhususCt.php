<?php

namespace App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetHapusItemModel;
use App\Models\AsetHapusModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PenghapusanTransferKeluarKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if(request()->ajax()) {
            return datatables()->of(AsetHapusModel::
            join('aset_barang', 'aset_hapus.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->join('aset_lokasi', 'aset_hapus.a_kd_al_tujuan','=','aset_lokasi.a_kd_al')
            ->where('a_kd_ajkt','C02')
            ->get())
            ->addColumn('a_id_ah_en', function ($data) {
                $a_id_ah_en = Crypt::encryptString($data->a_id_ah);
                return $a_id_ah_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        $daftar_lokasi = AsetLokasiModel::orderby('a_kd_al')->get();
        return view('Sis_aset.Penghapusan.TransferKeluar.Khusus.index',['daftar_kategori'=> $daftar_kategori, 'daftar_lokasi'=> $daftar_lokasi]);
    }
}
