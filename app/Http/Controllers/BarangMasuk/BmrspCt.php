<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangModel;
use App\Models\BmrspModel;
use App\Models\MataAnggaranModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BmrspCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(BmrspModel::
            join('mata_anggaran_kegiatan','barang_masuk_rumah_sakit_pesanan.kd_mak','=','mata_anggaran_kegiatan.kd_mak')
            ->where('barang_masuk_rumah_sakit_pesanan.id_urs',$datarumahsakit->id_urs)
            ->whereYear('barang_masuk_rumah_sakit_pesanan.tgl_bmrsp',$tahun_anggaran)
            ->get())
            ->addColumn('id_bmrsp', function ($data) {
                return $data->id_bmrsp; 
            })
            ->addColumn('id_bmrsp_en', function ($data) {
                $id_bmrsp_en = Crypt::encryptString($data->id_bmrsp);
                return $id_bmrsp_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_mata_anggaran = MataAnggaranModel::orderby('kd_mak')->where('sts_mak',1)->get();
        return view('BarangMasuk.Khusus.RumahSakit.Pesanan.index',['daftar_mata_anggaran'=> $daftar_mata_anggaran]);
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }

    public function store(Request $request)
    {  
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;     
        $thn_nota = substr($request->tgl_pesanan,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_nota)
        {  
            if($request->id_bmrsp == "")
            {    
                $jumlah_belum = BmrspModel::where('id_urs', $id_urs)->where('status_bmrsp', 0)->count();
                if($jumlah_belum >0)
                {
                    return response()->json(['status' => 5]);
                }
                else
                {                
                    $jumlah = BmrspModel::where('id_urs', $id_urs)->where('no_bmrsp', $request->no_pesanan)->where('no_sp2d', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {   
                        $data = new BmrspModel();
                        $data->id_urs = $id_urs;
                        $data->no_sp2d = $request->no_sp2d;
                        $data->kd_mak = $request->kd_mak;
                        $data->no_bmrsp = $request->no_pesanan;
                        $data->tgl_bmrsp = $request->tgl_pesanan;
                        $data->nilai_bmrsp = $request->nilai_pesanan;
                        $data->status_bmrsp = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
            else
            {
                $cekData = BmrspModel::where('id_urs', $id_urs)->where('id_bmrsp', $request->id_bmrsp)->first();
                if($cekData->no_sp2d == $request->no_sp2d and $cekData->kd_mak == $request->kd_mak and $cekData->no_bmrsp == $request->no_pesanan and $cekData->tgl_bmrsp == $request->tgl_pesanan and $cekData->nilai_bmrsp == $request->nilai_pesanan )
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $jumlah=0;      
                    if($cekData->no_sp2d != $request->no_sp2d)
                    {
                        $jumlah = BmrspModel::where('id_urs', $id_urs)->where('no_sp2d', $request->no_sp2d)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]); 
                        }
                    }          
                    if($cekData->no_bmrsp != $request->no_pesanan)
                    {
                        $jumlah = BmrspModel::where('id_urs', $id_urs)->where('no_bmrsp', $request->no_pesanan)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]); 
                        }
                    }
                    if($jumlah==0)
                    { 
                        $data = BmrspModel::where('id_bmrsp', $request->id_bmrsp)->first();  
                        $data->no_sp2d = $request->no_sp2d;
                        $data->kd_mak = $request->kd_mak;                 
                        $data->no_bmrsp = $request->no_pesanan;
                        $data->tgl_bmrsp = $request->tgl_pesanan;
                        $data->nilai_bmrsp = $request->nilai_pesanan;
                        $data->status_bmrsp = 0;
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
        $data = BmrspModel::where('id_bmrsp',$request->id_bmrsp)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BmrspModel::where('id_bmrsp', $request->id_bmrsp)->first();   
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
        $id_fk = $barisrektorat->id_urs;

        $jumlah_bk = BarangMasukRumahSakitModel::where('id_bmrsp', $request->id_bmrsp)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BmrspModel::where('id_bmrsp', $request->id_bmrsp)->first();

            $total_nilai_bmrs=0;
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::where('id_bmrsp', $request->id_bmrsp)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                $hrg_bmrs = $barisbmrs->hrg_bmrs;
                $nilai_bmrs = $jmlh_awal_bmrs * $hrg_bmrs;
                $total_nilai_bmrs = $total_nilai_bmrs + $nilai_bmrs;
            }

            if($cek_data->nilai_bmrsp != $total_nilai_bmrs)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $databarangmasukrumahsakit = BarangMasukRumahSakitModel::where('id_bmrsp', $request->id_bmrsp)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databarangmasukrumahsakit as $barisbmrs)
                {
                    $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                    $hrg_bmrs = $barisbmrs->hrg_bmrs;
                    $nilai_bmrs = $jmlh_awal_bmrs * $hrg_bmrs;
                    $total_nilai_bmrs = $total_nilai_bmrs + $nilai_bmrs;

                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmrs->kd_brg)->first();
                    $databmupdateitemnilai->nilai_brg = $databmupdateitemnilai->nilai_brg + $nilai_bmrs;
                    $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_awal_bmrs;
                    $databmupdateitemnilai->save();
                }
            }
            $cek_data->status_bmrsp = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}