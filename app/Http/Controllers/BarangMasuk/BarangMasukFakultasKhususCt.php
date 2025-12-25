<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BmfpModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VBarangMasukFakultasModel;
use App\Models\VBarangModel;
use App\Models\VStokBarangMasukModel;
use App\Models\VStokBarangMasukSemuaModel;
use App\Models\VStokBrgModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Crypt;

class BarangMasukFakultasKhususCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_bmfp = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VBarangMasukFakultasModel::
            where('id_fk',$datafakultas->id_fk)
            ->where('id_bmfp', $id_bmfp)
            ->orderby('id_bmf')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KategoriModel::orderby('kd_kt')->get();
        $cek_bmfp = BmfpModel::where('id_bmfp',$id_bmfp)->first();
        $total_hrg = VBarangMasukFakultasModel::where('id_bmfp',$id_bmfp)->sum('total_hrg');
        $daftar_barang = VBarangModel::orderby('nm_brg')->get();
        return view('BarangMasuk.Khusus.Fakultas.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'daftar_barang'=> $daftar_barang, 'cek_bmfp' =>$cek_bmfp, 'total_hrg' =>$total_hrg]);
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
        $id_bmfp = Crypt::decryptString($request->encripted_id);
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
            $cek_bmfp = BmfpModel::where('id_bmfp',$id_bmfp)->first();       
            if($request->id_bmf == "")
            {
                $jumlah_brg = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('id_bmfp', $cek_bmfp->id_bmfp)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('tglperolehan_bmf','>', $request->tgl_perolehan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $databmf = new BarangMasukFakultasModel();
                        $databmf->id_bmfp = $id_bmfp;
                        $databmf->kd_brg = $idItem;
                        $databmf->id_fk = $datafakultas->id_fk;
                        $databmf->kd_lks = $datafakultas->kd_lks;
                        $databmf->jmlh_awal_bmf = $request->jumlah;
                        $databmf->sisa_bmf = $request->jumlah;
                        $databmf->hrg_bmf = $request->harga;
                        $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                        $databmf->tglbuku_bmf = $cek_bmfp->tgl_bmfp;
                        $databmf->user_id = $user_id;
                        $databmf->save();

                        $total_hrg_baru = VBarangMasukFakultasModel::where('id_bmfp', $id_bmfp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                    }    
                }
            }   
            else
            {
                $cekData = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first();
                if($cekData->kd_brg == $idItem)
                {
                    $databmf = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first(); 
                    $databmf->jmlh_awal_bmf = $request->jumlah;
                    $databmf->sisa_bmf = $request->jumlah;
                    $databmf->hrg_bmf = $request->harga;
                    $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                    $databmf->user_id = $user_id;
                    $databmf->save();
                    $total_hrg_baru = VBarangMasukFakultasModel::where('id_bmfp', $id_bmfp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                }
                else
                {
                    $jumlah_brg = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('id_bmfp', $cek_bmfp->id_bmfp)->count();
                    if($jumlah_brg>0)
                    {
                        return response()->json(['status' => 4]);
                    }
                    else
                    {
                        $jumlah = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $idItem)->where('tglperolehan_bmf','>', $request->tgl_perolehan)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                        else
                        {
                            $databmf = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first(); 
                            $databmf->kd_brg = $idItem;
                            $databmf->jmlh_awal_bmf = $request->jumlah;
                            $databmf->sisa_bmf = $request->jumlah;
                            $databmf->hrg_bmf = $request->harga;
                            $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                            $databmf->user_id = $user_id;
                            $databmf->save();
                            $total_hrg_baru = VBarangMasukFakultasModel::where('id_bmfp', $id_bmfp)->sum('total_hrg');
                        return response()->json(['status' => 1, 'total_hrg' => number_format($total_hrg_baru, 0, ',', '.')]);
                        }
                    }
                }
            } 
        }
            
    }

    public function edit(Request $request)
    {   
        $data = BarangMasukFakultasModel::
        join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
        ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
        ->where('id_bmf',$request->id_bmf)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();  

        $datacek = BarangMasukFakultasModel::
        where('id_bmf', $request->id_bmf)->first();

        $jumlah = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $datacek->kd_brg)->where('tglperolehan_bmf','>', $datacek->tglperolehan_bmf)->count();
        if($jumlah>0)
        {
            return response()->json(['status' => 3]);
        }
        else
        {
            $data = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first();            
            $data->delete();
            return response()->json(['status' => 1]);
        }
    }
}
