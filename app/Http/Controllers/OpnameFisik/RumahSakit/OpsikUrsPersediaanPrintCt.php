<?php

namespace App\Http\Controllers\OpnameFisik\RumahSakit;

use App\Http\Controllers\Controller;
use App\Models\LokasiModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikRumahSakitModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpurakModel;
use App\Models\OpurbkrModel;
use App\Models\OpurbmrModel;
use App\Models\OpuropModel;
use App\Models\OpursakModel;
use App\Models\OpursbkrsModel;
use App\Models\OpursbmrsModel;
use App\Models\OpursopModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikUrsPersediaanPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opurs = Crypt::decryptString($filter);
        $dataopsik = OpsikRumahSakitModel::
        join('unit_rumah_sakit','opsik_rumah_sakit.id_urs','=','unit_rumah_sakit.id_urs')
        ->where('id_opurs', $id_opurs)->first();
        $no_opurs = $dataopsik->no_opurs;
        $sem_opurs = $dataopsik->sem_opurs;
        $thn_opurs = $dataopsik->thn_opurs;
        $tgl_opurs = \Carbon\Carbon::parse($dataopsik->tgl_opurs)->formatLocalized('%d %B %Y');

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

        $header = function() use ($sem_opurs, $thn_opurs, $no_opurs, $tgl_opurs, $datalokasi, $lokasi) {
            PDF::ln(5);
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Lampiran Berita Acara Opname Fisik", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Nomor : $no_opurs", 0, 0, 'L', 0, '', true);
            PDF::ln();
            PDF::SetFont('times', '', 8);
            PDF::Cell(50, 0, "Tanggal : $tgl_opurs", 0, 0, 'L', 0, '', true);
            PDF::ln(5);
            PDF::SetFont('times', 'b', 14);
            PDF::Cell(0, 0, 'LAPORAN PERSEDIAAN BARANG', 0, 1, 'C', 0, '', 0);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(0, 0, "SEMESTER $sem_opurs TAHUN $thn_opurs", 0, 1, 'C', 0, '', 0);
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
            $datalap = OpsikUrsDetModel::
            where('id_opurs', $id_opurs)
            ->join('barang','opsik_rumah_sakit_detail.kd_brg','=','barang.kd_brg')
            ->get();
            foreach($datalap as $barislap)
            {
                $dataopbmrs = OpursbmrsModel::
                where('id_opursdet', $barislap->id_opursdet)
                ->first();

                $dataopbkrs = OpursbkrsModel::
                where('id_opursdet', $barislap->id_opursdet)
                ->first();

                $dataopop = OpursopModel::
                where('id_opursdet', $barislap->id_opursdet)
                ->first();

                $dataopak = OpursakModel::
                where('id_opursdet', $barislap->id_opursdet)
                ->first();
                $total_opursbmrs = rupiah($dataopbmrs->total_opursbmr);
                $total_opursbkrs = rupiah($dataopbkrs->total_opursbkrs);
                $total_opursop = rupiah($dataopop->total_opursop);
                $total_opursak = rupiah($dataopak->total_opursak);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(90, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "0", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "0", 1, 0, 'R', 0, '', true);
                PDF::Cell(15, 0, "$dataopbmrs->jmlh_opursbmrs", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$total_opursbmrs", 1, 0, 'R', 0, '', true);
                PDF::Cell(15, 0, "- $dataopbkrs->jmlh_opursbkrs", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "- $total_opursbkrs", 1, 0, 'R', 0, '', true);
                if($dataopop->status_opursop==1)
                {                    
                    PDF::Cell(15, 0, "$dataopop->jmlh_opursop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "$total_opursop", 1, 0, 'R', 0, '', true);
                }
                else if ($dataopop->status_opursop==2)
                {                    
                    PDF::Cell(15, 0, "- $dataopop->jmlh_opursop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "- $total_opursop", 1, 0, 'R', 0, '', true);
                }
                else if ($dataopop->status_opursop==3)
                {                    
                    PDF::Cell(15, 0, "$dataopop->jmlh_opursop", 1, 0, 'C', 0, '', true);
                    PDF::Cell(20, 0, "$total_opursop", 1, 0, 'R', 0, '', true);
                }
                PDF::Cell(15, 0, "$dataopak->jmlh_opursak", 1, 0, 'C', 0, '', true);
                PDF::Cell(20, 0, "$total_opursak", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $no++;
            }

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
