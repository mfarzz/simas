<?php

namespace App\Http\Controllers\Laporan\Rektorat;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangKeluarFakultasModel;
use App\Models\BarangKeluarRektoratDetailModel;
use App\Models\BarangKeluarRektoratModel;
use App\Models\BarangKeluarRumahSakitDetailModel;
use App\Models\BarangKeluarRumahSakitModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukRektoratModel;
use App\Models\BarangMasukRumahSakitModel;
use App\Models\FakultasModel;
use App\Models\JabpenfkModel;
use App\Models\JabpenuuModel;
use App\Models\JabpenurModel;
use App\Models\JabpenursModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpurdetitmModel;
use App\Models\OpursdetitmModel;
use App\Models\OpsikFkDetModel;
use App\Models\OpsikUrDetModel;
use App\Models\OpsikUrsDetModel;
use App\Models\TempBarangMasukModel;
use App\Models\UnitRumahSakitModel;
use App\Models\VLapPosisi4Model;
use App\Models\VOpfikFakultasDetailItemModel;
use App\Models\VOpfikRektoratDetailItemModel;
use App\Models\VOpfikRumahSakitDetailItemModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDF;

class LapPosisiPersediaanPrint extends Controller
{
    public function index($filter, $lokasi)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $lokasi = Crypt::decryptString($lokasi);
        $user_id = auth()->user()->id;

        TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm', 1)->delete();

        $datalokasi = null;
        if ($lokasi !== "") {
            $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();
        }

        if ($lokasi === "690522009KD") {
            $this->processRektoratByLokasi($tgl_akhir, $lokasi, $user_id);
        } elseif ($lokasi === "690522020KD") {
            $this->processRumahSakitByLokasi($tgl_akhir, $lokasi, $user_id);
        } elseif ($lokasi === "690522000KD") {
            $this->processUniversitas($tgl_akhir, $lokasi, $user_id);
        } elseif ($lokasi === "") {
            TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm', 1)->delete();
        } else {
            $this->processFakultasByLokasi($tgl_akhir, $lokasi, $user_id);
        }

        $this->generatePDF($tgl_akhir, $lokasi, $datalokasi, $user_id);
    }

    private function rupiah($angka)
    {
        return number_format((float)$angka, 0, ',', '.');
    }

    private function insertTempRows(array &$rows)
    {
        if (count($rows) === 0) return;

        foreach (array_chunk($rows, 500) as $chunk) {
            TempBarangMasukModel::insert($chunk);
        }
        $rows = [];
    }

    private function sumKeluarRektorat($id_bmr, $tglAwal, $tglAkhir, $excludeTglAkhir = false)
    {
        $q = BarangKeluarRektoratDetailModel::join(
            'barang_keluar_rektorat',
            'barang_keluar_rektorat_detail.id_bkr',
            '=',
            'barang_keluar_rektorat.id_bkr'
        )
            ->where('id_bmr', $id_bmr);

        if ($tglAwal !== null) {
            $q->whereBetween('tglambil_bkr', [$tglAwal, $tglAkhir]);
            if ($excludeTglAkhir) $q->where('tglambil_bkr', '!=', $tglAkhir);
        } else {
            $q->where('tglambil_bkr', '<=', $tglAkhir);
        }

        return (float)$q->sum('jmlh_bkrd');
    }

    private function sumKeluarRumahSakit($id_bmrs, $tglAwal, $tglAkhir, $excludeTglAkhir = false)
    {
        $q = BarangKeluarRumahSakitDetailModel::join(
            'barang_keluar_rumah_sakit',
            'barang_keluar_rumah_sakit_detail.id_bkrs',
            '=',
            'barang_keluar_rumah_sakit.id_bkrs'
        )
            ->where('id_bmrs', $id_bmrs);

        if ($tglAwal !== null) {
            $q->whereBetween('tglambil_bkrs', [$tglAwal, $tglAkhir]);
            if ($excludeTglAkhir) $q->where('tglambil_bkrs', '!=', $tglAkhir);
        } else {
            $q->where('tglambil_bkrs', '<=', $tglAkhir);
        }

        return (float)$q->sum('jmlh_bkrsd');
    }

    private function sumKeluarFakultas($id_bmf, $tglAwal, $tglAkhir, $excludeTglAkhir = false)
    {
        $q = BarangKeluarFakultasDetailModel::join(
            'barang_keluar_fakultas',
            'barang_keluar_fakultas_detail.id_bkf',
            '=',
            'barang_keluar_fakultas.id_bkf'
        )
            ->where('id_bmf', $id_bmf);

        if ($tglAwal !== null) {
            $q->whereBetween('tglambil_bkf', [$tglAwal, $tglAkhir]);
            if ($excludeTglAkhir) $q->where('tglambil_bkf', '!=', $tglAkhir);
        } else {
            $q->where('tglambil_bkf', '<=', $tglAkhir);
        }

        return (float)$q->sum('jmlh_bkfd');
    }

    private function processRektoratByLokasi($tgl_akhir, $lokasi, $user_id)
    {
        $rows = [];
        $now = now();

        BarangMasukRektoratModel::where('kd_lks', $lokasi)
            ->where('tglperolehan_bmr', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmr', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, &$rows, $now) {
                foreach ($items as $bmr) {
                    $this->processBarangMasukRektoratItem($bmr, $tgl_akhir, $user_id, $lokasi, $rows, $now);
                }
                $this->insertTempRows($rows);
            });

        $this->insertTempRows($rows);
    }

    private function processRumahSakitByLokasi($tgl_akhir, $lokasi, $user_id)
    {
        $rows = [];
        $now = now();

        BarangMasukRumahSakitModel::where('kd_lks', $lokasi)
            ->where('tglperolehan_bmrs', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmrs', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, &$rows, $now) {
                foreach ($items as $bmrs) {
                    $this->processBarangMasukRumahSakitItem($bmrs, $tgl_akhir, $user_id, $lokasi, $rows, $now);
                }
                $this->insertTempRows($rows);
            });

        $this->insertTempRows($rows);
    }

    private function processFakultasByLokasi($tgl_akhir, $lokasi, $user_id)
    {
        $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();
        if (!$datafakultas) return;

        $id_fk = $datafakultas->id_fk;

        $rows = [];
        $now = now();

        BarangMasukFakultasModel::where('kd_lks', $lokasi)
            ->where('tglperolehan_bmf', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmf', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, $id_fk, &$rows, $now) {
                foreach ($items as $bmf) {
                    $this->processBarangMasukFakultasItem($bmf, $tgl_akhir, $user_id, $lokasi, $rows, $now, $id_fk);
                }
                $this->insertTempRows($rows);
            });

        $this->insertTempRows($rows);
    }

    private function processUniversitas($tgl_akhir, $lokasi, $user_id)
    {
        $rows = [];
        $now = now();

        BarangMasukRektoratModel::where('tglperolehan_bmr', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmr', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, &$rows, $now) {
                foreach ($items as $bmr) {
                    $this->processBarangMasukRektoratItem($bmr, $tgl_akhir, $user_id, $lokasi, $rows, $now);
                }
                $this->insertTempRows($rows);
            });

        BarangMasukRumahSakitModel::where('tglperolehan_bmrs', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmrs', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, &$rows, $now) {
                foreach ($items as $bmrs) {
                    $this->processBarangMasukRumahSakitItem($bmrs, $tgl_akhir, $user_id, $lokasi, $rows, $now);
                }
                $this->insertTempRows($rows);
            });

        BarangMasukFakultasModel::where('tglperolehan_bmf', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmf', 'asc')
            ->chunk(200, function ($items) use ($tgl_akhir, $lokasi, $user_id, &$rows, $now) {
                foreach ($items as $bmf) {
                    $this->processBarangMasukFakultasItem($bmf, $tgl_akhir, $user_id, $lokasi, $rows, $now, null);
                }
                $this->insertTempRows($rows);
            });

        $this->insertTempRows($rows);
    }

    private function processBarangMasukRektoratItem($bmr, $tgl_akhir, $user_id, $lokasi, array &$rows, $now)
    {
        $opsik = OpsikUrDetModel::join('opsik_rektorat', 'opsik_rektorat_detail.id_opur', '=', 'opsik_rektorat.id_opur')
            ->where('kd_brg', $bmr->kd_brg)
            ->where('tgl_opur', '<=', $tgl_akhir)
            ->where('status_opur', 1)
            ->orderBy('tgl_opur', 'desc')
            ->first();

        if ($opsik) {
            $jumlahbk = BarangKeluarRektoratModel::where('kd_brg', $bmr->kd_brg)
                ->where('tglambil_bkr', '>', $opsik->tgl_opur)
                ->exists();

            if ($jumlahbk) {
                $detailItems = VOpfikRektoratDetailItemModel::join('barang_masuk_rektorat', 'v_opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                    ->where('v_opfik_rektorat_detail_item.id_bmr', $bmr->id_bmr)
                    ->where('id_opurdet', $opsik->id_opurdet)
                    ->where('jmlh_opurdetitm', '>', 0)
                    ->get();
            } else {
                $detailItems = OpurdetitmModel::join('barang_masuk_rektorat', 'opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                    ->where('opfik_rektorat_detail_item.id_bmr', $bmr->id_bmr)
                    ->where('id_opurdet', $opsik->id_opurdet)
                    ->get();
            }

            $sumKeluar = $this->sumKeluarRektorat($bmr->id_bmr, $opsik->tgl_opur, $tgl_akhir, true);

            foreach ($detailItems as $di) {
                $sisa = (float)$di->jmlh_opurdetitm - (float)$sumKeluar;
                $rows[] = [
                    'kd_brg' => $bmr->kd_brg,
                    'sisa_tbm' => $sisa,
                    'hrg_tbm' => $di->hrg_bmr,
                    'kd_lks' => $lokasi,
                    'user_id' => $user_id,
                    'jns_tbm' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            return;
        }

        $sumKeluar = $this->sumKeluarRektorat($bmr->id_bmr, null, $tgl_akhir, false);
        $sisa = (float)$bmr->jmlh_awal_bmr - (float)$sumKeluar;

        $rows[] = [
            'kd_brg' => $bmr->kd_brg,
            'sisa_tbm' => $sisa,
            'hrg_tbm' => $bmr->hrg_bmr,
            'kd_lks' => $lokasi,
            'user_id' => $user_id,
            'jns_tbm' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function processBarangMasukRumahSakitItem($bmrs, $tgl_akhir, $user_id, $lokasi, array &$rows, $now)
    {
        $opsik = OpsikUrsDetModel::join('opsik_rumah_sakit', 'opsik_rumah_sakit_detail.id_opurs', '=', 'opsik_rumah_sakit.id_opurs')
            ->where('kd_brg', $bmrs->kd_brg)
            ->where('tgl_opurs', '<=', $tgl_akhir)
            ->where('status_opurs', 1)
            ->orderBy('tgl_opurs', 'desc')
            ->first();

        if ($opsik) {
            $jumlahbk = BarangKeluarRumahSakitModel::where('kd_brg', $bmrs->kd_brg)
                ->where('tglambil_bkrs', '>', $opsik->tgl_opurs)
                ->exists();

            if ($jumlahbk) {
                $detailItems = VOpfikRumahSakitDetailItemModel::join('barang_masuk_rumah_sakit', 'v_opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                    ->where('v_opfik_rumah_sakit_detail_item.id_bmrs', $bmrs->id_bmrs)
                    ->where('id_opursdet', $opsik->id_opursdet)
                    ->where('jmlh_opursdetitm', '>', 0)
                    ->get();
            } else {
                $detailItems = OpursdetitmModel::join('barang_masuk_rumah_sakit', 'opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                    ->where('opfik_rumah_sakit_detail_item.id_bmrs', $bmrs->id_bmrs)
                    ->where('id_opursdet', $opsik->id_opursdet)
                    ->get();
            }

            $sumKeluar = $this->sumKeluarRumahSakit($bmrs->id_bmrs, $opsik->tgl_opurs, $tgl_akhir, true);

            foreach ($detailItems as $di) {
                $sisa = (float)$di->jmlh_opursdetitm - (float)$sumKeluar;
                $rows[] = [
                    'kd_brg' => $bmrs->kd_brg,
                    'sisa_tbm' => $sisa,
                    'hrg_tbm' => $di->hrg_bmrs,
                    'kd_lks' => $lokasi,
                    'user_id' => $user_id,
                    'jns_tbm' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            return;
        }

        $sumKeluar = $this->sumKeluarRumahSakit($bmrs->id_bmrs, null, $tgl_akhir, false);
        $sisa = (float)$bmrs->jmlh_awal_bmrs - (float)$sumKeluar;

        $rows[] = [
            'kd_brg' => $bmrs->kd_brg,
            'sisa_tbm' => $sisa,
            'hrg_tbm' => $bmrs->hrg_bmrs,
            'kd_lks' => $lokasi,
            'user_id' => $user_id,
            'jns_tbm' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function processBarangMasukFakultasItem($bmf, $tgl_akhir, $user_id, $lokasi, array &$rows, $now, $id_fk = null)
    {
        $opsikQ = OpsikFkDetModel::join('opsik_fakultas', 'opsik_fakultas_detail.id_opfk', '=', 'opsik_fakultas.id_opfk')
            ->where('kd_brg', $bmf->kd_brg)
            ->where('tgl_opfk', '<=', $tgl_akhir)
            ->where('status_opfk', 1)
            ->orderBy('tgl_opfk', 'desc');

        if ($id_fk !== null) {
            $opsikQ->where('id_fk', $id_fk);
        }

        $opsik = $opsikQ->first();

        if ($opsik) {
            $bkQ = BarangKeluarFakultasModel::where('kd_brg', $bmf->kd_brg)
                ->where('tglambil_bkf', '>', $opsik->tgl_opfk);

            if ($id_fk !== null) {
                $bkQ->where('id_fk', $id_fk);
            }

            $jumlahbk = $bkQ->exists();

            if ($jumlahbk) {
                $detailItems = VOpfikFakultasDetailItemModel::join('barang_masuk_fakultas', 'v_opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                    ->where('v_opfik_fakultas_detail_item.id_bmf', $bmf->id_bmf)
                    ->where('id_opfkdet', $opsik->id_opfkdet)
                    ->where('jmlh_opfkdetitm', '>', 0)
                    ->get();
            } else {
                $detailItems = OpfkdetitmModel::join('barang_masuk_fakultas', 'opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                    ->where('opfik_fakultas_detail_item.id_bmf', $bmf->id_bmf)
                    ->where('id_opfkdet', $opsik->id_opfkdet)
                    ->get();
            }

            $sumKeluar = $this->sumKeluarFakultas($bmf->id_bmf, $opsik->tgl_opfk, $tgl_akhir, true);

            foreach ($detailItems as $di) {
                $sisa = (float)$di->jmlh_opfkdetitm - (float)$sumKeluar;
                $rows[] = [
                    'kd_brg' => $bmf->kd_brg,
                    'sisa_tbm' => $sisa,
                    'hrg_tbm' => $di->hrg_bmf,
                    'kd_lks' => $lokasi,
                    'user_id' => $user_id,
                    'jns_tbm' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            return;
        }

        $sumKeluar = $this->sumKeluarFakultas($bmf->id_bmf, null, $tgl_akhir, false);
        $sisa = (float)$bmf->jmlh_awal_bmf - (float)$sumKeluar;

        $rows[] = [
            'kd_brg' => $bmf->kd_brg,
            'sisa_tbm' => $sisa,
            'hrg_tbm' => $bmf->hrg_bmf,
            'kd_lks' => $lokasi,
            'user_id' => $user_id,
            'jns_tbm' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function generatePDF($tgl_akhir, $lokasi, $datalokasi, $user_id)
    {
        $tahunanggaran = substr($tgl_akhir, 0, 4);

        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();

        $tglHeader = Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tglHeader = strtoupper($tglHeader);

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN POSISI PERSEDIAN DI NERACA', 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, "UNTUK PERIODE YANG BERAKHIR TANGGAL $tglHeader", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "TAHUN ANGGARAN $tahunanggaran", 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', '', 10);

        PDF::ln(5);

        $nmLks = $datalokasi ? $datalokasi->nm_lks : '-';
        PDF::Cell(28, 0, "UAPKB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$nmLks", 0, 1, 'L', 0, '', true);

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

        $datalap = VLapPosisi4Model::join('kategori', 'v_lap_posisi4.v_kd_kt', '=', 'kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks', $lokasi)
            ->where('v_lap_posisi4.user_id', $user_id)
            ->where('v_lap_posisi4.v_jns_tbm', 1)
            ->orderBy('v_lap_posisi4.v_kd_kt')
            ->get();

        foreach ($datalap as $barislap) {
            $nilairp = $this->rupiah($barislap->total_nilai);
            PDF::Cell(28, 0, "$barislap->kd_kt", 1, 0, 'C', 0, '', true);
            PDF::Cell(120, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
            PDF::Cell(40, 0, "$nilairp", 1, 1, 'R', 0, '', true);
            PDF::ln(0);
            $total_nilai += (float)$barislap->total_nilai;
        }

        $total_nilai2 = $this->rupiah($total_nilai);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(28, 0, "", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "Jumlah", 1, 0, 'R', 0, '', true);
        PDF::Cell(40, 0, "$total_nilai2", 1, 1, 'R', 0, '', true);

        $tglTtd = ucwords(strtolower($tglHeader));

        if ($lokasi === "690522009KD") {
            $pejabatanpimpinan = JabpenurModel::join('jabatan_rektorat', 'jabatan_pengesahan_rektorat.id_jabur', '=', 'jabatan_rektorat.id_jabur')
                ->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 1)->first();
            $pejabatanop = JabpenurModel::join('jabatan_rektorat', 'jabatan_pengesahan_rektorat.id_jabur', '=', 'jabatan_rektorat.id_jabur')
                ->where('id_ur', 1)->where('jabatan_pengesahan_rektorat.id_jabur', 2)->first();

            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tglTtd", 0, 0, 'C', 0, '', true);
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
        } elseif ($lokasi === "690522020KD") {
            $datarumahsakit = UnitRumahSakitModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenursModel::join('jabatan_rumah_sakit', 'jabatan_pengesahan_rumah_sakit.id_jaburs', '=', 'jabatan_rumah_sakit.id_jaburs')
                ->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 1)->first();
            $pejabatanop = JabpenursModel::join('jabatan_rumah_sakit', 'jabatan_pengesahan_rumah_sakit.id_jaburs', '=', 'jabatan_rumah_sakit.id_jaburs')
                ->where('id_urs', $datarumahsakit->id_urs)->where('jabatan_pengesahan_rumah_sakit.id_jaburs', 2)->first();

            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tglTtd", 0, 0, 'C', 0, '', true);
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
        } elseif ($lokasi === "690522000KD") {
            $pejabatanpimpinan = JabpenuuModel::join('jabatan_universitas', 'jabatan_pengesahan_universitas.id_jabuni', '=', 'jabatan_universitas.id_jabuni')
                ->where('jabatan_pengesahan_universitas.id_jabuni', 1)->first();
            $pejabatanop = JabpenuuModel::join('jabatan_universitas', 'jabatan_pengesahan_universitas.id_jabuni', '=', 'jabatan_universitas.id_jabuni')
                ->where('jabatan_pengesahan_universitas.id_jabuni', 2)->first();

            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tglTtd", 0, 0, 'C', 0, '', true);
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
        } else {
            $datafakultas = FakultasModel::where('kd_lks', $lokasi)->first();

            $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas', 'jabatan_pengesahan_fakultas.id_jabfk', '=', 'jabatan_fakultas.id_jabfk')
                ->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
            $pejabatanop = JabpenfkModel::join('jabatan_fakultas', 'jabatan_pengesahan_fakultas.id_jabfk', '=', 'jabatan_fakultas.id_jabfk')
                ->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();

            PDF::SetFont('times', '', 10);
            PDF::ln(10);
            PDF::Cell(60, 0, "Disetujui tanggal: $tglTtd", 0, 0, 'C', 0, '', true);
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