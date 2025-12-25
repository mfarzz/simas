<?php

namespace App\Http\Controllers\Sis_aset\Laporan\JenisTransaksi;

use App\Http\Controllers\Controller;
use App\Models\AsetLokasiModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetTempLapModel;
use App\Models\User;
use App\Models\VAsetLapTransKategoriBrgModel;
use App\Models\VAsetLapTransKategoriModel;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapJnsTransPrint extends Controller
{
    public function index($tgl_awal, $tgl_akhir, $tercatat, $id_lokasi)
    {
        $tgl_awal = Crypt::decryptString($tgl_awal);
        $tgl_akhir = Crypt::decryptString($tgl_akhir);
        $tercatat = Crypt::decryptString($tercatat);
        $a_kd_al = Crypt::decryptString($id_lokasi);

        $user_id = auth()->user()->id;
        $datalokasi = AsetLokasiModel::where('a_kd_al', $a_kd_al)->first();

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
}
