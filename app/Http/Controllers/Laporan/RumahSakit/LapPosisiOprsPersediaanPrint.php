<?php

namespace App\Http\Controllers\Laporan\RumahSakit;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\LokasiModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpurdetitmModel;
use App\Models\OpursdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitJabatanModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikRumahSakitDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapPosisiOprsPersediaanPrint extends Controller
{
    Public Function index($filter)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = "690522020KD";
        $user_id = auth()->user()->id;
        $id_ursj = auth()->user()->id_ursj;
        $barisrumahsakit = UnitRumahSakitJabatanModel::where('id_ursj','=',$id_ursj)->first();
        $id_urs = $barisrumahsakit->id_urs;

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
        if($jumlah != 0)
        {
            $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
            $datadeletetbm->delete();
        }

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $datarumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  

        $pejabatanpimpinan = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 1)->first();
        $pejabatanop = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 2)->first();

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
            ->where('id_urs', '=', $id_urs)
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
                ->where('id_urs', '=', $id_urs)
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
                ->where('tgl_opurs', '<=', $tgl_akhir )
                ->orderBy('tgl_opurs','desc')
                ->first();

                $jumlahbk = BarangKeluarRumahSakitModel::
                where('id_urs', '=', $id_urs)
                ->where('kd_brg', '=', $barisbmrs->kd_brg)
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

        PDF::Output('laporan_persedian.pdf');
    }
}
