<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangModel;
use App\Models\BkprModel;
use App\Models\BkrnModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BkrnCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();
        if(request()->ajax()) {
            return datatables()->of(BkrnModel::join('barang_keluar_penerima_rektorat','barang_keluar_rektorat_nota.id_bkpr','=','barang_keluar_penerima_rektorat.id_bkpr')
            ->where('barang_keluar_rektorat_nota.id_ur',$datarektorat->id_ur)
            ->get())
            ->addColumn('id_bkrn', function ($data) {
                return $data->id_bkrn;
            })
            ->addColumn('id_bkrn_en', function ($data) {
                $id_bkrn_en = Crypt::encryptString($data->id_bkrn);
                return $id_bkrn_en;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $daftar_penerima = BkprModel::where('id_ur',$datarektorat->id_ur)->where('status_bkpr',1)->orderby('nm_bkpr')->get();
        return view('BarangKeluar.Khusus.Rektorat.Nota.index',['daftar_penerima'=>$daftar_penerima]);
    }

    public function cek(Request $request)
    {
        $tgl_awal = Crypt::encryptString($request->tgl_awal);
        $tgl_akhir = Crypt::encryptString($request->tgl_akhir);
        return response()->json(['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]);
    }

    public function getPenerima(Request $request){
        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();
        $idUnitjabatan = BkrnModel::where('id_ur', $datarektorat->id_ur)->pluck('id_bkpr','nm_bkpr');
        return response()->json($idUnitjabatan);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date("Y-m-d");
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;
        if($request->id_bkrn == "")
        {
            $jumlah_belum = BkrnModel::where('id_ur', $id_ur)->where('status_bkrn', 0)->count();
            if($jumlah_belum >0)
            {
                return response()->json(['status' => 5]);
            }
            else
            {
                $jumlah = BkrnModel::where('id_ur', $id_ur)->where('no_bkrn', $request->no_nota)->count();
                if($jumlah>0)
                {
                    return response()->json(['status' => 2]);
                }
                else
                {
                    $data = new BkrnModel();
                    $data->id_ur = $id_ur;
                    $data->id_bkpr = $request->penerima;
                    $data->no_bkrn = $request->no_nota;
                    $data->tgl_bkrn = $request->tgl_nota;
                    $data->status_bkrn = 0;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 1]);
                }
            }
        }
        else
        {
            $cekData = BkrnModel::where('id_ur', $id_ur)->where('id_bkrn', $request->id_bkrn)->first();
            if($cekData->no_bkrn == $request->no_nota and $cekData->tgl_bkrn == $request->tgl_nota and $cekData->id_bkpr == $request->penerima )
            {
                return response()->json(['status' => 3]);
            }
            else
            {
                $jumlah=0;
                if($cekData->no_bkrn != $request->no_nota)
                {
                    $jumlah = BkrnModel::where('id_ur', $id_ur)->where('no_bkrn', $request->no_nota)->count();
                    if($jumlah>0)
                    {
                        return response()->json(['status' => 2]);
                    }
                }
                if($jumlah==0)
                {
                    $data = BkrnModel::where('id_bkrn', $request->id_bkrn)->first();
                    $data->no_bkrn = $request->no_nota;
                    $data->tgl_bkrn = $request->tgl_nota;
                    $data->id_bkpr = $request->penerima;
                    $data->user_id = $user_id;
                    $data->save();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }

    public function edit(Request $request)
    {
        $data = BkrnModel::where('id_bkrn',$request->id_bkrn)->first();
        return Response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = BkrnModel::where('id_bkrn', $request->id_bkrn)->first();
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

        $jumlah_bk = BarangKeluarRektoratModel::where('id_bkrn', $request->id_bkrn)->count();
        if($jumlah_bk == 0)
        {
            return response()->json(['status' => 2]);
        }
        else
        {
            $cek_data = BkrnModel::where('id_bkrn', $request->id_bkrn)->first();
            $databarangkeluarrektorat = BarangKeluarRektoratModel::where('id_bkrn', $cek_data->id_bkrn)
                ->orderBy('kd_brg', 'asc')
                ->get();

            // Cache barang records to avoid re-querying the same row multiple times
            $barangCache = [];

            foreach ($databarangkeluarrektorat as $barisbkr)
            {
                $kd_brg = $barisbkr->kd_brg;

                if (!isset($barangCache[$kd_brg])) {
                    $barangCache[$kd_brg] = BarangModel::where('kd_brg', $kd_brg)->first();
                }

                $proses = 0;
                $jumlah_keluar = $barisbkr->jmlh_bkr;

                $databarangmasukrektorat = BarangMasukRektoratModel::where('id_ur', $id_ur)
                    ->where('kd_brg', $kd_brg)
                    ->where('sisa_bmr', '!=', 0)
                    ->orderBy('tglperolehan_bmr', 'asc')
                    ->get();

                foreach ($databarangmasukrektorat as $barisbmr)
                {
                    if ($proses == 1) break;

                    $sisabmr = $barisbmr->sisa_bmr;

                    if ($jumlah_keluar <= $sisabmr)
                    {
                        $sisa       = $sisabmr - $jumlah_keluar;
                        $nilai_baru = $barisbmr->hrg_bmr * $jumlah_keluar;

                        BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)
                            ->update(['sisa_bmr' => $sisa]);

                        $databkfdetail = new BarangKeluarRektoratDetailModel();
                        $databkfdetail->id_bkr    = $barisbkr->id_bkr;
                        $databkfdetail->id_bmr    = $barisbmr->id_bmr;
                        $databkfdetail->jmlh_bkrd = $jumlah_keluar;
                        $databkfdetail->user_id   = $user_id;
                        $databkfdetail->save();

                        $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                        $barangCache[$kd_brg]->stok_brg  -= $jumlah_keluar;
                        $proses = 1;
                    }
                    else
                    {
                        $sisa = $jumlah_keluar - $sisabmr;
                        if ($sisa >= 0)
                        {
                            $nilai_baru = $barisbmr->hrg_bmr * $sisabmr;

                            BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)
                                ->update(['sisa_bmr' => 0]);

                            $databkdetail = new BarangKeluarRektoratDetailModel();
                            $databkdetail->id_bkr    = $barisbkr->id_bkr;
                            $databkdetail->id_bmr    = $barisbmr->id_bmr;
                            $databkdetail->jmlh_bkrd = $sisabmr;
                            $databkdetail->user_id   = $user_id;
                            $databkdetail->save();

                            $jumlah_keluar = $sisa;

                            $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                            $barangCache[$kd_brg]->stok_brg  -= $sisabmr;
                            // proses stays 0, continue to next batch row
                        }
                        else
                        {
                            $sisa       = $sisabmr - $jumlah_keluar;
                            $nilai_baru = $barisbmr->hrg_bmr * $jumlah_keluar;

                            BarangMasukRektoratModel::where('id_bmr', $barisbmr->id_bmr)
                                ->update(['sisa_bmr' => $sisa]);

                            $databkdetail = new BarangKeluarRektoratDetailModel();
                            $databkdetail->id_bkr    = $barisbkr->id_bkr;
                            $databkdetail->id_bmr    = $barisbmr->id_bmr;
                            $databkdetail->jmlh_bkrd = $sisabmr;
                            $databkdetail->user_id   = $user_id;
                            $databkdetail->save();

                            $barangCache[$kd_brg]->nilai_brg -= $nilai_baru;
                            $barangCache[$kd_brg]->stok_brg  -= $jumlah_keluar;
                            $proses = 1;
                        }
                    }
                }

                $stoktotal = BarangModel::where('kd_brg', $kd_brg)->value('stok_brg');
                DB::table('barang')->where('kd_brg', $kd_brg)->update([
                    'stok_brg'  => $stoktotal - $barisbkr->jmlh_bkr,
                    'nilai_brg' => $barangCache[$kd_brg]->nilai_brg,
                ]);
                $barangCache[$kd_brg]->stok_brg = $stoktotal - $barisbkr->jmlh_bkr;
            }

            $cek_data->status_bkrn = 1;
            $cek_data->user_id = $user_id;
            $cek_data->save();
            return response()->json(['status' => 1]);
        }
    }
}
