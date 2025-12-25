<?php

namespace App\Http\Controllers\BarangUsang;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\BarangUsangDetailModel;
use App\Models\BarangUsangModel;
use App\Models\KelompokModel;
use App\Models\SubSubKategoriModel;
use App\Models\VBarangUsangModel;
use App\Models\VStokBrgModel;
use Illuminate\Http\Request;
use Datatables;

class BarangUsangKhususCt extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(VBarangUsangModel::
            get())
            ->addColumn('action', 'components.form-action.form-action-d')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_kategori = KelompokModel::orderby('kd_kl')->get();
        return view('BarangUsang.Khusus.index',['daftar_kategori'=> $daftar_kategori]);
    }
    public function getSubkategori(Request $request){
        $idSubkategori = SubSubKategoriModel::where('kd_kl', $request->subKat)->pluck('kd_sskt','nm_sskt');
        return response()->json($idSubkategori);
    }

    public function getItem(Request $request){
        $idItem = VStokBrgModel::where('kd_sskt', $request->item)->pluck('kd_brg','ket');
        return response()->json($idItem);
    }
    public function store(request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;

        $stok = BarangMasukModel::where('kd_brg', $request->idItem)->sum('sisa_bm');
                
        if($request->jumlah > $stok)
        {
            return response()->json(['status' => 0]);
        }
        else
        {
            $stok_terakhir = $stok - $request->jumlah;
            $databu = new BarangUsangModel();
            $databu->ket_bu = $request->ket_bu;             
            $databu->kd_brg = $request->idItem;
            $databu->jmlh_bu = $request->jumlah;
            $databu->tgltentu_bu = $request->tgl_tentu;
            $databu->user_id = $user_id;
            $databu->save();
            $id_bu = $databu->id;

            $totalbu=0;
            $proses=0;
            $tnilai_baru=0;
            $jumlah_usang = $request->jumlah;
            $databarangmasuk = BarangMasukModel::where('kd_brg', $request->idItem)
            ->where('sisa_bm',  '!=', 0)
            ->orderBy('tglperolehan_bm','asc')
            ->get();
            foreach($databarangmasuk as $barisbm)
            {
                $sisabm = $barisbm->sisa_bm;
                if($jumlah_usang <= $sisabm)
                {
                    $sisa = $sisabm - $jumlah_usang;                            
                    if($proses==0)
                    {
                        $nilai_baru = $barisbm->hrg_bm * $jumlah_usang;
                        $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                        $databmupdate->sisa_bm = $sisa;
                        $databmupdate->save();

                        $databkdetail = new BarangUsangDetailModel();
                        $databkdetail->id_bu = $id_bu;
                        $databkdetail->id_bm = $barisbm->id;
                        $databkdetail->jmlh_bud = $jumlah_usang;
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
                        $sisa = $jumlah_usang - $sisabm;
                        if($sisa >= 0)
                        {          
                            $nilai_baru = $barisbm->hrg_bm * $sisabm;
                            $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                            $databmupdate->sisa_bm = 0;                            
                            $databmupdate->save();

                            $databudetail = new BarangUsangDetailModel();
                            $databudetail->id_bu = $id_bu;
                            $databudetail->id_bm = $barisbm->id;
                            $databudetail->jmlh_bud = $sisabm;
                            $databudetail->user_id = $user_id;
                            $databudetail->save();
                            $proses=0;
                            $jumlah_usang = $sisa;

                            $databarang = BarangModel::where('kd_brg', $request->idItem)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;
                            
                            $databmupdateitemnilai = BarangModel::where('kd_brg', $request->idItem)->first(); 
                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                            $databmupdateitemnilai->save();
                        }
                        else
                        {
                            $sisa = $sisabm - $jumlah_usang;
                            $nilai_baru = $barisbm->hrg_bm * $jumlah_usang;
                            $databmupdate = BarangMasukModel::where('id', $barisbm->id)->first();                   
                            $databmupdate->sisa_bm = $sisa;                            
                            $databmupdate->save();

                            $databkdetail = new BarangKeluarDetailModel();
                            $databkdetail->id_bu = $id_bu;
                            $databkdetail->id_bm = $barisbm->id;
                            $databkdetail->jmlh_bud = $sisabm;
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
            $databmupdateitem->stok_brg = $stok_terakhir;                    
            $databmupdateitem->save();
            return response()->json(['status' => 1]);
        }
    }
    public function destroy(Request $request)
    {
        $tnilai_baru=0;
        $databud = BarangUsangDetailModel::where('id_bu', $request->id)->get();
        foreach($databud as $barisbud)
        {                
            $databm = BarangMasukModel::where('id', $barisbud->id_bm)->first();
            $stok_skrg = $databm->sisa_bm + $barisbud->jmlh_bud;
            $nilai_baru = $barisbud->jmlh_bud * $databm->hrg_bm;

            $databmupdate = BarangMasukModel::where('id', $barisbud->id_bm)->first();                   
            $databmupdate->sisa_bm = $stok_skrg;                            
            $databmupdate->save();

            $datadeletebud = BarangUsangDetailModel::where('id', $barisbud->id)->first();
            $datadeletebud->delete();                  
            $tnilai_baru = $nilai_baru + $tnilai_baru;
        }
        $data = BarangUsangModel::where('id', $request->id)->first(); 
        $jmlh_bu = $data->jmlh_bu;
        
        $databmupdateitem = BarangModel::where('kd_brg', $data->kd_brg)->first();
        $databmupdateitem->stok_brg = $databmupdateitem->stok_brg + $jmlh_bu;
        $databmupdateitem->nilai_brg = $databmupdateitem->nilai_brg + $tnilai_baru;
        $databmupdateitem->save();

        $data->delete();
        return Response()->json(1);
    }
}