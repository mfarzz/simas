<?php
namespace App\Http\Controllers\Laporan\Fakultas;
use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\JabpenfkModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikFkDetModel;
use App\Models\TempBarangMasukModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikFakultasDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class LapPosisiOpfPersediaanPrint extends Controller
{
    Public Function index($filter)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $user_id = auth()->user()->id;

        // Single query replaces duplicate $barislok + $datafakultas
        $barislok = User::
        join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)
        ->first();

        $lokasi = $barislok->kd_lks;
        $id_fk  = $barislok->id_fk;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
        $pejabatanop       = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();

        // Delete previous temp data in one shot (no count check needed)
        DB::table('temp_barang_masuk')
            ->where('user_id', $user_id)
            ->where('jns_tbm', '1')
            ->delete();

        $databarangmasukfakultas = BarangMasukFakultasModel::
            where('kd_lks', '=', $lokasi)
            ->where('tglperolehan_bmf', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmf', 'asc')
            ->get();

        // Pre-aggregate all keluar sums grouped by id_bmf — one query instead of N
        $idBmfList = $databarangmasukfakultas->pluck('id_bmf')->toArray();

        $keluarSums = DB::table('barang_keluar_fakultas_detail')
            ->join('barang_keluar_fakultas', 'barang_keluar_fakultas_detail.id_bkf', '=', 'barang_keluar_fakultas.id_bkf')
            ->whereIn('barang_keluar_fakultas_detail.id_bmf', $idBmfList)
            ->where('tglambil_bkf', '<=', $tgl_akhir)
            ->selectRaw('id_bmf, COALESCE(SUM(jmlh_bkfd), 0) as total')
            ->groupBy('id_bmf')
            ->pluck('total', 'id_bmf');

        $masukSums = DB::table('barang_masuk_fakultas_detail')
            ->whereIn('id_bmf', $idBmfList)
            ->where('tglperolehan_bmfd', '<=', $tgl_akhir)
            ->selectRaw('id_bmf, COALESCE(SUM(jmlh_bmfd), 0) as total')
            ->groupBy('id_bmf')
            ->pluck('total', 'id_bmf');

        $opsikSums = DB::table('barang_keluar_fakultas_detail')
            ->join('opsik_fakultas', 'opsik_fakultas.id_opfk', '=', 'barang_keluar_fakultas_detail.id_opfk')
            ->whereIn('barang_keluar_fakultas_detail.id_bmf', $idBmfList)
            ->where('tgl_opfk', '<=', $tgl_akhir)
            ->selectRaw('barang_keluar_fakultas_detail.id_bmf, COALESCE(SUM(jmlh_bkfd), 0) as total')
            ->groupBy('barang_keluar_fakultas_detail.id_bmf')
            ->pluck('total', 'id_bmf');

        // Build batch insert array
        $batchInsert = [];
        foreach ($databarangmasukfakultas as $barisbmf) {
            $id   = $barisbmf->id_bmf;
            $sisa = ($barisbmf->jmlh_awal_bmf + ($masukSums[$id] ?? 0))
                  - (($keluarSums[$id] ?? 0) + ($opsikSums[$id] ?? 0));

            $batchInsert[] = [
                'kd_brg'   => $barisbmf->kd_brg,
                'sisa_tbm' => $sisa,
                'hrg_tbm'  => $barisbmf->hrg_bmf,
                'kd_lks'   => $lokasi,
                'user_id'  => $user_id,
                'jns_tbm'  => 1,
            ];
        }

        // Batch insert — single INSERT instead of N saves
        if (!empty($batchInsert)) {
            foreach (array_chunk($batchInsert, 500) as $chunk) {
                DB::table('temp_barang_masuk')->insert($chunk);
            }
        }

        function rupiah($angka){

            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;

        }

        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();
        PDF::SetFont('times', '', 8);
        $tgl = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tgl = strtoupper($tgl);

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN POSISI PERSEDIAN DI NERACA', 0, 1, 'C', 0, '', 0);
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
            join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
            ->where('v_lap_posisi4.user_id','=',$user_id)
            ->where('v_lap_posisi4.v_jns_tbm','=',1)
            ->get();
            foreach($datalap as $barislap)
            {
                $nilairp = rupiah($barislap->total_nilai);
                PDF::Cell(28, 0, "$barislap->kd_kt", 1, 0, 'C', 0, '', true);
                PDF::Cell(120, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
                PDF::Cell(40, 0, "$nilairp", 1, 1, 'R', 0, '', true);
                PDF::ln(0);
                $total_nilai = $total_nilai + $barislap->total_nilai;
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
        $tgl = ucwords(strtolower($tgl));
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
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

        PDF::Output('laporan_posisi_persedian_di_neraca.pdf');
    }
}
