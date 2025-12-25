<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangModel;
use App\Models\BkprsModel;
use App\Models\BkrsnModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BkrsnCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  
        if(request()->ajax()) {
            return datatables()->of(BkrsnModel::join('barang_keluar_penerima_rumah_sakit','barang_keluar_rumah_sakit_nota.id_bkprs','=','barang_keluar_penerima_rumah_sakit.id_bkprs')
            ->where('barang_keluar_rumah_sakit_nota.id_urs',$datarumahsakit->id_urs)
            ->get())
            ->addColumn('id_bkrsn', function ($data) {
                return $data->id_bkrsn; 
            })
            ->addColumn('id_bkrsn_en', function ($data) {
                $id_bkrsn_en = Crypt::encryptString($data->id_bkrsn);
                return $id_bkrsn_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_penerima = BkprsModel::where('id_urs',$datarumahsakit->id_urs)->where('status_bkprs',1)->orderby('nm_bkprs')->get();  
        return view('BarangKeluar.Khusus.RumahSakit.Nota.index',['daftar_penerima'=>$daftar_penerima]);
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }
    
    public function getPenerima(Request $request){
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  
        $idUnitjabatan = BkrsnModel::where('id_urs', $datarumahsakit->id_urs)->pluck('id_bkprs','nm_bkprs');
        return response()->json($idUnitjabatan);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;
        $thn_nota = substr($request->tgl_nota,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_nota)
        {
            if($request->id_bkrsn == "")
            {
                $jumlah_belum = BkrsnModel::where('id_urs', $id_urs)->where('status_bkrsn', 0)->count();
                if($jumlah_belum >0)
                {
                    return response()->json(['status' => 5]);
                }
                else
                {
                    $jumlah = BkrsnModel::where('id_urs', $id_urs)->where('no_bkrsn', $request->no_nota)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $data = new BkrsnModel();
                        $data->id_urs = $id_urs;
                        $data->id_bkprs = $request->penerima;
                        $data->no_bkrsn = $request->no_nota;
                        $data->tgl_bkrsn = $request->tgl_nota;
                        $data->status_bkrsn = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
            else
            {
                $cekData = BkrsnModel::where('id_urs', $id_urs)->where('id_bkrsn', $request->id_bkrsn)->first();
                if($cekData->no_bkrsn == $request->no_nota and $cekData->tgl_bkrsn == $request->tgl_nota and $cekData->id_bkprs == $request->penerima )
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $jumlah=0;               
                    if($cekData->no_bkrsn != $request->no_nota)
                    {
                        $jumlah = BkrsnModel::where('id_urs', $id_urs)->where('no_bkrsn', $request->no_nota)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]); 
                        }
                    }
                    if($jumlah==0)
                    { 
                        $data = BkrsnModel::where('id_bkrsn', $request->id_bkrsn)->first();                   
                        $data->no_bkrsn = $request->no_nota;
                        $data->tgl_bkrsn = $request->tgl_nota;
                        $data->id_bkprs = $request->penerima;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 4]);
                    }
                }            
            }
        }
        else
        {
            return response()->json(['status' => 6]);
        }        
    }

    public function edit(Request $request)
    {   
        $data = BkrsnModel::where('id_bkrsn',$request->id_bkrsn)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BkrsnModel::where('id_bkrsn', $request->id_bkrsn)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrektorat = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrektorat->id_urs;

        $jumlah_bk = BarangKeluarRumahSakitModel::where('id_bkrsn', $request->id_bkrsn)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {            
            $cek_data = BkrsnModel::where('id_bkrsn', $request->id_bkrsn)->first();
            $databarangkeluarrumahsakit = BarangKeluarRumahSakitModel::where('id_bkrsn', $cek_data->id_bkrsn)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangkeluarrumahsakit as $barisbkrs)
            {   
                $stoktotal = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->sum('stok_brg');
                $proses=0;
                $tnilai_baru=0;
                $jumlah_keluar = $barisbkrs->jmlh_bkrs;
                $databarangmasukrumahsakit = BarangMasukRumahSakitModel::where('id_urs', $id_urs)->where('kd_brg', $barisbkrs->kd_brg)
                ->where('sisa_bmrs',  '!=', 0)
                ->orderBy('tglperolehan_bmrs','asc')
                ->get();
                foreach($databarangmasukrumahsakit as $barisbmrs)
                {
                    $sisabmrs = $barisbmrs->sisa_bmrs;
                    if($jumlah_keluar <= $sisabmrs)
                    {
                        $sisa = $sisabmrs - $jumlah_keluar;                            
                        if($proses==0)
                        {                            
                            $nilai_baru = $barisbmrs->hrg_bmrs * $jumlah_keluar;
                            $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                            $databmupdate->sisa_bmrs = $sisa;
                            $databmupdate->save();
                            
                            $databkfdetail = new BarangKeluarRumahSakitDetailModel();
                            $databkfdetail->id_bkrs = $barisbkrs->id_bkrs;
                            $databkfdetail->id_bmrs = $barisbmrs->id_bmrs;
                            $databkfdetail->jmlh_bkrsd = $jumlah_keluar;
                            $databkfdetail->user_id = $user_id;
                            $databkfdetail->save();
                            $proses=1;
                            $databarang = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();                                
                            $nilai_terakhir = $databarang->nilai_brg;
                            
                            $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();                                
                            $tnilai_baru = $nilai_baru + $tnilai_baru;
                            $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $tnilai_baru;
                            $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                            $databmupdateitemnilai->save();
                        }
                    }                        
                    else
                    {
                        if($proses==0)
                        {
                            $sisa = $jumlah_keluar - $sisabmrs;
                            if($sisa >= 0)
                            {   
                                $nilai_baru = $barisbmrs->hrg_bmrs * $sisabmrs;
                                $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                $databmupdate->sisa_bmrs = 0;                            
                                $databmupdate->save();

                                $databkdetail = new BarangKeluarRumahSakitDetailModel();
                                $databkdetail->id_bkrs = $barisbkrs->id_bkrs;
                                $databkdetail->id_bmrs = $barisbmrs->id_bmrs;
                                $databkdetail->jmlh_bkrsd = $sisabmrs;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();
                                $proses=0;
                                $jumlah_keluar = $sisa;

                                $databarang = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;
                                
                                $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                                $databmupdateitemnilai->save();
                            }
                            else
                            {
                                $sisa = $sisabmrs - $jumlah_keluar;
                                $nilai_baru = $barisbmrs->hrg_bmrs * $jumlah_keluar;
                                $databmupdate = BarangMasukRumahSakitModel::where('id_bmrs', $barisbmrs->id_bmrs)->first();                   
                                $databmupdate->sisa_bmrs = $sisa;                            
                                $databmupdate->save();

                                $databkdetail = new BarangKeluarRumahSakitDetailModel();
                                $databkdetail->id_bkrs = $barisbkrs->id_bkrs;
                                $databkdetail->id_bmrs = $barisbmrs->id_bmrs;
                                $databkdetail->jmlh_bkrsd = $sisabmrs;
                                $databkdetail->user_id = $user_id;
                                $databkdetail->save();                                
                                $proses=1;

                                $databarang = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();                                
                                $nilai_terakhir = $databarang->nilai_brg;

                                $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();
                                $databmupdateitemnilai->nilai_brg = $nilai_terakhir - $nilai_baru;
                                $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg - $jumlah_keluar;
                                $databmupdateitemnilai->save();
                            }
                        }
                    }
                }
                $databmupdateitem = BarangModel::where('kd_brg', $barisbkrs->kd_brg)->first();                   
                $databmupdateitem->stok_brg = $stoktotal - $barisbkrs->jmlh_bkrs;                    
                $databmupdateitem->save();
            }
            $cek_data->status_bkrsn = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}