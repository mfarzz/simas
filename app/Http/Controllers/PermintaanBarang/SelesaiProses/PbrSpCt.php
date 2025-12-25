<?php

namespace App\Http\Controllers\PermintaanBarang\SelesaiProses;

use App\Http\Controllers\Controller;
use App\Models\PbrModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class PbrSpCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(PbrModel::
            join('permintaan_barang_status','permintaan_barang_rektorat.id_pbs','=','permintaan_barang_status.id_pbs')
            ->where('permintaan_barang_rektorat.proses_pbr','2')
            ->where('permintaan_barang_rektorat.id_ur',$datarektorat->id_ur)
            ->whereYear('permintaan_barang_rektorat.tgl_pbr',$tahun_anggaran)
            ->get())
            ->addColumn('id_pbr', function ($data) {
                return $data->id_pbr; 
            })
            ->addColumn('id_pbr_en', function ($data) {
                $id_pbr_en = Crypt::encryptString($data->id_pbr);
                return $id_pbr_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('PermintaanBarang.Rektorat.SelesaiProses.index');
    }
}