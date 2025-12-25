<?php

namespace App\Http\Controllers\PermintaanBarang;

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

class PbfdCt extends Controller
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
        return view('PermintaanBarang.Fakultas.Daftar.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_pbf' =>$cek_pbf]);
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
        $id_pbf = Crypt::decryptString($request->encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();     
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
            $cek_pbf = PbfModel::where('id_pbf',$id_pbf)->first();       
            if($request->id_pbfd == "")
            {
                $jumlah_brg = PbfdModel::where('kd_brg', $idItem)->where('id_pbf', $cek_pbf->id_pbf)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {       
                    $datapbfd = new PbfdModel();
                    $datapbfd->id_pbf = $id_pbf;
                    $datapbfd->kd_brg = $idItem;
                    $datapbfd->jmlh_ajuan_pbfd = $request->jumlah;
                    $datapbfd->jmlh_setuju_pbfd = 0;
                    $datapbfd->status_pbfd = 0;
                    $datapbfd->ket_pbfd = "";
                    $datapbfd->user_id = $user_id;
                    $datapbfd->save();
                    return response()->json(['status' => 1]);
                }
            }   
            else
            {
                $cekData = PbfdModel::where('id_pbfd', $request->id_pbfd)->first();
                if($cekData->kd_brg == $idItem)
                {
                    $datapbfd = PbfdModel::where('id_pbfd', $request->id_pbfd)->first(); 
                    $datapbfd->id_pbf = $id_pbf;
                    $datapbfd->kd_brg = $idItem;
                    $datapbfd->jmlh_ajuan_pbfd = $request->jumlah;
                    $datapbfd->jmlh_setuju_pbfd = 0;
                    $datapbfd->status_pbfd = 0;
                    $datapbfd->ket_pbfd = "";
                    $datapbfd->user_id = $user_id;;
                    $datapbfd->save();
                    return response()->json(['status' => 1]);
                }
                else
                {
                    $jumlah_brg = PbfdModel::where('kd_brg', $idItem)->where('id_pbf', $cek_pbf->id_pbf)->count();
                    if($jumlah_brg>0)
                    {
                        return response()->json(['status' => 4]);
                    }
                    else
                    {

                        $datapbfd = PbfdModel::where('id_pbfd', $request->id_pbfd)->first(); 
                        $datapbfd->id_pbf = $id_pbf;
                        $datapbfd->kd_brg = $idItem;
                        $datapbfd->jmlh_ajuan_pbfd = $request->jumlah;
                        $datapbfd->jmlh_setuju_pbfd = 0;
                        $datapbfd->status_pbfd = 0;
                        $datapbfd->ket_pbfd = "";
                        $datapbfd->user_id = $user_id;;
                        $datapbfd->save();                            
                        return response()->json(['status' => 1]);
                    }
                }
            } 
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

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');        
        $data = PbfdModel::where('id_pbfd', $request->id_pbfd)->first();            
        $data->delete();
        return response()->json(['status' => 1]);
    }
}
