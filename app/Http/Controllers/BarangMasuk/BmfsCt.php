<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BmfsModel;
use App\Models\FakultasJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class BmfsCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(BmfsModel::
            where('barang_masuk_fakultas_sp2d.id_fk',$datafakultas->id_fk)
            ->whereYear('barang_masuk_fakultas_sp2d.tgl_bmfs',$tahun_anggaran)
            ->get())
            ->addColumn('id_bmfs', function ($data) {
                return $data->id_bmfs; 
            })
            ->addColumn('id_bmfs_en', function ($data) {
                $id_bmfs_en = Crypt::encryptString($data->id_bmfs);
                return $id_bmfs_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('BarangMasuk.Khusus.Fakultas.Sp2d.index');
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
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        if($request->id_bmfs == "")
        {
            $jumlah_belum = BmfsModel::where('id_fk', $id_fk)->where('status_bmfs', 0)->count();
            if($jumlah_belum >0)
            {
                return response()->json(['status' => 5]);
            }
            else
            {
                $jumlah = BmfsModel::where('id_fk', $id_fk)->where('no_bmfs', $request->no_sp2d)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {
                    $data = new BmfsModel();
                    $data->id_fk = $id_fk;
                    $data->no_bmfs = $request->no_sp2d;
                    $data->tgl_bmfs = $request->tgl_sp2d;
                    $data->nilai_bmfs = $request->nilai_sp2d;
                    $data->status_bmfs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = BmfsModel::where('id_fk', $id_fk)->where('id_bmfs', $request->id_bmfs)->first();
            if($cekData->no_bmfs == $request->no_sp2d and $cekData->tgl_bmfs == $request->tgl_sp2d and $cekData->nilai_bmfs == $request->nilai_sp2d )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;               
                if($cekData->no_bmfs != $request->no_sp2d)
                {
                    $jumlah = BmfsModel::where('id_fk', $id_fk)->where('no_bmfs', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]); 
                    }
                }
                if($jumlah==0)
                { 
                    $data = BmfsModel::where('id_bmfs', $request->id_bmfs)->first();                   
                    $data->no_bmfs = $request->no_sp2d;
                    $data->tgl_bmfs = $request->tgl_sp2d;
                    $data->nilai_bmfs = $request->nilai_sp2d;
                    $data->status_bmfs = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }            
        }
    }

    public function edit(Request $request)
    {   
        $data = BmfsModel::where('id_bmfs',$request->id_bmfs)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BmfsModel::where('id_bmfs', $request->id_bmfs)->first();   
        $data->delete();         
        return Response()->json(0);
    }

    public function validasi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;

        $jumlah_bk = BarangMasukFakultasModel::where('id_bmfs', $request->id_bmfs)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BmfsModel::where('id_bmfs', $request->id_bmfs)->first();

            $total_nilai_bmf=0;
            $databarangmasukfakultas = BarangMasukFakultasModel::where('id_bmfs', $request->id_bmfs)
            ->orderBy('kd_brg','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                $hrg_bmf = $barisbmf->hrg_bmf;
                $nilai_bmf = $jmlh_awal_bmf * $hrg_bmf;
                $total_nilai_bmf = $total_nilai_bmf + $nilai_bmf;
            }

            if($cek_data->nilai_bmfs != $total_nilai_bmf)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $databarangmasukfakultas = BarangMasukFakultasModel::where('id_bmfs', $request->id_bmfs)
                ->orderBy('kd_brg','asc')
                ->get();
                foreach($databarangmasukfakultas as $barisbmf)
                {
                    $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                    $hrg_bmf = $barisbmf->hrg_bmf;
                    $nilai_bmf = $jmlh_awal_bmf * $hrg_bmf;
                    $total_nilai_bmf = $total_nilai_bmf + $nilai_bmf;

                    $databmupdateitemnilai = BarangModel::where('kd_brg', $barisbmf->kd_brg)->first();
                    $databmupdateitemnilai->nilai_brg = $databmupdateitemnilai->nilai_brg + $nilai_bmf;
                    $databmupdateitemnilai->stok_brg = $databmupdateitemnilai->stok_brg + $jmlh_awal_bmf;
                    $databmupdateitemnilai->save();
                }
            }
            $cek_data->status_bmfs = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}