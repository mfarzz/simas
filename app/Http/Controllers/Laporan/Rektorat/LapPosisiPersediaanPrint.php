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
        $tgl_akhir = Crypt::decryptString($filter);        
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
        if($jumlah != 0)
        {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();
        }

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        if($lokasi == "690522009KD")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();  
            }
            $nocek = 1;
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmr', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $jumlahopsik = OpsikUrDetModel::
                join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')                
                ->where('kd_brg', '=', $barisbmr->kd_brg)
                ->where('tgl_opur', '<=', $tgl_akhir )
                ->where('status_opur', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmr = 0;
                    $databarangopsik = OpsikUrDetModel::
                    join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')                    
                    ->where('kd_brg', '=', $barisbmr->kd_brg)
                    ->where('tgl_opur', '<=', $tgl_akhir )
                    ->orderBy('tgl_opur','desc')
                    ->first();
    
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
                            $tjmlh_bkrd = 0;
                            $databarangkeluar = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkrd)
                            {
                                if($barisbkrd->tglambil_bkr != $tgl_akhir)
                                {
                                    $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisopsikdetailitem->id_bmr)
                            ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkrd = 0;
                                $databarangkeluar = BarangKeluarRektoratDetailModel::
                                join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                                ->where('id_bmr', '=', $barisbmr->id_bmr)
                                ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkrd)
                                {
                                    if($barisbkrd->tglambil_bkr != $tgl_akhir)
                                    {
                                        $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkrd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkrd = 0;
                    $databarangkeluar = BarangKeluarRektoratDetailModel::
                    join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                    ->where('id_bmr', '=', $barisbmr->id_bmr)
                    ->where('tglambil_bkr', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkrd)                    
                    {
                        $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                    }
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                    $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd) ;
                    //echo "$barisbmf->id_bmf = $jmlh_awal_bmf =  $tjmlh_bkfd = $sisa_tbmf<br>";
    
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
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();  
            }
            $nocek = 1;
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $jumlahopsik = OpsikUrsDetModel::
                join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')                
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
                ->where('tgl_opurs', '<=', $tgl_akhir )
                ->where('status_opurs', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmrs = 0;
                    $databarangopsik = OpsikUrsDetModel::
                    join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')                    
                    ->where('kd_brg', '=', $barisbmrs->kd_brg)
                    ->where('tgl_opurs', '<=', $tgl_akhir )
                    ->orderBy('tgl_opurs','desc')
                    ->first();
    
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
                            $tjmlh_bkrsd = 0;
                            $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkrsd)
                            {
                                if($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                                {
                                    $tjmlh_bkrd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisopsikdetailitem->id_bmrs)
                            ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkrsd = 0;
                                $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                                join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                                ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                                ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkrsd)
                                {
                                    if($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                                    {
                                        $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkrsd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkrsd = 0;
                    $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                    join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                    ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                    ->where('tglambil_bkrs', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkrsd)                    
                    {
                        $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                    }
                    $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                    $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) ;
                    //echo "$tgl_akhir = $barisbmrs->id_bmrs = $jmlh_awal_bmrs =  $tjmlh_bkrsd = $sisa_tbmrs<br>";
    
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
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();  
            }

            $nocek = 1;
            $databarangmasukrektorat = BarangMasukRektoratModel::            
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                $jumlahopsik = OpsikUrDetModel::
                join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')                
                ->where('kd_brg', '=', $barisbmr->kd_brg)
                ->where('tgl_opur', '<=', $tgl_akhir )
                ->where('status_opur', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmr = 0;
                    $databarangopsik = OpsikUrDetModel::
                    join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')                    
                    ->where('kd_brg', '=', $barisbmr->kd_brg)
                    ->where('tgl_opur', '<=', $tgl_akhir )
                    ->orderBy('tgl_opur','desc')
                    ->first();

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
                            $tjmlh_bkrd = 0;
                            $databarangkeluar = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisbmr->id_bmr)
                            ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkrd)
                            {
                                if($barisbkrd->tglambil_bkr != $tgl_akhir)
                                {
                                    $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarRektoratDetailModel::
                            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                            ->where('id_bmr', '=', $barisopsikdetailitem->id_bmr)
                            ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkrd = 0;
                                $databarangkeluar = BarangKeluarRektoratDetailModel::
                                join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                                ->where('id_bmr', '=', $barisbmr->id_bmr)
                                ->whereBetween('tglambil_bkr', [$databarangopsik->tgl_opur, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkrd)
                                {
                                    if($barisbkrd->tglambil_bkr != $tgl_akhir)
                                    {
                                        $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkrd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkrd = 0;
                    $databarangkeluar = BarangKeluarRektoratDetailModel::
                    join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
                    ->where('id_bmr', '=', $barisbmr->id_bmr)
                    ->where('tglambil_bkr', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkrd)                    
                    {
                        $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                    }
                    $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                    $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd) ;
                    //echo "$barisbmf->id_bmf = $jmlh_awal_bmf =  $tjmlh_bkfd = $sisa_tbmf<br>";

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
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                $jumlahopsik = OpsikUrsDetModel::
                join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')                
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
                ->where('tgl_opurs', '<=', $tgl_akhir )
                ->where('status_opurs', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmrs = 0;
                    $databarangopsik = OpsikUrsDetModel::
                    join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')                    
                    ->where('kd_brg', '=', $barisbmrs->kd_brg)
                    ->where('tgl_opurs', '<=', $tgl_akhir )
                    ->orderBy('tgl_opurs','desc')
                    ->first();

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
                            $tjmlh_bkrsd = 0;
                            $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                            ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkrsd)
                            {
                                if($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                                {
                                    $tjmlh_bkrd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarRumahSakitDetailModel::
                            join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                            ->where('id_bmrs', '=', $barisopsikdetailitem->id_bmrs)
                            ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkrsd = 0;
                                $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                                join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                                ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                                ->whereBetween('tglambil_bkrs', [$databarangopsik->tgl_opurs, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkrsd)
                                {
                                    if($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                                    {
                                        $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkrsd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkrsd = 0;
                    $databarangkeluar = BarangKeluarRumahSakitDetailModel::
                    join('barang_keluar_rumah_sakit','barang_keluar_rumah_sakit_detail.id_bkrs','=','barang_keluar_rumah_sakit.id_bkrs')
                    ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                    ->where('tglambil_bkrs', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkrsd)                    
                    {
                        $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                    }
                    $jmlh_awal_bmrs = $barisbmrs->jmlh_awal_bmrs;
                    $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) ;
                    //echo "$tgl_akhir = $barisbmrs->id_bmrs = $jmlh_awal_bmrs =  $tjmlh_bkrsd = $sisa_tbmrs<br>";

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
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $jumlahopsik = OpsikFkDetModel::                    
                join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')                
                ->where('kd_brg', '=', $barisbmf->kd_brg)
                ->where('tgl_opfk', '<=', $tgl_akhir )
                ->where('status_opfk', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmf = 0;
                    $databarangopsik = OpsikFkDetModel::                    
                    join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')                    
                    ->where('kd_brg', '=', $barisbmf->kd_brg)
                    ->where('tgl_opfk', '<=', $tgl_akhir )
                    ->orderBy('tgl_opfk','desc')
                    ->first();

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
                            $tjmlh_bkfd = 0;
                            $databarangkeluar = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkfd)
                            {
                                if($barisbkfd->tglambil_bkf != $tgl_akhir)
                                {
                                    $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisopsikdetailitem->id_bmf)
                            ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkfd = 0;
                                $databarangkeluar = BarangKeluarFakultasDetailModel::
                                join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                                ->where('id_bmf', '=', $barisbmf->id_bmf)
                                ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkfd)
                                {
                                    if($barisbkfd->tglambil_bkf != $tgl_akhir)
                                    {
                                        $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkfd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkfd = 0;
                    $databarangkeluar = BarangKeluarFakultasDetailModel::
                    join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tglambil_bkf', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkfd)                    
                    {
                        $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                    }
                    $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                    $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) ;
                    //echo "$barisbmf->id_bmf = $jmlh_awal_bmf =  $tjmlh_bkfd = $sisa_tbmf<br>";

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
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();  
            }
        }
        else
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();  
            }
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk = $datafakultas->id_fk;

            $nocek = 1;
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
            //->where('kd_brg', '=', '118101000013')
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                $jumlahopsik = OpsikFkDetModel::                    
                join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                ->where('id_fk', '=', $id_fk)
                ->where('kd_brg', '=', $barisbmf->kd_brg)
                ->where('tgl_opfk', '<=', $tgl_akhir )
                ->where('status_opfk', '=', 1 )
                ->count();                     
                if($jumlahopsik>=1)
                {     
                    $tjmlh_opsik = 0;
                    $hrg_bmf = 0;
                    $databarangopsik = OpsikFkDetModel::                    
                    join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                    ->where('id_fk', '=', $id_fk)
                    ->where('kd_brg', '=', $barisbmf->kd_brg)
                    ->where('tgl_opfk', '<=', $tgl_akhir )
                    ->orderBy('tgl_opfk','desc')
                    ->first();

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
                            $tjmlh_bkfd = 0;
                            $databarangkeluar = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisbmf->id_bmf)
                            ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                            ->get();
                            foreach($databarangkeluar as $barisbkfd)
                            {
                                if($barisbkfd->tglambil_bkf != $tgl_akhir)
                                {
                                    $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                                }
                            }
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
                            //echo "$barisbmf->id_bmf <br>";
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
                            $jumlahbarangkeluar = BarangKeluarFakultasDetailModel::
                            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                            ->where('id_bmf', '=', $barisopsikdetailitem->id_bmf)
                            ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                            ->count();                                  
                            if($jumlahbarangkeluar >= 1)
                            {                        
                                $tjmlh_bkfd = 0;
                                $databarangkeluar = BarangKeluarFakultasDetailModel::
                                join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                                ->where('id_bmf', '=', $barisbmf->id_bmf)
                                ->whereBetween('tglambil_bkf', [$databarangopsik->tgl_opfk, $tgl_akhir])
                                ->get();
                                foreach($databarangkeluar as $barisbkfd)
                                {
                                    if($barisbkfd->tglambil_bkf != $tgl_akhir)
                                    {
                                        $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                                    }
                                }
                            }
                            else
                            {
                                $tjmlh_bkfd = 0;
                            }
                        
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
                }
                else
                {
                    $tjmlh_bkfd = 0;
                    $databarangkeluar = BarangKeluarFakultasDetailModel::
                    join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tglambil_bkf', '<=', $tgl_akhir )
                    ->get();
                    foreach($databarangkeluar as $barisbkfd)                    
                    {
                        $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                    }
                    $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                    $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) ;
                    //echo "$barisbmf->id_bmf = $jmlh_awal_bmf =  $tjmlh_bkfd = $sisa_tbmf<br>";

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
