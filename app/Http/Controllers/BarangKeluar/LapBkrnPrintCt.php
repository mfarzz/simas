<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\LokasiModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapBkrnPrintCt extends Controller
{
    Public Function index($filter, $filter2)
    {
        $tgl_awal = Crypt::decryptString($filter);
        $tgl_akhir = Crypt::decryptString($filter2);
        $user_id = auth()->user()->id;

        $barislok = User::
        join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
        ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
        ->where('users.id', $user_id)
        ->first();

        $datarektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
            ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
            ->where('users.id', $user_id)->first();  

        $lokasi = $barislok->kd_lks;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();
       

        function rupiah($angka){
	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Barang Keluar');
        PDF::SetMargins(10, 39, 10);
        PDF::SetFont('times', '', 8);
        // Define the header function
        $tgl_awal = \Carbon\Carbon::parse($tgl_awal)->formatLocalized('%d %B %Y');
        $tgl_awal = strtoupper($tgl_awal);
        $tgl_akhir = \Carbon\Carbon::parse($tgl_akhir)->formatLocalized('%d %B %Y');
        $tgl_akhir = strtoupper($tgl_akhir);
        $header = function() use ($tgl_akhir, $tahunanggaran, $datalokasi, $lokasi, $tgl_awal) {
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'LAPORAN BARANG KELUAR', 0, 1, 'C', 0, '', 0);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(0, 0, "DARI TANGGAL $tgl_awal SAMPAI TANGGAL $tgl_akhir", 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(10, 0, "No", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "No Nota", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Kategori Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(50, 0, "Nama Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(25, 0, "Jumlah Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Harga Per Satuan", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Total", 1, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::SetFont('times', '', 10);
            //PDF::ln(140);
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
            $total_nilai = 0;
            $no=1;
            $datalap = BarangKeluarRektoratDetailModel::
            join('barang_keluar_rektorat','barang_keluar_rektorat_detail.id_bkr','=','barang_keluar_rektorat.id_bkr')
            ->join('barang_keluar_rektorat_nota','barang_keluar_rektorat.id_bkrn','=','barang_keluar_rektorat_nota.id_bkrn')
            ->join('barang_masuk_rektorat','barang_keluar_rektorat_detail.id_bmr','=','barang_masuk_rektorat.id_bmr')
            ->join('barang','barang_masuk_rektorat.kd_brg','=','barang.kd_brg')
            ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
            ->where('barang_keluar_rektorat.id_ur','=',$datarektorat->id_ur)
            ->where('tglambil_bkr', '<=', $tgl_akhir )
            ->where('tglambil_bkr', '>=', $tgl_awal )
            ->get();
            foreach($datalap as $barislap)
            {
                $hrg_bmr_rp = rupiah($barislap->hrg_bmr);
                $total = $barislap->hrg_bmr * $barislap->jmlh_bkrd;
                $total_rp = rupiah($total);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$barislap->no_bkrn", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
                PDF::Cell(50, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(25, 0, "$barislap->jmlh_bkrd", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$hrg_bmr_rp", 1, 0, 'R', 0, '', true);
                PDF::Cell(30, 0, "$total_rp", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $no++;
                //$total_nilai = $total_nilai + $barislap->total_nilai;
            }
            /*$total_nilai2 = rupiah($total_nilai);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(28, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(120, 0, "Jumlah", 1, 0, 'R', 0, '', true);
            PDF::Cell(40, 0, "$total_nilai2", 1, 1, 'R', 0, '', true);*/
        
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Disetujui tanggal:", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "Penanggungjawab Barang,", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Petugas Pengelola Persediaan,", 0, 1, 'C', 0, '', true);

        


        PDF::Output('laporan_barang_masuk.pdf');
    }
}
