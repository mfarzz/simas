<?php

namespace App\Http\Controllers\OpnameFisik\RumahSakit;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\OpsikRumahSakitModel;
use App\Models\OpsikUrsDetModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VStokBarangMasukRektoratSemuaModel;
use App\Models\VStokBarangMasukRumahSakitSemuaModel;
use App\Models\VStokBrgMasukRektoratTotalModel;
use App\Models\VStokBrgMasukRumahSakitTotalModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikUrsDetCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_opurs = Crypt::decryptString($encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(OpsikUrsDetModel::
            join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
            ->join('barang','opsik_rumah_sakit_detail.kd_brg','=','barang.kd_brg')
            ->where('opsik_rumah_sakit_detail.id_opurs', $id_opurs)
            ->orderby('opsik_rumah_sakit_detail.id_opurs')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_opurs = OpsikRumahSakitModel::where('id_opurs',$id_opurs)->first();
        return view('OpnameFisik.RumahSakit.Detail.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'cek_opurs' =>$cek_opurs]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();

        $idItem = VStokBarangMasukRumahSakitSemuaModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_opurs = Crypt::decryptString($request->encripted_id);
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
        ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
        ->where('users.id', $user_id)->first();
        $cek_opurs = OpsikRumahSakitModel::where('id_opurs',$id_opurs)->first();
        if($request->id_opursdet == "")
        {   
            $jumlah_brg = OpsikUrsDetModel::where('kd_brg', $request->idItem)->where('id_opurs', $cek_opurs->id_opurs)->count();
            if($jumlah_brg>0)
            {
                return response()->json(['status' => 4]);
            }
            else
            {
                $cek_stok = VStokBrgMasukRumahSakitTotalModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $request->idItem)->first(); 
                $dataopursdet = new OpsikUrsDetModel();
                $dataopursdet->id_opurs = $id_opurs;
                $dataopursdet->kd_brg = $request->idItem;
                $dataopursdet->stok_sistem_opursdet = $cek_stok->stok_brg;
                $dataopursdet->stok_opsik_opursdet = $request->stok_fisik;
                if($cek_stok->stok_brg != $request->stok_fisik)
                {
                    $dataopursdet->status_opursdet = 1;    
                }
                else
                {
                    $dataopursdet->status_opursdet = 0;    
                }
                $dataopursdet->user_id = $user_id;
                $dataopursdet->save();
                return response()->json(['status' => 1]);
            }
        }   
        else
        {
            $cekData = OpsikUrsDetModel::where('id_opursdet', $request->id_opursdet)->first();
            if($cekData->kd_brg == $request->idItem)
            {
                $dataopursdet = OpsikUrsDetModel::where('id_opurdet', $request->id_opursdet)->first(); 
                $dataopursdet->stok_opsik_opursdet = $request->stok_fisik;
                $dataopursdet->user_id = $user_id;
                $dataopursdet->save();
                return response()->json(['status' => 1]);
            }
            else
            {                
                $jumlah_brg = OpsikUrsDetModel::where('kd_brg', $request->idItem)->where('id_opurs', $cek_opurs->id_opurs)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $cek_stok = VStokBrgMasukRumahSakitTotalModel::where('id_urs', $datarumahsakit->id_urs)->where('kd_brg', $request->idItem)->first();

                    $dataopursdet = OpsikUrsDetModel::where('id_opursdet', $request->id_opursdet)->first(); 
                    $dataopursdet->kd_brg = $request->idItem;
                    $dataopursdet->stok_sistem_opursdet = $cek_stok->stok_brg;
                    $dataopursdet->stok_opsik_opursdet = $request->stok_fisik;
                    if($cek_stok->stok_brg != $request->stok_fisik)
                    {
                        $dataopursdet->status_opursdet = 1;    
                    }
                    else
                    {
                        $dataopursdet->status_opursdet = 0;    
                    }
                    $dataopursdet->user_id = $user_id;
                    $dataopursdet->save();
                    return response()->json(['status' => 1]);
                }
            }
        }     
    }

    public function edit(Request $request)
    {   
        $data = OpsikUrsDetModel::
        join('barang','opsik_rumah_sakit_detail.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_opursdet',$request->id_opursdet)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
       
        $data = OpsikUrsDetModel::where('id_opursdet', $request->id_opursdet)->first();            
        $data->delete();
        return response()->json(['status' => 1]);
    }
}
