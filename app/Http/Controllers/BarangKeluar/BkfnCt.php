<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\BkfnModel;
use App\Models\BkpfModel;
use App\Models\FakultasJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BkfnCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();
        $tahun_anggaran = session('tahun_anggaran');
        if(request()->ajax()) {
            return datatables()->of(BkfnModel::join('barang_keluar_penerima_fakultas','barang_keluar_fakultas_nota.id_bkpf','=','barang_keluar_penerima_fakultas.id_bkpf')
            ->where('barang_keluar_fakultas_nota.id_fk',$datafakultas->id_fk)
            ->whereYear('barang_keluar_fakultas_nota.tgl_bkfn',$tahun_anggaran)
            ->get())
            ->addColumn('id_bkfn', function ($data) {
                return $data->id_bkfn;
            })
            ->addColumn('id_bkfn_en', function ($data) {
                $id_bkfn_en = Crypt::encryptString($data->id_bkfn);
                return $id_bkfn_en;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_penerima = BkpfModel::where('id_fk',$datafakultas->id_fk)->where('status_bkpf',1)->orderby('nm_bkpf')->get();
        return view('BarangKeluar.Khusus.Fakultas.Nota.index',['daftar_penerima'=>$daftar_penerima]);
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }

    public function getPenerima(Request $request){
        $user_id = auth()->user()->id;
        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();
        $idUnitjabatan = BkpfModel::where('id_fk', $datafakultas->id_fk)->pluck('id_bkpf','nm_bkpf');
        return response()->json($idUnitjabatan);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $barisfakultas = FakultasJabatanModel::where('id_fkj','=',$id_fkj)->first();
        $id_fk = $barisfakultas->id_fk;
        $thn_nota = substr($request->tgl_nota,0,4);
        $tahun_anggaran = session('tahun_anggaran');
        if($tahun_anggaran == $thn_nota)
        {
            if($request->id_bkfn == "")
            {
                $jumlah_belum = BkfnModel::where('id_fk', $id_fk)->where('status_bkfn', 0)->count();
                if($jumlah_belum >0)
                {
                    return response()->json(['status' => 5]);
                }
                else
                {
                    $jumlah = BkfnModel::where('id_fk', $id_fk)->where('no_bkfn', $request->no_nota)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                    else
                    {
                        $data = new BkfnModel();
                        $data->id_fk = $id_fk;
                        $data->id_bkpf = $request->penerima;
                        $data->no_bkfn = $request->no_nota;
                        $data->tgl_bkfn = $request->tgl_nota;
                        $data->status_bkfn = 0;
                        $data->user_id = $user_id;
                        $data->save();
                        return response()->json(['status' => 1]);
                    }
                }
            }
            else
            {
                $cekData = BkfnModel::where('id_fk', $id_fk)->where('id_bkfn', $request->id_bkfn)->first();
                if($cekData->no_bkfn == $request->no_nota and $cekData->tgl_bkfn == $request->tgl_nota and $cekData->id_bkpf == $request->penerima )
                {
                    return response()->json(['status' => 3]);
                }
                else
                {
                    $jumlah=0;
                    if($cekData->no_bkfn != $request->no_nota)
                    {
                        $jumlah = BkfnModel::where('id_fk', $id_fk)->where('no_bkfn', $request->no_nota)->count();
                        if($jumlah>0)
                        {
                            return response()->json(['status' => 2]);
                        }
                    }
                    if($jumlah==0)
                    {
                        $data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();
                        $data->no_bkfn = $request->no_nota;
                        $data->tgl_bkfn = $request->tgl_nota;
                        $data->id_bkpf = $request->penerima;
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
        $data = BkfnModel::where('id_bkfn',$request->id_bkfn)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();
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

        $jumlah_bk = BarangKeluarFakultasModel::where('id_bkfn', $request->id_bkfn)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BkfnModel::where('id_bkfn', $request->id_bkfn)->first();
            $databarangkeluarfakultas = BarangKeluarFakultasModel::where('id_bkfn', $cek_data->id_bkfn)
                ->orderBy('kd_brg', 'asc')
                ->get();

            // Cache barang records to avoid re-querying the same row multiple times
            $barangCache = [];

            foreach ($databarangkeluarfakultas as $barisbkf)
            {
                $kd_brg = $barisbkf->kd_brg;

                // Load and cache barang once per kd_brg
                if (!isset($barangCache[$kd_brg])) {
                    $barangCache[$kd_brg] = BarangModel::where('kd_brg', $kd_brg)->first();
                }

                $proses = 0;
                $jumlah_keluar = $barisbkf->jmlh_bkf;

                $databarangmasukfakultas = BarangMasukFakultasModel::where('id_fk', $id_fk)
                    ->where('kd_brg', $kd_brg)
                    ->where('sisa_bmf', '!=', 0)
                    ->orderBy('tglperolehan_bmf', 'asc')
                    ->get();

                foreach ($databarangmasukfakultas as $barisbmf)
                {
                    if ($proses == 1) break;

                    $sisabmf = $barisbmf->sisa_bmf;

                    if ($jumlah_keluar <= $sisabmf)
                    {
                        $sisa       = $sisabmf - $jumlah_keluar;
                        $nilai_baru = $barisbmf->hrg_bmf * $jumlah_keluar;

                        // Update sisa stok barang masuk directly (avoid re-fetch)
                        BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)
                            ->update(['sisa_bmf' => $sisa]);

                        $databkfdetail = new BarangKeluarFakultasDetailModel();
                        $databkfdetail->id_bkf   = $barisbkf->id_bkf;
                        $databkfdetail->id_bmf   = $barisbmf->id_bmf;
                        $databkfdetail->jmlh_bkfd = $jumlah_keluar;
                        $databkfdetail->user_id  = $user_id;
                        $databkfdetail->save();

                        // Update cached barang
                        $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                        $barangCache[$kd_brg]->stok_brg  -= $jumlah_keluar;
                        $proses = 1;
                    }
                    else
                    {
                        $sisa = $jumlah_keluar - $sisabmf;
                        if ($sisa >= 0)
                        {
                            $nilai_baru = $barisbmf->hrg_bmf * $sisabmf;

                            BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)
                                ->update(['sisa_bmf' => 0]);

                            $databkdetail = new BarangKeluarFakultasDetailModel();
                            $databkdetail->id_bkf    = $barisbkf->id_bkf;
                            $databkdetail->id_bmf    = $barisbmf->id_bmf;
                            $databkdetail->jmlh_bkfd = $sisabmf;
                            $databkdetail->user_id   = $user_id;
                            $databkdetail->save();

                            $jumlah_keluar = $sisa;

                            $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                            $barangCache[$kd_brg]->stok_brg  -= $sisabmf;
                            // proses stays 0, continue to next batch row
                        }
                        else
                        {
                            $sisa       = $sisabmf - $jumlah_keluar;
                            $nilai_baru = $barisbmf->hrg_bmf * $jumlah_keluar;

                            BarangMasukFakultasModel::where('id_bmf', $barisbmf->id_bmf)
                                ->update(['sisa_bmf' => $sisa]);

                            $databkdetail = new BarangKeluarFakultasDetailModel();
                            $databkdetail->id_bkf    = $barisbkf->id_bkf;
                            $databkdetail->id_bmf    = $barisbmf->id_bmf;
                            $databkdetail->jmlh_bkfd = $sisabmf;
                            $databkdetail->user_id   = $user_id;
                            $databkdetail->save();

                            $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                            $barangCache[$kd_brg]->stok_brg  -= $jumlah_keluar;
                            $proses = 1;
                        }
                    }
                }

                // Final stok reconciliation: set stok to (original total - jmlh_bkf)
                $stoktotal = BarangModel::where('kd_brg', $kd_brg)->value('stok_brg');
                DB::table('barang')->where('kd_brg', $kd_brg)->update([
                    'stok_brg'  => $stoktotal - $barisbkf->jmlh_bkf,
                    'nilai_brg' => $barangCache[$kd_brg]->nilai_brg,
                ]);
                // Keep cache in sync
                $barangCache[$kd_brg]->stok_brg = $stoktotal - $barisbkf->jmlh_bkf;
            }

            $cek_data->status_bkfn = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}
