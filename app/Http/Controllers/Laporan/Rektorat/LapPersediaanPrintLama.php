<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\JabpenuuModel;
use App\Models\LokasiModel;
use App\Models\OpsikFkDetModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpurdetitmModel;
use App\Models\OpursdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\User;
use App\Models\VLapPosisi2Model;
use App\Models\VLapPosisi3Model;
use App\Models\VLapPosisi4Model;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapPersediaanPrint extends Controller
{
    Public Function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
        if($jumlah != 0)
        {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();
        }

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        if($lokasi == "690522009KD")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();  
            }
            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                if($barisbmr->sisa_bmr==$barisbmr->jmlh_awal_bmr)
                {
                    $datatbmr = new TempBarangMasukModel();                    
                    $datatbmr->kd_brg = $barisbmr->kd_brg;
                    $datatbmr->sisa_tbm = $barisbmr->jmlh_awal_bmr;
                    $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmr->kd_lks = $lokasi;
                    $datatbmr->user_id = $user_id;
                    $datatbmr->jns_tbm = 2;
                    $datatbmr->save();
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

                    $tjmlh_opsik = 0;
                if($barisbmr->sisa_bmr!=$barisbmr->jmlh_awal_bmr)
                {                    
                    
                    $jumlah = OpsikUrDetModel::  
                    join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                    ->where('kd_brg', '=', $barisbmr->kd_brg)
                    ->where('tgl_opur', '<=', $tgl_akhir )
                    ->count();
                    if($jumlah != 0)
                    {
                        $databarangopsik = OpsikUrDetModel::                    
                        join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                        ->where('kd_brg', '=', $barisbmr->kd_brg)
                        ->where('tgl_opur', '<=', $tgl_akhir )
                        ->first();
                        $stok_sistem_oprdet = $databarangopsik->stok_sistem_oprdet;
                        $stok_opsik_oprdet = $databarangopsik->stok_opsik_oprdet;
                        if($stok_sistem_oprdet < $stok_opsik_oprdet)
                        {
                            $tjmlh_opsik = $stok_opsik_oprdet - $stok_sistem_oprdet;
                        }
                        else if($stok_sistem_oprdet > $stok_opsik_oprdet)
                        {
                            $tjmlh_opsik =  $stok_opsik_oprdet -  $stok_sistem_oprdet ;
                        }
                    }
                }

                $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd) + $tjmlh_opsik;

                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmr->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmr;
                    $datatbmf->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();
                }
            }
        }
        else if($lokasi == "690522020KD")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();  
            }
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                if($barisbmrs->sisa_bmrs==$barisbmrs->jmlh_awal_bmrs)
                {
                    $datatbmrs = new TempBarangMasukModel();                    
                    $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                    $datatbmrs->sisa_tbm = $barisbmrs->jmlh_awal_bmrs;
                    $datatbmrs->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmrs->kd_lks = $lokasi;
                    $datatbmrs->user_id = $user_id;
                    $datatbmrs->jns_tbm = 2;
                    $datatbmrs->save();
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
                    //$diambil = $barisbmf->sisa_bmf + $tjmlh_bkfd;

                    /*if($barisbmrs->sisa_bmrs!=$barisbmrs->jmlh_awal_bmrs)
                    {
                        $tjmlh_opsikkurang = 0;
                        $databarangopsikkurang = OpursdetitmModel::
                        join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                        ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                        ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('tgl_opurs', '<=', $tgl_akhir )
                        ->where('id_bkrsd', '<', '1' )
                        ->get();
                        foreach($databarangopsikkurang as $barisopsikkurang)                    
                        {
                            $tjmlh_opsikkurang = $barisopsikkurang->jmlh_opursdetitm + $tjmlh_opsikkurang;
                        }
                    }

                    $tjmlh_opsiktambah = 0;
                    if($barisbmrs->sisa_bmrs!=$barisbmrs->jmlh_awal_bmrs)
                    {
                        $databarangopsiktambah = OpursdetitmModel::
                        join('opsik_rumah_sakit_detail','opfik_rumah_sakit_detail_item.id_opursdet','=','opsik_rumah_sakit_detail.id_opursdet')
                        ->join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                        ->where('id_bmrs', '=', $barisbmrs->id_bmrs)
                        ->where('tgl_opurs', '<=', $tgl_akhir )
                        ->where('id_bkrsd', '>', 0 )
                        ->get();
                        foreach($databarangopsiktambah as $barisopsiktambah)                    
                        {
                            $tjmlh_opsiktambah = $barisopsiktambah->jmlh_opursdetitm + $tjmlh_opsiktambah;
                        }
                    }*/

                    $tjmlh_opsik = 0;
                    if($barisbmrs->sisa_bmrs!=$barisbmrs->jmlh_awal_bmrs)
                    {                    
                        
                        $jumlah = OpsikUrsDetModel::                    
                        join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                        ->where('kd_brg', '=', $barisbmrs->kd_brg)
                        ->where('tgl_opurs', '<=', $tgl_akhir )
                        ->where('status_opurs', '=', 1 )
                        ->count();
                        if($jumlah != 0)
                        {
                            $jmlh_bm = BarangMasukRumahSakitModel::
                            where('kd_lks', '=', $lokasi )
                            ->where('kd_brg', '=', $barisbmrs->kd_brg )
                            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
                            ->orderBy('tglperolehan_bmrs','asc')
                            ->count();

                            $databarangopsik = OpsikUrsDetModel::                    
                            join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                            ->where('kd_brg', '=', $barisbmrs->kd_brg)
                            ->where('tgl_opurs', '<=', $tgl_akhir )
                            ->first();
                            $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                            $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                            if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                            }
                            else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik =  $stok_opsik_opfkdet;
                            }
                            $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                        }
                    }

                    if($jumlah==0)
                    {
                        $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) + $tjmlh_opsik;
                    }
                    else
                    {
                        $sisa_tbmrs = $tjmlh_opsik;
                    }
                    //$sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) + $tjmlh_opsiktambah - $tjmlh_opsikkurang;

                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmrs->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmrs;
                    $datatbmf->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();
                }
            }
        }
        else if($lokasi == "690522000KD")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();  
            }
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )      
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                {
                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $barisbmf->jmlh_awal_bmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();
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
                   
                    $tjmlh_opsik = 0;
                    $jumlah = 0;
                    if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                    {                       
                        $jumlah = OpsikFkDetModel::                    
                        join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                        ->where('id_fk', '=', $id_fk)
                        ->where('kd_brg', '=', $barisbmf->kd_brg)
                        ->where('tgl_opfk', '<=', $tgl_akhir )
                        ->where('status_opfk', '=', 1 )
                        ->count();
                        if($jumlah != 0)
                        {
                            //echo "sip";
                            $jmlh_bm = BarangMasukFakultasModel::
                            where('kd_lks', '=', $lokasi )
                            ->where('kd_brg', '=', $barisbmf->kd_brg )
                            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
                            ->orderBy('tglperolehan_bmf','asc')
                            ->count();

                            $databarangopsik = OpsikFkDetModel::                    
                            join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                            ->where('id_fk', '=', $id_fk)
                            ->where('kd_brg', '=', $barisbmf->kd_brg)
                            ->where('tgl_opfk', '<=', $tgl_akhir )
                            ->first();
                            $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                            $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                            if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                            }
                            else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik =  $stok_opsik_opfkdet;
                            }
                            $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                        }
                    }
                    if($jumlah==0)
                    {
                        $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) + $tjmlh_opsik;
                    }
                    else
                    {
                        $sisa_tbmf = $tjmlh_opsik;
                    }
                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();                
                }
            }

            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmrs','asc')
            ->get();
            foreach($databarangmasukrumahsakit as $barisbmrs)
            {
                if($barisbmrs->sisa_bmrs==$barisbmrs->jmlh_awal_bmrs)
                {
                    $datatbmrs = new TempBarangMasukModel();                    
                    $datatbmrs->kd_brg = $barisbmrs->kd_brg;
                    $datatbmrs->sisa_tbm = $barisbmrs->jmlh_awal_bmrs;
                    $datatbmrs->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmrs->kd_lks = $lokasi;
                    $datatbmrs->user_id = $user_id;
                    $datatbmrs->jns_tbm = 3;
                    $datatbmrs->save();
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
                   
                    $tjmlh_opsik = 0;
                    if($barisbmrs->sisa_bmrs!=$barisbmrs->jmlh_awal_bmrs)
                    {                    
                        
                        $jumlah = OpsikUrsDetModel::                    
                        join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                        ->where('kd_brg', '=', $barisbmrs->kd_brg)
                        ->where('tgl_opurs', '<=', $tgl_akhir )
                        ->where('status_opurs', '=', 1 )
                        ->count();
                        if($jumlah != 0)
                        {
                            $jmlh_bm = BarangMasukRumahSakitModel::
                            where('kd_lks', '=', $lokasi )
                            ->where('kd_brg', '=', $barisbmrs->kd_brg )
                            ->where('tglperolehan_bmrs', '<=', $tgl_akhir )
                            ->orderBy('tglperolehan_bmrs','asc')
                            ->count();

                            $databarangopsik = OpsikUrsDetModel::                    
                            join('opsik_rumah_sakit','opsik_rumah_sakit_detail.id_opurs','=','opsik_rumah_sakit.id_opurs')
                            ->where('kd_brg', '=', $barisbmrs->kd_brg)
                            ->where('tgl_opurs', '<=', $tgl_akhir )
                            ->first();
                            $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                            $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                            if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                            }
                            else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik =  $stok_opsik_opfkdet;
                            }
                            $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                        }
                    }

                    if($jumlah==0)
                    {
                        $sisa_tbmrs = ($jmlh_awal_bmrs - $tjmlh_bkrsd) + $tjmlh_opsik;
                    }
                    else
                    {
                        $sisa_tbmrs = $tjmlh_opsik;
                    }

                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmrs->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmrs;
                    $datatbmf->hrg_tbm = $barisbmrs->hrg_bmrs;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 3;
                    $datatbmf->save();
                }
            }

            $databarangmasukrektorat = BarangMasukRektoratModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmr', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmr','asc')
            ->get();
            foreach($databarangmasukrektorat as $barisbmr)
            {
                if($barisbmr->sisa_bmr==$barisbmr->jmlh_awal_bmr)
                {
                    $datatbmr = new TempBarangMasukModel();                    
                    $datatbmr->kd_brg = $barisbmr->kd_brg;
                    $datatbmr->sisa_tbm = $barisbmr->jmlh_awal_bmr;
                    $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmr->kd_lks = $lokasi;
                    $datatbmr->user_id = $user_id;
                    $datatbmr->jns_tbm = 2;
                    $datatbmr->save();
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
                    //$diambil = $barisbmf->sisa_bmf + $tjmlh_bkfd;

                    if($barisbmr->sisa_bmr!=$barisbmr->jmlh_awal_bmr)
                    {
                        $tjmlh_opsikkurang = 0;
                        $databarangopsikkurang = OpurdetitmModel::
                        join('opsik_rektorat_detail','opfik_rektorat_detail_item.id_opurdet','=','opsik_rektorat_detail.id_opurdet')
                        ->join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                        ->where('id_bmr', '=', $barisbmr->id_bmr)
                        ->where('tgl_opur', '<=', $tgl_akhir )
                        ->where('id_bkrd', '<', '1' )
                        ->get();
                        foreach($databarangopsikkurang as $barisopsikkurang)                    
                        {
                            $tjmlh_opsikkurang = $barisopsikkurang->jmlh_opurdetitm + $tjmlh_opsikkurang;
                        }
                    }

                    $tjmlh_opsik = 0;
                    if($barisbmr->sisa_bmr!=$barisbmr->jmlh_awal_bmr)
                    {                    
                        
                        $jumlah = OpsikUrDetModel::  
                        join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                        ->where('kd_brg', '=', $barisbmr->kd_brg)
                        ->where('tgl_opur', '<=', $tgl_akhir )
                        ->count();
                        if($jumlah != 0)
                        {
                            $jmlh_bm = BarangMasukRektoratModel::
                            where('kd_lks', '=', $lokasi )
                            ->where('kd_brg', '=', $barisbmr->kd_brg )
                            ->where('tglperolehan_bmr', '<=', $tgl_akhir )
                            ->orderBy('tglperolehan_bmr','asc')
                            ->where('status_opur', '=', 1 )
                            ->count();

                            $databarangopsik = OpsikUrDetModel::                    
                            join('opsik_rektorat','opsik_rektorat_detail.id_opur','=','opsik_rektorat.id_opur')
                            ->where('kd_brg', '=', $barisbmr->kd_brg)
                            ->where('tgl_opur', '<=', $tgl_akhir )
                            ->first();
                            $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                            $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                            if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                            }
                            else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik =  $stok_opsik_opfkdet;
                            }
                            $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                        }
                    }

                    if($jumlah==0)
                    {
                        $sisa_tbmr = ($jmlh_awal_bmr - $tjmlh_bkrd) + $tjmlh_opsik;
                    }
                    else
                    {
                        $sisa_tbmr = $tjmlh_opsik;
                    }

                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmr->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmr;
                    $datatbmf->hrg_tbm = $barisbmr->hrg_bmr;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();
                }
            }
        }
        else if($lokasi == "")
        {
            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();  
            }
        }
        else
        {
            $barislok = FakultasModel::
            where('kd_lks', $lokasi)
            ->first(); 
            $lokasi = $barislok->kd_lks;
            $id_fk = $barislok->id_fk;

            $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
            if($jumlah != 0)
            {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();  
            }
            $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi )
            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
            ->orderBy('tglperolehan_bmf','asc')
            ->get();
            foreach($databarangmasukfakultas as $barisbmf)
            {
                if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
                {
                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $barisbmf->jmlh_awal_bmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();
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
                    //$diambil = $barisbmf->sisa_bmf + $tjmlh_bkfd;
    
                    /*if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                    {
                        $tjmlh_opsikkurang = 0;
                        $databarangopsikkurang = OpfkdetitmModel::
                        join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                        ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->where('tgl_opfk', '<=', $tgl_akhir )
                        ->where('id_bkfd', '<', '1' )
                        ->get();
                        foreach($databarangopsikkurang as $barisopsikkurang)                    
                        {
                            $tjmlh_opsikkurang = $barisopsikkurang->jmlh_opfkdetitm + $tjmlh_opsikkurang;
                        }
                    }
    
                    $tjmlh_opsiktambah = 0;
                    if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                    {
                        $databarangopsiktambah = OpfkdetitmModel::
                        join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                        ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                        ->where('id_bmf', '=', $barisbmf->id_bmf)
                        ->where('tgl_opfk', '<=', $tgl_akhir )
                        ->where('id_bkfd', '>', 0 )
                        ->get();
                        foreach($databarangopsiktambah as $barisopsiktambah)                    
                        {
                            $tjmlh_opsiktambah = $barisopsiktambah->jmlh_opfkdetitm + $tjmlh_opsiktambah;
                        }
                    }*/
                    $tjmlh_opsik = 0;
                    $jumlah = 0;
                    if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                    {                       
                        $jumlah = OpsikFkDetModel::                    
                        join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                        ->where('id_fk', '=', $id_fk)
                        ->where('kd_brg', '=', $barisbmf->kd_brg)
                        ->where('tgl_opfk', '<=', $tgl_akhir )
                        ->where('status_opfk', '=', 1 )
                        ->count();
                        if($jumlah != 0)
                        {
                            //echo "sip";
                            $jmlh_bm = BarangMasukFakultasModel::
                            where('kd_lks', '=', $lokasi )
                            ->where('kd_brg', '=', $barisbmf->kd_brg )
                            ->where('tglperolehan_bmf', '<=', $tgl_akhir )
                            ->orderBy('tglperolehan_bmf','asc')
                            ->count();
    
                            $databarangopsik = OpsikFkDetModel::                    
                            join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                            ->where('id_fk', '=', $id_fk)
                            ->where('kd_brg', '=', $barisbmf->kd_brg)
                            ->where('tgl_opfk', '<=', $tgl_akhir )
                            ->first();
                            $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                            $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                            if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                            }
                            else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                            {
                                $tjmlh_opsik =  $stok_opsik_opfkdet;
                            }
                            $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                        }
                    }
                    if($jumlah==0)
                    {
                        $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) + $tjmlh_opsik;
                    }
                    else
                    {
                        $sisa_tbmf = $tjmlh_opsik;
                    }
                    
                    //echo "$sisa_tbmf = $barisbmf->sisa_bmf = $barisbmf->jmlh_awal_bmf";
                    //echo "$id_fk = $barisbmf->kd_brg = $jmlh_awal_bmf = $tjmlh_bkfd = $tjmlh_opsik = $sisa_tbmf = $stok_sistem_opfkdet = $stok_opsik_opfkdet  = $jmlh_bm <br>";
                    
                    //echo "$jmlh_awal_bmf = $tjmlh_bkfd<br>";
    
                    $datatbmf = new TempBarangMasukModel();                    
                    $datatbmf->kd_brg = $barisbmf->kd_brg;
                    $datatbmf->sisa_tbm = $sisa_tbmf;
                    $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                    $datatbmf->kd_lks = $lokasi;
                    $datatbmf->user_id = $user_id;
                    $datatbmf->jns_tbm = 2;
                    $datatbmf->save();                
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
        $tgl = \Carbon\Carbon::parse($tgl_akhir)->format('d M Y');

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN PERSEDIAAN', 0, 1, 'C', 0, '', 0);
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
        PDF::Cell(20, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(115, 0, "URAIAN", 1, 0, 'C', 0, '', true);
        PDF::Cell(25, 0, "KUANTITAS", 1, 0, 'C', 0, '', true);
        PDF::Cell(30, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::SetFont('times', '', 10);
        //if($lokasi == "023170800677513009KD")
        //{            
            $total_nilai = 0;
            $datalap = VLapPosisi4Model::
            join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
            ->where('v_lap_posisi4.user_id','=',$user_id)
            ->where('v_lap_posisi4.v_jns_tbm','=',2)
            ->get();
            foreach($datalap as $barislap)
            {
                PDF::SetFont('times', '', 10);
                PDF::SetTextColor(128, 0, 0);
                $total_nilai = $total_nilai + $barislap->total_nilai;
                $datalapsubsub = VLapPosisi3Model::
                join('kategori','v_lap_posisi3.v_kd_kt','=','kategori.kd_kt')
                ->where('v_lap_posisi3.v_kd_kt','=',$barislap->v_kd_kt)
                ->where('v_lap_posisi3.user_id','=',$user_id)
                ->where('v_lap_posisi3.v_jns_tbm','=',2)
                ->get();
                foreach($datalapsubsub as $barislapsubsub)
                {
                    $nilaisubsubrp = rupiah($barislapsubsub->total_nilai);
                    PDF::Cell(20, 0, "$barislapsubsub->kd_kt", 1, 0, 'R', 0, '', true);
                    PDF::Cell(140, 0, "$barislapsubsub->nm_kt", 1, 0, 'L', 0, '', true);
                    PDF::Cell(30, 0, "$nilaisubsubrp", 1, 1, 'R', 0, '', true);
                    PDF::ln(0);

                    $datalapbrg = VLapPosisi2Model::
                    join('barang','v_lap_posisi2.v_kd_brg','=','barang.kd_brg')
                    ->where('v_lap_posisi2.v_kd_kt','=',$barislapsubsub->v_kd_kt)
                    ->where('v_lap_posisi2.user_id','=',$user_id)
                    ->where('v_lap_posisi2.v_jns_tbm','=',2)
                    ->get();
                    foreach($datalapbrg as $barisbrg)
                    {
                        PDF::SetTextColor(0, 0, 0);
                        $nilaibrg = rupiah($barisbrg->total_nilai);
                        PDF::Cell(20, 0, "", 1, 0, 'R', 0, '', true);
                        PDF::Cell(115, 0, "$barisbrg->no_brg - $barisbrg->nm_brg", 1, 0, 'L', 0, '', true);
                        PDF::Cell(25, 0, "$barisbrg->sisa_barang", 1, 0, 'L', 0, '', true);
                        PDF::Cell(30, 0, "$nilaibrg", 1, 1, 'R', 0, '', true);
                        PDF::ln(0);
                    }
                }
            }
            $total_nilai2 = rupiah($total_nilai);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(20, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(140, 0, "Jumlah", 1, 0, 'R', 0, '', true);
            PDF::Cell(30, 0, "$total_nilai2", 1, 1, 'R', 0, '', true);
        //}
        //else if($lokasi == "023170800677513000KD")
        //{

        //}
        //else
        //{

        //}

        if($lokasi == "690522009KD")
        {
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

        PDF::Output('laporan_posisi_persedian_di_neraca.pdf');
    }
}
