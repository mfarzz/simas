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
    Public Function index($filter, $lokasi)
    {
        set_time_limit(300);
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->delete();

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        if($lokasi == "690522009KD")
        {
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $databarangopsik = OpsikUrDetModel::
                join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                ->where('kd_brg', '=', $barisbmr->kd_brg)
                ->where('tgl_opur', '<=', $tgl_akhir )
                ->where('status_opur', '=', 1 )
                ->orderBy('tgl_opur','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarRektoratModel::
                    where('kd_brg', '=', $barisbmr->kd_brg)
                    ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikRektoratDetailItemModel::
                        join('barang_masuk_rektorat','v_opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        ->where('v_opfik_rektorat_detail_item.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $databarangopsik->id_opurdet)
                        ->where('jmlh_opurdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                            ->where('tglambil_bkr', '<', $tgl_akhir)
                            ->sum('jmlh_bkrd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                            $bulk_tbmr[] = [
                                'kd_brg'  => $barisbmr->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmr,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpurdetitmModel::
                        join('barang_masuk_rektorat','opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        ->where('opfik_rektorat_detail_item.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $databarangopsik->id_opurdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                            ->where('tglambil_bkr', '<', $tgl_akhir)
                            ->sum('jmlh_bkrd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                            $bulk_tbmr[] = [
                                'kd_brg'  => $barisbmr->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmr,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                    join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                    ->where('id_bmr', '=', $barisbmr->id_bmr)
                    ->where('tglambil_bkr', '<=', $tgl_akhir )
                    ->sum('jmlh_bkrd');
                    $sisa_tbmr = $barisbmr->jmlh_awal_bmr - $tjmlh_bkrd;
                    $bulk_tbmr[] = [
                        'kd_brg'  => $barisbmr->kd_brg,
                        'sisa_tbm'=> $sisa_tbmr,
                        'hrg_tbm' => $barisbmr->hrg_bmr,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmr)) {
                foreach (array_chunk($bulk_tbmr, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
            }
        }
        elseif($lokasi == "690522020KD")
        {
            $bulk_tbmrs = [];
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $databarangopsik = OpsikUrsDetModel::
                join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
                ->where('tgl_opurs', '<=', $tgl_akhir )
                ->where('status_opurs', '=', 1 )
                ->orderBy('tgl_opurs','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarRumahSakitModel::
                    where('kd_brg', '=', $barisbmrs->kd_brg)
                    ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikRumahSakitDetailItemModel::
                        join('barang_masuk_rumah_sakit','v_opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        ->where('v_opfik_rumah_sakit_detail_item.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $databarangopsik->id_opursdet)
                        ->where('jmlh_opursdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                            ->where('tglambil_bkrs', '<', $tgl_akhir)
                            ->sum('jmlh_bkrsd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                            $bulk_tbmrs[] = [
                                'kd_brg'  => $barisbmrs->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpursdetitmModel::
                        join('barang_masuk_rumah_sakit','opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        ->where('opfik_rumah_sakit_detail_item.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $databarangopsik->id_opursdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                            ->where('tglambil_bkrs', '<', $tgl_akhir)
                            ->sum('jmlh_bkrsd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                            $bulk_tbmrs[] = [
                                'kd_brg'  => $barisbmrs->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                    join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                    ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                    ->where('tglambil_bkrs', '<=', $tgl_akhir )
                    ->sum('jmlh_bkrsd');
                    $sisa_tbmrs = $barisbmrs->jmlh_awal_bmrs - $tjmlh_bkrsd;
                    $bulk_tbmrs[] = [
                        'kd_brg'  => $barisbmrs->kd_brg,
                        'sisa_tbm'=> $sisa_tbmrs,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmrs)) {
                foreach (array_chunk($bulk_tbmrs, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
            }
        }
        else if($lokasi == "690522000KD") //universitas
        {
            // --- Rektorat ---
            $bulk_tbmr = [];
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $databarangopsik = OpsikUrDetModel::
                join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                ->where('kd_brg', '=', $barisbmr->kd_brg)
                ->where('tgl_opur', '<=', $tgl_akhir )
                ->where('status_opur', '=', 1 )
                ->orderBy('tgl_opur','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarRektoratModel::
                    where('kd_brg', '=', $barisbmr->kd_brg)
                    ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikRektoratDetailItemModel::
                        join('barang_masuk_rektorat','v_opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        ->where('v_opfik_rektorat_detail_item.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $databarangopsik->id_opurdet)
                        ->where('jmlh_opurdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                            ->where('tglambil_bkr', '<', $tgl_akhir)
                            ->sum('jmlh_bkrd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                            $bulk_tbmr[] = [
                                'kd_brg'  => $barisbmr->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmr,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpurdetitmModel::
                        join('barang_masuk_rektorat','opfik_rektorat_detail_item.id_bmr','=','barang_masuk_rektorat.id_bmr')
                        ->where('opfik_rektorat_detail_item.id_bmr', '=', $barisbmr->id_bmr)
                        ->where('id_opurdet', '=', $databarangopsik->id_opurdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)
                            ->where('tglambil_bkr', '<', $tgl_akhir)
                            ->sum('jmlh_bkrd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                            $bulk_tbmr[] = [
                                'kd_brg'  => $barisbmr->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmr,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkrd = BarangKeluarRektoratDetailModel::
                    join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                    ->where('id_bmr', '=', $barisbmr->id_bmr)
                    ->where('tglambil_bkr', '<=', $tgl_akhir )
                    ->sum('jmlh_bkrd');
                    $sisa_tbmr = $barisbmr->jmlh_awal_bmr - $tjmlh_bkrd;
                    $bulk_tbmr[] = [
                        'kd_brg'  => $barisbmr->kd_brg,
                        'sisa_tbm'=> $sisa_tbmr,
                        'hrg_tbm' => $barisbmr->hrg_bmr,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmr)) {
                foreach (array_chunk($bulk_tbmr, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
            }

            // --- Rumah Sakit ---
            $bulk_tbmrs = [];
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $databarangopsik = OpsikUrsDetModel::
                join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
                ->where('tgl_opurs', '<=', $tgl_akhir )
                ->where('status_opurs', '=', 1 )
                ->orderBy('tgl_opurs','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarRumahSakitModel::
                    where('kd_brg', '=', $barisbmrs->kd_brg)
                    ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikRumahSakitDetailItemModel::
                        join('barang_masuk_rumah_sakit','v_opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        ->where('v_opfik_rumah_sakit_detail_item.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $databarangopsik->id_opursdet)
                        ->where('jmlh_opursdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                            ->where('tglambil_bkrs', '<', $tgl_akhir)
                            ->sum('jmlh_bkrsd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                            $bulk_tbmrs[] = [
                                'kd_brg'  => $barisbmrs->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpursdetitmModel::
                        join('barang_masuk_rumah_sakit','opfik_rumah_sakit_detail_item.id_bmrs','=','barang_masuk_rumah_sakit.id_bmrs')
                        ->where('opfik_rumah_sakit_detail_item.id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('id_opursdet', '=', $databarangopsik->id_opursdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)
                            ->where('tglambil_bkrs', '<', $tgl_akhir)
                            ->sum('jmlh_bkrsd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;
                            $bulk_tbmrs[] = [
                                'kd_brg'  => $barisbmrs->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkrsd = BarangKeluarRumahSakitDetailModel::
                    join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                    ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                    ->where('tglambil_bkrs', '<=', $tgl_akhir )
                    ->sum('jmlh_bkrsd');
                    $sisa_tbmrs = $barisbmrs->jmlh_awal_bmrs - $tjmlh_bkrsd;
                    $bulk_tbmrs[] = [
                        'kd_brg'  => $barisbmrs->kd_brg,
                        'sisa_tbm'=> $sisa_tbmrs,
                        'hrg_tbm' => $barisbmrs->hrg_bmrs,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmrs)) {
                foreach (array_chunk($bulk_tbmrs, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
            }

            // --- Fakultas ---
            $bulk_tbmf = [];
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $databarangopsik = OpsikFkDetModel::
                join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->where('kd_brg', '=', $barisbmf->kd_brg)
                ->where('tgl_opfk', '<=', $tgl_akhir )
                ->where('status_opfk', '=', 1 )
                ->orderBy('tgl_opfk','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarFakultasModel::
                    where('kd_brg', '=', $barisbmf->kd_brg)
                    ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikFakultasDetailItemModel::
                        join('barang_masuk_fakultas','v_opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('v_opfik_fakultas_detail_item.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $databarangopsik->id_opfkdet)
                        ->where('jmlh_opfkdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                            ->where('tglambil_bkf', '<', $tgl_akhir)
                            ->sum('jmlh_bkfd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                            $bulk_tbmf[] = [
                                'kd_brg'  => $barisbmf->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmf,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpfkdetitmModel::
                        join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('opfik_fakultas_detail_item.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $databarangopsik->id_opfkdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                            ->where('tglambil_bkf', '<', $tgl_akhir)
                            ->sum('jmlh_bkfd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                            $bulk_tbmf[] = [
                                'kd_brg'  => $barisbmf->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmf,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                    join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tglambil_bkf', '<=', $tgl_akhir )
                    ->sum('jmlh_bkfd');
                    $sisa_tbmf = $barisbmf->jmlh_awal_bmf - $tjmlh_bkfd;
                    $bulk_tbmf[] = [
                        'kd_brg'  => $barisbmf->kd_brg,
                        'sisa_tbm'=> $sisa_tbmf,
                        'hrg_tbm' => $barisbmf->hrg_bmf,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmf)) {
                foreach (array_chunk($bulk_tbmf, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
            }
        }
        else if($lokasi == "")
        {
            // lokasi kosong: data sudah dihapus di awal, tidak ada yang diproses
        }
        else
        {
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk = $datafakultas->id_fk;

            $bulk_tbmf = [];
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $databarangopsik = OpsikFkDetModel::
                join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->where('id_fk', '=', $id_fk)
                ->where('kd_brg', '=', $barisbmf->kd_brg)
                ->where('tgl_opfk', '<=', $tgl_akhir )
                ->where('status_opfk', '=', 1 )
                ->orderBy('tgl_opfk','desc')
                ->first();
                if($databarangopsik)
                {
                    $jumlahbk = BarangKeluarFakultasModel::
                    where('id_fk', '=', $id_fk)
                    ->where('kd_brg', '=', $barisbmf->kd_brg)
                    ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                    ->count();
                    if($jumlahbk >= 1)
                    {
                        $databarangopsikdetailitem = VOpfikFakultasDetailItemModel::
                        join('barang_masuk_fakultas','v_opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('v_opfik_fakultas_detail_item.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $databarangopsik->id_opfkdet)
                        ->where('jmlh_opfkdetitm', '>', 0)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                            ->where('tglambil_bkf', '<', $tgl_akhir)
                            ->sum('jmlh_bkfd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                            $bulk_tbmf[] = [
                                'kd_brg'  => $barisbmf->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmf,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                    else
                    {
                        $databarangopsikdetailitem = OpfkdetitmModel::
                        join('barang_masuk_fakultas','opfik_fakultas_detail_item.id_bmf','=','barang_masuk_fakultas.id_bmf')
                        ->where('opfik_fakultas_detail_item.id_bmf', '=', $barisbmf->id_bmf)
                        ->where('id_opfkdet', '=', $databarangopsik->id_opfkdet)
                        ->get();
                        foreach($databarangopsikdetailitem as $barisopsikdetailitem)
                        {
                            $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)
                            ->where('tglambil_bkf', '<', $tgl_akhir)
                            ->sum('jmlh_bkfd');
                            $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;
                            $bulk_tbmf[] = [
                                'kd_brg'  => $barisbmf->kd_brg,
                                'sisa_tbm'=> $tjmlh_opsik,
                                'hrg_tbm' => $barisopsikdetailitem->hrg_bmf,
                                'kd_lks'  => $lokasi,
                                'user_id' => $user_id,
                                'jns_tbm' => 1,
                            ];
                        }
                    }
                }
                else
                {
                    $tjmlh_bkfd = BarangKeluarFakultasDetailModel::
                    join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tglambil_bkf', '<=', $tgl_akhir )
                    ->sum('jmlh_bkfd');
                    $sisa_tbmf = $barisbmf->jmlh_awal_bmf - $tjmlh_bkfd;
                    $bulk_tbmf[] = [
                        'kd_brg'  => $barisbmf->kd_brg,
                        'sisa_tbm'=> $sisa_tbmf,
                        'hrg_tbm' => $barisbmf->hrg_bmf,
                        'kd_lks'  => $lokasi,
                        'user_id' => $user_id,
                        'jns_tbm' => 1,
                    ];
                }
            }
            if (!empty($bulk_tbmf)) {
                foreach (array_chunk($bulk_tbmf, 500) as $chunk) {
                    TempBarangMasukModel::insert($chunk);
                }
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
