<?php

namespace App\Http\Controllers\OpnameFisik\Universitas;

use App\Http\Controllers\Controller;
use App\Models\BarangMasukFakultasModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikFakultasModel;
use App\Models\OpsikFkDetModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikUvPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opfk = Crypt::decryptString($filter);
        $dataopsik = OpsikFakultasModel::
        join('fakultas','opsik_fakultas.id_fk','=','fakultas.id_fk')
        ->where('id_opfk', $id_opfk)->first();
        $no_opfk = $dataopsik->no_opfk;
        $sem_opfk = $dataopsik->sem_opfk;
        $thn_opfk = $dataopsik->thn_opfk;
        $tgl_opfk = \Carbon\Carbon::parse($dataopsik->tgl_opfk)->formatLocalized('%d %B %Y');

        $user_id = auth()->user()->id;

        $lokasi = $dataopsik->kd_lks;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();


        function rupiah($angka){	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        PDF::SetTitle('Opname Fisik Barang');
        PDF::SetMargins(10, 51, 10);
        PDF::SetFont('times', '', 8);

        $header = function() use ($sem_opfk, $thn_opfk, $no_opfk, $tgl_opfk, $datalokasi, $lokasi) {
            PDF::ln(5);
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Lampiran Berita Acara Opname Fisik", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Nomor : $no_opfk", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Tanggal : $tgl_opfk", 0, 0, 'L', 0, '', true);
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'OPNAME FISIK BARANG PERSEDIAAN (KESELURUHAN)', 0, 1, 'C', 0, '', 0);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(0, 0, "SEMESTER $sem_opfk TAHUN $thn_opfk", 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(40, 0, "Kategori Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(50, 0, "Sub Item", 1, 0, 'C', 0, '', true);
            PDF::Cell(90, 0, "Nama Barang", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Satuan", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Volume", 1, 0, 'C', 0, '', true);
            PDF::Cell(25, 0, "Harga Satuan", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Jumlah", 1, 0, 'C', 0, '', true);
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

        PDF::SetFooterCallback($footer);
        $no=1;
            $datalap = BarangMasukFakultasModel::
            join('barang','barang_masuk_fakultas.kd_brg','=','barang.kd_brg')
            ->join('subsubkategori','barang.kd_sskt','=','subsubkategori.kd_sskt')
            ->join('subkategori','subsubkategori.kd_skt','=','subkategori.kd_skt')
            ->join('kelompok','subsubkategori.kd_kl','=','kelompok.kd_kl')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->get();
            foreach($datalap as $barislap)
            {
                $hrg_bmf_rp = rupiah($barislap->hrg_bmf);
                $total = $barislap->hrg_bmf * $barislap->sisa_bmf;
                $total_rp = rupiah($total);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(40, 0, "$barislap->nm_kl", 1, 0, 'L', 0, '', true);
                PDF::Cell(50, 0, "$barislap->nm_skt", 1, 0, 'L', 0, '', true);
                PDF::Cell(90, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(20, 0, "$barislap->nm_js", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "$barislap->sisa_bmf", 1, 0, 'C', 0, '', true);
                PDF::Cell(25, 0, "$hrg_bmf_rp", 1, 0, 'R', 0, '', true);
                PDF::Cell(30, 0, "$total_rp", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $no++;
                //$total_nilai = $total_nilai + $barislap->total_nilai;
            }

     
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Mengetahui:", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "Ka. Seksi Perlengkapan dan Logistik,", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "Tim Opname Fisik,", 0, 1, 'C', 0, '', true);

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
