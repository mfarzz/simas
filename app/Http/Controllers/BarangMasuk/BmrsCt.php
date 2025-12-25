<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\BmrsModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BmrsCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(BmrsModel::
            where('barang_masuk_rektorat_sp2d.id_ur',$datarektorat->id_ur)
            ->whereYear('barang_masuk_rektorat_sp2d.tgl_bmrs',$tahun_anggaran)
            ->get())
            ->addColumn('id_bmrs', function ($data) {
                return $data->id_bmrs; 
            })
            ->addColumn('id_bmrs_en', function ($data) {
                $id_bmrs_en = Crypt::encryptString($data->id_bmrs);
                return $id_bmrs_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('BarangMasuk.Khusus.Rektorat.Sp2d.index');
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
        if($request->id_bmrs == "")
        {
            $jumlah_belum = BmrsModel::where('id_ur', $id_ur)->where('status_bmrs', 0)->count();
            if($jumlah_belum >0)
            {
                return response()->json(['status' => 5]);
            }
            else
            {                
                $jumlah = BmrsModel::where('id_ur', $id_ur)->where('no_bmrs', $request->no_sp2d)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {
                    $data = new BmrsModel();
                    $data->id_ur = $id_ur;
                    $data->no_bmrs = $request->no_sp2d;
                    $data->tgl_bmrs = $request->tgl_sp2d;
                    $data->nilai_bmrs = $request->nilai_sp2d;
                    $data->status_bmrs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = BmrsModel::where('id_ur', $id_ur)->where('id_bmrs', $request->id_bmrs)->first();
            if($cekData->no_bmrs == $request->no_sp2d and $cekData->tgl_bmrs == $request->tgl_sp2d and $cekData->nilai_bmrs == $request->nilai_sp2d )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->no_bmrs != $request->no_sp2d)
                {
                    $jumlah = BmrsModel::where('id_ur', $id_ur)->where('no_bmrs', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BmrsModel::where('id_bmrs', $request->id_bmrs)->first();                   
                    $data->no_bmrs = $request->no_sp2d;
                    $data->tgl_bmrs = $request->tgl_sp2d;
                    $data->nilai_bmrs = $request->nilai_sp2d;
                    $data->status_bmrs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }        
    }

    public function edit(Request $request)
    {   
        $data = BmrsModel::where('id_bmrs',$request->id_bmrs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BmrsModel::where('id_bmrs', $request->id_bmrs)->first();   
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
        $id_ur = $barisrektorat->id_ur;

        $jumlah_bk = BarangMasukRektoratModel::where('id_bmrs', $request->id_bmrs)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BmrsModel::where('id_bmrs', $request->id_bmrs)->first();

            $total_nilai_bmr=0;
            $databarangmasukrektorat = BarangMasukRektoratModel::where('id_bmrs', $request->id_bmrs)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                $hrg_bmr = $barisbmr->hrg_bmr;
                $nilai_bmr = $jmlh_awal_bmr * $hrg_bmr;
                $total_nilai_bmr = $total_nilai_bmr + $nilai_bmr;
            }

            //if($cek_data->nilai_bmrs != $total_nilai_bmr)
            //{
              //  return response()->json(['status' => 3]);
            //}
            //else
            //{
                $databarangmasukrektorat = BarangMasukRektoratModel::where('id_bmrs', $request->id_bmrs)
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
            //}
            $cek_data->status_bmrs = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}