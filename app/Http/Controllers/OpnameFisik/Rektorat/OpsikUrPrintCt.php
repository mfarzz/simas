<?php

namespace App\Http\Controllers\OpnameFisik\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\JabpenurModel;
use App\Models\LokasiModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpurakModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikUrPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opur = Crypt::decryptString($filter);
        $dataopsik = OpsikRektoratModel::
            where('id_opur', $id_opur)->first();
        $no_opur = $dataopsik->no_opur;
        $sem_opur = $dataopsik->sem_opur;
        $thn_opur = $dataopsik->thn_opur;
        $tgl_opur = \Carbon\Carbon::parse($dataopsik->tgl_opur)->formatLocalized('%d %B %Y');

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

        $pejabatanpimpinan = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', $datarektorat->id_ur)->where('jabatan_pengesahan_rektorat.id_jabur', 1)->first();
        $pejabatanop = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', $datarektorat->id_ur)->where('jabatan_pengesahan_rektorat.id_jabur', 2)->first();


        function rupiah($angka){	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        PDF::SetTitle('Opname Fisik Barang');
        PDF::SetMargins(10, 51, 10);
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
            PDF::Cell(0, 0, 'OPNAME FISIK BARANG PERSEDIAAN', 0, 1, 'C', 0, '', 0);
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
            PDF::Cell(110, 0, "Nama Barang", 1, 0, 'C', 0, '', true);
            PDF::Cell(20, 0, "Satuan", 1, 0, 'C', 0, '', true);
            PDF::Cell(15, 0, "Volume", 1, 0, 'C', 0, '', true);
            PDF::Cell(30, 0, "Jumlah", 1, 0, 'C', 0, '', true);
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

        PDF::SetFooterCallback($footer);
        $no=1;
            $datalap = OpsikUrDetModel::
            join('barang','opsik_rektorat_detail.kd_brg','=','barang.kd_brg')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where('id_opur','=',$id_opur)
            ->get();
            foreach($datalap as $barislap)
            {

                $dataopak = OpurakModel::
                where('id_opurdet', $barislap->id_opurdet)
                ->first();
                $total_opurak = rupiah($dataopak->total_opurak);
                //$hrg_bmf_rp = rupiah($barislap->hrg_bmf);
                //$total = $barislap->hrg_bmf * $barislap->jmlh_bkfd;
                //$total_rp = rupiah($total);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(110, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(20, 0, "$barislap->nm_js", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "$barislap->stok_opsik_opurdet", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$total_opurak", 1, 0, 'R', 0, '', true);
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

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
