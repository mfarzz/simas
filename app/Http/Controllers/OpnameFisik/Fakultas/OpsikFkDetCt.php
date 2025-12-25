<?php

namespace App\Http\Controllers\OpnameFisik\Fakultas;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\BmfsModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\OpsikFakultasModel;
use App\Models\OpsikFkDetModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VOpsikBarangFakultasModel;
use App\Models\VStokBarangMasukFakultasSemuaModel;
use App\Models\VStokBrgMasukFakultasTotalModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikFkDetCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_opfk = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VOpsikBarangFakultasModel::
            where('v_opsik_barang_fakultas.id_opfk', $id_opfk)
            ->orderby('v_opsik_barang_fakultas.id_opfk')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_opfk = OpsikFakultasModel::where('id_opfk',$id_opfk)->first();
        return view('OpnameFisik.Fakultas.Detail.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'cek_opfk' =>$cek_opfk]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $idItem = VStokBarangMasukFakultasSemuaModel::where('id_fk', $datafakultas->id_fk)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_opfk = Crypt::decryptString($request->encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();     
        $cek_opfk = OpsikFakultasModel::where('id_opfk',$id_opfk)->first();
        if($request->id_opfkdet == "")
        {   
            $jumlah_brg = OpsikFkDetModel::where('kd_brg', $request->idItem)->where('id_opfk', $cek_opfk->id_opfk)->count();
            if($jumlah_brg>0)
            {
                return response()->json(['status' => 4]);
            }
            else
            {
                $cek_stok = VStokBrgMasukFakultasTotalModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->first(); 
                $dataopfkdet = new OpsikFkDetModel();
                $dataopfkdet->id_opfk = $id_opfk;
                $dataopfkdet->kd_brg = $request->idItem;
                $dataopfkdet->stok_sistem_opfkdet = $cek_stok->stok_brg;
                $dataopfkdet->stok_opsik_opfkdet = $request->stok_fisik;
                $dataopfkdet->user_id = $user_id;
                $dataopfkdet->save();
                return response()->json(['status' => 1]);
            }
        }   
        else
        {
            $cekData = OpsikFkDetModel::where('id_opfkdet', $request->id_opfkdet)->first();
            if($cekData->kd_brg == $request->idItem)
            {
                $dataopfkdet = OpsikFkDetModel::where('id_opfkdet', $request->id_opfkdet)->first(); 
                $dataopfkdet->stok_opsik_opfkdet = $request->stok_fisik;
                $dataopfkdet->user_id = $user_id;
                $dataopfkdet->save();
                return response()->json(['status' => 1]);
            }
            else
            {                
                $jumlah_brg = OpsikFkDetModel::where('kd_brg', $request->idItem)->where('id_opfk', $cek_opfk->id_opfk)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $cek_stok = VStokBrgMasukFakultasTotalModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->first();

                    $dataopfkdet = OpsikFkDetModel::where('id_opfkdet', $request->id_opfkdet)->first(); 
                    $dataopfkdet->kd_brg = $request->idItem;
                    $dataopfkdet->stok_sistem_opfkdet = $cek_stok->stok_brg;
                    $dataopfkdet->stok_opsik_opfkdet = $request->stok_fisik;
                    $dataopfkdet->user_id = $user_id;
                    $dataopfkdet->save();
                    return response()->json(['status' => 1]);
                }
            }
        }     
    }

    public function edit(Request $request)
    {   
        $data = OpsikFkDetModel::
        join('barang','opsik_fakultas_detail.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_opfkdet',$request->id_opfkdet)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
       
        $data = OpsikFkDetModel::where('id_opfkdet', $request->id_opfkdet)->first();            
        $data->delete();
        return response()->json(['status' => 1]);
    }
}
