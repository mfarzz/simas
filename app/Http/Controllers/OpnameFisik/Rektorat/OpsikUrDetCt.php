<?php

namespace App\Http\Controllers\OpnameFisik\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikUrDetModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VStokBarangMasukRektoratSemuaModel;
use App\Models\VStokBrgMasukRektoratTotalModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikUrDetCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_opur = Crypt::decryptString($encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(OpsikUrDetModel::
            join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
            ->join('barang','opsik_rektorat_detail.kd_brg','=','barang.kd_brg')
            ->where('opsik_rektorat_detail.id_opur', $id_opur)
            ->orderby('opsik_rektorat_detail.id_opur')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_opur = OpsikRektoratModel::where('id_opur',$id_opur)->first();
        return view('OpnameFisik.Rektorat.Detail.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'cek_opur' =>$cek_opur]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();

        $idItem = VStokBarangMasukRektoratSemuaModel::where('id_ur', $datarektorat->id_ur)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_opur = Crypt::decryptString($request->encripted_id);
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)->first();
        $cek_opur = OpsikRektoratModel::where('id_opur',$id_opur)->first();
        if($request->id_opurdet == "")
        {   
            $jumlah_brg = OpsikUrDetModel::where('kd_brg', $request->idItem)->where('id_opur', $cek_opur->id_opur)->count();
            if($jumlah_brg>0)
            {
                return response()->json(['status' => 4]);
            }
            else
            {
                $cek_stok = VStokBrgMasukRektoratTotalModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $request->idItem)->first(); 
                $dataopurdet = new OpsikUrDetModel();
                $dataopurdet->id_opur = $id_opur;
                $dataopurdet->kd_brg = $request->idItem;
                $dataopurdet->stok_sistem_opurdet = $cek_stok->stok_brg;
                $dataopurdet->stok_opsik_opurdet = $request->stok_fisik;
                if($cek_stok->stok_brg != $request->stok_fisik)
                {
                    $dataopurdet->status_opurdet = 1;    
                }
                else
                {
                    $dataopurdet->status_opurdet = 0;    
                }
                $dataopurdet->user_id = $user_id;
                $dataopurdet->save();
                return response()->json(['status' => 1]);
            }
        }   
        else
        {
            $cekData = OpsikUrDetModel::where('id_opurdet', $request->id_opurdet)->first();
            if($cekData->kd_brg == $request->idItem)
            {
                $dataopurdet = OpsikUrDetModel::where('id_opurdet', $request->id_opurdet)->first(); 
                $dataopurdet->stok_opsik_opurdet = $request->stok_fisik;
                $dataopurdet->user_id = $user_id;
                $dataopurdet->save();
                return response()->json(['status' => 1]);
            }
            else
            {                
                $jumlah_brg = OpsikUrDetModel::where('kd_brg', $request->idItem)->where('id_opur', $cek_opur->id_opur)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $cek_stok = VStokBrgMasukRektoratTotalModel::where('id_ur', $datarektorat->id_ur)->where('kd_brg', $request->idItem)->first();

                    $dataopurdet = OpsikUrDetModel::where('id_opurdet', $request->id_opurdet)->first(); 
                    $dataopurdet->kd_brg = $request->idItem;
                    $dataopurdet->stok_sistem_opurdet = $cek_stok->stok_brg;
                    $dataopurdet->stok_opsik_opurdet = $request->stok_fisik;
                    if($cek_stok->stok_brg != $request->stok_fisik)
                    {
                        $dataopurdet->status_opurdet = 1;    
                    }
                    else
                    {
                        $dataopurdet->status_opurdet = 0;    
                    }
                    $dataopurdet->user_id = $user_id;
                    $dataopurdet->save();
                    return response()->json(['status' => 1]);
                }
            }
        }     
    }

    public function edit(Request $request)
    {   
        $data = OpsikUrDetModel::
        join('barang','opsik_rektorat_detail.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_opurdet',$request->id_opurdet)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
       
        $data = OpsikUrDetModel::where('id_opurdet', $request->id_opurdet)->first();            
        $data->delete();
        return response()->json(['status' => 1]);
    }
}
