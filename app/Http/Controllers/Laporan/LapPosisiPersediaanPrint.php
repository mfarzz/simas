<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\JabpenuuModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpursdetitmModel;
use App\Models\OpurdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class LapPosisiPersediaanPrint extends Controller
{
    Public Function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        // Single delete upfront — no repeated count+delete per branch
        DB::table('temp_barang_masuk')
            ->where('user_id', $user_id)
            ->where('jns_tbm', '1')
            ->delete();

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        if($lokasi == "690522009KD")
        {
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();

            $idBmrList = $databarangmasukrektorat->pluck('id_bmr')->toArray();
            $keluarSumsR = DB::table('barang_keluar_rektorat_detail')
                ->join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                ->whereIn('barang_keluar_rektorat_detail.id_bmr', $idBmrList)
                ->where('tglambil_bkr', '<=', $tgl_akhir)
                ->selectRaw('id_bmr, COALESCE(SUM(jmlh_bkrd), 0) as total')
                ->groupBy('id_bmr')
                ->pluck('total', 'id_bmr');

            $opsikCache = [];
            $batchInsert = [];
            foreach($databarangmasukrektorat as $barisbmr)
            {
                if($barisbmr->sisa_bmr==$barisbmr->jmlh_awal_bmr)
                {
                    $batchInsert[] = [
                        'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $barisbmr->jmlh_awal_bmr,
                        'hrg_tbm' => $barisbmr->hrg_bmr, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $tjmlh_bkrd = $keluarSumsR[$barisbmr->id_bmr] ?? 0;
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;

                    $tjmlh_opsik = 0;
                    $kd_brg = $barisbmr->kd_brg;
                    if (!array_key_exists($kd_brg, $opsikCache)) {
                        $opsikCache[$kd_brg] = OpsikUrDetModel::
                            join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                            ->where('kd_brg', '=', $kd_brg)
                            ->where('tgl_opur', '<=', $tgl_akhir)
                            ->first();
                    }
                    if ($opsikCache[$kd_brg]) {
                        $stok_sistem_oprdet = $opsikCache[$kd_brg]->stok_sistem_oprdet;
                        $stok_opsik_oprdet  = $opsikCache[$kd_brg]->stok_opsik_oprdet;
                        if ($stok_sistem_oprdet < $stok_opsik_oprdet)
                            $tjmlh_opsik = $stok_opsik_oprdet - $stok_sistem_oprdet;
                        elseif ($stok_sistem_oprdet > $stok_opsik_oprdet)
                            $tjmlh_opsik = $stok_opsik_oprdet - $stok_sistem_oprdet;
                    }

                    $batchInsert[] = [
                        'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => ($jmlh_awal_bmr - $tjmlh_bkrd) + $tjmlh_opsik,
                        'hrg_tbm' => $barisbmr->hrg_bmr, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsert, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);
        }
        elseif($lokasi == "690522020KD")
        {
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();

            $idBmrsList = $databarangmasukrumahsakit->pluck('id_bmrs')->toArray();
            $keluarSumsRS = DB::table('barang_keluar_rumah_sakit_detail')
                ->join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                ->whereIn('barang_keluar_rumah_sakit_detail.id_bmrs', $idBmrsList)
                ->where('tglambil_bkrs', '<=', $tgl_akhir)
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_bkrd), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $opsikKurangRS = DB::table('opfik_rumah_sakit_detail_item')
                ->join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList)
                ->where('tgl_opurs', '<=', $tgl_akhir)
                ->where('id_bkrsd', '<', '1')
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_opurdetitm), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $opsikTambahRS = DB::table('opfik_rumah_sakit_detail_item')
                ->join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList)
                ->where('tgl_opurs', '<=', $tgl_akhir)
                ->where('id_bkrsd', '>', 0)
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_opursdetitm), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $batchInsert = [];
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                if($barisbmrs->sisa_bmrs==$barisbmrs->jmlh_awal_bmrs)
                {
                    $batchInsert[] = [
                        'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $barisbmrs->jmlh_awal_bmrs,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $id = $barisbmrs->id_bmrs;
                    $sisa = ($barisbmrs->jmlh_awal_bmrs - ($keluarSumsRS[$id] ?? 0))
                          + ($opsikTambahRS[$id] ?? 0) - ($opsikKurangRS[$id] ?? 0);
                    $batchInsert[] = [
                        'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $sisa,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsert, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);
        }
        else if($lokasi == "690522000KD")
        {
            // Branch: Universitas — Rektorat + RS + Fakultas
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();

            $idBmrList2 = $databarangmasukrektorat->pluck('id_bmr')->toArray();
            $keluarSumsR2 = DB::table('barang_keluar_rektorat_detail')
                ->join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                ->whereIn('barang_keluar_rektorat_detail.id_bmr', $idBmrList2)
                ->where('tglambil_bkr', '<=', $tgl_akhir)
                ->selectRaw('id_bmr, COALESCE(SUM(jmlh_bkrd), 0) as total')
                ->groupBy('id_bmr')
                ->pluck('total', 'id_bmr');

            $opsikKurangR2 = DB::table('opfik_rektorat_detail_item')
                ->join('opsik_rektorat_detail','opfik_rektorat_detail_item.id_opurdet','=','opsik_rektorat_detail.id_opurdet')
                ->join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                ->whereIn('opfik_rektorat_detail_item.id_bmr', $idBmrList2)
                ->where('tgl_opur', '<=', $tgl_akhir)
                ->where('id_bkrd', '<', '1')
                ->selectRaw('id_bmr, COALESCE(SUM(jmlh_opfkdetitm), 0) as total')
                ->groupBy('id_bmr')
                ->pluck('total', 'id_bmr');

            $opsikTambahR2 = DB::table('opfik_rektorat_detail_item')
                ->join('opsik_rektorat_detail','opfik_rektorat_detail_item.id_opurdet','=','opsik_rektorat_detail.id_opurdet')
                ->join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                ->whereIn('opfik_rektorat_detail_item.id_bmr', $idBmrList2)
                ->where('tgl_opur', '<=', $tgl_akhir)
                ->where('id_bkrd', '>', 0)
                ->selectRaw('id_bmr, COALESCE(SUM(jmlh_opurdetitm), 0) as total')
                ->groupBy('id_bmr')
                ->pluck('total', 'id_bmr');

            $batchInsert = [];
            foreach($databarangmasukrektorat as $barisbmr)
            {
                if($barisbmr->sisa_bmr==$barisbmr->jmlh_awal_bmr)
                {
                    $batchInsert[] = [
                        'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $barisbmr->jmlh_awal_bmr,
                        'hrg_tbm' => $barisbmr->hrg_bmr, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $id = $barisbmr->id_bmr;
                    $sisa = ($barisbmr->jmlh_awal_bmr - ($keluarSumsR2[$id] ?? 0))
                          + ($opsikTambahR2[$id] ?? 0) - ($opsikKurangR2[$id] ?? 0);
                    $batchInsert[] = [
                        'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $sisa,
                        'hrg_tbm' => $barisbmr->hrg_bmr, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsert, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);

            // RS sub-loop for 690522000KD — use batch insert
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();

            $idBmrsList2 = $databarangmasukrumahsakit->pluck('id_bmrs')->toArray();
            $keluarSumsRS2 = DB::table('barang_keluar_rumah_sakit_detail')
                ->join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                ->whereIn('barang_keluar_rumah_sakit_detail.id_bmrs', $idBmrsList2)
                ->where('tglambil_bkrs', '<=', $tgl_akhir)
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_bkrd), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $opsikKurangRS2 = DB::table('opfik_rumah_sakit_detail_item')
                ->join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList2)
                ->where('tgl_opurs', '<=', $tgl_akhir)
                ->where('id_bkrsd', '<', '1')
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_opursdetitm), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $opsikTambahRS2 = DB::table('opfik_rumah_sakit_detail_item')
                ->join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList2)
                ->where('tgl_opurs', '<=', $tgl_akhir)
                ->where('id_bkrsd', '>', 0)
                ->selectRaw('id_bmrs, COALESCE(SUM(jmlh_opursdetitm), 0) as total')
                ->groupBy('id_bmrs')
                ->pluck('total', 'id_bmrs');

            $batchInsertRS2 = [];
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                if($barisbmrs->sisa_bmrs==$barisbmrs->jmlh_awal_bmrs)
                {
                    $batchInsertRS2[] = [
                        'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $barisbmrs->jmlh_awal_bmrs,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $id = $barisbmrs->id_bmrs;
                    $sisa = ($barisbmrs->jmlh_awal_bmrs - ($keluarSumsRS2[$id] ?? 0))
                          + ($opsikTambahRS2[$id] ?? 0) - ($opsikKurangRS2[$id] ?? 0);
                    $batchInsertRS2[] = [
                        'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $sisa,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsertRS2, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);

            // Fakultas sub-loop for 690522000KD — use batch insert
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();

            $idBmfList2 = $databarangmasukfakultas->pluck('id_bmf')->toArray();
            $keluarSumsF2 = DB::table('barang_keluar_fakultas_detail')
                ->join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                ->whereIn('barang_keluar_fakultas_detail.id_bmf', $idBmfList2)
                ->where('tglambil_bkf', '<=', $tgl_akhir)
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_bkfd), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $opsikKurangF2 = DB::table('opfik_fakultas_detail_item')
                ->join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $idBmfList2)
                ->where('tgl_opfk', '<=', $tgl_akhir)
                ->where('id_bkfd', '<', '1')
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_opfkdetitm), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $opsikTambahF2 = DB::table('opfik_fakultas_detail_item')
                ->join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $idBmfList2)
                ->where('tgl_opfk', '<=', $tgl_akhir)
                ->where('id_bkfd', '>', 0)
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_opfkdetitm), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $batchInsert2 = [];
            foreach($databarangmasukfakultas as $barisbmf)
            {
                if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                {
                    $batchInsert2[] = [
                        'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $barisbmf->jmlh_awal_bmf,
                        'hrg_tbm' => $barisbmf->hrg_bmf, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $id = $barisbmf->id_bmf;
                    $sisa = ($barisbmf->jmlh_awal_bmf - ($keluarSumsF2[$id] ?? 0))
                          + ($opsikTambahF2[$id] ?? 0) - ($opsikKurangF2[$id] ?? 0);
                    $batchInsert2[] = [
                        'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $sisa,
                        'hrg_tbm' => $barisbmf->hrg_bmf, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsert2, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);
        }
        else if($lokasi == "")
        {
            // No-op: already deleted at top
        }
        else
        {
            // Branch: per-Fakultas (kd_lks = specific lokasi)
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();

            $idBmfListE = $databarangmasukfakultas->pluck('id_bmf')->toArray();
            $keluarSumsFE = DB::table('barang_keluar_fakultas_detail')
                ->join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                ->whereIn('barang_keluar_fakultas_detail.id_bmf', $idBmfListE)
                ->where('tglambil_bkf', '<=', $tgl_akhir)
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_bkfd), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $opsikKurangFE = DB::table('opfik_fakultas_detail_item')
                ->join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $idBmfListE)
                ->where('tgl_opfk', '<=', $tgl_akhir)
                ->where('id_bkfd', '<', '1')
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_opfkdetitm), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $opsikTambahFE = DB::table('opfik_fakultas_detail_item')
                ->join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $idBmfListE)
                ->where('tgl_opfk', '<=', $tgl_akhir)
                ->where('id_bkfd', '>', 0)
                ->selectRaw('id_bmf, COALESCE(SUM(jmlh_opfkdetitm), 0) as total')
                ->groupBy('id_bmf')
                ->pluck('total', 'id_bmf');

            $batchInsertE = [];
            foreach($databarangmasukfakultas as $barisbmf)
            {
                if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                {
                    $batchInsertE[] = [
                        'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $barisbmf->jmlh_awal_bmf,
                        'hrg_tbm' => $barisbmf->hrg_bmf, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
                else
                {
                    $id = $barisbmf->id_bmf;
                    $sisa = ($barisbmf->jmlh_awal_bmf - ($keluarSumsFE[$id] ?? 0))
                          + ($opsikTambahFE[$id] ?? 0) - ($opsikKurangFE[$id] ?? 0);
                    $batchInsertE[] = [
                        'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $sisa,
                        'hrg_tbm' => $barisbmf->hrg_bmf, 'kd_lks' => $lokasi,
                        'user_id' => $user_id, 'jns_tbm' => 1,
                    ];
                }
            }
            foreach (array_chunk($batchInsertE, 500) as $chunk)
                DB::table('temp_barang_masuk')->insert($chunk);
        }

        function rupiah($angka){

            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;

        }

        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();
        $tgl = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tgl = strtoupper($tgl);

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN POSISI PERSEDIAN DI NERACA', 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, "UNTUK PERIODE YANG BERAKHIR TANGGAL $tgl", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "TAHUN ANGGARAN $tahunanggaran", 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', '', 10);

        PDF::ln(5);
        PDF::Cell(28, 0, "UAPKB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$datalokasi->nm_lks", 0, 1, 'L', 0, '', true);
        PDF::ln(0);
        PDF::Cell(28, 0, "Kode UAPKPB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$lokasi", 0, 1, 'L', 0, '', true);

        PDF::SetFont('times', 'b', 10);
        PDF::ln(5);
        PDF::Cell(28, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "URAIAN", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::SetFont('times', '', 10);
        //if($lokasi == "023170800677513009KD")
        //{
            $total_nilai = 0;
            $datalap = VLapPosisi4Model::
            join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
            ->where('v_lap_posisi4.user_id','=',$user_id)
            ->where('v_lap_posisi4.v_jns_tbm','=',1)
            ->orderby('v_lap_posisi4.v_kd_kt')
            ->get();
            foreach($datalap as $barislap)
            {
                $nilairp = rupiah($barislap->total_nilai);
                PDF::Cell(28, 0, "$barislap->kd_kt", 1, 0, 'C', 0, '', true);
                PDF::Cell(120, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
                PDF::Cell(40, 0, "$nilairp", 1, 1, 'R', 0, '', true);
                PDF::ln(0);
                $total_nilai = $total_nilai + $barislap->total_nilai;
            }
            $total_nilai2 = rupiah($total_nilai);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(28, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(120, 0, "Jumlah", 1, 0, 'R', 0, '', true);
            PDF::Cell(40, 0, "$total_nilai2", 1, 1, 'R', 0, '', true);
        //}
        //else if($lokasi == "023170800677513000KD")
        //{

        //}
        //else
        //{

        //}
        if($lokasi == "690522009KD")
        {
            $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 1)->first();
            $pejabatanop = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabur", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabur", 0, 1, 'C', 0, '', true);
            PDF::ln(20);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenur", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabpenur", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenur", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenur", 0, 1, 'C', 0, '', true);
        }
        else if($lokasi == "690522020KD")
        {
            $datarumahsakit = UnitRumahSakitModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 1)->first();
            $pejabatanop = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jaburs", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jaburs", 0, 1, 'C', 0, '', true);
            PDF::ln(20);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenurs", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabpenurs", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenurs", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenurs", 0, 1, 'C', 0, '', true);
        }
        else if($lokasi == "690522000KD")
        {
            $pejabatanpimpinan = JabpenuuModel::join('jabatan_universitas','jabatan_pengesahan_universitas.id_jabuni','=','jabatan_universitas.id_jabuni')->where('jabatan_pengesahan_universitas.id_jabuni', 1)->first();
            $pejabatanop = JabpenuuModel::join('jabatan_universitas','jabatan_pengesahan_universitas.id_jabuni','=','jabatan_universitas.id_jabuni')->where('jabatan_pengesahan_universitas.id_jabuni', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabuni", 0, 1, 'C', 0, '', true);
            PDF::ln(20);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabpenuni", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenuni", 0, 1, 'C', 0, '', true);
        }
        else
        {

            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
            $pejabatanop = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabfk", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabfk", 0, 1, 'C', 0, '', true);
            PDF::ln(20);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenfk", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabpenfk", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenfk", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenfk", 0, 1, 'C', 0, '', true);
        }




        PDF::Output('laporan_persedian.pdf');
    }
}
