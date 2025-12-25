<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\BmrpModel;
use App\Models\FakultasJabatanModel;
use App\Models\MataAnggaranModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BmrpCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(BmrpModel::
            join('mata_anggaran_kegiatan','barang_masuk_rektorat_pesanan.kd_mak','=','mata_anggaran_kegiatan.kd_mak')
            ->where('barang_masuk_rektorat_pesanan.id_ur',$datarektorat->id_ur)
            ->whereYear('barang_masuk_rektorat_pesanan.tgl_bmrp',$tahun_anggaran)
            ->get())
            ->addColumn('id_bmrp', function ($data) {
                return $data->id_bmrp; 
            })
            ->addColumn('id_bmrp_en', function ($data) {
                $id_bmrp_en = Crypt::encryptString($data->id_bmrp);
                return $id_bmrp_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_mata_anggaran = MataAnggaranModel::orderby('kd_mak')->where('sts_mak',1)->get();
        return view('BarangMasuk.Khusus.Rektorat.Pesanan.index',['daftar_mata_anggaran'=> $daftar_mata_anggaran]);
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
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;        
        if($request->id_bmrp == "")
        {            
            $jumlah_belum = BmrpModel::where('id_ur', $id_ur)->where('status_bmrp', 0)->count();
            if($jumlah_belum >0)
            {
                return response()->json(['status' => 5]);
            }
            else
            {                
                $jumlah = BmrpModel::where('id_ur', $id_ur)->where('no_bmrp', $request->no_pesanan)->where('no_sp2d', $request->no_sp2d)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {                    
                    $data = new BmrpModel();
                    $data->id_ur = $id_ur;
                    $data->no_sp2d = $request->no_sp2d;
                    $data->kd_mak = $request->kd_mak;
                    $data->no_bmrp = $request->no_pesanan;
                    $data->tgl_bmrp = $request->tgl_pesanan;
                    $data->nilai_bmrp = $request->nilai_pesanan;
                    $data->status_bmrp = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = BmrpModel::where('id_ur', $id_ur)->where('id_bmrp', $request->id_bmrp)->first();
            if($cekData->no_sp2d == $request->no_sp2d and $cekData->kd_mak == $request->kd_mak and $cekData->no_bmrp == $request->no_pesanan and $cekData->tgl_bmrp == $request->tgl_pesanan and $cekData->nilai_bmrp == $request->nilai_pesanan )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;    
                if($cekData->no_sp2d != $request->no_sp2d)
                {
                    $jumlah = BmrpModel::where('id_ur', $id_ur)->where('no_sp2d', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }           
                if($cekData->no_bmrp != $request->no_pesanan)
                {
                    $jumlah = BmrpModel::where('id_ur', $id_ur)->where('no_bmrp', $request->no_pesanan)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BmrpModel::where('id_bmrp', $request->id_bmrp)->first();   
                    $data->no_sp2d = $request->no_sp2d;
                    $data->kd_mak = $request->kd_mak;                
                    $data->no_bmrp = $request->no_pesanan;
                    $data->tgl_bmrp = $request->tgl_pesanan;
                    $data->nilai_bmrp = $request->nilai_pesanan;
                    $data->status_bmrp = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }
    }

    public function edit(Request $request)
    {   
        $data = BmrpModel::where('id_bmrp',$request->id_bmrp)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BmrpModel::where('id_bmrp', $request->id_bmrp)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_fk = $barisrektorat->id_ur;

        $jumlah_bk = BarangMasukRektoratModel::where('id_bmrp', $request->id_bmrp)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BmrpModel::where('id_bmrp', $request->id_bmrp)->first();

            $total_nilai_bmr=0;
            $databarangmasukrektorat = BarangMasukRektoratModel::where('id_bmrp', $request->id_bmrp)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                $hrg_bmr = $barisbmr->hrg_bmr;
                $nilai_bmr = $jmlh_awal_bmr * $hrg_bmr;
                $total_nilai_bmr = $total_nilai_bmr + $nilai_bmr;
            }

            if($cek_data->nilai_bmrp != $total_nilai_bmr)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $databarangmasukrektorat = BarangMasukRektoratModel::where('id_bmrp', $request->id_bmrp)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databarangmasukrektorat as $barisbmr)
                {
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                    $hrg_bmr = $barisbmr->hrg_bmr;
                    $nilai_bmr = $jmlh_awal_bmr * $hrg_bmr;
                    $total_nilai_bmr = $total_nilai_bmr + $nilai_bmr;

                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmr->kd_brg)->first();
                    $databmupdateitemnilai->nilai_brg = $databmupdateitemnilai->nilai_brg + $nilai_bmr;
                    $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_awal_bmr;
                    $databmupdateitemnilai->save();
                }
            }
            $cek_data->status_bmrp = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}