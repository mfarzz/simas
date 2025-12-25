<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\UnitRektoratModel;
use App\Models\VBarangKeluarModel;
use App\Models\VStokBarangMasukSemuaModel;
use App\Models\VStokBrgModel;
use Illuminate\Http\Request;
use Datatables;

class BarangKeluarKhususCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(VBarangKeluarModel::
            get())
            ->addColumn('action', 'employee-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        $daftar_unit_rektorat = UnitRektoratModel::orderby('nm_ur')->get();
        return view('BarangKeluar.Khusus.index',['daftar_kategori'=> $daftar_kategori, 'daftar_unit_rektorat'=> $daftar_unit_rektorat]);
    }
    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = VStokBarangMasukSemuaModel::where('kd_sskt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $stokrektorat = BarangMasukModel::where('kd_brg', $request->idItem)->sum('sisa_bm');
        $stokfakultas = BarangMasukFakultasModel::where('kd_brg', $request->idItem)->sum('sisa_bmf');
        $stok = $stokrektorat + $stokfakultas;
                
        if($request->jumlah > $stok)
        {
            return response()->json(['status' => 0]);
        }
        else
        {
            $stok_terakhir = $stokrektorat - $request->jumlah;
            $databk = new BarangKeluarModel();       
            $databk->kd_brg = $request->idItem;
            $databk->id_ur = $request->id_ur;
            $databk->nm_penerima = $request->nm_penerima;
            $databk->jmlh_bk = $request->jumlah;
            $databk->tglambil_bk = $request->tgl_keluar;
            $databk->user_id = $user_id;
            $databk->save();
            $id_bk = $databk->id;

            $totalbk=0;
            $proses=0;
            $tnilai_baru=0;
            $jumlah_keluar = $request->jumlah;
            $databarangmasuk = BarangMasukModel::where('kd_brg', $request->idItem)
            ->where('sisa_bm',  '!=', 0)
            ->orderBy('tglperolehan_bm','asc')
            ->get();
            foreach($databarangmasuk as $barisbm)
            {
                $sisabm = $barisbm->sisa_bm;
                if($jumlah_keluar <= $sisabm)
                {
                    $sisa = $sisabm - $jumlah_keluar;                            
                    if($proses==0)
                    {
                        $nilai_baru = $barisbm->hrg_bm * $jumlah_keluar;
                        $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                        $databmupdate->sisa_bm = $sisa;
                        $databmupdate->save();

                        $databkdetail = new BarangKeluarDetailModel();
                        $databkdetail->id_bk = $id_bk;
                        $databkdetail->id_bm = $barisbm->id;
                        $databkdetail->jmlh_bkd = $jumlah_keluar;
                        $databkdetail->user_id = $user_id;
                        $databkdetail->save();
                        $proses=1;
                        $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                        $nilai_terakhir = $databarang->nilai_brg;

                        $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();                                
                        $tnilai_baru = $nilai_baru + $tnilai_baru;
                        $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $tnilai_baru;
                        $databmupdateitemnilai->save();
                    }
                }                        
                else
                {
                    if($proses==0)
                    {
                        $sisa = $jumlah_keluar - $sisabm;
                        if($sisa >= 0)
                        {          
                            $nilai_baru = $barisbm->hrg_bm * $sisabm;
                            $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                            $databmupdate->sisa_bm = 0;                            
                            $databmupdate->save();

                            $databkdetail = new BarangKeluarDetailModel();
                            $databkdetail->id_bk = $id_bk;
                            $databkdetail->id_bm = $barisbm->id;
                            $databkdetail->jmlh_bkd = $sisabm;
                            $databkdetail->user_id = $user_id;
                            $databkdetail->save();
                            $proses=0;
                            $jumlah_keluar = $sisa;

                            $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;
                            
                            $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                            $databmupdateitemnilai->save();
                        }
                        else
                        {
                            $sisa = $sisabm - $jumlah_keluar;
                            $nilai_baru = $barisbm->hrg_bm * $jumlah_keluar;
                            $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                            $databmupdate->sisa_bm = $sisa;                            
                            $databmupdate->save();

                            $databkdetail = new BarangKeluarDetailModel();
                            $databkdetail->id_bk = $id_bk;
                            $databkdetail->id_bm = $barisbm->id;
                            $databkdetail->jmlh_bkd = $sisabm;
                            $databkdetail->user_id = $user_id;
                            $databkdetail->save();                                
                            $proses=1;

                            $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;

                            $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first();
                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                            $databmupdateitemnilai->save();
                        }
                    }
                }

            }

            $databmupdateitem = BarangModel::where('kd_brg', $request->idItem)->first();                   
            $databmupdateitem->stok_brg = $stok_terakhir + $stokfakultas;                    
            $databmupdateitem->save();
            return response()->json(['status' => 1]);
        }
    }
    public function destroy(Request $request)
    {
        $tnilai_baru=0;
        $databkd = BarangKeluarDetailModel::where('id_bk', $request->id)->get();
        foreach($databkd as $barisbkd)
        {                
            $databm = BarangMasukModel::where('id', $barisbkd->id_bm)->first();
            $stok_skrg = $databm->sisa_bm + $barisbkd->jmlh_bkd;
            $nilai_baru = $barisbkd->jmlh_bkd * $databm->hrg_bm;

            $databmupdate = BarangMasukModel::where('id', $barisbkd->id_bm)->first();                   
            $databmupdate->sisa_bm = $stok_skrg;                            
            $databmupdate->save();

            $datadeletebkd = BarangKeluarDetailModel::where('id', $barisbkd->id)->first();
            $datadeletebkd->delete();                  
            $tnilai_baru = $nilai_baru + $tnilai_baru;
        }
        $data = BarangKeluarModel::where('id', $request->id)->first(); 
        $jmlh_bk = $data->jmlh_bk;
        
        $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
        $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bk;
        $databmupdateitem->nilai_brg = $databmupdateitem->nilai_brg + $tnilai_baru;
        $databmupdateitem->save();
        $data->delete();

        return Response()->json(1);
    }
}