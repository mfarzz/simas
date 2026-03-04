<?php

namespace App\Http\Controllers\BarangMasuk;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BmfpModel;
use App\Models\FakultasJabatanModel;
use App\Models\MataAnggaranModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BmfpCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();
        $tahun_anggaran = session('tahun_anggaran');

        if(request()->ajax()) {
            return datatables()->of(BmfpModel::
            join('mata_anggaran_kegiatan','barang_masuk_fakultas_pesanan.kd_mak','=','mata_anggaran_kegiatan.kd_mak')
            ->where('barang_masuk_fakultas_pesanan.id_fk',$datafakultas->id_fk)
            ->whereYear('barang_masuk_fakultas_pesanan.tgl_bmfp',$tahun_anggaran)
            ->get())
            ->addColumn('id_bmfp', function ($data) {
                return $data->id_bmfp;
            })
            ->addColumn('id_bmfp_en', function ($data) {
                $id_bmfp_en = Crypt::encryptString($data->id_bmfp);
                return $id_bmfp_en;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_mata_anggaran = MataAnggaranModel::orderby('kd_mak')->where('sts_mak',1)->get();
        return view('BarangMasuk.Khusus.Fakultas.Pesanan.index',['daftar_mata_anggaran'=> $daftar_mata_anggaran]);
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
        $thn_nota = substr($request->tgl_pesanan,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_nota)
        {
            if($request->id_bmfp == "")
            {
                $jumlah_belum = BmfpModel::where('id_fk', $id_fk)->where('status_bmfp', 0)->count();
                if($jumlah_belum >0)
                {
                    return response()->json(['status' => 5]);
                }
                else
                {
                    $jumlah = BmfpModel::where('id_fk', $id_fk)->where('no_bmfp', $request->no_pesanan)->where('no_sp2d', $request->no_sp2d)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $data = new BmfpModel();
                        $data->id_fk = $id_fk;
                        $data->no_sp2d = $request->no_sp2d;
                        $data->kd_mak = $request->kd_mak;
                        $data->no_bmfp = $request->no_pesanan;
                        $data->tgl_bmfp = $request->tgl_pesanan;
                        $data->nilai_bmfp = $request->nilai_pesanan;
                        $data->status_bmfp = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
            else
            {
                $cekData = BmfpModel::where('id_fk', $id_fk)->where('id_bmfp', $request->id_bmfp)->first();
                if($cekData->no_sp2d == $request->no_sp2d and $cekData->kd_mak == $request->kd_mak and $cekData->no_bmfp == $request->no_pesanan and $cekData->tgl_bmfp == $request->tgl_pesanan and $cekData->nilai_bmfp == $request->nilai_pesanan )
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $jumlah=0;
                    if($cekData->no_sp2d != $request->no_sp2d)
                    {
                        $jumlah = BmfpModel::where('id_fk', $id_fk)->where('no_sp2d', $request->no_sp2d)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                    }
                    if($cekData->no_bmfp != $request->no_pesanan)
                    {
                        $jumlah = BmfpModel::where('id_fk', $id_fk)->where('no_bmfp', $request->no_pesanan)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                    }
                    if($jumlah==0)
                    {
                        $data = BmfpModel::where('id_bmfp', $request->id_bmfp)->first();
                        $data->no_sp2d = $request->no_sp2d;
                        $data->kd_mak = $request->kd_mak;
                        $data->no_bmfp = $request->no_pesanan;
                        $data->tgl_bmfp = $request->tgl_pesanan;
                        $data->nilai_bmfp = $request->nilai_pesanan;
                        $data->status_bmfp = 0;
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
        $data = BmfpModel::where('id_bmfp',$request->id_bmfp)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BmfpModel::where('id_bmfp', $request->id_bmfp)->first();
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

        $jumlah_bk = BarangMasukFakultasModel::where('id_bmfp', $request->id_bmfp)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BmfpModel::where('id_bmfp', $request->id_bmfp)->first();

            // Hitung total nilai dengan 1 query agregat, tanpa loop
            $total_nilai_bmf = BarangMasukFakultasModel::where('id_bmfp', $request->id_bmfp)
                ->selectRaw('COALESCE(SUM(jmlh_awal_bmf * hrg_bmf), 0) as total')
                ->value('total');

            if($cek_data->nilai_bmfp != $total_nilai_bmf)
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                // Ambil semua item sekali saja (bukan 2 kali)
                $databarangmasukfakultas = BarangMasukFakultasModel::where('id_bmfp', $request->id_bmfp)
                    ->orderBy('kd_brg', 'asc')
                    ->get();

                // Kumpulkan delta stok dan nilai per kd_brg
                $updateMap = [];
                foreach($databarangmasukfakultas as $barisbmf)
                {
                    $kd = $barisbmf->kd_brg;
                    if(!isset($updateMap[$kd])) {
                        $updateMap[$kd] = ['stok' => 0, 'nilai' => 0];
                    }
                    $updateMap[$kd]['stok']  += $barisbmf->jmlh_awal_bmf;
                    $updateMap[$kd]['nilai'] += $barisbmf->jmlh_awal_bmf * $barisbmf->hrg_bmf;
                }

                // Satu UPDATE per kd_brg, bukan SELECT+save per baris
                foreach($updateMap as $kd_brg => $delta)
                {
                    DB::table('barang')->where('kd_brg', $kd_brg)->update([
                        'stok_brg'  => DB::raw('stok_brg + ' . $delta['stok']),
                        'nilai_brg' => DB::raw('nilai_brg + ' . $delta['nilai']),
                    ]);
                }
            }
            $cek_data->status_bmfp = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}
