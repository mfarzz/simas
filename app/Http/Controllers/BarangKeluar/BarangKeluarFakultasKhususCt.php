<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BkfnModel;
use App\Models\FakultasJabatanModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VBarangKeluarFakultasModel;
use App\Models\VStokBarangMasukFakultasSemuaModel;
use App\Models\VStokBarangMasukModel;
use App\Models\VStokBarangMasukSemuaModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BarangKeluarFakultasKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bkfn = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   
        if(request()->ajax()) {
            return datatables()->of(VBarangKeluarFakultasModel::
            where('id_fk',$datafakultas->id_fk)
            ->where('id_bkfn', $id_bkfn)
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bkfn = BkfnModel::where('id_bkfn',$id_bkfn)->first();
        $daftar_barang = VStokBarangMasukFakultasSemuaModel::where('id_fk', $datafakultas->id_fk)->orderby('nm_brg')->get();
        return view('BarangKeluar.Khusus.Fakultas.index',['encripted_id'=> $encripted_id, 'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bkfn' =>$cek_bkfn]);
    }

    public function getItem(Request $request){
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $idItem = VStokBarangMasukFakultasSemuaModel::where('id_fk', $datafakultas->id_fk)->where('kd_kt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $id_bkfn = Crypt::decryptString($request->encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        $cek_bkfn = BkfnModel::where('id_bkfn',$id_bkfn)->first();

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
            if($request->id_bkf == "")
            {
                $jumlah_brg = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('id_bkfn', $cek_bkfn->id_bkfn)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('tglambil_bkf','>', $cek_bkfn->tgl_bkfn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $idItem)->sum('sisa_bmf');

                        if($request->jumlah > $stokfakultas)
                        {
                            return response()->json(['status' => 2]); 
                        }
                        else
                        {
                            $databkr = new BarangKeluarFakultasModel();
                            $databkr->id_fk = $id_fk;
                            $databkr->id_bkfn = $id_bkfn;
                            $databkr->kd_brg = $idItem;
                            $databkr->jmlh_bkf = $request->jumlah;
                            $databkr->tglambil_bkf = $cek_bkfn->tgl_bkfn;
                            $databkr->user_id = $user_id;
                            $databkr->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
            else
            {     
                $cekData = BarangKeluarFakultasModel::where('id_bkf', $request->id_bkf)->first();
                if($cekData->kd_brg == $idItem)
                {    
                    $stokfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $idItem)->sum('sisa_bmf');

                    if($request->jumlah > $stokfakultas)
                    {
                        return response()->json(['status' => 2]); 
                    }
                    else
                    {
                        $databkf = BarangKeluarFakultasModel::where('id_bkf', $request->id_bkf)->first(); 
                        $databkf->jmlh_bkf = $request->jumlah;
                        $databkf->user_id = $user_id;
                        $databkf->save();
                        return response()->json(['status' => 1]);
                    }
                }
                else
                {
                    $jumlah = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('tglambil_bkf','>', $cek_bkfn->tgl_bkfn)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 3]);
                    }
                    else
                    {
                        $stokfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)->where('kd_brg', $idItem)->sum('sisa_bmf');

                        if($request->jumlah > $stokfakultas)
                        {
                            return response()->json(['status' => 2]); 
                        }
                        else
                        {
                            $databkf = BarangKeluarFakultasModel::where('id_bkf', $request->id_bkf)->first(); 
                            $databkf->kd_brg = $idItem;
                            $databkf->jmlh_bkf = $request->jumlah;
                            $databkf->user_id = $user_id;
                            $databkf->save();
                            return response()->json(['status' => 1]);
                        }
                    }
                }
            }
        }
    }

    public function edit(Request $request)
    {   
        $data = BarangKeluarFakultasModel::
        join('barang','barang_keluar_fakultas.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bkf',$request->id_bkf)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
            
        $datacek = BarangKeluarFakultasModel::
        where('id_bkf', $request->id_bkf)->first();
        
        $jumlah = BarangKeluarFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $datacek->kd_brg)->where('tglambil_bkf','>', $datacek->tglambil_bkf)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $data = BarangKeluarFakultasModel::where('id_bkf', $request->id_bkf)->first(); 
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}