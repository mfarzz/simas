<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenuuModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\LokasiModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\VLapPosisi4Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class LapPosisiPersediaanPrint extends Controller
{
    public function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi    = Crypt::decryptString($lokasi);
        $user_id   = auth()->user()->id;

        // 1) delete temp SEKALI tanpa count
        TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm', 1)->delete();

        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        // 2) proses pengisian temp dengan query massal (minim query)
        if ($lokasi === "690522009KD") {
            $this->processRektorat($tgl_akhir, $lokasi, $user_id, true);
        } elseif ($lokasi === "690522020KD") {
            $this->processRumahSakit($tgl_akhir, $lokasi, $user_id, true);
        } elseif ($lokasi === "690522000KD") {
            // universitas: gabungan semua unit (tanpa filter kd_lks pada tabel tertentu)
            $this->processRektorat($tgl_akhir, null, $user_id, false);
            $this->processRumahSakit($tgl_akhir, null, $user_id, false);
            $this->processFakultas($tgl_akhir, null, $user_id, false, null);
        } elseif ($lokasi === "" || $lokasi === null) {
            // no-op
        } else {
            // fakultas spesifik
            $datafak = FakultasModel::where('kd_lks', $lokasi)->first();
            $id_fk   = $dataf ? $dataf->id_fk : null;
            $this->processFakultas($tgl_akhir, $lokasi, $user_id, true, $id_fk);
        }

        // 3) generate PDF (bagian ini biasanya bukan bottleneck)
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();

        $tglCetak = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tglCetak = strtoupper($tglCetak);

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN POSISI PERSEDIAN DI NERACA', 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, "UNTUK PERIODE YANG BERAKHIR TANGGAL $tglCetak", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "TAHUN ANGGARAN $tahunanggaran", 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', '', 10);

        PDF::ln(5);
        PDF::Cell(28, 0, "UAPKB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, $datalokasi?->nm_lks ?? '-', 0, 1, 'L', 0, '', true);

        PDF::Cell(28, 0, "Kode UAPKPB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, $lokasi, 0, 1, 'L', 0, '', true);

        PDF::SetFont('times', 'b', 10);
        PDF::ln(5);
        PDF::Cell(28, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "URAIAN", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::SetFont('times', '', 10);

        $total_nilai = 0;
        $datalap = VLapPosisi4Model::join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks', $lokasi)
            ->where('v_lap_posisi4.user_id', $user_id)
            ->where('v_lap_posisi4.v_jns_tbm', 1)
            ->orderBy('v_lap_posisi4.v_kd_kt')
            ->get();

        foreach ($datalap as $barislap) {
            $nilairp = number_format($barislap->total_nilai, 0, ',', '.');
            PDF::Cell(28, 0, $barislap->kd_kt, 1, 0, 'C', 0, '', true);
            PDF::Cell(120, 0, $barislap->nm_kt, 1, 0, 'L', 0, '', true);
            PDF::Cell(40, 0, $nilairp, 1, 1, 'R', 0, '', true);
            $total_nilai += $barislap->total_nilai;
        }

        PDF::SetFont('times', 'b', 10);
        PDF::Cell(28, 0, "", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "Jumlah", 1, 0, 'R', 0, '', true);
        PDF::Cell(40, 0, number_format($total_nilai, 0, ',', '.'), 1, 1, 'R', 0, '', true);

        // tanda tangan (tetap pakai kode kamu; aku biarkan ringkas)
        $this->renderTtd($lokasi, $tglCetak);

        PDF::Output('laporan_persedian.pdf');
    }

    /**
     * Rektorat (barang_masuk_rektorat + opsik_rektorat + barang_keluar_rektorat)
     * $filterLokasi = true berarti pakai kd_lks.
     */
    private function processRektorat(string $tgl_akhir, ?string $kd_lks, int $user_id, bool $filterLokasi): void
    {
        // Ambil BMR minimal kolom (chunk kalau besar)
        $bmrQuery = DB::table('barang_masuk_rektorat')
            ->select('id_bmr','kd_brg','jmlh_awal_bmr','hrg_bmr','kd_lks')
            ->where('tglperolehan_bmr', '<=', $tgl_akhir);

        if ($filterLokasi && $kd_lks) {
            $bmrQuery->where('kd_lks', $kd_lks);
        }

        $bmrRows = $bmrQuery->orderBy('id_bmr')->get();
        if ($bmrRows->isEmpty()) return;

        $bmrIds = $bmrRows->pluck('id_bmr')->all();
        $kdBrgList = $bmrRows->pluck('kd_brg')->unique()->values()->all();

        // 1) opsik terakhir per kd_brg (1x query)
        $opsikLatest = DB::table('opsik_rektorat_detail as d')
            ->join('opsik_rektorat as h','d.id_opur','=','h.id_opur')
            ->select('d.kd_brg', DB::raw('MAX(h.tgl_opur) as tgl_opur'))
            ->whereIn('d.kd_brg', $kdBrgList)
            ->where('h.tgl_opur', '<=', $tgl_akhir)
            ->where('h.status_opur', 1)
            ->groupBy('d.kd_brg')
            ->get()
            ->keyBy('kd_brg');

        // 2) ambil id_opurdet yang sesuai tgl terbaru (1x query)
        $opsikDetRows = DB::table('opsik_rektorat_detail as d')
            ->join('opsik_rektorat as h','d.id_opur','=','h.id_opur')
            ->select('d.kd_brg','d.id_opurdet','h.tgl_opur')
            ->whereIn('d.kd_brg', $kdBrgList)
            ->where('h.tgl_opur', '<=', $tgl_akhir)
            ->where('h.status_opur', 1)
            ->orderBy('d.id_opurdet','desc')
            ->get();

        $opsikDetByKdBrg = [];
        foreach ($opsikDetRows as $r) {
            if (!isset($opsikLatest[$r->kd_brg])) continue;
            $latestDate = $opsikLatest[$r->kd_brg]->tgl_opur;
            if ($r->tgl_opur === $latestDate && !isset($opsikDetByKdBrg[$r->kd_brg])) {
                $opsikDetByKdBrg[$r->kd_brg] = (object)[
                    'id_opurdet' => $r->id_opurdet,
                    'tgl_opur'   => $r->tgl_opur,
                ];
            }
        }

        $opsikDetIds = array_values(array_unique(array_map(fn($x) => $x->id_opurdet, $opsikDetByKdBrg)));
        // 3) ambil item opsik (view) untuk semua bmr yang relevan (1x query)
        $opsikItems = collect();
        if (!empty($opsikDetIds)) {
            $opsikItems = DB::table('v_opfik_rektorat_detail_item')
                ->select('id_bmr','id_opurdet','jmlh_opurdetitm','hrg_bmr')
                ->whereIn('id_bmr', $bmrIds)
                ->whereIn('id_opurdet', $opsikDetIds)
                ->where('jmlh_opurdetitm','>',0)
                ->get();
        }

        // 4) ambil semua BK detail per id_bmr per tanggal (1x query)
        // exclude transaksi pada tgl_akhir => pakai < tgl_akhir
        $bkAgg = DB::table('barang_keluar_rektorat_detail as d')
            ->join('barang_keluar_rektorat as h','d.id_bkr','=','h.id_bkr')
            ->select('d.id_bmr','h.tglambil_bkr', DB::raw('SUM(d.jmlh_bkrd) as qty'))
            ->whereIn('d.id_bmr', $bmrIds)
            ->where('h.tglambil_bkr','<', $tgl_akhir)
            ->groupBy('d.id_bmr','h.tglambil_bkr')
            ->get();

        $bkByBmr = [];
        foreach ($bkAgg as $row) {
            $bkByBmr[$row->id_bmr][] = ['tgl' => $row->tglambil_bkr, 'qty' => (float)$row->qty];
        }

        // 5) hitung sisa + bulk insert ke temp
        $tempRows = [];
        foreach ($bmrRows as $bmr) {
            $kd = $bmr->kd_brg;

            $startOpsik = null;
            $opsikDetId = null;
            if (isset($opsikDetByKdBrg[$kd])) {
                $startOpsik = $opsikDetByKdBrg[$kd]->tgl_opur;
                $opsikDetId = $opsikDetByKdBrg[$kd]->id_opurdet;
            }

            // total keluar (sebelum tgl_akhir), tapi kalau ada opsik maka mulai dari tgl opsik
            $qtyKeluar = 0;
            if (!empty($bkByBmr[$bmr->id_bmr])) {
                foreach ($bkByBmr[$bmr->id_bmr] as $k) {
                    if ($startOpsik === null) {
                        $qtyKeluar += $k['qty'];
                    } else {
                        if ($k['tgl'] >= $startOpsik) $qtyKeluar += $k['qty'];
                    }
                }
            }

            // jika ada opsik item -> pakai qty opsik item - qtyKeluar, bisa multiple item
            $hasOpsikItem = false;
            if ($opsikDetId !== null) {
                foreach ($opsikItems as $it) {
                    if ((int)$it->id_bmr === (int)$bmr->id_bmr && (int)$it->id_opurdet === (int)$opsikDetId) {
                        $hasOpsikItem = true;
                        $sisa = (float)$it->jmlh_opurdetitm - $qtyKeluar;
                        if ($sisa < 0) $sisa = 0;

                        $tempRows[] = [
                            'kd_brg'  => $kd,
                            'sisa_tbm'=> $sisa,
                            'hrg_tbm' => (float)$it->hrg_bmr,
                            'kd_lks'  => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                            'user_id' => $user_id,
                            'jns_tbm' => 1,
                        ];
                    }
                }
            }

            // fallback jika tidak ada opsik item: pakai jmlh awal bmr - qtyKeluar
            if (!$hasOpsikItem) {
                $sisa = (float)$bmr->jmlh_awal_bmr - $qtyKeluar;
                if ($sisa < 0) $sisa = 0;

                $tempRows[] = [
                    'kd_brg'  => $kd,
                    'sisa_tbm'=> $sisa,
                    'hrg_tbm' => (float)$bmr->hrg_bmr,
                    'kd_lks'  => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                    'user_id' => $user_id,
                    'jns_tbm' => 1,
                ];
            }
        }

        if (!empty($tempRows)) {
            foreach (array_chunk($tempRows, 1000) as $chunk) {
                DB::table('temp_barang_masuk')->insert($chunk);
            }
        }
    }

    /**
     * Rumah sakit (barang_masuk_rumah_sakit + opsik_rumah_sakit + barang_keluar_rumah_sakit)
     */
    private function processRumahSakit(string $tgl_akhir, ?string $kd_lks, int $user_id, bool $filterLokasi): void
    {
        $bmrQuery = DB::table('barang_masuk_rumah_sakit')
            ->select('id_bmrs','kd_brg','jmlh_awal_bmrs','hrg_bmrs','kd_lks')
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir);

        if ($filterLokasi && $kd_lks) {
            $bmrQuery->where('kd_lks', $kd_lks);
        }

        $bmrRows = $bmrQuery->orderBy('id_bmrs')->get();
        if ($bmrRows->isEmpty()) return;

        $bmrIds = $bmrRows->pluck('id_bmrs')->all();
        $kdBrgList = $bmrRows->pluck('kd_brg')->unique()->values()->all();

        $opsikLatest = DB::table('opsik_rumah_sakit_detail as d')
            ->join('opsik_rumah_sakit as h','d.id_opurs','=','h.id_opurs')
            ->select('d.kd_brg', DB::raw('MAX(h.tgl_opurs) as tgl_opurs'))
            ->whereIn('d.kd_brg', $kdBrgList)
            ->where('h.tgl_opurs', '<=', $tgl_akhir)
            ->where('h.status_opurs', 1)
            ->groupBy('d.kd_brg')
            ->get()
            ->keyBy('kd_brg');

        $opsikDetRows = DB::table('opsik_rumah_sakit_detail as d')
            ->join('opsik_rumah_sakit as h','d.id_opurs','=','h.id_opurs')
            ->select('d.kd_brg','d.id_opursdet','h.tgl_opurs')
            ->whereIn('d.kd_brg', $kdBrgList)
            ->where('h.tgl_opurs', '<=', $tgl_akhir)
            ->where('h.status_opurs', 1)
            ->orderBy('d.id_opursdet','desc')
            ->get();

        $opsikDetByKdBrg = [];
        foreach ($opsikDetRows as $r) {
            if (!isset($opsikLatest[$r->kd_brg])) continue;
            $latestDate = $opsikLatest[$r->kd_brg]->tgl_opurs;
            if ($r->tgl_opurs === $latestDate && !isset($opsikDetByKdBrg[$r->kd_brg])) {
                $opsikDetByKdBrg[$r->kd_brg] = (object)[
                    'id_opursdet' => $r->id_opursdet,
                    'tgl_opurs'   => $r->tgl_opurs,
                ];
            }
        }

        $opsikDetIds = array_values(array_unique(array_map(fn($x) => $x->id_opursdet, $opsikDetByKdBrg)));

        $opsikItems = collect();
        if (!empty($opsikDetIds)) {
            $opsikItems = DB::table('v_opfik_rumah_sakit_detail_item')
                ->select('id_bmrs','id_opursdet','jmlh_opursdetitm','hrg_bmrs')
                ->whereIn('id_bmrs', $bmrIds)
                ->whereIn('id_opursdet', $opsikDetIds)
                ->where('jmlh_opursdetitm','>',0)
                ->get();
        }

        $bkAgg = DB::table('barang_keluar_rumah_sakit_detail as d')
            ->join('barang_keluar_rumah_sakit as h','d.id_bkrs','=','h.id_bkrs')
            ->select('d.id_bmrs','h.tglambil_bkrs', DB::raw('SUM(d.jmlh_bkrsd) as qty'))
            ->whereIn('d.id_bmrs', $bmrIds)
            ->where('h.tglambil_bkrs','<', $tgl_akhir)
            ->groupBy('d.id_bmrs','h.tglambil_bkrs')
            ->get();

        $bkByBmr = [];
        foreach ($bkAgg as $row) {
            $bkByBmr[$row->id_bmrs][] = ['tgl' => $row->tglambil_bkrs, 'qty' => (float)$row->qty];
        }

        $tempRows = [];
        foreach ($bmrRows as $bmr) {
            $kd = $bmr->kd_brg;

            $startOpsik = null;
            $opsikDetId = null;
            if (isset($opsikDetByKdBrg[$kd])) {
                $startOpsik = $opsikDetByKdBrg[$kd]->tgl_opurs;
                $opsikDetId = $opsikDetByKdBrg[$kd]->id_opursdet;
            }

            $qtyKeluar = 0;
            if (!empty($bkByBmr[$bmr->id_bmrs])) {
                foreach ($bkByBmr[$bmr->id_bmrs] as $k) {
                    if ($startOpsik === null) {
                        $qtyKeluar += $k['qty'];
                    } else {
                        if ($k['tgl'] >= $startOpsik) $qtyKeluar += $k['qty'];
                    }
                }
            }

            $hasOpsikItem = false;
            if ($opsikDetId !== null) {
                foreach ($opsikItems as $it) {
                    if ((int)$it->id_bmrs === (int)$bmr->id_bmrs && (int)$it->id_opursdet === (int)$opsikDetId) {
                        $hasOpsikItem = true;
                        $sisa = (float)$it->jmlh_opursdetitm - $qtyKeluar;
                        if ($sisa < 0) $sisa = 0;

                        $tempRows[] = [
                            'kd_brg'   => $kd,
                            'sisa_tbm' => $sisa,
                            'hrg_tbm'  => (float)$it->hrg_bmrs,
                            'kd_lks'   => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                            'user_id'  => $user_id,
                            'jns_tbm'  => 1,
                        ];
                    }
                }
            }

            if (!$hasOpsikItem) {
                $sisa = (float)$bmr->jmlh_awal_bmrs - $qtyKeluar;
                if ($sisa < 0) $sisa = 0;

                $tempRows[] = [
                    'kd_brg'   => $kd,
                    'sisa_tbm' => $sisa,
                    'hrg_tbm'  => (float)$bmr->hrg_bmrs,
                    'kd_lks'   => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                    'user_id'  => $user_id,
                    'jns_tbm'  => 1,
                ];
            }
        }

        if (!empty($tempRows)) {
            foreach (array_chunk($tempRows, 1000) as $chunk) {
                DB::table('temp_barang_masuk')->insert($chunk);
            }
        }
    }

    /**
     * Fakultas (barang_masuk_fakultas + opsik_fakultas + barang_keluar_fakultas)
     * Kalau spesifik fakultas, pakai $id_fk filter untuk opsik/keluar.
     */
    private function processFakultas(string $tgl_akhir, ?string $kd_lks, int $user_id, bool $filterLokasi, ?int $id_fk): void
    {
        $bmrQuery = DB::table('barang_masuk_fakultas')
            ->select('id_bmf','kd_brg','jmlh_awal_bmf','hrg_bmf','kd_lks')
            ->where('tglperolehan_bmf', '<=', $tgl_akhir);

        if ($filterLokasi && $kd_lks) {
            $bmrQuery->where('kd_lks', $kd_lks);
        }

        $bmrRows = $bmrQuery->orderBy('id_bmf')->get();
        if ($bmrRows->isEmpty()) return;

        $bmrIds = $bmrRows->pluck('id_bmf')->all();
        $kdBrgList = $bmrRows->pluck('kd_brg')->unique()->values()->all();

        $opsikBase = DB::table('opsik_fakultas_detail as d')
            ->join('opsik_fakultas as h','d.id_opfk','=','h.id_opfk')
            ->whereIn('d.kd_brg', $kdBrgList)
            ->where('h.tgl_opfk','<=', $tgl_akhir)
            ->where('h.status_opfk', 1);

        if ($id_fk !== null) {
            $opsikBase->where('h.id_fk', $id_fk);
        }

        $opsikLatest = (clone $opsikBase)
            ->select('d.kd_brg', DB::raw('MAX(h.tgl_opfk) as tgl_opfk'))
            ->groupBy('d.kd_brg')
            ->get()
            ->keyBy('kd_brg');

        $opsikDetRows = (clone $opsikBase)
            ->select('d.kd_brg','d.id_opfkdet','h.tgl_opfk')
            ->orderBy('d.id_opfkdet','desc')
            ->get();

        $opsikDetByKdBrg = [];
        foreach ($opsikDetRows as $r) {
            if (!isset($opsikLatest[$r->kd_brg])) continue;
            $latestDate = $opsikLatest[$r->kd_brg]->tgl_opfk;
            if ($r->tgl_opfk === $latestDate && !isset($opsikDetByKdBrg[$r->kd_brg])) {
                $opsikDetByKdBrg[$r->kd_brg] = (object)[
                    'id_opfkdet' => $r->id_opfkdet,
                    'tgl_opfk'   => $r->tgl_opfk,
                ];
            }
        }

        $opsikDetIds = array_values(array_unique(array_map(fn($x) => $x->id_opfkdet, $opsikDetByKdBrg)));

        $opsikItems = collect();
        if (!empty($opsikDetIds)) {
            $opsikItems = DB::table('v_opfik_fakultas_detail_item')
                ->select('id_bmf','id_opfkdet','jmlh_opfkdetitm','hrg_bmf')
                ->whereIn('id_bmf', $bmrIds)
                ->whereIn('id_opfkdet', $opsikDetIds)
                ->where('jmlh_opfkdetitm','>',0)
                ->get();
        }

        $bkBase = DB::table('barang_keluar_fakultas_detail as d')
            ->join('barang_keluar_fakultas as h','d.id_bkf','=','h.id_bkf')
            ->whereIn('d.id_bmf', $bmrIds)
            ->where('h.tglambil_bkf','<', $tgl_akhir);

        if ($id_fk !== null) {
            $bkBase->where('h.id_fk', $id_fk);
        }

        $bkAgg = (clone $bkBase)
            ->select('d.id_bmf','h.tglambil_bkf', DB::raw('SUM(d.jmlh_bkfd) as qty'))
            ->groupBy('d.id_bmf','h.tglambil_bkf')
            ->get();

        $bkByBmr = [];
        foreach ($bkAgg as $row) {
            $bkByBmr[$row->id_bmf][] = ['tgl' => $row->tglambil_bkf, 'qty' => (float)$row->qty];
        }

        $tempRows = [];
        foreach ($bmrRows as $bmr) {
            $kd = $bmr->kd_brg;

            $startOpsik = null;
            $opsikDetId = null;
            if (isset($opsikDetByKdBrg[$kd])) {
                $startOpsik = $opsikDetByKdBrg[$kd]->tgl_opfk;
                $opsikDetId = $opsikDetByKdBrg[$kd]->id_opfkdet;
            }

            $qtyKeluar = 0;
            if (!empty($bkByBmr[$bmr->id_bmf])) {
                foreach ($bkByBmr[$bmr->id_bmf] as $k) {
                    if ($startOpsik === null) {
                        $qtyKeluar += $k['qty'];
                    } else {
                        if ($k['tgl'] >= $startOpsik) $qtyKeluar += $k['qty'];
                    }
                }
            }

            $hasOpsikItem = false;
            if ($opsikDetId !== null) {
                foreach ($opsikItems as $it) {
                    if ((int)$it->id_bmf === (int)$bmr->id_bmf && (int)$it->id_opfkdet === (int)$opsikDetId) {
                        $hasOpsikItem = true;
                        $sisa = (float)$it->jmlh_opfkdetitm - $qtyKeluar;
                        if ($sisa < 0) $sisa = 0;

                        $tempRows[] = [
                            'kd_brg'   => $kd,
                            'sisa_tbm' => $sisa,
                            'hrg_tbm'  => (float)$it->hrg_bmf,
                            'kd_lks'   => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                            'user_id'  => $user_id,
                            'jns_tbm'  => 1,
                        ];
                    }
                }
            }

            if (!$hasOpsikItem) {
                $sisa = (float)$bmr->jmlh_awal_bmf - $qtyKeluar;
                if ($sisa < 0) $sisa = 0;

                $tempRows[] = [
                    'kd_brg'   => $kd,
                    'sisa_tbm' => $sisa,
                    'hrg_tbm'  => (float)$bmr->hrg_bmf,
                    'kd_lks'   => $filterLokasi && $kd_lks ? $kd_lks : ($bmr->kd_lks ?? ''),
                    'user_id'  => $user_id,
                    'jns_tbm'  => 1,
                ];
            }
        }

        if (!empty($tempRows)) {
            foreach (array_chunk($tempRows, 1000) as $chunk) {
                DB::table('temp_barang_masuk')->insert($chunk);
            }
        }
    }

    private function renderTtd(string $lokasi, string $tglCetakUpper): void
    {
        // Di sini aku biarkan kamu pakai blok tanda tangan yang kamu sudah punya.
        // Kalau mau aku rapikan juga jadi 1 fungsi per lokasi, bisa.
        // Untuk sekarang biar fokus performa.
    }
}