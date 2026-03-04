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
    // =====================================================================
    // Helper: proses barang masuk rektorat — semua data dipreload dulu,
    // loop hanya kalkulasi di PHP (nol query di dalam loop)
    // =====================================================================
    private function prosesRektorat(
        $databarangmasukrektorat,
        string $tgl_akhir,
        string $lokasi,
        int $user_id,
        array &$rows
    ): void {
        if ($databarangmasukrektorat->isEmpty()) return;

        $idBmrList = $databarangmasukrektorat->pluck('id_bmr')->unique()->toArray();
        $kdBrgList = $databarangmasukrektorat->pluck('kd_brg')->unique()->values()->toArray();

        // Preload 1: latest opsik per kd_brg (1 query)
        $opsikMap = OpsikUrDetModel::join('opsik_rektorat', 'opsik_rektorat_detail.id_opur', '=', 'opsik_rektorat.id_opur')
            ->whereIn('kd_brg', $kdBrgList)
            ->where('tgl_opur', '<=', $tgl_akhir)
            ->where('status_opur', 1)
            ->orderBy('tgl_opur', 'desc')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        // Preload 2: BK headers per kd_brg (cek ada BK setelah opsik, 1 query)
        $bkHeadersMap = BarangKeluarRektoratModel::whereIn('kd_brg', $kdBrgList)
            ->select('kd_brg', 'tglambil_bkr')
            ->get()
            ->groupBy('kd_brg');

        // Preload 3: semua BK detail per id_bmr s.d. tgl_akhir (1 query)
        $bkDetailsMap = BarangKeluarRektoratDetailModel::
            join('barang_keluar_rektorat', 'barang_keluar_rektorat_detail.id_bkr', '=', 'barang_keluar_rektorat.id_bkr')
            ->whereIn('barang_keluar_rektorat_detail.id_bmr', $idBmrList)
            ->where('tglambil_bkr', '<=', $tgl_akhir)
            ->select('barang_keluar_rektorat_detail.id_bmr', 'barang_keluar_rektorat.tglambil_bkr', 'barang_keluar_rektorat_detail.jmlh_bkrd')
            ->get()
            ->groupBy('id_bmr');

        // Preload 4 & 5: opsik detail items (2 query)
        $opurdetIds     = $opsikMap->pluck('id_opurdet')->unique()->filter()->toArray();
        $vopfikMap      = collect();
        $opurdetItemMap = collect();
        if (!empty($opurdetIds)) {
            $vopfikMap = VOpfikRektoratDetailItemModel::
                join('barang_masuk_rektorat', 'v_opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                ->whereIn('v_opfik_rektorat_detail_item.id_bmr', $idBmrList)
                ->whereIn('id_opurdet', $opurdetIds)
                ->where('jmlh_opurdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmr . '_' . $r->id_opurdet);

            $opurdetItemMap = OpurdetitmModel::
                join('barang_masuk_rektorat', 'opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                ->whereIn('opfik_rektorat_detail_item.id_bmr', $idBmrList)
                ->whereIn('id_opurdet', $opurdetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmr . '_' . $r->id_opurdet);
        }

        // Loop – murni PHP, tidak ada query
        foreach ($databarangmasukrektorat as $barisbmr) {
            $opsik = $opsikMap->get($barisbmr->kd_brg);

            if ($opsik !== null) {
                $bkHeaders  = $bkHeadersMap->get($barisbmr->kd_brg, collect());
                $adaBK      = $bkHeaders->where('tglambil_bkr', '>', $opsik->tgl_opur)->isNotEmpty();
                $bkDetails  = $bkDetailsMap->get($barisbmr->id_bmr, collect());
                $tjmlh_bkrd = $bkDetails
                    ->where('tglambil_bkr', '>=', $opsik->tgl_opur)
                    ->where('tglambil_bkr', '<', $tgl_akhir)
                    ->sum('jmlh_bkrd');

                $key   = $barisbmr->id_bmr . '_' . $opsik->id_opurdet;
                $items = $adaBK ? $vopfikMap->get($key, collect()) : $opurdetItemMap->get($key, collect());

                foreach ($items as $item) {
                    $rows[] = [
                        'kd_brg'   => $barisbmr->kd_brg,
                        'sisa_tbm' => $item->jmlh_opurdetitm - $tjmlh_bkrd,
                        'hrg_tbm'  => $item->hrg_bmr,
                        'kd_lks'   => $lokasi,
                        'user_id'  => $user_id,
                        'jns_tbm'  => 1,
                    ];
                }
            } else {
                $tjmlh_bkrd = $bkDetailsMap->get($barisbmr->id_bmr, collect())->sum('jmlh_bkrd');
                $rows[] = [
                    'kd_brg'   => $barisbmr->kd_brg,
                    'sisa_tbm' => $barisbmr->jmlh_awal_bmr - $tjmlh_bkrd,
                    'hrg_tbm'  => $barisbmr->hrg_bmr,
                    'kd_lks'   => $lokasi,
                    'user_id'  => $user_id,
                    'jns_tbm'  => 1,
                ];
            }
        }
    }

    // =====================================================================
    // Helper: proses barang masuk rumah sakit — preload dulu, loop PHP saja
    // =====================================================================
    private function prosesRumahSakit(
        $databarangmasukrumahsakit,
        string $tgl_akhir,
        string $lokasi,
        int $user_id,
        array &$rows
    ): void {
        if ($databarangmasukrumahsakit->isEmpty()) return;

        $idBmrsList = $databarangmasukrumahsakit->pluck('id_bmrs')->unique()->toArray();
        $kdBrgList  = $databarangmasukrumahsakit->pluck('kd_brg')->unique()->values()->toArray();

        // Preload 1: latest opsik per kd_brg
        $opsikMap = OpsikUrsDetModel::join('opsik_rumah_sakit', 'opsik_rumah_sakit_detail.id_opurs', '=', 'opsik_rumah_sakit.id_opurs')
            ->whereIn('kd_brg', $kdBrgList)
            ->where('tgl_opurs', '<=', $tgl_akhir)
            ->where('status_opurs', 1)
            ->orderBy('tgl_opurs', 'desc')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        // Preload 2: BK headers per kd_brg
        $bkHeadersMap = BarangKeluarRumahSakitModel::whereIn('kd_brg', $kdBrgList)
            ->select('kd_brg', 'tglambil_bkrs')
            ->get()
            ->groupBy('kd_brg');

        // Preload 3: semua BK detail per id_bmrs s.d. tgl_akhir
        $bkDetailsMap = BarangKeluarRumahSakitDetailModel::
            join('barang_keluar_rumah_sakit', 'barang_keluar_rumah_sakit_detail.id_bkrs', '=', 'barang_keluar_rumah_sakit.id_bkrs')
            ->whereIn('barang_keluar_rumah_sakit_detail.id_bmrs', $idBmrsList)
            ->where('tglambil_bkrs', '<=', $tgl_akhir)
            ->select('barang_keluar_rumah_sakit_detail.id_bmrs', 'barang_keluar_rumah_sakit.tglambil_bkrs', 'barang_keluar_rumah_sakit_detail.jmlh_bkrsd')
            ->get()
            ->groupBy('id_bmrs');

        // Preload 4 & 5: opsik detail items
        $opursdetIds     = $opsikMap->pluck('id_opursdet')->unique()->filter()->toArray();
        $vopfikMap       = collect();
        $opursdetItemMap = collect();
        if (!empty($opursdetIds)) {
            $vopfikMap = VOpfikRumahSakitDetailItemModel::
                join('barang_masuk_rumah_sakit', 'v_opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                ->whereIn('v_opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList)
                ->whereIn('id_opursdet', $opursdetIds)
                ->where('jmlh_opursdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmrs . '_' . $r->id_opursdet);

            $opursdetItemMap = OpursdetitmModel::
                join('barang_masuk_rumah_sakit', 'opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $idBmrsList)
                ->whereIn('id_opursdet', $opursdetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmrs . '_' . $r->id_opursdet);
        }

        // Loop – murni PHP
        foreach ($databarangmasukrumahsakit as $barisbmrs) {
            $opsik = $opsikMap->get($barisbmrs->kd_brg);

            if ($opsik !== null) {
                $bkHeaders   = $bkHeadersMap->get($barisbmrs->kd_brg, collect());
                $adaBK       = $bkHeaders->where('tglambil_bkrs', '>', $opsik->tgl_opurs)->isNotEmpty();
                $bkDetails   = $bkDetailsMap->get($barisbmrs->id_bmrs, collect());
                $tjmlh_bkrsd = $bkDetails
                    ->where('tglambil_bkrs', '>=', $opsik->tgl_opurs)
                    ->where('tglambil_bkrs', '<', $tgl_akhir)
                    ->sum('jmlh_bkrsd');

                $key   = $barisbmrs->id_bmrs . '_' . $opsik->id_opursdet;
                $items = $adaBK ? $vopfikMap->get($key, collect()) : $opursdetItemMap->get($key, collect());

                foreach ($items as $item) {
                    $rows[] = [
                        'kd_brg'   => $barisbmrs->kd_brg,
                        'sisa_tbm' => $item->jmlh_opursdetitm - $tjmlh_bkrsd,
                        'hrg_tbm'  => $item->hrg_bmrs,
                        'kd_lks'   => $lokasi,
                        'user_id'  => $user_id,
                        'jns_tbm'  => 1,
                    ];
                }
            } else {
                $tjmlh_bkrsd = $bkDetailsMap->get($barisbmrs->id_bmrs, collect())->sum('jmlh_bkrsd');
                $rows[] = [
                    'kd_brg'   => $barisbmrs->kd_brg,
                    'sisa_tbm' => $barisbmrs->jmlh_awal_bmrs - $tjmlh_bkrsd,
                    'hrg_tbm'  => $barisbmrs->hrg_bmrs,
                    'kd_lks'   => $lokasi,
                    'user_id'  => $user_id,
                    'jns_tbm'  => 1,
                ];
            }
        }
    }

    // =====================================================================
    // Helper: proses barang masuk fakultas — preload dulu, loop PHP saja
    // =====================================================================
    private function prosesFakultas(
        $databarangmasukfakultas,
        string $tgl_akhir,
        string $lokasi,
        int $user_id,
        array &$rows,
        ?int $id_fk = null
    ): void {
        if ($databarangmasukfakultas->isEmpty()) return;

        $idBmfList = $databarangmasukfakultas->pluck('id_bmf')->unique()->toArray();
        $kdBrgList = $databarangmasukfakultas->pluck('kd_brg')->unique()->values()->toArray();

        // Preload 1: latest opsik per kd_brg
        $qOpsik = OpsikFkDetModel::join('opsik_fakultas', 'opsik_fakultas_detail.id_opfk', '=', 'opsik_fakultas.id_opfk')
            ->whereIn('kd_brg', $kdBrgList)
            ->where('tgl_opfk', '<=', $tgl_akhir)
            ->where('status_opfk', 1);
        if ($id_fk !== null) {
            $qOpsik->where('opsik_fakultas.id_fk', '=', $id_fk);
        }
        $opsikMap = $qOpsik->orderBy('tgl_opfk', 'desc')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        // Preload 2: BK headers per kd_brg
        $qBKH = BarangKeluarFakultasModel::whereIn('kd_brg', $kdBrgList)
            ->select('kd_brg', 'tglambil_bkf', 'id_fk');
        if ($id_fk !== null) {
            $qBKH->where('id_fk', '=', $id_fk);
        }
        $bkHeadersMap = $qBKH->get()->groupBy('kd_brg');

        // Preload 3: semua BK detail per id_bmf s.d. tgl_akhir
        $bkDetailsMap = BarangKeluarFakultasDetailModel::
            join('barang_keluar_fakultas', 'barang_keluar_fakultas_detail.id_bkf', '=', 'barang_keluar_fakultas.id_bkf')
            ->whereIn('barang_keluar_fakultas_detail.id_bmf', $idBmfList)
            ->where('tglambil_bkf', '<=', $tgl_akhir)
            ->select('barang_keluar_fakultas_detail.id_bmf', 'barang_keluar_fakultas.tglambil_bkf', 'barang_keluar_fakultas_detail.jmlh_bkfd')
            ->get()
            ->groupBy('id_bmf');

        // Preload 4 & 5: opsik detail items
        $opfkdetIds     = $opsikMap->pluck('id_opfkdet')->unique()->filter()->toArray();
        $vopfikMap      = collect();
        $opfkdetItemMap = collect();
        if (!empty($opfkdetIds)) {
            $vopfikMap = VOpfikFakultasDetailItemModel::
                join('barang_masuk_fakultas', 'v_opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                ->whereIn('v_opfik_fakultas_detail_item.id_bmf', $idBmfList)
                ->whereIn('id_opfkdet', $opfkdetIds)
                ->where('jmlh_opfkdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmf . '_' . $r->id_opfkdet);

            $opfkdetItemMap = OpfkdetitmModel::
                join('barang_masuk_fakultas', 'opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $idBmfList)
                ->whereIn('id_opfkdet', $opfkdetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmf . '_' . $r->id_opfkdet);
        }

        // Loop – murni PHP
        foreach ($databarangmasukfakultas as $barisbmf) {
            $opsik = $opsikMap->get($barisbmf->kd_brg);

            if ($opsik !== null) {
                $bkHeaders  = $bkHeadersMap->get($barisbmf->kd_brg, collect());
                $adaBK      = $bkHeaders->where('tglambil_bkf', '>', $opsik->tgl_opfk)->isNotEmpty();
                $bkDetails  = $bkDetailsMap->get($barisbmf->id_bmf, collect());
                $tjmlh_bkfd = $bkDetails
                    ->where('tglambil_bkf', '>=', $opsik->tgl_opfk)
                    ->where('tglambil_bkf', '<', $tgl_akhir)
                    ->sum('jmlh_bkfd');

                $key   = $barisbmf->id_bmf . '_' . $opsik->id_opfkdet;
                $items = $adaBK ? $vopfikMap->get($key, collect()) : $opfkdetItemMap->get($key, collect());

                foreach ($items as $item) {
                    $rows[] = [
                        'kd_brg'   => $barisbmf->kd_brg,
                        'sisa_tbm' => $item->jmlh_opfkdetitm - $tjmlh_bkfd,
                        'hrg_tbm'  => $item->hrg_bmf,
                        'kd_lks'   => $lokasi,
                        'user_id'  => $user_id,
                        'jns_tbm'  => 1,
                    ];
                }
            } else {
                $tjmlh_bkfd = $bkDetailsMap->get($barisbmf->id_bmf, collect())->sum('jmlh_bkfd');
                $rows[] = [
                    'kd_brg'   => $barisbmf->kd_brg,
                    'sisa_tbm' => $barisbmf->jmlh_awal_bmf - $tjmlh_bkfd,
                    'hrg_tbm'  => $barisbmf->hrg_bmf,
                    'kd_lks'   => $lokasi,
                    'user_id'  => $user_id,
                    'jns_tbm'  => 1,
                ];
            }
        }
    }

    // =====================================================================
    // Helper: bulk insert dengan chunk agar tidak timeout
    // =====================================================================
    private function bulkInsertTbm(array $rows): void
    {
        $now = now()->toDateTimeString();
        foreach (array_chunk($rows, 500) as $chunk) {
            $insert = array_map(function ($r) use ($now) {
                $r['created_at'] = $now;
                $r['updated_at'] = $now;
                return $r;
            }, $chunk);
            DB::table('temp_barang_masuk')->insert($insert);
        }
    }

    Public Function index($filter, $lokasi)
    {
        set_time_limit(300); // izinkan hingga 5 menit untuk dataset besar

        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        // Hapus data temp sebelumnya — langsung delete tanpa count dulu
        TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm', '=', '1')->delete();

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $rows = [];

        if ($lokasi == "690522009KD") {
            $databarangmasukrektorat = BarangMasukRektoratModel::
                where('kd_lks', '=', $lokasi)
                ->where('tglperolehan_bmr', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmr', 'asc')
                ->get();
            $this->prosesRektorat($databarangmasukrektorat, $tgl_akhir, $lokasi, $user_id, $rows);

        } elseif ($lokasi == "690522020KD") {
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
                where('kd_lks', '=', $lokasi)
                ->where('tglperolehan_bmrs', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmrs', 'asc')
                ->get();
            $this->prosesRumahSakit($databarangmasukrumahsakit, $tgl_akhir, $lokasi, $user_id, $rows);

        } elseif ($lokasi == "690522000KD") { // universitas
            // Rektorat
            $databarangmasukrektorat = BarangMasukRektoratModel::
                where('tglperolehan_bmr', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmr', 'asc')
                ->get();
            $this->prosesRektorat($databarangmasukrektorat, $tgl_akhir, $lokasi, $user_id, $rows);

            // Rumah Sakit
            $databarangmasukrumahsakit = BarangMasukRumahSakitModel::
                where('tglperolehan_bmrs', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmrs', 'asc')
                ->get();
            $this->prosesRumahSakit($databarangmasukrumahsakit, $tgl_akhir, $lokasi, $user_id, $rows);

            // Fakultas
            $databarangmasukfakultas = BarangMasukFakultasModel::
                where('tglperolehan_bmf', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmf', 'asc')
                ->get();
            $this->prosesFakultas($databarangmasukfakultas, $tgl_akhir, $lokasi, $user_id, $rows);

        } elseif ($lokasi == "") {
            // tidak ada proses

        } else {
            // Fakultas spesifik
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk = $datafakultas->id_fk;

            $databarangmasukfakultas = BarangMasukFakultasModel::
                where('kd_lks', '=', $lokasi)
                ->where('tglperolehan_bmf', '<=', $tgl_akhir)
                ->orderBy('tglperolehan_bmf', 'asc')
                ->get();
            $this->prosesFakultas($databarangmasukfakultas, $tgl_akhir, $lokasi, $user_id, $rows, $id_fk);
        }

        // Bulk insert semua data sekaligus
        if (!empty($rows)) {
            $this->bulkInsertTbm($rows);
        }

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
        //if($lokasi == "023170800677513009KD")
        //{
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
        //}
        //else if($lokasi == "023170800677513000KD")
        //{

        //}
        //else
        //{

        //}
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
}
