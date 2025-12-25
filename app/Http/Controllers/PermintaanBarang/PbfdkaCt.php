<?php

namespace App\Http\Controllers\PermintaanBarang;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\PbfdModel;
use App\Models\PbfModel;
use App\Models\VBarangModel;
use App\Models\VPermintaanBarangFakultasModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbfdkaCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_pbf = Crypt::decryptString($encripted_id);

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
        ->join('fakultas','permintaan_barang_fakultas.id_fk','=','fakultas.id_fk')
        ->where('id_pbf',$id_pbf)->first();
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('PermintaanBarang.KasiLogistik.Fakultas.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbf' =>$cek_pbf]);
    }

    public function getItem(Request $request){
        $idItem = VBarangModel::where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        if($request->id_pbfd == "")
        {
            
        }   
        else
        {
            if($request->status_ajuan == "2")
            {
                $jumlah_Setujui = 0;
            }
            else
            {
                $jumlah_Setujui = $request->jumlah_setujui;
            }
            $datapbfd = PbfdModel::where('id_pbfd', $request->id_pbfd)->first(); 
            $datapbfd->jmlh_setuju_pbfd = $jumlah_Setujui;
            $datapbfd->status_pbfd = $request->status_ajuan;
            $datapbfd->ket_pbfd = "$request->ket";
            $datapbfd->user_id = $user_id;;
            $datapbfd->save();                            
            return response()->json(['status' => 1]);
        }
    }

    public function edit(Request $request)
    {   
        $data = PbfdModel::
        join('barang','permintaan_barang_fakultas_detail.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_pbfd',$request->id_pbfd)->first();
        return Response()->json($data);
    }
}
