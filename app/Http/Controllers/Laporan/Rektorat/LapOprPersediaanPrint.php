<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\JabpenurModel;
use App\Models\LokasiModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpurdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRektoratJabatanModel;
use App\Models\User;
use App\Models\VLapPosisi2Model;
use App\Models\VLapPosisi3Model;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikRektoratDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapOprPersediaanPrint extends Controller
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

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
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
                        $datatbmr->jns_tbm = 2;
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
                        $datatbmr->jns_tbm = 2;
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

                $datatbmf = new TempBarangMasukModel();                    
                $datatbmf->kd_brg = $barisbmr->kd_brg;
                $datatbmf->sisa_tbm = $sisa_tbmr;
                $datatbmf->hrg_tbm = $barisbmr->hrg_bmr;
                $datatbmf->kd_lks = $lokasi;
                $datatbmf->user_id = $user_id;
                $datatbmf->jns_tbm = 2;
                $datatbmf->save();
            }
            $nocek++;            
        }

        function rupiah($angka){
	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::SetMargins(10, 43.4, 10);
        PDF::SetFont('times', '', 8);
        // Define the header function
        $tgl = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tgl = strtoupper($tgl);
        $header = function() use ($tgl_akhir, $tahunanggaran, $datalokasi, $lokasi, $tgl) {
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'LAPORAN PERSEDIAN', 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(100, 0, "NAMA BARANG", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "SATUAN", 1, 0, 'C', 0, '', true);
            PDF::Cell(25, 0, "JUMLAH", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "HARGA", 1, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::SetFont('times', '', 10);
            PDF::ln(140);
        };

        // Set the header for each page
        PDF::SetHeaderCallback($header);
        PDF::AddPage();

        // Define the footer function
        $footer = function () {
            PDF::SetFont('times', 'I', 8);
            // Add page number to the right side of the footer
            PDF::SetY(-15); // Set vertical position
            PDF::Cell(0, 10, 'Halaman ' . PDF::PageNo(), 0, 0, 'R');
        };

        // Set the footer for each page
        PDF::SetFooterCallback($footer);
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
                    ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
                    ->where('v_lap_posisi2.v_kd_kt','=',$barislapsubsub->v_kd_kt)
                    ->where('v_lap_posisi2.user_id','=',$user_id)
                    ->where('v_lap_posisi2.v_jns_tbm','=',2)
                    ->get();
                    foreach($datalapbrg as $barisbrg)
                    {
                        $sisa_barang = floor($barisbrg->sisa_barang);
                        PDF::SetTextColor(0, 0, 0);
                        $nilaibrg = rupiah($barisbrg->total_nilai);
                        PDF::Cell(20, 0, "", 1, 0, 'R', 0, '', true);
                        PDF::Cell(100, 0, "$barisbrg->no_brg - $barisbrg->nm_brg", 1, 0, 'L', 0, '', true);
                        PDF::Cell(15, 0, "$barisbrg->nm_js", 1, 0, 'C', 0, '', true);
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

        


        PDF::Output('laporan_posisi_persedian_di_neraca.pdf');
    }
}
