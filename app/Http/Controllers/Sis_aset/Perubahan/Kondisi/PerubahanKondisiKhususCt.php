<?php

namespace App\Http\Controllers\Sis_aset\Perubahan\Kondisi;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetUbahItemModel;
use App\Models\AsetUbahModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerubahanKondisiKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if(request()->ajax()) {
            return datatables()->of(AsetUbahModel::
            join('aset_lokasi', 'aset_ubah.a_kd_al','=','aset_lokasi.a_kd_al')
            ->join('aset_barang', 'aset_ubah.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->where('a_kd_ajkt','B03')
            ->get())
            ->addColumn('a_id_au_en', function ($data) {
                $a_id_au_en = Crypt::encryptString($data->a_id_au);
                return $a_id_au_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        return view('Sis_aset.Perubahan.Kondisi.Khusus.index',['daftar_kategori'=> $daftar_kategori]);
    }
}
