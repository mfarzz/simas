<?php

namespace App\Http\Controllers\BarangKeluar;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\JabpenfkModel;
use App\Models\LokasiModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapBkfnPrintCt extends Controller
{
    Public Function index($filter, $filter2)
    {
        $tgl_awal = Crypt::decryptString($filter);
        $tgl_akhir = Crypt::decryptString($filter2);
        $user_id = auth()->user()->id;

        $barislok = User::
        join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)
        ->first();

        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $lokasi = $barislok->kd_lks;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
        $pejabatanop = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();
       

        function rupiah($angka){
	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Barang Keluar');
        PDF::SetMargins(10, 39, 10);
        PDF::SetFont('times', '', 8);
        // Define the header function
        $tgl_awal_cek = \Carbon\Carbon::parse($tgl_awal)->locale('id')->isoFormat('D MMMM Y');
        $tgl_awal_cek2 = strtoupper($tgl_awal_cek);
        $tgl_akhir_cek = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tgl_akhir_cek2 = strtoupper($tgl_akhir_cek);
        $header = function() use ($tgl_akhir_cek2, $tahunanggaran, $datalokasi, $lokasi, $tgl_awal_cek2) {
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'LAPORAN BARANG KELUAR', 0, 1, 'C', 0, '', 0);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(0, 0, "DARI TANGGAL $tgl_awal_cek2 SAMPAI TANGGAL $tgl_akhir_cek2", 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(80, 0, "Kategori Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(80, 0, "Nama Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(25, 0, "Jumlah Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Harga Per Satuan", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Total", 1, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::SetFont('times', '', 10);
            //PDF::ln(140);
        };

        // Set the header for each page
        PDF::SetHeaderCallback($header);
        PDF::AddPage('L');

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
            $datalap = BarangKeluarFakultasDetailModel::
            join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
            ->join('barang_keluar_fakultas_nota','barang_keluar_fakultas.id_bkfn','=','barang_keluar_fakultas_nota.id_bkfn')
            ->join('barang_masuk_fakultas','barang_keluar_fakultas_detail.id_bmf','=','barang_masuk_fakultas.id_bmf')
            ->join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
            ->join('kategori','barang.kd_kt','=','kategori.kd_kt')
            ->where('barang_keluar_fakultas.id_fk','=',$datafakultas->id_fk)
            ->where('tglambil_bkf', '<=', $tgl_akhir )
            ->where('tglambil_bkf', '>=', $tgl_awal )
            ->get();
            foreach($datalap as $barislap)
            {
                $hrg_bmf_rp = rupiah($barislap->hrg_bmf);
                $total = $barislap->hrg_bmf * $barislap->jmlh_bkfd;
                $total_rp = rupiah($total);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$barislap->no_bkfn", 1, 0, 'C', 0, '', true);
                PDF::Cell(80, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
                PDF::Cell(80, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(25, 0, "$barislap->jmlh_bkfd", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$hrg_bmf_rp", 1, 0, 'R', 0, '', true);
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

        


        PDF::Output('laporan_barang_masuk.pdf');
    }
}
