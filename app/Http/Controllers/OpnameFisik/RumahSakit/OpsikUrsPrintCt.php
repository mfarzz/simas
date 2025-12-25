<?php

namespace App\Http\Controllers\OpnameFisik\RumahSakit;

use App\Http\Controllers\Controller;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\LokasiModel;
use App\Models\OpsikRektoratModel;
use App\Models\OpsikRumahSakitModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpurakModel;
use App\Models\OpursakModel;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PDF;

class OpsikUrsPrintCt extends Controller
{
    Public Function index($filter)
    {
        $id_opurs = Crypt::decryptString($filter);
        $dataopsik = OpsikRumahSakitModel::
            where('id_opurs', $id_opurs)->first();
        $no_opurs = $dataopsik->no_opurs;
        $sem_opurs = $dataopsik->sem_opurs;
        $thn_opurs = $dataopsik->thn_opurs;
        $tgl_opurs = \Carbon\Carbon::parse($dataopsik->tgl_opurs)->formatLocalized('%d %B %Y');

        $user_id = auth()->user()->id;

        $barislok = User::
        join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
        ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
        ->where('users.id', $user_id)
        ->first();

        $datarektorat = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
            ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
            ->where('users.id', $user_id)->first();  

        $lokasi = $barislok->kd_lks;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $pejabatanpimpinan = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarektorat->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 1)->first();
        $pejabatanop = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarektorat->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 2)->first();


        function rupiah($angka){	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        PDF::SetTitle('Opname Fisik Barang');
        PDF::SetMargins(10, 51, 10);
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
            PDF::Cell(0, 0, 'OPNAME FISIK BARANG PERSEDIAAN', 0, 1, 'C', 0, '', 0);
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
            $datalap = OpsikUrsDetModel::
            join('barang','opsik_rumah_sakit_detail.kd_brg','=','barang.kd_brg')
            ->join('jenis_satuan','barang.id_js','=','jenis_satuan.id_js')
            ->where('id_opurs','=',$id_opurs)
            ->get();
            foreach($datalap as $barislap)
            {
                $dataopak = OpursakModel::
                where('id_opursdet', $barislap->id_opursdet)
                ->first();
                $total_opursak = rupiah($dataopak->total_opursak);                
                PDF::SetFont('times', '', 10);
                PDF::Cell(10, 0, "$no", 1, 0, 'C', 0, '', true);
                PDF::Cell(110, 0, "$barislap->nm_brg", 1, 0, 'L', 0, '', true);
                PDF::Cell(20, 0, "$barislap->nm_js", 1, 0, 'L', 0, '', true);
                PDF::Cell(15, 0, "$barislap->stok_opsik_opursdet", 1, 0, 'C', 0, '', true);
                PDF::Cell(30, 0, "$total_opursak", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $no++;
            }

     
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Mengetahui:", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jaburs", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "$pejabatanop->nm_jaburs", 0, 1, 'C', 0, '', true);
        PDF::ln(20);
        PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenurs", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "$pejabatanop->nm_jabpenurs", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenurs", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenurs", 0, 1, 'C', 0, '', true);

        PDF::Output('lampiran-opname-fisik.pdf');
    }
}
