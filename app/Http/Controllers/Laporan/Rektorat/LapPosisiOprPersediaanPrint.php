<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\JabpenurModel;
use App\Models\LokasiModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpurdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikRektoratDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapPosisiOprPersediaanPrint extends Controller
{
    Public Function index($filter)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = "690522009KD";
        $user_id = auth()->user()->id;
        $id_urj = auth()->user()->id_urj;
        $barisrektorat = UnitRektoratJabatanModel::where('id_urj','=',$id_urj)->first();
        $id_ur = $barisrektorat->id_ur;

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
        if($jumlah != 0)
        {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
            $datadeletetbm->delete();
        }

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  

        $pejabatanpimpinan = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', $datarektorat->id_ur)->where('jabatan_pengesahan_rektorat.id_jabur', 1)->first();
        $pejabatanop = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', $datarektorat->id_ur)->where('jabatan_pengesahan_rektorat.id_jabur', 2)->first();

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
            ->where('id_ur', '=', $id_ur)
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
                ->where('id_ur', '=', $id_ur)
                ->where('kd_brg', '=', $barisbmr->kd_brg)
                ->where('tgl_opur', '<=', $tgl_akhir )
                ->orderBy('tgl_opur','desc')
                ->first();

                $jumlahbk = BarangKeluarRektoratModel::
                where('id_ur', '=', $id_ur)
                ->where('kd_brg', '=', $barisbmr->kd_brg)
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

        /*$databarangmasukrektorat = BarangMasukRektoratModel::
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
                $datatbmr->jns_tbm = 1;
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

                $datatbmr = new TempBarangMasukModel();                    
                $datatbmr->kd_brg = $barisbmr->kd_brg;
                $datatbmr->sisa_tbm = $sisa_tbmr;
                $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                $datatbmr->kd_lks = $lokasi;
                $datatbmr->user_id = $user_id;
                $datatbmr->jns_tbm = 1;
                $datatbmr->save();
            }
        }*/

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

        PDF::Output('laporan_persedian.pdf');
    }
}
