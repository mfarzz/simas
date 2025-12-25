<?php

namespace App\Http\Controllers\OpnameFisik\Fakultas;

use App\Http\Controllers\Controller;
use App\Models\JabpenfkModel;
use App\Models\LokasiModel;
use App\Models\OpfkakModel;
use App\Models\OpsikFakultasModel;
use App\Models\OpsikFkDetModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikFkPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opfk = Crypt::decryptString($filter);
        $dataopsik = OpsikFakultasModel::
            where('id_opfk', $id_opfk)->first();
        $no_opfk = $dataopsik->no_opfk;
        $sem_opfk = $dataopsik->sem_opfk;
        $thn_opfk = $dataopsik->thn_opfk;
        $tgl_opfk = \Carbon\Carbon::parse($dataopsik->tgl_opfk)->formatLocalized('%d %B %Y');

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
            PDF::Cell(0, 0, 'OPNAME FISIK BARANG PERSEDIAAN', 0, 1, 'C', 0, '', 0);
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
            $datalap = OpsikFkDetModel::
            join('barang','opsik_fakultas_detail.kd_brg','=','barang.kd_brg')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where('id_opfk','=',$id_opfk)
            ->get();
            foreach($datalap as $barislap)
            {

                $dataopak = OpfkakModel::
                where('id_opfkdet', $barislap->id_opfkdet)
                ->first();
                $total_opfkak = rupiah($dataopak->total_opfkak);
                //$hrg_bmf_rp = rupiah($barislap->hrg_bmf);
                //$total = $barislap->hrg_bmf * $barislap->jmlh_bkfd;
                //$total_rp = rupiah($total);
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(110, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(20, 0, "$barislap->nm_js", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "$barislap->stok_opsik_opfkdet", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$total_opfkak", 1, 1, 'R', 0, '', true);
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

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
