<?php

namespace App\Http\Controllers\Sis_aset\Laporan\Rektorat\JenisTransaksi;

use App\Http\Controllers\Controller;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetTempLapModel;
use App\Models\User;
use App\Models\VAsetLapTransKategoriBrgModel;
use App\Models\VAsetLapTransKategoriModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapOprJnsTransPrint extends Controller
{
    public function index($tgl_awal, $tgl_akhir, $tercatat)
    {
        $tgl_awal = Crypt::decryptString($tgl_awal);
        $tgl_akhir = Crypt::decryptString($tgl_akhir);
        $tercatat = Crypt::decryptString($tercatat);

        $user_id = auth()->user()->id;
        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();   
            
        $datalokasi = AsetLokasiModel::where('id_ur', $datarektorat->id_ur)->first();

        $jumlah = AsetTempLapModel::where('user_id', $user_id)->where('a_jns_atl','=','1')->count();
        if($jumlah != 0)
        {
            $datadeletetbm = AsetTempLapModel::where('user_id', $user_id)->where('a_jns_atl','=','1');
            $datadeletetbm->delete();
        }

        $t_a_hrg_atl=0;
        $t_a_jmlh_atl=0;
        $a_kd_brg=0;
        $id_atl = 0;
        $dataperolehan = AsetPerolehanItemModel::
        join('aset_perolehan','aset_perolehan_item.a_id_ap','=','aset_perolehan.a_id_ap')
        ->where('a_kd_al', '=', $datalokasi->a_kd_al )
        ->whereBetween('a_tgl_perolehan_ap', [$tgl_awal, $tgl_akhir])
        ->orderBy('a_tgl_perolehan_ap','asc')
        ->orderBy('a_kd_brg','asc')
        ->get();
        foreach($dataperolehan as $barisperolehan)
        {            
            if($a_kd_brg == $barisperolehan->a_kd_brg)
            {
                $t_a_jmlh_atl = $barisperolehan->a_kuantitas_api + $t_a_jmlh_atl;
                $t_a_hrg_atl = $barisperolehan->a_nilai_api + $t_a_hrg_atl;
                $dataasettempupt = AsetTempLapModel::where('id_atl', $id_atl)->first();
                $dataasettempupt->a_kd_brg = $barisperolehan->a_kd_brg;
                $dataasettempupt->a_jmlh_atl = $t_a_jmlh_atl;
                $dataasettempupt->a_hrg_atl = $t_a_hrg_atl;
                $dataasettempupt->a_kd_al = $datalokasi->a_kd_al;
                $dataasettempupt->a_jns_atl = 1;
                $dataasettempupt->user_id = $user_id;
                $dataasettempupt->save();
            }
            else
            {
                $t_a_jmlh_atl = 0;
                $t_a_hrg_atl = 0;

                $t_a_jmlh_atl = $barisperolehan->a_kuantitas_api + $t_a_jmlh_atl;
                $t_a_hrg_atl = $barisperolehan->a_nilai_api + $t_a_hrg_atl;
                $dataasettemp = new AsetTempLapModel();                    
                $dataasettemp->a_kd_brg = $barisperolehan->a_kd_brg;
                $dataasettemp->a_jmlh_atl = $t_a_jmlh_atl;
                $dataasettemp->a_hrg_atl = $t_a_hrg_atl;
                $dataasettemp->a_kd_al = $datalokasi->a_kd_al;
                $dataasettemp->a_jns_atl = 1;
                $dataasettemp->user_id = $user_id;
                $dataasettemp->save();
                $id_atl = $dataasettemp->id_atl;
            }

            //echo "$a_kd_brg == $barisperolehan->a_kd_brg = $barisperolehan->a_id_api = $barisperolehan->a_kd_brg = $barisperolehan->a_kd_brg_api = $barisperolehan->a_no_api = $barisperolehan->a_nilai_api = $t_a_jmlh_atl = $t_a_hrg_atl <br>";

            $a_kd_brg = $barisperolehan->a_kd_brg;
        }

        function rupiah($angka){
	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan  Daftar Barang Milik UNAND Menurut Jenis Transaksi');        
        PDF::AddPage('L');
        $tgl_awal = \Carbon\Carbon::parse($tgl_awal)->format('d M Y');
        $tgl_akhir = \Carbon\Carbon::parse($tgl_akhir)->format('d M Y');

        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, 'LAPORAN DAFTAR BARANG MILIK UNAND MENURUT JENIS TRANSAKSI', 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, "RINCIAN PER SUB-SUB KELOMPOK BARANG", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "POSISI $tgl_awal S/D $tgl_akhir", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "TAHUN ANGGARAN $tahunanggaran", 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', '', 10);

        PDF::ln(5);
        PDF::Cell(28, 0, "NAMA UAPKB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$datalokasi->a_nm_al", 0, 1, 'L', 0, '', true);
        PDF::ln(0);
        /*PDF::Cell(28, 0, "Kode UAPKPB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$lokasi", 0, 1, 'L', 0, '', true);*/
        PDF::ln(0);
        PDF::Cell(28, 0, "JENIS TRANSAKSI", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$datalokasi->a_nm_al", 0, 1, 'L', 0, '', true);
        PDF::ln(0);
        /*PDF::Cell(28, 0, "Kode UAPKPB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$lokasi", 0, 1, 'L', 0, '', true);*/

        PDF::ln(5);
        PDF::Cell(160, 0, "AKUN NERACA/SUB-SUB KELOMPOK BARANG", 1, 0, 'C', 0, '', true);
        PDF::Cell(20, 0, "SAT", 1, 0, 'C', 0, '', true);
        PDF::Cell(80, 0, "INTRAKOMPTABEL", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(40, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "AKUN NERACA/SUB-SUB KELOMPOK BARANG", 1, 0, 'C', 0, '', true);
        PDF::Cell(20, 0, "SAT", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "KUANTITAS", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(40, 0, "1", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "2", 1, 0, 'C', 0, '', true);
        PDF::Cell(20, 0, "3", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "4", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "5", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::SetFont('times', '', 10);
        $total_nilai = 0;
        $datalap = VAsetLapTransKategoriModel::
        where('v_aset_lap_trans_kategori.a_kd_al','=', "$datalokasi->a_kd_al")
        ->where('v_aset_lap_trans_kategori.user_id','=',$user_id)
        ->where('v_aset_lap_trans_kategori.a_jns_atl','=',1)
        ->get();
        foreach($datalap as $barislap)
        {
            $a_hrg_atl = rupiah($barislap->a_hrg_atl);
            PDF::SetFont('times', 'b', 10);
            PDF::SetTextColor(0, 0, 255);
            PDF::Cell(40, 0, "$barislap->a_kd_kt", 1, 0, 'L', 0, '', true);
            PDF::Cell(120, 0, "$barislap->a_nm_kt", 1, 0, 'L', 0, '', true);
            PDF::Cell(20, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(40, 0, "$barislap->a_jmlh_atl", 1, 0, 'R', 0, '', true);
            PDF::Cell(40, 0, "$a_hrg_atl", 1, 1, 'R', 0, '', true);
            PDF::ln(0);
            $datalapbrg = VAsetLapTransKategoriBrgModel::
            where('v_aset_lap_trans_kategori_brg.a_kd_kt','=', "$barislap->a_kd_kt")
            ->where('v_aset_lap_trans_kategori_brg.a_kd_al','=', "$datalokasi->a_kd_al")
            ->where('v_aset_lap_trans_kategori_brg.user_id','=',$user_id)
            ->where('v_aset_lap_trans_kategori_brg.a_jns_atl','=',1)
            ->orderBy('v_aset_lap_trans_kategori_brg.a_kd_brg')
            ->get();
            foreach($datalapbrg as $barisbrg)
            {
                PDF::SetFont('times', '', 10);
                PDF::SetTextColor(0, 0, 0);
                $a_hrg_atl_brg = rupiah($barisbrg->a_hrg_atl);
                PDF::Cell(40, 0, "$barisbrg->a_kd_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(120, 0, "$barisbrg->a_nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(20, 0, "", 1, 0, 'C', 0, '', true);
                PDF::Cell(40, 0, "$barisbrg->a_jmlh_atl", 1, 0, 'R', 0, '', true);
                PDF::Cell(40, 0, "$a_hrg_atl_brg", 1, 1, 'R', 0, '', true);
                PDF::ln(0);
            }
        }
        
        PDF::SetFont('times', '', 10);
        PDF::ln(10);        
        PDF::Cell(160, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Padang", 0, 1, 'C', 0, '', true);
        PDF::ln(0);        
        PDF::Cell(160, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Penanggung Jawab UAKPB", 0, 1, 'C', 0, '', true);
        PDF::ln(0);        
        PDF::Cell(160, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Direktur Umum dan Pengelolaan Aset", 0, 1, 'C', 0, '', true);

        PDF::Output('laporan_daftar_barang_milik_negara_menurut_jenis_transaksi.pdf');

    }
    /*Public Function index($filter)
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

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2')->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','2');
        $datadeletetbm->delete();  
        }
        $databarangmasukrektorat = BarangMasukRektoratModel::
        where('id_ur', '=', $id_ur )
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
                foreach($databarangkeluar as $barisbkd)                    
                {
                    $tjmlh_bkrd = $barisbkd->jmlh_bkrd + $tjmlh_bkrd;
                }
                $jmlh_awal_bmr = $barisbmr->jmlh_awal_bmr;
                //$diambil = $barisbmr->sisa_bmr + $tjmlh_bkrd;
                $sisa_tbmr = $jmlh_awal_bmr - $tjmlh_bkrd; 

                $datatbmr = new TempBarangMasukModel();                    
                $datatbmr->kd_brg = $barisbmr->kd_brg;
                $datatbmr->sisa_tbm = $sisa_tbmr;
                $datatbmr->hrg_tbm = $barisbmr->hrg_bmr;
                $datatbmr->kd_lks = $lokasi;
                $datatbmr->user_id = $user_id;
                $datatbmr->jns_tbm = 2;
                $datatbmr->save();
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
        PDF::Cell(28, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "URAIAN", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::SetFont('times', '', 10);
        //if($lokasi == "023170800677513009KD")
        //{            
            $total_nilai = 0;
            $datalap = VLapPosisi4Model::
            join('kelompok','v_lap_posisi4.v_kd_kl','=','kelompok.kd_kl')
            ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
            ->where('v_lap_posisi4.user_id','=',$user_id)
            ->where('v_lap_posisi4.v_jns_tbm','=',2)
            ->get();
            foreach($datalap as $barislap)
            {
                $nilairp = rupiah($barislap->total_nilai);
                PDF::SetFont('times', 'b', 10);
                PDF::Cell(28, 0, "$barislap->kd_kl", 1, 0, 'C', 0, '', true);
                PDF::Cell(120, 0, "$barislap->nm_kl", 1, 0, 'L', 0, '', true);
                PDF::Cell(40, 0, "", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                PDF::SetFont('times', '', 10);
                $total_nilai = $total_nilai + $barislap->total_nilai;
                $datalapsubsub = VLapPosisi3Model::
                join('subsubkategori','v_lap_posisi3.v_kd_sskt','=','subsubkategori.kd_sskt')
                ->where('v_lap_posisi3.v_kd_kl','=',$barislap->v_kd_kl)
                ->where('v_lap_posisi3.user_id','=',$user_id)
                ->where('v_lap_posisi3.v_jns_tbm','=',2)
                ->get();
                foreach($datalapsubsub as $barislapsubsub)
                {
                    $nilaisubsubrp = rupiah($barislapsubsub->total_nilai);
                    PDF::Cell(28, 0, "$barislapsubsub->kd_sskt", 1, 0, 'R', 0, '', true);
                    PDF::Cell(120, 0, "$barislapsubsub->nm_sskt", 1, 0, 'L', 0, '', true);
                    PDF::Cell(40, 0, "$nilaisubsubrp", 1, 1, 'R', 0, '', true);
                    PDF::ln(0);

                    $datalapbrg = VLapPosisi2Model::
                    join('barang','v_lap_posisi2.v_kd_brg','=','barang.kd_brg')
                    ->where('v_lap_posisi2.v_kd_sskt','=',$barislapsubsub->v_kd_sskt)
                    ->where('v_lap_posisi2.user_id','=',$user_id)
                    ->where('v_lap_posisi2.v_jns_tbm','=',2)
                    ->get();
                    foreach($datalapbrg as $barisbrg)
                    {
                        $nilaibrg = rupiah($barisbrg->total_nilai);
                        PDF::Cell(28, 0, "$barisbrg->no_brg", 1, 0, 'R', 0, '', true);
                        PDF::Cell(120, 0, "- $barisbrg->nm_brg", 1, 0, 'L', 0, '', true);
                        PDF::Cell(40, 0, "$nilaibrg", 1, 1, 'R', 0, '', true);
                        PDF::ln(0);
                    }
                }
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
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Disetujui tanggal:", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "Kuasa Pengguna Barang,", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Petugas Pengelola Persediaan,", 0, 1, 'C', 0, '', true);

        


        PDF::Output('laporan_posisi_persedian_di_neraca.pdf');
    }*/
}
