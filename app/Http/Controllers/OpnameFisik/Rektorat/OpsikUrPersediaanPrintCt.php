<?php

namespace App\Http\Controllers\OpnameFisik\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\LokasiModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpurakModel;
use App\Models\OpurbkrModel;
use App\Models\OpurbmrModel;
use App\Models\OpuropModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikUrPersediaanPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opur = Crypt::decryptString($filter);
        $dataopsik = OpsikRektoratModel::
        join('unit_rektorat','opsik_rektorat.id_ur','=','unit_rektorat.id_ur')
        ->where('id_opur', $id_opur)->first();
        $no_opur = $dataopsik->no_opur;
        $sem_opur = $dataopsik->sem_opur;
        $thn_opur = $dataopsik->thn_opur;
        $tgl_opur = \Carbon\Carbon::parse($dataopsik->tgl_opur)->formatLocalized('%d %B %Y');

        $user_id = auth()->user()->id;

        $lokasi = $dataopsik->kd_lks;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        function rupiah($angka){	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        PDF::SetTitle('Opname Fisik Barang');
        PDF::SetMargins(10, 56, 10);
        PDF::SetFont('times', '', 8);

        $header = function() use ($sem_opur, $thn_opur, $no_opur, $tgl_opur, $datalokasi, $lokasi) {
            PDF::ln(5);
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Lampiran Berita Acara Opname Fisik", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Nomor : $no_opur", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Tanggal : $tgl_opur", 0, 0, 'L', 0, '', true);
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'LAPORAN PERSEDIAAN BARANG', 0, 1, 'C', 0, '', 0);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(0, 0, "SEMESTER $sem_opur TAHUN $thn_opur", 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(90, 0, "Nama Barang", 1, 0, 'C', 0, '', true);
            PDF::Cell(35, 0, "Saldo Awal", 1, 0, 'C', 0, '', true);
            PDF::Cell(35, 0, "Barang Masuk", 1, 0, 'C', 0, '', true);
            PDF::Cell(35, 0, "Barang Keluar", 1, 0, 'C', 0, '', true);
            PDF::Cell(35, 0, "Opsik", 1, 0, 'C', 0, '', true);
            PDF::Cell(35, 0, "Saldo Akhir", 1, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(10, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(90, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Jumlah", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Total", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Jumlah", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Total", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Jumlah", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Total", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Jumlah", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Total", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Jumlah", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Total", 1, 1, 'C', 0, '', true);
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
            $datalap = OpsikUrDetModel::
            where('id_opur', $id_opur)
            ->join('barang','opsik_rektorat_detail.kd_brg','=','barang.kd_brg')
            ->get();
            foreach($datalap as $barislap)
            {
                $dataopbmr = OpurbmrModel::
                where('id_opurdet', $barislap->id_opurdet)
                ->first();

                $dataopbkr = OpurbkrModel::
                where('id_opurdet', $barislap->id_opurdet)
                ->first();

                $dataopop = OpuropModel::
                where('id_opurdet', $barislap->id_opurdet)
                ->first();

                $dataopak = OpurakModel::
                where('id_opurdet', $barislap->id_opurdet)
                ->first();
                $total_opurbmr = rupiah($dataopbmr->total_opurbmr);
                $total_opurbkr = rupiah($dataopbkr->total_opurbkr);
                $total_opurop = rupiah($dataopop->total_opurop);
                $total_opurak = rupiah($dataopak->total_opurak);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(90, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "0", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "0", 1, 0, 'R', 0, '', true);
                PDF::Cell(15, 0, "$dataopbmr->jmlh_opurbmr", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$total_opurbmr", 1, 0, 'R', 0, '', true);
                PDF::Cell(15, 0, "- $dataopbkr->jmlh_opurbkr", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "- $total_opurbkr", 1, 0, 'R', 0, '', true);
                if($dataopop->status_opurop==1)
                {                    
                    PDF::Cell(15, 0, "$dataopop->jmlh_opurop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "$total_opurop", 1, 0, 'R', 0, '', true);
                }
                else if ($dataopop->status_opurop==2)
                {                    
                    PDF::Cell(15, 0, "- $dataopop->jmlh_opurop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "- $total_opurop", 1, 0, 'R', 0, '', true);
                }
                else if ($dataopop->status_opurop==3)
                {                    
                    PDF::Cell(15, 0, "$dataopop->jmlh_opurop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "$total_opurop", 1, 0, 'R', 0, '', true);
                }
                PDF::Cell(15, 0, "$dataopak->jmlh_opurak", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$total_opurak", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $no++;
            }

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
