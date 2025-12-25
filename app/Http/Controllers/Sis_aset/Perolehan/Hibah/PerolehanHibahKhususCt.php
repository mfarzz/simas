<?php

namespace App\Http\Controllers\Sis_aset\Perolehan\Hibah;

use App\Http\Controllers\Controller;
use App\Models\AsetBarangModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PerolehanHibahKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        if(request()->ajax()) {
            return datatables()->of(AsetPerolehanModel::
            join('aset_lokasi', 'aset_perolehan.a_kd_al','=','aset_lokasi.a_kd_al')
            ->join('aset_barang', 'aset_perolehan.a_kd_brg','=','aset_barang.a_kd_brg')
            ->join('aset_kategori_sub_3', 'aset_kategori_sub_3.a_kd_kt_sub_3','=','aset_barang.a_kd_kt_sub_3')
            ->join('aset_kategori_sub_2', 'aset_kategori_sub_2.a_kd_kt_sub_2','=','aset_kategori_sub_3.a_kd_kt_sub_2')
            ->join('aset_kategori_sub', 'aset_kategori_sub.a_kd_kt_sub','=','aset_kategori_sub_2.a_kd_kt_sub')
            ->join('aset_kategori', 'aset_kategori.a_kd_kt','=','aset_kategori_sub.a_kd_kt')
            ->where('a_kd_ajkt','A04')
            ->get())
            ->addColumn('a_id_ap_en', function ($data) {
                $a_id_ap_en = Crypt::encryptString($data->a_id_ap);
                return $a_id_ap_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = AsetKategoriModel::orderby('a_kd_kt')->get();
        return view('Sis_aset.Perolehan.Hibah.Khusus.index',['daftar_kategori'=> $daftar_kategori]);
    }
}
