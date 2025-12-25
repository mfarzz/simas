<?php

namespace App\Http\Controllers\PermintaanBarang;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\PbrdModel;
use App\Models\PbrModel;
use App\Models\User;
use App\Models\VBarangModel;
use App\Models\VPermintaanBarangRektoratModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class PbrdCt extends Controller
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
        ->where('id_pbr',$id_pbr)->first();
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('PermintaanBarang.Rektorat.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbr' =>$cek_pbr]);
    }

    public function getItem(Request $request){
        $idItem = VBarangModel::where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_pbr = Crypt::decryptString($request->encripted_id);   
        if($request->idBarang == "")
        {
            $idItem = $request->idItem;
        }
        else
        {
            $idItem = $request->idBarang;
        }
        if($request->idItem == "" and $request->idBarang == "")
        {
            return response()->json(['status' => 5]);
        }
        else
        {
            $cek_pbr = PbrModel::where('id_pbr',$id_pbr)->first();       
            if($request->id_pbrd == "")
            {
                $jumlah_brg = PbrdModel::where('kd_brg', $idItem)->where('id_pbr', $cek_pbr->id_pbr)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {       
                    $datapbrd = new PbrdModel();
                    $datapbrd->id_pbr = $id_pbr;
                    $datapbrd->kd_brg = $idItem;
                    $datapbrd->jmlh_ajuan_pbrd = $request->jumlah;
                    $datapbrd->jmlh_setuju_pbrd = 0;
                    $datapbrd->status_pbrd = 0;
                    $datapbrd->ket_pbrd = "";
                    $datapbrd->user_id = $user_id;
                    $datapbrd->save();
                    return response()->json(['status' => 1]);
                }
            }   
            else
            {
                $cekData = PbrdModel::where('id_pbrd', $request->id_pbrd)->first();
                if($cekData->kd_brg == $idItem)
                {
                    $datapbrd = PbrdModel::where('id_pbrd', $request->id_pbrd)->first(); 
                    $datapbrd->id_pbr = $id_pbr;
                    $datapbrd->kd_brg = $idItem;
                    $datapbrd->jmlh_ajuan_pbrd = $request->jumlah;
                    $datapbrd->jmlh_setuju_pbrd = 0;
                    $datapbrd->status_pbrd = 0;
                    $datapbrd->ket_pbrd = "";
                    $datapbrd->user_id = $user_id;;
                    $datapbrd->save();
                    return response()->json(['status' => 1]);
                }
                else
                {
                    $jumlah_brg = PbrdModel::where('kd_brg', $idItem)->where('id_pbr', $cek_pbr->id_pbr)->count();
                    if($jumlah_brg>0)
                    {
                        return response()->json(['status' => 4]);
                    }
                    else
                    {
                        $datapbrd = PbrdModel::where('id_pbrd', $request->id_pbrd)->first(); 
                        $datapbrd->id_pbr = $id_pbr;
                        $datapbrd->kd_brg = $idItem;
                        $datapbrd->jmlh_ajuan_pbrd = $request->jumlah;
                        $datapbrd->jmlh_setuju_pbrd = 0;
                        $datapbrd->status_pbrd = 0;
                        $datapbrd->ket_pbrd = "";
                        $datapbrd->user_id = $user_id;;
                        $datapbrd->save();                            
                        return response()->json(['status' => 1]);
                    }
                }
            } 
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

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');        
        $data = PbrdModel::where('id_pbrd', $request->id_pbrd)->first();            
        $data->delete();
        return response()->json(['status' => 1]);
    }
}
