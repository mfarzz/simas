<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\User;
use App\Models\VBarangMasukFakultasModel;
use App\Models\VStokBarangMasukModel;
use App\Models\VStokBarangMasukSemuaModel;
use App\Models\VStokBrgModel;
use Illuminate\Http\Request;
use Datatables;

class BarangMasukFakultasKhususCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id')
            ->where('users.id', $user_id)->first();   

        if(request()->ajax()) {
            return datatables()->of(VBarangMasukFakultasModel::
            where('id_fk',$datafakultas->id_fk)
            ->get())
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        return view('BarangMasuk.Khusus.Fakultas.index',['daftar_kategori'=> $daftar_kategori]);
    }

    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = VStokBarangMasukSemuaModel::where('kd_sskt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $stok = BarangMasukModel::where('kd_brg', $request->idItem)->sum('sisa_bm');
        
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)->first();                   

        $databmf = new BarangMasukFakultasModel();
        $databmf->kd_brg = $request->idItem;
        $databmf->id_fk = $datafakultas->id_fk;
        $databmf->kd_lks = $datafakultas->kd_lks;
        $databmf->jmlh_awal_bmf = $request->jumlah;
        $databmf->sisa_bmf = $request->jumlah;
        $databmf->tglperolehan_bmf = $request->tgl_perolehan;
        $databmf->tglbuku_bmf = $request->tgl_buku;
        $databmf->user_id = $user_id;
        $databmf->save();
        $id_bmf = $databmf->id_bmf;

        $proses=0;
        $jumlah_masuk = $request->jumlah;
        $databarangmasuk = BarangMasukModel::where('kd_brg', $request->idItem)
        ->where('sisa_bm',  '!=', 0)
        ->orderBy('tglperolehan_bm','asc')
        ->get();
        foreach($databarangmasuk as $barisbm)
        {
            $sisabm = $barisbm->sisa_bm;
            if($jumlah_masuk <= $sisabm)
            {
                $sisa = $sisabm - $jumlah_masuk;                            
                if($proses==0)
                {
                    $databmupdate = BarangMasukModel::where('id_bmf', $barisbm->id_bmf)->first();                   
                    $databmupdate->sisa_bm = $sisa;
                    $databmupdate->save();

                    $databmfdetail = new BarangMasukFakultasDetailModel();
                    $databmfdetail->id_bmf = $id_bmf;
                    $databmfdetail->id_bm = $barisbm->id;
                    $databmfdetail->jmlh_bmfd = $jumlah_masuk;
                    $databmfdetail->sisa_bmfd = $jumlah_masuk;
                    $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                    $databmfdetail->user_id = $user_id;
                    $databmfdetail->save();
                    $proses=1;                         
                }
            }                        
            else
            {
                if($proses==0)
                {
                    $sisa = $jumlah_masuk - $sisabm;
                    if($sisa >= 0)
                    {   
                        $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                        $databmupdate->sisa_bm = 0;                            
                        $databmupdate->save();

                        $databmfdetail = new BarangMasukFakultasDetailModel();
                        $databmfdetail->id_bmf = $id_bmf;
                        $databmfdetail->id_bm = $barisbm->id;
                        $databmfdetail->jmlh_bmfd = $sisabm;
                        $databmfdetail->sisa_bmfd = $sisabm;
                        $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                        $databmfdetail->user_id = $user_id;
                        $databmfdetail->save();
                        $proses=0;
                        $jumlah_masuk = $sisa;
                    }
                    else
                    {
                        $sisa = $sisabm - $jumlah_masuk;
                        $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                        $databmupdate->sisa_bm = $sisa;                            
                        $databmupdate->save();

                        $databmfdetail = new BarangMasukFakultasDetailModel();
                        $databmfdetail->id_bmf = $id_bmf;
                        $databmfdetail->id_bm = $barisbm->id;
                        $databmfdetail->jmlh_bmfd = $sisabm;
                        $databmfdetail->sisa_bmfd = $sisabm;
                        $databmfdetail->hrg_bmfd = $barisbm->hrg_bm;
                        $databmfdetail->user_id = $user_id;
                        $databmfdetail->save();                                
                        $proses=1;
                    }
                }
            }
        }
            return response()->json(['status' => 1]);
  
    }

    public function destroy(Request $request)
    {
        $databmfd = BarangMasukFakultasDetailModel::where('id_bmf', $request->id)->get();
        foreach($databmfd as $barisbmfd)
        {                
            $databm = BarangMasukModel::where('id', $barisbmfd->id_bm)->first();
            $stok_skrg = $databm->sisa_bm + $barisbmfd->jmlh_bmfd;

            $databmupdate = BarangMasukModel::where('id', $barisbmfd->id_bm)->first();                   
            $databmupdate->sisa_bm = $stok_skrg;                            
            $databmupdate->save();

            $datadeletebmfd = BarangMasukFakultasDetailModel::where('id', $barisbmfd->id)->first();
            $datadeletebmfd->delete();                  
        }

        $datadeletebmf = BarangMasukFakultasModel::where('id', $request->id)->first();
        $datadeletebmf->delete();

        return Response()->json(0);
    }
}
