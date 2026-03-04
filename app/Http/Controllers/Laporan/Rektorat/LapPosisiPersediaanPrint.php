<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\JabpenuuModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikFkDetModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\OpursdetitmModel;
use App\Models\OpurdetitmModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikFakultasDetailItemModel;
use App\Models\VOpfikRektoratDetailItemModel;
use App\Models\VOpfikRumahSakitDetailItemModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class LapPosisiPersediaanPrint extends Controller
{
    Public Function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);        
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        // Hapus data temp sekali di awal
        DB::table('temp_barang_masuk')
            ->where('user_id', $user_id)
            ->where('jns_tbm', 1)
            ->delete();

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        // ====================================================================
        // BRANCH: REKTORAT (690522009KD)
        // ====================================================================
        if ($lokasi == "690522009KD")
        {
            $this->processRektorat($tgl_akhir, $lokasi, $user_id, $lokasi);
        }
        // ====================================================================
        // BRANCH: RUMAH SAKIT (690522020KD)
        // ====================================================================
        elseif ($lokasi == "690522020KD")
        {
            $this->processRumahSakit($tgl_akhir, $lokasi, $user_id, $lokasi);
        }
        // ====================================================================
        // BRANCH: UNIVERSITAS (690522000KD) = Rektorat + RS + semua Fakultas
        // ====================================================================
        else if ($lokasi == "690522000KD")
        {
            $this->processRektorat($tgl_akhir, $lokasi, $user_id, null);
            $this->processRumahSakit($tgl_akhir, $lokasi, $user_id, null);
            $this->processFakultas($tgl_akhir, $lokasi, $user_id, null, null);
        }
        // ====================================================================
        // BRANCH: Lokasi kosong — tidak ada yang diproses
        // ====================================================================
        else if ($lokasi == "")
        {
            // no-op
        }
        // ====================================================================
        // BRANCH: Per-Fakultas (lokasi = kd_lks fakultas tertentu)
        // ====================================================================
        else
        {
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk = $datafakultas->id_fk;
            $this->processFakultas($tgl_akhir, $lokasi, $user_id, $lokasi, $id_fk);
        }

        // ====================================================================
        // PDF Output
        // ====================================================================
        function rupiah($angka){
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();
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

        $total_nilai = 0;
        $datalap = VLapPosisi4Model::
        join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
        ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
        ->where('v_lap_posisi4.user_id','=',$user_id)
        ->where('v_lap_posisi4.v_jns_tbm','=',1)
        ->orderby('v_lap_posisi4.v_kd_kt')
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

        if($lokasi == "690522009KD")
        {
            $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 1)->first();
            $pejabatanop = JabpenurModel::join('jabatan_rektorat','jabatan_pengesahan_rektorat.id_jabur','=','jabatan_rektorat.id_jabur')->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
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
        }
        else if($lokasi == "690522020KD")
        {
            $datarumahsakit = UnitRumahSakitModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 1)->first();
            $pejabatanop = JabpenursModel::join('jabatan_rumah_sakit','jabatan_pengesahan_rumah_sakit.id_jaburs','=','jabatan_rumah_sakit.id_jaburs')->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
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
        }
        else if($lokasi == "690522000KD")
        {
            $pejabatanpimpinan = JabpenuuModel::join('jabatan_universitas','jabatan_pengesahan_universitas.id_jabuni','=','jabatan_universitas.id_jabuni')->where('jabatan_pengesahan_universitas.id_jabuni', 1)->first();
            $pejabatanop = JabpenuuModel::join('jabatan_universitas','jabatan_pengesahan_universitas.id_jabuni','=','jabatan_universitas.id_jabuni')->where('jabatan_pengesahan_universitas.id_jabuni', 2)->first();

            $tgl = ucwords(strtolower($tgl));
            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabuni", 0, 1, 'C', 0, '', true);
            PDF::ln(20);
            PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "$pejabatanop->nm_jabpenuni", 0, 1, 'C', 0, '', true);
            PDF::ln(0);
            PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenuni", 0, 0, 'C', 0, '', true);
            PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
            PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenuni", 0, 1, 'C', 0, '', true);
        }
        else
        {
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
            $pejabatanop = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();

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
        }

        PDF::Output('laporan_persedian.pdf');
    }

    // =========================================================================
    // HELPER: Proses barang_masuk_rektorat
    //   $kdLksFilter = string  → filter WHERE kd_lks = ? (branch Rektorat)
    //   $kdLksFilter = null    → tanpa filter kd_lks (branch Universitas)
    // =========================================================================
    private function processRektorat(string $tglAkhir, string $kd_lks_insert, int $userId, ?string $kdLksFilter): void
    {
        // 1. Ambil semua barang masuk rektorat
        $q = BarangMasukRektoratModel::where('tglperolehan_bmr', '<=', $tglAkhir)
            ->orderBy('tglperolehan_bmr', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', $kdLksFilter);
        }
        $rows = $q->get();
        if ($rows->isEmpty()) return;

        $allIdBmr  = $rows->pluck('id_bmr')->all();
        $allKdBrg  = $rows->pluck('kd_brg')->unique()->values()->all();

        // 2a. Cek kd_brg mana yang punya opsik valid (status=1)
        $kdBrgWithOpsik = DB::table('opsik_rektorat_detail as od')
            ->join('opsik_rektorat as o', 'od.id_opur', '=', 'o.id_opur')
            ->whereIn('od.kd_brg', $allKdBrg)
            ->where('o.tgl_opur', '<=', $tglAkhir)
            ->where('o.status_opur', 1)
            ->distinct()
            ->pluck('od.kd_brg')
            ->all();

        // 2b. Ambil opsik TERBARU per kd_brg TANPA filter status (sesuai kode asli)
        $opsikSnap = collect();
        if (!empty($kdBrgWithOpsik)) {
            $opsikSnap = DB::table('opsik_rektorat_detail as od')
                ->join('opsik_rektorat as o', 'od.id_opur', '=', 'o.id_opur')
                ->whereIn('od.kd_brg', $kdBrgWithOpsik)
                ->where('o.tgl_opur', '<=', $tglAkhir)
                ->orderBy('o.tgl_opur', 'desc')
                ->select('od.id_opurdet', 'od.kd_brg', 'o.tgl_opur')
                ->get()
                ->groupBy('kd_brg')
                ->map(fn($g) => $g->first());
        }

        // 3. Cek kd_brg yang ada barang keluar SETELAH tgl opsik
        //    Sesuai asli: cek count(bkr > tgl_opur), pakai barang_keluar_rektorat header
        $kdBrgWithKeluarAfterOpsik = [];
        if ($opsikSnap->isNotEmpty()) {
            $keluarMaxPerKdBrg = DB::table('barang_keluar_rektorat')
                ->whereIn('kd_brg', $opsikSnap->keys()->all())
                ->select('kd_brg', DB::raw('MAX(tglambil_bkr) as max_tgl'))
                ->groupBy('kd_brg')
                ->pluck('max_tgl', 'kd_brg');
            foreach ($opsikSnap as $kdBrg => $snap) {
                $max = $keluarMaxPerKdBrg[$kdBrg] ?? null;
                $kdBrgWithKeluarAfterOpsik[$kdBrg] = ($max !== null && $max > $snap->tgl_opur);
            }
        }

        // 4. Pre-fetch detail item opsik dari VIEW (untuk yg ada keluar setelah opsik)
        //    Sesuai asli: WHERE jmlh_opurdetitm > 0
        $allOpurDetIds = $opsikSnap->pluck('id_opurdet')->filter()->all();
        $viewItems = collect();
        if (!empty($allOpurDetIds)) {
            $viewItems = DB::table('v_opfik_rektorat_detail_item as vi')
                ->join('barang_masuk_rektorat as bmr', 'vi.id_bmr', '=', 'bmr.id_bmr')
                ->whereIn('vi.id_bmr', $allIdBmr)
                ->whereIn('vi.id_opurdet', $allOpurDetIds)
                ->where('vi.jmlh_opurdetitm', '>', 0)
                ->select('vi.id_bmr', 'vi.id_opurdet', 'vi.jmlh_opurdetitm', 'bmr.hrg_bmr')
                ->get()
                ->groupBy('id_bmr');
        }

        // 5. Pre-fetch detail item opsik dari TABEL (untuk yg tidak ada keluar setelah opsik)
        $tableItems = collect();
        if (!empty($allOpurDetIds)) {
            $tableItems = DB::table('opfik_rektorat_detail_item as oi')
                ->join('barang_masuk_rektorat as bmr', 'oi.id_bmr', '=', 'bmr.id_bmr')
                ->whereIn('oi.id_bmr', $allIdBmr)
                ->whereIn('oi.id_opurdet', $allOpurDetIds)
                ->select('oi.id_bmr', 'oi.id_opurdet', 'oi.jmlh_opurdetitm', 'bmr.hrg_bmr')
                ->get()
                ->groupBy('id_bmr');
        }

        // 6. Pre-fetch semua barang keluar detail yang relevan untuk case OPSIK
        //    Sesuai asli: whereBetween [tgl_opur, tgl_akhir], eksklusif tgl_akhir (cek != tgl_akhir di loop)
        //    Kita ambil semua < tgl_akhir dulu, filter >= tgl_opur di PHP
        $keluarRowsForOpsik = DB::table('barang_keluar_rektorat_detail as d')
            ->join('barang_keluar_rektorat as h', 'd.id_bkr', '=', 'h.id_bkr')
            ->whereIn('d.id_bmr', $allIdBmr)
            ->where('h.tglambil_bkr', '<', $tglAkhir)   // eksklusif tgl_akhir (sesuai != tgl_akhir asli)
            ->select('d.id_bmr', 'd.jmlh_bkrd', 'h.tglambil_bkr')
            ->get()
            ->groupBy('id_bmr');

        // 7. Pre-fetch SUM keluar s.d. tgl_akhir (inklusif) untuk case TANPA opsik
        $keluarSumNoOpsik = DB::table('barang_keluar_rektorat_detail as d')
            ->join('barang_keluar_rektorat as h', 'd.id_bkr', '=', 'h.id_bkr')
            ->whereIn('d.id_bmr', $allIdBmr)
            ->where('h.tglambil_bkr', '<=', $tglAkhir)
            ->selectRaw('d.id_bmr, COALESCE(SUM(d.jmlh_bkrd), 0) as total')
            ->groupBy('d.id_bmr')
            ->pluck('total', 'id_bmr');

        // 8. Bangun batch insert
        $batch = [];
        $now   = now();

        foreach ($rows as $row) {
            $snap = $opsikSnap[$row->kd_brg] ?? null;

            if ($snap !== null) {
                // Ada opsik — pilih view atau tabel sesuai ada/tidaknya keluar setelah opsik
                $adaKeluar      = $kdBrgWithKeluarAfterOpsik[$row->kd_brg] ?? false;
                $itemCollection = $adaKeluar
                    ? ($viewItems[$row->id_bmr] ?? collect())
                    : ($tableItems[$row->id_bmr] ?? collect());

                foreach ($itemCollection as $item) {
                    if ($item->id_opurdet != $snap->id_opurdet) continue;

                    // SUM keluar antara tgl_opur dan tgl_akhir (eksklusif tgl_akhir)
                    $keluarRows = $keluarRowsForOpsik[$row->id_bmr] ?? collect();
                    $sumKeluar  = $keluarRows
                        ->where('tglambil_bkr', '>=', $snap->tgl_opur)
                        ->sum('jmlh_bkrd');

                    $batch[] = [
                        'kd_brg'     => $row->kd_brg,
                        'sisa_tbm'   => $item->jmlh_opurdetitm - $sumKeluar,
                        'hrg_tbm'    => $item->hrg_bmr,
                        'kd_lks'     => $kd_lks_insert,
                        'user_id'    => $userId,
                        'jns_tbm'    => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                // Tidak ada opsik — jmlh_awal - semua keluar s.d. tgl_akhir
                $sumKeluar = $keluarSumNoOpsik[$row->id_bmr] ?? 0;
                $batch[] = [
                    'kd_brg'     => $row->kd_brg,
                    'sisa_tbm'   => $row->jmlh_awal_bmr - $sumKeluar,
                    'hrg_tbm'    => $row->hrg_bmr,
                    'kd_lks'     => $kd_lks_insert,
                    'user_id'    => $userId,
                    'jns_tbm'    => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }

    // =========================================================================
    // HELPER: Proses barang_masuk_rumah_sakit
    //   $kdLksFilter = string → filter WHERE kd_lks = ? (branch RS)
    //   $kdLksFilter = null   → tanpa filter kd_lks (branch Universitas)
    // =========================================================================
    private function processRumahSakit(string $tglAkhir, string $kd_lks_insert, int $userId, ?string $kdLksFilter): void
    {
        $q = BarangMasukRumahSakitModel::where('tglperolehan_bmrs', '<=', $tglAkhir)
            ->orderBy('tglperolehan_bmrs', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', $kdLksFilter);
        }
        $rows = $q->get();
        if ($rows->isEmpty()) return;

        $allIdBmrs = $rows->pluck('id_bmrs')->all();
        $allKdBrg  = $rows->pluck('kd_brg')->unique()->values()->all();

        // Opsik: cek existence (status=1), lalu ambil terbaru TANPA filter status
        $kdBrgWithOpsik = DB::table('opsik_rumah_sakit_detail as od')
            ->join('opsik_rumah_sakit as o', 'od.id_opurs', '=', 'o.id_opurs')
            ->whereIn('od.kd_brg', $allKdBrg)
            ->where('o.tgl_opurs', '<=', $tglAkhir)
            ->where('o.status_opurs', 1)
            ->distinct()
            ->pluck('od.kd_brg')
            ->all();

        $opsikSnap = collect();
        if (!empty($kdBrgWithOpsik)) {
            $opsikSnap = DB::table('opsik_rumah_sakit_detail as od')
                ->join('opsik_rumah_sakit as o', 'od.id_opurs', '=', 'o.id_opurs')
                ->whereIn('od.kd_brg', $kdBrgWithOpsik)
                ->where('o.tgl_opurs', '<=', $tglAkhir)
                ->orderBy('o.tgl_opurs', 'desc')
                ->select('od.id_opursdet', 'od.kd_brg', 'o.tgl_opurs')
                ->get()
                ->groupBy('kd_brg')
                ->map(fn($g) => $g->first());
        }

        $kdBrgWithKeluarAfterOpsik = [];
        if ($opsikSnap->isNotEmpty()) {
            $keluarMaxPerKdBrg = DB::table('barang_keluar_rumah_sakit')
                ->whereIn('kd_brg', $opsikSnap->keys()->all())
                ->select('kd_brg', DB::raw('MAX(tglambil_bkrs) as max_tgl'))
                ->groupBy('kd_brg')
                ->pluck('max_tgl', 'kd_brg');
            foreach ($opsikSnap as $kdBrg => $snap) {
                $max = $keluarMaxPerKdBrg[$kdBrg] ?? null;
                $kdBrgWithKeluarAfterOpsik[$kdBrg] = ($max !== null && $max > $snap->tgl_opurs);
            }
        }

        $allOpursDetIds = $opsikSnap->pluck('id_opursdet')->filter()->all();

        $viewItems = collect();
        if (!empty($allOpursDetIds)) {
            $viewItems = DB::table('v_opfik_rumah_sakit_detail_item as vi')
                ->join('barang_masuk_rumah_sakit as bmrs', 'vi.id_bmrs', '=', 'bmrs.id_bmrs')
                ->whereIn('vi.id_bmrs', $allIdBmrs)
                ->whereIn('vi.id_opursdet', $allOpursDetIds)
                ->where('vi.jmlh_opursdetitm', '>', 0)
                ->select('vi.id_bmrs', 'vi.id_opursdet', 'vi.jmlh_opursdetitm', 'bmrs.hrg_bmrs')
                ->get()
                ->groupBy('id_bmrs');
        }

        $tableItems = collect();
        if (!empty($allOpursDetIds)) {
            $tableItems = DB::table('opfik_rumah_sakit_detail_item as oi')
                ->join('barang_masuk_rumah_sakit as bmrs', 'oi.id_bmrs', '=', 'bmrs.id_bmrs')
                ->whereIn('oi.id_bmrs', $allIdBmrs)
                ->whereIn('oi.id_opursdet', $allOpursDetIds)
                ->select('oi.id_bmrs', 'oi.id_opursdet', 'oi.jmlh_opursdetitm', 'bmrs.hrg_bmrs')
                ->get()
                ->groupBy('id_bmrs');
        }

        $keluarRowsForOpsik = DB::table('barang_keluar_rumah_sakit_detail as d')
            ->join('barang_keluar_rumah_sakit as h', 'd.id_bkrs', '=', 'h.id_bkrs')
            ->whereIn('d.id_bmrs', $allIdBmrs)
            ->where('h.tglambil_bkrs', '<', $tglAkhir)
            ->select('d.id_bmrs', 'd.jmlh_bkrsd', 'h.tglambil_bkrs')
            ->get()
            ->groupBy('id_bmrs');

        $keluarSumNoOpsik = DB::table('barang_keluar_rumah_sakit_detail as d')
            ->join('barang_keluar_rumah_sakit as h', 'd.id_bkrs', '=', 'h.id_bkrs')
            ->whereIn('d.id_bmrs', $allIdBmrs)
            ->where('h.tglambil_bkrs', '<=', $tglAkhir)
            ->selectRaw('d.id_bmrs, COALESCE(SUM(d.jmlh_bkrsd), 0) as total')
            ->groupBy('d.id_bmrs')
            ->pluck('total', 'id_bmrs');

        $batch = [];
        $now   = now();

        foreach ($rows as $row) {
            $snap = $opsikSnap[$row->kd_brg] ?? null;

            if ($snap !== null) {
                $adaKeluar      = $kdBrgWithKeluarAfterOpsik[$row->kd_brg] ?? false;
                $itemCollection = $adaKeluar
                    ? ($viewItems[$row->id_bmrs] ?? collect())
                    : ($tableItems[$row->id_bmrs] ?? collect());

                foreach ($itemCollection as $item) {
                    if ($item->id_opursdet != $snap->id_opursdet) continue;

                    $keluarRows = $keluarRowsForOpsik[$row->id_bmrs] ?? collect();
                    $sumKeluar  = $keluarRows
                        ->where('tglambil_bkrs', '>=', $snap->tgl_opurs)
                        ->sum('jmlh_bkrsd');

                    $batch[] = [
                        'kd_brg'     => $row->kd_brg,
                        'sisa_tbm'   => $item->jmlh_opursdetitm - $sumKeluar,
                        'hrg_tbm'    => $item->hrg_bmrs,
                        'kd_lks'     => $kd_lks_insert,
                        'user_id'    => $userId,
                        'jns_tbm'    => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                $sumKeluar = $keluarSumNoOpsik[$row->id_bmrs] ?? 0;
                $batch[] = [
                    'kd_brg'     => $row->kd_brg,
                    'sisa_tbm'   => $row->jmlh_awal_bmrs - $sumKeluar,
                    'hrg_tbm'    => $row->hrg_bmrs,
                    'kd_lks'     => $kd_lks_insert,
                    'user_id'    => $userId,
                    'jns_tbm'    => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }

    // =========================================================================
    // HELPER: Proses barang_masuk_fakultas
    //   $kdLksFilter = string → filter WHERE kd_lks = ? (branch per-Fakultas)
    //   $kdLksFilter = null   → tanpa filter kd_lks (branch Universitas)
    //   $idFkFilter  = int    → filter opsik/keluar by id_fk (branch per-Fakultas)
    //   $idFkFilter  = null   → tanpa filter id_fk (branch Universitas)
    // =========================================================================
    private function processFakultas(string $tglAkhir, string $kd_lks_insert, int $userId, ?string $kdLksFilter, ?int $idFkFilter): void
    {
        $q = BarangMasukFakultasModel::where('tglperolehan_bmf', '<=', $tglAkhir)
            ->orderBy('tglperolehan_bmf', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', $kdLksFilter);
        }
        $rows = $q->get();
        if ($rows->isEmpty()) return;

        $allIdBmf = $rows->pluck('id_bmf')->all();
        $allKdBrg = $rows->pluck('kd_brg')->unique()->values()->all();

        // Opsik: cek existence (status=1), lalu ambil terbaru TANPA filter status
        $existQ = DB::table('opsik_fakultas_detail as od')
            ->join('opsik_fakultas as o', 'od.id_opfk', '=', 'o.id_opfk')
            ->whereIn('od.kd_brg', $allKdBrg)
            ->where('o.tgl_opfk', '<=', $tglAkhir)
            ->where('o.status_opfk', 1);
        if ($idFkFilter !== null) {
            $existQ->where('o.id_fk', $idFkFilter);
        }
        $kdBrgWithOpsik = $existQ->distinct()->pluck('od.kd_brg')->all();

        $opsikSnap = collect();
        if (!empty($kdBrgWithOpsik)) {
            $snapQ = DB::table('opsik_fakultas_detail as od')
                ->join('opsik_fakultas as o', 'od.id_opfk', '=', 'o.id_opfk')
                ->whereIn('od.kd_brg', $kdBrgWithOpsik)
                ->where('o.tgl_opfk', '<=', $tglAkhir);
            if ($idFkFilter !== null) {
                $snapQ->where('o.id_fk', $idFkFilter);
            }
            $opsikSnap = $snapQ
                ->orderBy('o.tgl_opfk', 'desc')
                ->select('od.id_opfkdet', 'od.kd_brg', 'o.tgl_opfk')
                ->get()
                ->groupBy('kd_brg')
                ->map(fn($g) => $g->first());
        }

        // Cek keluar setelah opsik (dengan filter id_fk jika per-Fakultas)
        $kdBrgWithKeluarAfterOpsik = [];
        if ($opsikSnap->isNotEmpty()) {
            $bkfQ = DB::table('barang_keluar_fakultas')
                ->whereIn('kd_brg', $opsikSnap->keys()->all());
            if ($idFkFilter !== null) {
                $bkfQ->where('id_fk', $idFkFilter);
            }
            $keluarMaxPerKdBrg = $bkfQ
                ->select('kd_brg', DB::raw('MAX(tglambil_bkf) as max_tgl'))
                ->groupBy('kd_brg')
                ->pluck('max_tgl', 'kd_brg');
            foreach ($opsikSnap as $kdBrg => $snap) {
                $max = $keluarMaxPerKdBrg[$kdBrg] ?? null;
                $kdBrgWithKeluarAfterOpsik[$kdBrg] = ($max !== null && $max > $snap->tgl_opfk);
            }
        }

        $allOpfkDetIds = $opsikSnap->pluck('id_opfkdet')->filter()->all();

        $viewItems = collect();
        if (!empty($allOpfkDetIds)) {
            $viewItems = DB::table('v_opfik_fakultas_detail_item as vi')
                ->join('barang_masuk_fakultas as bmf', 'vi.id_bmf', '=', 'bmf.id_bmf')
                ->whereIn('vi.id_bmf', $allIdBmf)
                ->whereIn('vi.id_opfkdet', $allOpfkDetIds)
                ->where('vi.jmlh_opfkdetitm', '>', 0)
                ->select('vi.id_bmf', 'vi.id_opfkdet', 'vi.jmlh_opfkdetitm', 'bmf.hrg_bmf')
                ->get()
                ->groupBy('id_bmf');
        }

        $tableItems = collect();
        if (!empty($allOpfkDetIds)) {
            $tableItems = DB::table('opfik_fakultas_detail_item as oi')
                ->join('barang_masuk_fakultas as bmf', 'oi.id_bmf', '=', 'bmf.id_bmf')
                ->whereIn('oi.id_bmf', $allIdBmf)
                ->whereIn('oi.id_opfkdet', $allOpfkDetIds)
                ->select('oi.id_bmf', 'oi.id_opfkdet', 'oi.jmlh_opfkdetitm', 'bmf.hrg_bmf')
                ->get()
                ->groupBy('id_bmf');
        }

        $keluarRowsForOpsik = DB::table('barang_keluar_fakultas_detail as d')
            ->join('barang_keluar_fakultas as h', 'd.id_bkf', '=', 'h.id_bkf')
            ->whereIn('d.id_bmf', $allIdBmf)
            ->where('h.tglambil_bkf', '<', $tglAkhir)
            ->select('d.id_bmf', 'd.jmlh_bkfd', 'h.tglambil_bkf')
            ->get()
            ->groupBy('id_bmf');

        $keluarSumNoOpsik = DB::table('barang_keluar_fakultas_detail as d')
            ->join('barang_keluar_fakultas as h', 'd.id_bkf', '=', 'h.id_bkf')
            ->whereIn('d.id_bmf', $allIdBmf)
            ->where('h.tglambil_bkf', '<=', $tglAkhir)
            ->selectRaw('d.id_bmf, COALESCE(SUM(d.jmlh_bkfd), 0) as total')
            ->groupBy('d.id_bmf')
            ->pluck('total', 'id_bmf');

        $batch = [];
        $now   = now();

        foreach ($rows as $row) {
            $snap = $opsikSnap[$row->kd_brg] ?? null;

            if ($snap !== null) {
                $adaKeluar      = $kdBrgWithKeluarAfterOpsik[$row->kd_brg] ?? false;
                $itemCollection = $adaKeluar
                    ? ($viewItems[$row->id_bmf] ?? collect())
                    : ($tableItems[$row->id_bmf] ?? collect());

                foreach ($itemCollection as $item) {
                    if ($item->id_opfkdet != $snap->id_opfkdet) continue;

                    $keluarRows = $keluarRowsForOpsik[$row->id_bmf] ?? collect();
                    $sumKeluar  = $keluarRows
                        ->where('tglambil_bkf', '>=', $snap->tgl_opfk)
                        ->sum('jmlh_bkfd');

                    $batch[] = [
                        'kd_brg'     => $row->kd_brg,
                        'sisa_tbm'   => $item->jmlh_opfkdetitm - $sumKeluar,
                        'hrg_tbm'    => $item->hrg_bmf,
                        'kd_lks'     => $kd_lks_insert,
                        'user_id'    => $userId,
                        'jns_tbm'    => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                $sumKeluar = $keluarSumNoOpsik[$row->id_bmf] ?? 0;
                $batch[] = [
                    'kd_brg'     => $row->kd_brg,
                    'sisa_tbm'   => $row->jmlh_awal_bmf - $sumKeluar,
                    'hrg_tbm'    => $row->hrg_bmf,
                    'kd_lks'     => $kd_lks_insert,
                    'user_id'    => $userId,
                    'jns_tbm'    => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }
}