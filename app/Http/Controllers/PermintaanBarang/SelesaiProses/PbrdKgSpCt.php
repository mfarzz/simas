<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\PbrModel;
use App\Models\VBarangModel;
use App\Models\VPermintaanBarangRektoratModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbrdKgSpCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_pbr = Crypt::decryptString($encripted_id);

        if(request()->ajax()) {
            return datatables()->of(VPermintaanBarangRektoratModel::
            where('id_pbr', $id_pbr)
            ->orderby('id_pbrd')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $cek_pbr = PbrModel::
        join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')
        ->join('unit_rektorat','permintaan_barang_rektorat.id_ur','=','unit_rektorat.id_ur')
        ->where('id_pbr',$id_pbr)->first();
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('PermintaanBarang.KepalaGudang.Rektorat.SelesaiProses.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbr' =>$cek_pbr]);
    }
}
