<?php

namespace App\Http\Controllers\PermintaanBarang;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\PbrdModel;
use App\Models\PbrModel;
use App\Models\VBarangModel;
use App\Models\VPermintaanBarangRektoratModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbrdKaCt extends Controller
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
        return view('PermintaanBarang.KasiLogistik.Rektorat.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbr' =>$cek_pbr]);
    }

    public function getItem(Request $request){
        $idItem = VBarangModel::where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_id = auth()->user()->id;
        if($request->id_pbrd == "")
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
            $datapbrd = PbrdModel::where('id_pbrd', $request->id_pbrd)->first(); 
            $datapbrd->jmlh_setuju_pbrd = $jumlah_Setujui;
            $datapbrd->status_pbrd = $request->status_ajuan;
            $datapbrd->ket_pbrd = "$request->ket";
            $datapbrd->user_id = $user_id;;
            $datapbrd->save();                            
            return response()->json(['status' => 1]);
        }
    }

    public function edit(Request $request)
    {   
        $data = PbrdModel::
        join('barang','permintaan_barang_rektorat_detail.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_pbrd',$request->id_pbrd)->first();
        return Response()->json($data);
    }
}
