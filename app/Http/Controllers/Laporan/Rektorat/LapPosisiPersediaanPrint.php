<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\JabpenuuModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikFkDetModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpursdetitmModel;
use App\Models\OpurdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikFakultasDetailItemModel;
use App\Models\VOpfikRektoratDetailItemModel;
use App\Models\VOpfikRumahSakitDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapPosisiPersediaanPrint extends Controller
{
    /**
     * Hapus cache perhitungan sementara untuk user agar perhitungan selalu fresh.
     */
    private function clearUserTemp(int $userId): void
    {
        TempBarangMasukModel::where('user_id', $userId)->where('jns_tbm', 1)->delete();
    }

    private function latestOpsikRektorat(string $kdBrg, string $tglAkhir)
    {
        return OpsikUrDetModel::join('opsik_rektorat', 'opsik_rektorat_detail.id_opur', '=', 'opsik_rektorat.id_opur')
            ->where('kd_brg', $kdBrg)
            ->where('tgl_opur', '<=', $tglAkhir)
            ->where('status_opur', 1)
            ->orderBy('tgl_opur', 'desc')
            ->select('opsik_rektorat_detail.*', 'opsik_rektorat.tgl_opur')
            ->first();
    }

    private function latestOpsikRumahSakit(string $kdBrg, string $tglAkhir)
    {
        return OpsikUrsDetModel::join('opsik_rumah_sakit', 'opsik_rumah_sakit_detail.id_opurs', '=', 'opsik_rumah_sakit.id_opurs')
            ->where('kd_brg', $kdBrg)
            ->where('tgl_opurs', '<=', $tglAkhir)
            ->where('status_opurs', 1)
            ->orderBy('tgl_opurs', 'desc')
            ->select('opsik_rumah_sakit_detail.*', 'opsik_rumah_sakit.tgl_opurs')
            ->first();
    }

    private function latestOpsikFakultas(string $kdBrg, string $tglAkhir, ?int $idFk = null)
    {
        $query = OpsikFkDetModel::join('opsik_fakultas', 'opsik_fakultas_detail.id_opfk', '=', 'opsik_fakultas.id_opfk')
            ->where('kd_brg', $kdBrg)
            ->where('tgl_opfk', '<=', $tglAkhir)
            ->where('status_opfk', 1);

        if ($idFk !== null) {
            $query->where('id_fk', $idFk);
        }

        return $query->orderBy('tgl_opfk', 'desc')
            ->select('opsik_fakultas_detail.*', 'opsik_fakultas.tgl_opfk', 'opsik_fakultas.id_fk')
            ->first();
    }

    private function hasBarangKeluarRektoratAfter(string $kdBrg, string $tglOpur): bool
    {
        return BarangKeluarRektoratModel::where('kd_brg', $kdBrg)
            ->where('tglambil_bkr', '>', $tglOpur)
            ->exists();
    }

    private function hasBarangKeluarRumahSakitAfter(string $kdBrg, string $tglOpur): bool
    {
        return BarangKeluarRumahSakitModel::where('kd_brg', $kdBrg)
            ->where('tglambil_bkrs', '>', $tglOpur)
            ->exists();
    }

    private function hasBarangKeluarFakultasAfter(string $kdBrg, string $tglOpur, ?int $idFk = null): bool
    {
        $query = BarangKeluarFakultasModel::where('kd_brg', $kdBrg)
            ->where('tglambil_bkf', '>', $tglOpur);

        if ($idFk !== null) {
            $query->where('id_fk', $idFk);
        }

        return $query->exists();
    }

    private function sumBarangKeluarRektorat(int $idBmr, string $startDate, string $endDate, bool $excludeEndDate = false): int
    {
        $query = BarangKeluarRektoratDetailModel::join('barang_keluar_rektorat', 'barang_keluar_rektorat_detail.id_bkr', '=', 'barang_keluar_rektorat.id_bkr')
            ->where('id_bmr', $idBmr)
            ->whereBetween('tglambil_bkr', [$startDate, $endDate]);

        if ($excludeEndDate) {
            $query->where('tglambil_bkr', '<>', $endDate);
        }

        return (int) $query->sum('jmlh_bkrd');
    }

    private function sumBarangKeluarRumahSakit(int $idBmrs, string $startDate, string $endDate, bool $excludeEndDate = false): int
    {
        $query = BarangKeluarRumahSakitDetailModel::join('barang_keluar_rumah_sakit', 'barang_keluar_rumah_sakit_detail.id_bkrs', '=', 'barang_keluar_rumah_sakit.id_bkrs')
            ->where('id_bmrs', $idBmrs)
            ->whereBetween('tglambil_bkrs', [$startDate, $endDate]);

        if ($excludeEndDate) {
            $query->where('tglambil_bkrs', '<>', $endDate);
        }

        return (int) $query->sum('jmlh_bkrsd');
    }

    private function sumBarangKeluarFakultas(int $idBmf, string $startDate, string $endDate, bool $excludeEndDate = false): int
    {
        $query = BarangKeluarFakultasDetailModel::join('barang_keluar_fakultas', 'barang_keluar_fakultas_detail.id_bkf', '=', 'barang_keluar_fakultas.id_bkf')
            ->where('id_bmf', $idBmf)
            ->whereBetween('tglambil_bkf', [$startDate, $endDate]);

        if ($excludeEndDate) {
            $query->where('tglambil_bkf', '<>', $endDate);
        }

        return (int) $query->sum('jmlh_bkfd');
    }
    Public Function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        // Bersihkan data temp sekali di awal agar perhitungan tidak menumpuk.
        $this->clearUserTemp($user_id);

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        if($lokasi == "690522009KD")
        {
            $nocek = 1;
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmr', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $opsik = $opsikCache[$barisbmr->kd_brg] ??= $this->latestOpsikRektorat($barisbmr->kd_brg, $tgl_akhir);

                if($opsik)
                {
                    $keluarKey = $barisbmr->kd_brg.'|'.$opsik->tgl_opur;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarRektoratAfter($barisbmr->kd_brg, $opsik->tgl_opur);

                    $detailQuery = $keluarAfter
                        ? VOpfikRektoratDetailItemModel::join('barang_masuk_rektorat','v_opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        : OpurdetitmModel::join('barang_masuk_rektorat','opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $opsik->id_opurdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opurdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkrd = $this->sumBarangKeluarRektorat($barisbmr->id_bmr, $opsik->tgl_opur, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                        $hrg_bmr = $barisopsikdetailitem->hrg_bmr;

                        $datatbmr = new TempBarangMasukModel();
                        $datatbmr->kd_brg = $barisbmr->kd_brg;
                        $datatbmr->sisa_tbm = $tjmlh_opsik;
                        $datatbmr->hrg_tbm = $hrg_bmr;
                        $datatbmr->kd_lks = $lokasi;
                        $datatbmr->user_id = $user_id;
                        $datatbmr->jns_tbm = 1;
                        $datatbmr->save();
                    }
                }
                else
                {
                    $tjmlh_bkrd = $this->sumBarangKeluarRektorat($barisbmr->id_bmr, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                    $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd);

                    $datatbmr = new TempBarangMasukModel();
                    $datatbmr->kd_brg = $barisbmr->kd_brg;
                    $datatbmr->sisa_tbm = $sisa_tbmr;
                    $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmr->kd_lks = $lokasi;
                    $datatbmr->user_id = $user_id;
                    $datatbmr->jns_tbm = 1;
                    $datatbmr->save();
                }
                $nocek++;
            }
        }
        elseif($lokasi == "690522020KD")
        {
            $nocek = 1;
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $opsik = $opsikCache[$barisbmrs->kd_brg] ??= $this->latestOpsikRumahSakit($barisbmrs->kd_brg, $tgl_akhir);

                if($opsik)
                {
                    $keluarKey = $barisbmrs->kd_brg.'|'.$opsik->tgl_opurs;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarRumahSakitAfter($barisbmrs->kd_brg, $opsik->tgl_opurs);

                    $detailQuery = $keluarAfter
                        ? VOpfikRumahSakitDetailItemModel::join('barang_masuk_rumah_sakit','v_opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        : OpursdetitmModel::join('barang_masuk_rumah_sakit','opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $opsik->id_opursdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opursdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkrsd = $this->sumBarangKeluarRumahSakit($barisbmrs->id_bmrs, $opsik->tgl_opurs, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                        $hrg_bmrs = $barisopsikdetailitem->hrg_bmrs;

                        $datatbmrs = new TempBarangMasukModel();
                        $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                        $datatbmrs->sisa_tbm = $tjmlh_opsik;
                        $datatbmrs->hrg_tbm = $hrg_bmrs;
                        $datatbmrs->kd_lks = $lokasi;
                        $datatbmrs->user_id = $user_id;
                        $datatbmrs->jns_tbm = 1;
                        $datatbmrs->save();
                    }
                }
                else
                {
                    $tjmlh_bkrsd = $this->sumBarangKeluarRumahSakit($barisbmrs->id_bmrs, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                    $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) ;

                    $datatbmrs = new TempBarangMasukModel();
                    $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                    $datatbmrs->sisa_tbm = $sisa_tbmrs;
                    $datatbmrs->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmrs->kd_lks = $lokasi;
                    $datatbmrs->user_id = $user_id;
                    $datatbmrs->jns_tbm = 1;
                    $datatbmrs->save();
                }
                $nocek++;
            }
        }
        else if($lokasi == "690522000KD") //universitas
        {
            $nocek = 1;
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $opsik = $opsikCache[$barisbmr->kd_brg] ??= $this->latestOpsikRektorat($barisbmr->kd_brg, $tgl_akhir);

                if($opsik)
                {
                    $keluarKey = $barisbmr->kd_brg.'|'.$opsik->tgl_opur;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarRektoratAfter($barisbmr->kd_brg, $opsik->tgl_opur);

                    $detailQuery = $keluarAfter
                        ? VOpfikRektoratDetailItemModel::join('barang_masuk_rektorat','v_opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        : OpurdetitmModel::join('barang_masuk_rektorat','opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $opsik->id_opurdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opurdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkrd = $this->sumBarangKeluarRektorat($barisbmr->id_bmr, $opsik->tgl_opur, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                        $hrg_bmr = $barisopsikdetailitem->hrg_bmr;

                        $datatbmr = new TempBarangMasukModel();
                        $datatbmr->kd_brg = $barisbmr->kd_brg;
                        $datatbmr->sisa_tbm = $tjmlh_opsik;
                        $datatbmr->hrg_tbm = $hrg_bmr;
                        $datatbmr->kd_lks = $lokasi;
                        $datatbmr->user_id = $user_id;
                        $datatbmr->jns_tbm = 1;
                        $datatbmr->save();
                    }
                }
                else
                {
                    $tjmlh_bkrd = $this->sumBarangKeluarRektorat($barisbmr->id_bmr, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                    $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd) ;

                    $datatbmr = new TempBarangMasukModel();
                    $datatbmr->kd_brg = $barisbmr->kd_brg;
                    $datatbmr->sisa_tbm = $sisa_tbmr;
                    $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmr->kd_lks = $lokasi;
                    $datatbmr->user_id = $user_id;
                    $datatbmr->jns_tbm = 1;
                    $datatbmr->save();
                }
                $nocek++;
            }

            $nocek = 1;
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('tglperolehan_bmrs', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $opsik = $opsikCache[$barisbmrs->kd_brg] ??= $this->latestOpsikRumahSakit($barisbmrs->kd_brg, $tgl_akhir);

                if($opsik)
                {
                    $keluarKey = $barisbmrs->kd_brg.'|'.$opsik->tgl_opurs;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarRumahSakitAfter($barisbmrs->kd_brg, $opsik->tgl_opurs);

                    $detailQuery = $keluarAfter
                        ? VOpfikRumahSakitDetailItemModel::join('barang_masuk_rumah_sakit','v_opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        : OpursdetitmModel::join('barang_masuk_rumah_sakit','opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $opsik->id_opursdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opursdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkrsd = $this->sumBarangKeluarRumahSakit($barisbmrs->id_bmrs, $opsik->tgl_opurs, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                        $hrg_bmrs = $barisopsikdetailitem->hrg_bmrs;

                        $datatbmrs = new TempBarangMasukModel();
                        $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                        $datatbmrs->sisa_tbm = $tjmlh_opsik;
                        $datatbmrs->hrg_tbm = $hrg_bmrs;
                        $datatbmrs->kd_lks = $lokasi;
                        $datatbmrs->user_id = $user_id;
                        $datatbmrs->jns_tbm = 1;
                        $datatbmrs->save();
                    }
                }
                else
                {
                    $tjmlh_bkrsd = $this->sumBarangKeluarRumahSakit($barisbmrs->id_bmrs, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                    $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) ;

                    $datatbmrs = new TempBarangMasukModel();
                    $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                    $datatbmrs->sisa_tbm = $sisa_tbmrs;
                    $datatbmrs->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmrs->kd_lks = $lokasi;
                    $datatbmrs->user_id = $user_id;
                    $datatbmrs->jns_tbm = 1;
                    $datatbmrs->save();
                }
                $nocek++;
            }

            $nocek = 1;
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $opsik = $opsikCache[$barisbmf->kd_brg] ??= $this->latestOpsikFakultas($barisbmf->kd_brg, $tgl_akhir);

                if($opsik)
                {
                    $keluarKey = $barisbmf->kd_brg.'|'.$opsik->tgl_opfk;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarFakultasAfter($barisbmf->kd_brg, $opsik->tgl_opfk);

                    $detailQuery = $keluarAfter
                        ? VOpfikFakultasDetailItemModel::join('barang_masuk_fakultas','v_opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        : OpfkdetitmModel::join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $opsik->id_opfkdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opfkdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkfd = $this->sumBarangKeluarFakultas($barisbmf->id_bmf, $opsik->tgl_opfk, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                        $hrg_bmf = $barisopsikdetailitem->hrg_bmf;

                        $datatbmf = new TempBarangMasukModel();
                        $datatbmf->kd_brg = $barisbmf->kd_brg;
                        $datatbmf->sisa_tbm = $tjmlh_opsik;
                        $datatbmf->hrg_tbm = $hrg_bmf;
                        $datatbmf->kd_lks = $lokasi;
                        $datatbmf->user_id = $user_id;
                        $datatbmf->jns_tbm = 1;
                        $datatbmf->save();
                    }
                }
                else
                {
                    $tjmlh_bkfd = $this->sumBarangKeluarFakultas($barisbmf->id_bmf, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                    $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) ;

                    $datatbmf = new TempBarangMasukModel();
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 1;
                    $datatbmf->save();
                }
                $nocek++;
            }
        }
        else if($lokasi == "")
        {
        }
        else
        {
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk = $datafakultas->id_fk;

            $nocek = 1;
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            $opsikCache = [];
            $keluarAfterCache = [];
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $opsik = $opsikCache[$barisbmf->kd_brg] ??= $this->latestOpsikFakultas($barisbmf->kd_brg, $tgl_akhir, $id_fk);

                if($opsik)
                {
                    $keluarKey = $barisbmf->kd_brg.'|'.$opsik->tgl_opfk.'|'.$id_fk;
                    $keluarAfter = $keluarAfterCache[$keluarKey] ??= $this->hasBarangKeluarFakultasAfter($barisbmf->kd_brg, $opsik->tgl_opfk, $id_fk);

                    $detailQuery = $keluarAfter
                        ? VOpfikFakultasDetailItemModel::join('barang_masuk_fakultas','v_opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        : OpfkdetitmModel::join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf');

                    $databarangopsikdetailitem = $detailQuery
                        ->where($detailQuery->getModel()->getTable().'.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $opsik->id_opfkdet)
                        ->when($keluarAfter, function ($q) {
                            return $q->where('jmlh_opfkdetitm', '>', 0);
                        })
                        ->get();

                    foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                    {
                        $tjmlh_bkfd = $this->sumBarangKeluarFakultas($barisbmf->id_bmf, $opsik->tgl_opfk, $tgl_akhir, true);
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                        $hrg_bmf = $barisopsikdetailitem->hrg_bmf;

                        $datatbmf = new TempBarangMasukModel();
                        $datatbmf->kd_brg = $barisbmf->kd_brg;
                        $datatbmf->sisa_tbm = $tjmlh_opsik;
                        $datatbmf->hrg_tbm = $hrg_bmf;
                        $datatbmf->kd_lks = $lokasi;
                        $datatbmf->user_id = $user_id;
                        $datatbmf->jns_tbm = 1;
                        $datatbmf->save();
                    }
                }
                else
                {
                    $tjmlh_bkfd = $this->sumBarangKeluarFakultas($barisbmf->id_bmf, '1900-01-01', $tgl_akhir, false);
                    $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                    $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) ;

                    $datatbmf = new TempBarangMasukModel();
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 1;
                    $datatbmf->save();
                }
                $nocek++;
            }
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
