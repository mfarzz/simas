<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\PbfdModel;
use App\Models\PbfModel;
use App\Models\User;
use App\Models\VBarangModel;
use App\Models\VPermintaanBarangFakultasModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbfdSpCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_pbf = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VPermintaanBarangFakultasModel::
            where('id_pbf', $id_pbf)
            ->orderby('id_pbfd')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $cek_pbf = PbfModel::
        join('permintaan_barang_status','permintaan_barang_fakultas.id_pbs','=','permintaan_barang_status.id_pbs')
        ->where('id_pbf',$id_pbf)->first();
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('PermintaanBarang.Fakultas.SelesaiProses.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbf' =>$cek_pbf]);
    }
}
