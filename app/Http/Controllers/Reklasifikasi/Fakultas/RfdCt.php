<?php

namespace App\Http\Controllers\Reklasifikasi\Fakultas;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\RfdModel;
use App\Models\RfModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class RfdCt extends Controller
{
    public function index($encripted_id)
    {
        $user_id = auth()->user()->id;
        $id_rf = Crypt::decryptString($encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(RfdModel::
            join('reklasifikasi_fakultas','reklasifikasi_fakultas_detail.id_rf','=','reklasifikasi_fakultas.id_rf')
            ->where('reklasifikasi_fakultas_detail.id_rf', $id_rf)
            ->orderby('id_bmf')
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        $cek_rf = RfModel::where('id_rf',$id_rf)->first();
        $daftar_bm = BarangMasukFakultasModel::
        join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
        ->where('id_bmfs',$cek_rf->id_bmfs)->get();
        return view('Reklasifikasi.Fakultas.Detail.index',['encripted_id'=> $encripted_id,'daftar_kategori'=> $daftar_kategori, 'cek_rf' =>$cek_rf, 'daftar_bm'=>$daftar_bm]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = BarangModel::where('kd_sskt', $request->item)->pluck('kd_brg','nm_brg');
        return response()->json($idItem);
    }

    /*public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_bmfs = Crypt::decryptString($request->encripted_id);
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();     
        $cek_bmfs = BmfsModel::where('id_bmfs',$id_bmfs)->first();       
        if($request->id_bmf == "")
        {
            $jumlah_brg = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->where('id_bmfs', $cek_bmfs->id_bmfs)->count();
            if($jumlah_brg>0)
            {
                return response()->json(['status' => 4]);
            }
            else
            {
                $jumlah = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->where('tglperolehan_bmf','>', $request->tgl_perolehan)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {
                    $databmf = new BarangMasukFakultasModel();
                    $databmf->id_bmfs = $id_bmfs;
                    $databmf->kd_brg = $request->idItem;
                    $databmf->id_fk = $datafakultas->id_fk;
                    $databmf->kd_lks = $datafakultas->kd_lks;
                    $databmf->jmlh_awal_bmf = $request->jumlah;
                    $databmf->sisa_bmf = $request->jumlah;
                    $databmf->hrg_bmf = $request->harga;
                    $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                    $databmf->tglbuku_bmf = $cek_bmfs->tgl_bmfs;
                    $databmf->user_id = $user_id;
                    $databmf->save();
                    return response()->json(['status' => 1]);
                }    
            }
        }   
        else
        {
            $cekData = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first();
            if($cekData->kd_brg == $request->idItem)
            {
                $databmf = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first(); 
                $databmf->jmlh_awal_bmf = $request->jumlah;
                $databmf->sisa_bmf = $request->jumlah;
                $databmf->hrg_bmf = $request->harga;
                $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                $databmf->user_id = $user_id;
                $databmf->save();
                return response()->json(['status' => 1]);
            }
            else
            {
                $jumlah_brg = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->where('id_bmfs', $cek_bmfs->id_bmfs)->count();
                if($jumlah_brg>0)
                {
                    return response()->json(['status' => 4]);
                }
                else
                {
                    $jumlah = BarangMasukFakultasModel::where('id_fk', $datafakultas->id_fk)->where('kd_brg', $request->idItem)->where('tglperolehan_bmf','>', $request->tgl_perolehan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $databmf = BarangMasukFakultasModel::where('id_bmf', $request->id_bmf)->first(); 
                        $databmf->kd_brg = $request->idItem;
                        $databmf->jmlh_awal_bmf = $request->jumlah;
                        $databmf->sisa_bmf = $request->jumlah;
                        $databmf->hrg_bmf = $request->harga;
                        $databmf->tglperolehan_bmf = $request->tgl_perolehan;
                        $databmf->user_id = $user_id;
                        $databmf->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
        }     
    }

    public function edit(Request $request)
    {   
        $data = BarangMasukFakultasModel::
        join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
        ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
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
    }*/
}
