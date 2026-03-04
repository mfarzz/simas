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

        if ($lokasi == "690522009KD")
        {
            $this->processRektorat($tgl_akhir, $lokasi, $user_id, $lokasi);
        }
        elseif ($lokasi == "690522020KD")
        {
            $this->processRumahSakit($tgl_akhir, $lokasi, $user_id, $lokasi);
        }
        else if ($lokasi == "690522000KD")
        {
            $this->processRektorat($tgl_akhir, $lokasi, $user_id, null);
            $this->processRumahSakit($tgl_akhir, $lokasi, $user_id, null);
            $this->processFakultas($tgl_akhir, $lokasi, $user_id, null, null);
        }
        else if ($lokasi == "")
        {
            // no-op
        }
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

    /**
     * Proses barang_masuk_rektorat — logika IDENTIK dengan kode asli,
     * tapi data di-prefetch sebelum loop untuk menghindari N+1 queries.
     */
    private function processRektorat(string $tgl_akhir, string $lokasi, int $user_id, ?string $kdLksFilter): void
    {
        $q = BarangMasukRektoratModel::where('tglperolehan_bmr', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmr', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', '=', $kdLksFilter);
        }
        $databarangmasukrektorat = $q->get();
        if ($databarangmasukrektorat->isEmpty()) return;

        // --- PRE-FETCH semua data yang dibutuhkan ---
        $allKdBrg = $databarangmasukrektorat->pluck('kd_brg')->unique()->values()->all();
        $allIdBmr = $databarangmasukrektorat->pluck('id_bmr')->all();

        // Opsik count per kd_brg (status=1)
        $opsikCounts = DB::table('opsik_rektorat_detail')
            ->join('opsik_rektorat', 'opsik_rektorat_detail.id_opur', '=', 'opsik_rektorat.id_opur')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opur', '<=', $tgl_akhir)
            ->where('status_opur', '=', 1)
            ->selectRaw('kd_brg, COUNT(*) as cnt')
            ->groupBy('kd_brg')
            ->pluck('cnt', 'kd_brg');

        // Opsik terbaru per kd_brg TANPA filter status (sesuai kode asli)
        $opsikLatest = DB::table('opsik_rektorat_detail')
            ->join('opsik_rektorat', 'opsik_rektorat_detail.id_opur', '=', 'opsik_rektorat.id_opur')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opur', '<=', $tgl_akhir)
            ->orderBy('tgl_opur', 'desc')
            ->select('id_opurdet', 'kd_brg', 'tgl_opur')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        // Keluar count per kd_brg (untuk cek ada/tidaknya keluar setelah opsik)
        // Kita ambil semua header keluar, grouped by kd_brg
        $keluarHeaders = DB::table('barang_keluar_rektorat')
            ->whereIn('kd_brg', $allKdBrg)
            ->select('kd_brg', 'tglambil_bkr')
            ->get()
            ->groupBy('kd_brg');

        // View items per id_bmr+id_opurdet
        $allOpurDetIds = $opsikLatest->pluck('id_opurdet')->filter()->all();
        $viewItemsAll = collect();
        if (!empty($allOpurDetIds)) {
            $viewItemsAll = DB::table('v_opfik_rektorat_detail_item')
                ->join('barang_masuk_rektorat', 'v_opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                ->whereIn('v_opfik_rektorat_detail_item.id_bmr', $allIdBmr)
                ->whereIn('id_opurdet', $allOpurDetIds)
                ->where('jmlh_opurdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmr . '_' . $r->id_opurdet);
        }

        // Table items per id_bmr+id_opurdet
        $tableItemsAll = collect();
        if (!empty($allOpurDetIds)) {
            $tableItemsAll = DB::table('opfik_rektorat_detail_item')
                ->join('barang_masuk_rektorat', 'opfik_rektorat_detail_item.id_bmr', '=', 'barang_masuk_rektorat.id_bmr')
                ->whereIn('opfik_rektorat_detail_item.id_bmr', $allIdBmr)
                ->whereIn('id_opurdet', $allOpurDetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmr . '_' . $r->id_opurdet);
        }

        // Keluar detail per id_bmr (semua tanggal, kita filter di PHP)
        $keluarDetailAll = DB::table('barang_keluar_rektorat_detail')
            ->join('barang_keluar_rektorat', 'barang_keluar_rektorat_detail.id_bkr', '=', 'barang_keluar_rektorat.id_bkr')
            ->whereIn('barang_keluar_rektorat_detail.id_bmr', $allIdBmr)
            ->select('barang_keluar_rektorat_detail.id_bmr', 'barang_keluar_rektorat_detail.jmlh_bkrd', 'barang_keluar_rektorat.tglambil_bkr')
            ->get()
            ->groupBy('id_bmr');

        // --- LOOP identik dengan kode asli ---
        $batch = [];
        $now = now();
        foreach ($databarangmasukrektorat as $barisbmr)
        {
            $jumlahopsik = $opsikCounts[$barisbmr->kd_brg] ?? 0;
            if ($jumlahopsik >= 1)
            {
                $databarangopsik = $opsikLatest[$barisbmr->kd_brg] ?? null;
                if ($databarangopsik === null) continue;

                // Cek keluar setelah opsik
                $headers = $keluarHeaders[$barisbmr->kd_brg] ?? collect();
                $jumlahbk = $headers->where('tglambil_bkr', '>', $databarangopsik->tgl_opur)->count();

                if ($jumlahbk >= 1)
                {
                    // VIEW items path
                    $key = $barisbmr->id_bmr . '_' . $databarangopsik->id_opurdet;
                    $items = $viewItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        $tjmlh_bkrd = 0;
                        $keluarRows = $keluarDetailAll[$barisbmr->id_bmr] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkr >= $databarangopsik->tgl_opur && $r->tglambil_bkr <= $tgl_akhir;
                        });
                        foreach ($filteredKeluar as $barisbkrd)
                        {
                            if ($barisbkrd->tglambil_bkr != $tgl_akhir)
                            {
                                $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                            }
                        }
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                        $hrg_bmr = $barisopsikdetailitem->hrg_bmr;

                        $batch[] = [
                            'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $hrg_bmr, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
                else
                {
                    // TABLE items path
                    $key = $barisbmr->id_bmr . '_' . $databarangopsik->id_opurdet;
                    $items = $tableItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        $keluarRows = $keluarDetailAll[$barisopsikdetailitem->id_bmr] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkr >= $databarangopsik->tgl_opur && $r->tglambil_bkr <= $tgl_akhir;
                        });
                        $jumlahbarangkeluar = $filteredKeluar->count();
                        if ($jumlahbarangkeluar >= 1)
                        {
                            $tjmlh_bkrd = 0;
                            // Keluar pakai id_bmr dari barisbmr (sesuai kode asli baris 151)
                            $keluarRows2 = $keluarDetailAll[$barisbmr->id_bmr] ?? collect();
                            $filteredKeluar2 = $keluarRows2->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                                return $r->tglambil_bkr >= $databarangopsik->tgl_opur && $r->tglambil_bkr <= $tgl_akhir;
                            });
                            foreach ($filteredKeluar2 as $barisbkrd)
                            {
                                if ($barisbkrd->tglambil_bkr != $tgl_akhir)
                                {
                                    $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                                }
                            }
                        }
                        else
                        {
                            $tjmlh_bkrd = 0;
                        }

                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opurdetitm - $tjmlh_bkrd;
                        $hrg_bmr = $barisopsikdetailitem->hrg_bmr;

                        $batch[] = [
                            'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $hrg_bmr, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
            }
            else
            {
                // No opsik path
                $tjmlh_bkrd = 0;
                $keluarRows = $keluarDetailAll[$barisbmr->id_bmr] ?? collect();
                foreach ($keluarRows as $barisbkrd)
                {
                    if ($barisbkrd->tglambil_bkr <= $tgl_akhir)
                    {
                        $tjmlh_bkrd = $barisbkrd->jmlh_bkrd + $tjmlh_bkrd;
                    }
                }
                $sisa_tbmr = $barisbmr->jmlh_awal_bmr - $tjmlh_bkrd;

                $batch[] = [
                    'kd_brg' => $barisbmr->kd_brg, 'sisa_tbm' => $sisa_tbmr,
                    'hrg_tbm' => $barisbmr->hrg_bmr, 'kd_lks' => $lokasi,
                    'user_id' => $user_id, 'jns_tbm' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }

    /**
     * Proses barang_masuk_rumah_sakit — logika IDENTIK dengan kode asli.
     */
    private function processRumahSakit(string $tgl_akhir, string $lokasi, int $user_id, ?string $kdLksFilter): void
    {
        $q = BarangMasukRumahSakitModel::where('tglperolehan_bmrs', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmrs', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', '=', $kdLksFilter);
        }
        $databarangmasukrumahsakit = $q->get();
        if ($databarangmasukrumahsakit->isEmpty()) return;

        $allKdBrg = $databarangmasukrumahsakit->pluck('kd_brg')->unique()->values()->all();
        $allIdBmrs = $databarangmasukrumahsakit->pluck('id_bmrs')->all();

        $opsikCounts = DB::table('opsik_rumah_sakit_detail')
            ->join('opsik_rumah_sakit', 'opsik_rumah_sakit_detail.id_opurs', '=', 'opsik_rumah_sakit.id_opurs')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opurs', '<=', $tgl_akhir)
            ->where('status_opurs', '=', 1)
            ->selectRaw('kd_brg, COUNT(*) as cnt')
            ->groupBy('kd_brg')
            ->pluck('cnt', 'kd_brg');

        $opsikLatest = DB::table('opsik_rumah_sakit_detail')
            ->join('opsik_rumah_sakit', 'opsik_rumah_sakit_detail.id_opurs', '=', 'opsik_rumah_sakit.id_opurs')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opurs', '<=', $tgl_akhir)
            ->orderBy('tgl_opurs', 'desc')
            ->select('id_opursdet', 'kd_brg', 'tgl_opurs')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        $keluarHeaders = DB::table('barang_keluar_rumah_sakit')
            ->whereIn('kd_brg', $allKdBrg)
            ->select('kd_brg', 'tglambil_bkrs')
            ->get()
            ->groupBy('kd_brg');

        $allOpursDetIds = $opsikLatest->pluck('id_opursdet')->filter()->all();

        $viewItemsAll = collect();
        if (!empty($allOpursDetIds)) {
            $viewItemsAll = DB::table('v_opfik_rumah_sakit_detail_item')
                ->join('barang_masuk_rumah_sakit', 'v_opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                ->whereIn('v_opfik_rumah_sakit_detail_item.id_bmrs', $allIdBmrs)
                ->whereIn('id_opursdet', $allOpursDetIds)
                ->where('jmlh_opursdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmrs . '_' . $r->id_opursdet);
        }

        $tableItemsAll = collect();
        if (!empty($allOpursDetIds)) {
            $tableItemsAll = DB::table('opfik_rumah_sakit_detail_item')
                ->join('barang_masuk_rumah_sakit', 'opfik_rumah_sakit_detail_item.id_bmrs', '=', 'barang_masuk_rumah_sakit.id_bmrs')
                ->whereIn('opfik_rumah_sakit_detail_item.id_bmrs', $allIdBmrs)
                ->whereIn('id_opursdet', $allOpursDetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmrs . '_' . $r->id_opursdet);
        }

        $keluarDetailAll = DB::table('barang_keluar_rumah_sakit_detail')
            ->join('barang_keluar_rumah_sakit', 'barang_keluar_rumah_sakit_detail.id_bkrs', '=', 'barang_keluar_rumah_sakit.id_bkrs')
            ->whereIn('barang_keluar_rumah_sakit_detail.id_bmrs', $allIdBmrs)
            ->select('barang_keluar_rumah_sakit_detail.id_bmrs', 'barang_keluar_rumah_sakit_detail.jmlh_bkrsd', 'barang_keluar_rumah_sakit.tglambil_bkrs')
            ->get()
            ->groupBy('id_bmrs');

        $batch = [];
        $now = now();
        foreach ($databarangmasukrumahsakit as $barisbmrs)
        {
            $jumlahopsik = $opsikCounts[$barisbmrs->kd_brg] ?? 0;
            if ($jumlahopsik >= 1)
            {
                $databarangopsik = $opsikLatest[$barisbmrs->kd_brg] ?? null;
                if ($databarangopsik === null) continue;

                $headers = $keluarHeaders[$barisbmrs->kd_brg] ?? collect();
                $jumlahbk = $headers->where('tglambil_bkrs', '>', $databarangopsik->tgl_opurs)->count();

                if ($jumlahbk >= 1)
                {
                    $key = $barisbmrs->id_bmrs . '_' . $databarangopsik->id_opursdet;
                    $items = $viewItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        // Bug asli line 267: pakai $tjmlh_bkrd bukan $tjmlh_bkrsd
                        $tjmlh_bkrsd = 0;
                        $keluarRows = $keluarDetailAll[$barisbmrs->id_bmrs] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkrs >= $databarangopsik->tgl_opurs && $r->tglambil_bkrs <= $tgl_akhir;
                        });
                        foreach ($filteredKeluar as $barisbkrsd)
                        {
                            if ($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                            {
                                // Replicate original bug: assigns wrong var so $tjmlh_bkrsd stays 0
                                $tjmlh_bkrd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                            }
                        }
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;

                        $batch[] = [
                            'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
                else
                {
                    $key = $barisbmrs->id_bmrs . '_' . $databarangopsik->id_opursdet;
                    $items = $tableItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        $keluarRows = $keluarDetailAll[$barisopsikdetailitem->id_bmrs] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkrs >= $databarangopsik->tgl_opurs && $r->tglambil_bkrs <= $tgl_akhir;
                        });
                        $jumlahbarangkeluar = $filteredKeluar->count();
                        if ($jumlahbarangkeluar >= 1)
                        {
                            $tjmlh_bkrsd = 0;
                            $keluarRows2 = $keluarDetailAll[$barisbmrs->id_bmrs] ?? collect();
                            $filteredKeluar2 = $keluarRows2->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                                return $r->tglambil_bkrs >= $databarangopsik->tgl_opurs && $r->tglambil_bkrs <= $tgl_akhir;
                            });
                            foreach ($filteredKeluar2 as $barisbkrsd)
                            {
                                if ($barisbkrsd->tglambil_bkrs != $tgl_akhir)
                                {
                                    $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                                }
                            }
                        }
                        else
                        {
                            $tjmlh_bkrsd = 0;
                        }

                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opursdetitm - $tjmlh_bkrsd;

                        $batch[] = [
                            'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $barisopsikdetailitem->hrg_bmrs, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
            }
            else
            {
                $tjmlh_bkrsd = 0;
                $keluarRows = $keluarDetailAll[$barisbmrs->id_bmrs] ?? collect();
                foreach ($keluarRows as $barisbkrsd)
                {
                    if ($barisbkrsd->tglambil_bkrs <= $tgl_akhir)
                    {
                        $tjmlh_bkrsd = $barisbkrsd->jmlh_bkrsd + $tjmlh_bkrsd;
                    }
                }
                $sisa_tbmrs = $barisbmrs->jmlh_awal_bmrs - $tjmlh_bkrsd;

                $batch[] = [
                    'kd_brg' => $barisbmrs->kd_brg, 'sisa_tbm' => $sisa_tbmrs,
                    'hrg_tbm' => $barisbmrs->hrg_bmrs, 'kd_lks' => $lokasi,
                    'user_id' => $user_id, 'jns_tbm' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }

    /**
     * Proses barang_masuk_fakultas — logika IDENTIK dengan kode asli.
     * $idFkFilter digunakan untuk opsik & keluar header check (per-Fakultas).
     */
    private function processFakultas(string $tgl_akhir, string $lokasi, int $user_id, ?string $kdLksFilter, ?int $idFkFilter): void
    {
        $q = BarangMasukFakultasModel::where('tglperolehan_bmf', '<=', $tgl_akhir)
            ->orderBy('tglperolehan_bmf', 'asc');
        if ($kdLksFilter !== null) {
            $q->where('kd_lks', '=', $kdLksFilter);
        }
        $databarangmasukfakultas = $q->get();
        if ($databarangmasukfakultas->isEmpty()) return;

        $allKdBrg = $databarangmasukfakultas->pluck('kd_brg')->unique()->values()->all();
        $allIdBmf = $databarangmasukfakultas->pluck('id_bmf')->all();

        // Opsik count (status=1)
        $opsikCountQ = DB::table('opsik_fakultas_detail')
            ->join('opsik_fakultas', 'opsik_fakultas_detail.id_opfk', '=', 'opsik_fakultas.id_opfk')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opfk', '<=', $tgl_akhir)
            ->where('status_opfk', '=', 1);
        if ($idFkFilter !== null) {
            $opsikCountQ->where('id_fk', '=', $idFkFilter);
        }
        $opsikCounts = $opsikCountQ
            ->selectRaw('kd_brg, COUNT(*) as cnt')
            ->groupBy('kd_brg')
            ->pluck('cnt', 'kd_brg');

        // Opsik terbaru TANPA filter status
        $opsikLatestQ = DB::table('opsik_fakultas_detail')
            ->join('opsik_fakultas', 'opsik_fakultas_detail.id_opfk', '=', 'opsik_fakultas.id_opfk')
            ->whereIn('kd_brg', $allKdBrg)
            ->where('tgl_opfk', '<=', $tgl_akhir);
        if ($idFkFilter !== null) {
            $opsikLatestQ->where('id_fk', '=', $idFkFilter);
        }
        $opsikLatest = $opsikLatestQ
            ->orderBy('tgl_opfk', 'desc')
            ->select('id_opfkdet', 'kd_brg', 'tgl_opfk')
            ->get()
            ->groupBy('kd_brg')
            ->map(fn($g) => $g->first());

        // Keluar headers
        $keluarHeaderQ = DB::table('barang_keluar_fakultas')
            ->whereIn('kd_brg', $allKdBrg);
        if ($idFkFilter !== null) {
            $keluarHeaderQ->where('id_fk', '=', $idFkFilter);
        }
        $keluarHeaders = $keluarHeaderQ
            ->select('kd_brg', 'tglambil_bkf')
            ->get()
            ->groupBy('kd_brg');

        $allOpfkDetIds = $opsikLatest->pluck('id_opfkdet')->filter()->all();

        $viewItemsAll = collect();
        if (!empty($allOpfkDetIds)) {
            $viewItemsAll = DB::table('v_opfik_fakultas_detail_item')
                ->join('barang_masuk_fakultas', 'v_opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                ->whereIn('v_opfik_fakultas_detail_item.id_bmf', $allIdBmf)
                ->whereIn('id_opfkdet', $allOpfkDetIds)
                ->where('jmlh_opfkdetitm', '>', 0)
                ->get()
                ->groupBy(fn($r) => $r->id_bmf . '_' . $r->id_opfkdet);
        }

        $tableItemsAll = collect();
        if (!empty($allOpfkDetIds)) {
            $tableItemsAll = DB::table('opfik_fakultas_detail_item')
                ->join('barang_masuk_fakultas', 'opfik_fakultas_detail_item.id_bmf', '=', 'barang_masuk_fakultas.id_bmf')
                ->whereIn('opfik_fakultas_detail_item.id_bmf', $allIdBmf)
                ->whereIn('id_opfkdet', $allOpfkDetIds)
                ->get()
                ->groupBy(fn($r) => $r->id_bmf . '_' . $r->id_opfkdet);
        }

        $keluarDetailAll = DB::table('barang_keluar_fakultas_detail')
            ->join('barang_keluar_fakultas', 'barang_keluar_fakultas_detail.id_bkf', '=', 'barang_keluar_fakultas.id_bkf')
            ->whereIn('barang_keluar_fakultas_detail.id_bmf', $allIdBmf)
            ->select('barang_keluar_fakultas_detail.id_bmf', 'barang_keluar_fakultas_detail.jmlh_bkfd', 'barang_keluar_fakultas.tglambil_bkf')
            ->get()
            ->groupBy('id_bmf');

        $batch = [];
        $now = now();
        foreach ($databarangmasukfakultas as $barisbmf)
        {
            $jumlahopsik = $opsikCounts[$barisbmf->kd_brg] ?? 0;
            if ($jumlahopsik >= 1)
            {
                $databarangopsik = $opsikLatest[$barisbmf->kd_brg] ?? null;
                if ($databarangopsik === null) continue;

                $headers = $keluarHeaders[$barisbmf->kd_brg] ?? collect();
                $jumlahbk = $headers->where('tglambil_bkf', '>', $databarangopsik->tgl_opfk)->count();

                if ($jumlahbk >= 1)
                {
                    $key = $barisbmf->id_bmf . '_' . $databarangopsik->id_opfkdet;
                    $items = $viewItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        $tjmlh_bkfd = 0;
                        $keluarRows = $keluarDetailAll[$barisbmf->id_bmf] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkf >= $databarangopsik->tgl_opfk && $r->tglambil_bkf <= $tgl_akhir;
                        });
                        foreach ($filteredKeluar as $barisbkfd)
                        {
                            if ($barisbkfd->tglambil_bkf != $tgl_akhir)
                            {
                                $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                            }
                        }
                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;

                        $batch[] = [
                            'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $barisopsikdetailitem->hrg_bmf, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
                else
                {
                    $key = $barisbmf->id_bmf . '_' . $databarangopsik->id_opfkdet;
                    $items = $tableItemsAll[$key] ?? collect();
                    foreach ($items as $barisopsikdetailitem)
                    {
                        $keluarRows = $keluarDetailAll[$barisopsikdetailitem->id_bmf] ?? collect();
                        $filteredKeluar = $keluarRows->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                            return $r->tglambil_bkf >= $databarangopsik->tgl_opfk && $r->tglambil_bkf <= $tgl_akhir;
                        });
                        $jumlahbarangkeluar = $filteredKeluar->count();
                        if ($jumlahbarangkeluar >= 1)
                        {
                            $tjmlh_bkfd = 0;
                            $keluarRows2 = $keluarDetailAll[$barisbmf->id_bmf] ?? collect();
                            $filteredKeluar2 = $keluarRows2->filter(function($r) use ($databarangopsik, $tgl_akhir) {
                                return $r->tglambil_bkf >= $databarangopsik->tgl_opfk && $r->tglambil_bkf <= $tgl_akhir;
                            });
                            foreach ($filteredKeluar2 as $barisbkfd)
                            {
                                if ($barisbkfd->tglambil_bkf != $tgl_akhir)
                                {
                                    $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                                }
                            }
                        }
                        else
                        {
                            $tjmlh_bkfd = 0;
                        }

                        $tjmlh_opsik = $barisopsikdetailitem->jmlh_opfkdetitm - $tjmlh_bkfd;

                        $batch[] = [
                            'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $tjmlh_opsik,
                            'hrg_tbm' => $barisopsikdetailitem->hrg_bmf, 'kd_lks' => $lokasi,
                            'user_id' => $user_id, 'jns_tbm' => 1,
                            'created_at' => $now, 'updated_at' => $now,
                        ];
                    }
                }
            }
            else
            {
                $tjmlh_bkfd = 0;
                $keluarRows = $keluarDetailAll[$barisbmf->id_bmf] ?? collect();
                foreach ($keluarRows as $barisbkfd)
                {
                    if ($barisbkfd->tglambil_bkf <= $tgl_akhir)
                    {
                        $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                    }
                }
                $sisa_tbmf = $barisbmf->jmlh_awal_bmf - $tjmlh_bkfd;

                $batch[] = [
                    'kd_brg' => $barisbmf->kd_brg, 'sisa_tbm' => $sisa_tbmf,
                    'hrg_tbm' => $barisbmf->hrg_bmf, 'kd_lks' => $lokasi,
                    'user_id' => $user_id, 'jns_tbm' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('temp_barang_masuk')->insert($chunk);
        }
    }
}