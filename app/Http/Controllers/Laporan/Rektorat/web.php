<?php

use App\Http\Controllers\BarangKeluar\BarangKeluarFakultasKhususCt;
use App\Http\Controllers\BarangKeluar\BarangKeluarKhususCt;
use App\Http\Controllers\BarangKeluar\BarangKeluarRektoratKhususCt;
use App\Http\Controllers\BarangKeluar\BarangKeluarRumahSakitKhususCt;
use App\Http\Controllers\BarangKeluar\BkfnCt;
use App\Http\Controllers\BarangKeluar\BkrnCt;
use App\Http\Controllers\BarangKeluar\BkrsnCt;
use App\Http\Controllers\BarangKeluar\LapBkfnPrintCt;
use App\Http\Controllers\BarangKeluar\LapBkrnPrintCt;
use App\Http\Controllers\BarangMasuk\BarangMasukFakultasKhususCt;
use App\Http\Controllers\BarangMasuk\BarangMasukKhususCt;
use App\Http\Controllers\BarangMasuk\BarangMasukRektoratKhususCt;
use App\Http\Controllers\BarangMasuk\BarangMasukRumahSakitKhususCt;
use App\Http\Controllers\BarangMasuk\BmfpCt;
use App\Http\Controllers\BarangMasuk\BmfsCt;
use App\Http\Controllers\BarangMasuk\BmrpCt;
use App\Http\Controllers\BarangMasuk\BmrsCt;
use App\Http\Controllers\BarangMasuk\BmrspCt;
use App\Http\Controllers\BarangUsang\BarangUsangKhususCt;
use App\Http\Controllers\BerandaCt;
use App\Http\Controllers\CekKodeKosong;
use App\Http\Controllers\Cekpesan;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BarangMasuk\LapBmfsPrintCt;
use App\Http\Controllers\BarangMasuk\LapBmrsPrintCt;
use App\Http\Controllers\BarangMasuk\LapBmrssPrintCt;
use App\Http\Controllers\Laporan\Fakultas\LapOpfPersediaanCt;
use App\Http\Controllers\Laporan\Fakultas\LapOpfPersediaanPrint;
use App\Http\Controllers\Laporan\Fakultas\LapPosisiOpfPersediaanCt;
use App\Http\Controllers\Laporan\Fakultas\LapPosisiOpfPersediaanPrint;
use App\Http\Controllers\Laporan\Rektorat\LapOprPersediaanCt;
use App\Http\Controllers\Laporan\Rektorat\LapOprPersediaanPrint;
use App\Http\Controllers\Laporan\Rektorat\LapPersediaanCt;
use App\Http\Controllers\Laporan\Rektorat\LapPersediaanPrint;
use App\Http\Controllers\Laporan\Rektorat\LapPosisiOprPersediaanCt;
use App\Http\Controllers\Laporan\Rektorat\LapPosisiOprPersediaanPrint;
use App\Http\Controllers\Laporan\Rektorat\LapPosisiPersediaanCt;
use App\Http\Controllers\Laporan\Rektorat\LapPosisiPersediaanPrint;
use App\Http\Controllers\Laporan\RumahSakit\LapOprsPersediaanCt;
use App\Http\Controllers\Laporan\RumahSakit\LapOprsPersediaanPrint;
use App\Http\Controllers\Laporan\RumahSakit\LapPosisiOprsPersediaanCt;
use App\Http\Controllers\Laporan\RumahSakit\LapPosisiOprsPersediaanPrint;
use App\Http\Controllers\MasterData\BarangCt;
use App\Http\Controllers\MasterData\BarangFakultasCt;
use App\Http\Controllers\MasterData\BarangRektoratCt;
use App\Http\Controllers\MasterData\BkpfCt;
use App\Http\Controllers\MasterData\BkprCt;
use App\Http\Controllers\MasterData\BkprsCt;
use App\Http\Controllers\MasterData\FakultasCt;
use App\Http\Controllers\MasterData\FakultasJabatanCt;
use App\Http\Controllers\MasterData\JabfkCt;
use App\Http\Controllers\MasterData\JabpenCt;
use App\Http\Controllers\MasterData\JabpenfkCt;
use App\Http\Controllers\MasterData\JabpenurCt;
use App\Http\Controllers\MasterData\JabpenursCt;
use App\Http\Controllers\MasterData\JaburCt;
use App\Http\Controllers\MasterData\JabursCt;
use App\Http\Controllers\MasterData\JenisSatuanCt;
use App\Http\Controllers\MasterData\KategoriCt;
use App\Http\Controllers\MasterData\KelompokCt;
use App\Http\Controllers\MasterData\PenggunaFakultasCt;
use App\Http\Controllers\MasterData\PenggunaRektoratCt;
use App\Http\Controllers\MasterData\PenggunaRumahSakitCt;
use App\Http\Controllers\MasterData\RefStatusProsesUntukCt;
use App\Http\Controllers\MasterData\SubKategoriCt;
use App\Http\Controllers\MasterData\SubSubKategoriCt;
use App\Http\Controllers\MasterData\UnitRektoratCt;
use App\Http\Controllers\MasterData\UnitRektoratJabatanCt;
use App\Http\Controllers\MasterData\UnitRumahSakitCt;
use App\Http\Controllers\MasterData\UnitRumahSakitJabatanCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkDetCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkPersediaanPrintCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkPrintCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrct;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrDetCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrPrintCt;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsct;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsDetCt;
use App\Http\Controllers\OpnameFisik\Universitas\OpsikUvCt;
use App\Http\Controllers\OpnameFisik\Universitas\OpsikUvPersediaanPrintCt;
use App\Http\Controllers\OpnameFisik\Universitas\OpsikUvPrintCt;
use App\Http\Controllers\OpnameFisik\Universitas\OpsikUvSelisihPrintCt;
use App\Http\Controllers\PengadaanBarang\PengadaanBarangAktifCt;
use App\Http\Controllers\PengadaanBarang\PengadaanBarangAktifDetailCt;
use App\Http\Controllers\Profile;
use App\Http\Controllers\Reklasifikasi\Fakultas\RfCt;
use App\Http\Controllers\Reklasifikasi\Fakultas\RfdCt;
use App\Http\Controllers\Sinkronisasi;
use App\Http\Controllers\Sis_aset\As_BerandaCt;
use App\Http\Controllers\Sis_aset\Laporan\JenisTransaksi\LapJnsTransCt;
use App\Http\Controllers\Sis_aset\Laporan\JenisTransaksi\LapJnsTransPrint;
use App\Http\Controllers\Sis_aset\Laporan\Kondisi\LapKondisiBrgCt;
use App\Http\Controllers\Sis_aset\Laporan\Kondisi\LapKondisiBrgPrint;
use App\Http\Controllers\Sis_aset\Laporan\Rektorat\JenisTransaksi\LapOprJnsTransCt;
use App\Http\Controllers\Sis_aset\Laporan\Rektorat\JenisTransaksi\LapOprJnsTransPrint;
use App\Http\Controllers\Sis_aset\Laporan\Rektorat\Kondisi\LapOprKondisiBrgCt;
use App\Http\Controllers\Sis_aset\Laporan\Rektorat\Kondisi\LapOprKondisiBrgPrint;
use App\Http\Controllers\Sis_aset\MasterData\AsetLokasiFakultasCt;
use App\Http\Controllers\Sis_aset\MasterData\AsetLokasiRektoratCt;
use App\Http\Controllers\Sis_aset\MasterData\AsetRuanganCt;
use App\Http\Controllers\Sis_aset\MasterData\AsetRuanganFakultasCt;
use App\Http\Controllers\Sis_aset\MasterData\AsetRuanganRektoratCt;
use App\Http\Controllers\Sis_aset\MasterData\BarangAsetCt;
use App\Http\Controllers\Sis_aset\MasterData\KategoriAsetCt;
use App\Http\Controllers\Sis_aset\MasterData\KategoriSub2AsetCt;
use App\Http\Controllers\Sis_aset\MasterData\KategoriSub3AsetCt;
use App\Http\Controllers\Sis_aset\MasterData\KategoriSubAsetCt;
use App\Http\Controllers\Sis_aset\Penggunaan\DaftarBarangRuanganFakultasCt;
use App\Http\Controllers\Sis_aset\Penggunaan\DaftarBarangRuanganFakultasRincianCt;
use App\Http\Controllers\Sis_aset\Penggunaan\DaftarBarangRuanganRektoratCt;
use App\Http\Controllers\Sis_aset\Penggunaan\DaftarBarangRuanganRektoratRincianCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\Hapus\PenghapusanHapusRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\HibahKeluar\PenghapusanHibahKeluarRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\ReklasifikasiKeluar\PenghapusanReklasifikasiKeluarFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\ReklasifikasiKeluar\PenghapusanReklasifikasiKeluarFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\ReklasifikasiKeluar\PenghapusanReklasifikasiKeluarRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\ReklasifikasiKeluar\PenghapusanReklasifikasiKeluarRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarKhususCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Penghapusan\TransferKeluar\PenghapusanTransferKeluarRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Hibah\PerolehanHibahRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianFakultasKhususKibCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianFakultasKhususRincianCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianKhususRincianCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianRektoratKhususKibCt;
use App\Http\Controllers\Sis_aset\Perolehan\Pembelian\PerolehanPembelianRektoratKhususRincianCt;
use App\Http\Controllers\Sis_aset\Perolehan\PenyelesaianKdp\PerolehanPenyelesaianKdpFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\PenyelesaianKdp\PerolehanPenyelesaianKdpFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\PenyelesaianLangsung\PerolehanPenyelesaianLangsungFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukFakultasKhususCekCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukKhususCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukRektoratKhususCekCt;
use App\Http\Controllers\Sis_aset\Perolehan\TransferMasuk\PerolehanTransferMasukRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Kondisi\PerubahanKondisiRektoratKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiFakultasKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiFakultasKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiKhususCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiRektoratKhususBarangCt;
use App\Http\Controllers\Sis_aset\Perubahan\Koreksi\PerubahanKoreksiRektoratKhususCt;
use App\Http\Livewire\BarangKeluar\BarangKeluarFakultasKhusus;
use App\Http\Livewire\BarangKeluar\BarangKeluarKhusus;
use App\Http\Livewire\BarangKeluar\BarangKeluarRektoratKhusus;
use App\Http\Livewire\BarangMasuk\BarangMasukFakultasKhusus;
use App\Http\Livewire\BarangMasuk\BarangMasukKhusus;
use App\Http\Livewire\BarangUsang\BarangUsangKhusus;
use App\Http\Livewire\Laporan\Fakultas\LapOpfPersediaan;
use App\Http\Livewire\Laporan\Fakultas\LapPosisiOpfPersediaan;
use App\Http\Livewire\Laporan\Rektorat\LapOprPersediaan;
use App\Http\Livewire\Laporan\Rektorat\LapPersediaan;
use App\Http\Livewire\Laporan\Rektorat\LapPosisiOprPersediaan;
use App\Http\Livewire\Laporan\Rektorat\LapPosisiPersediaan;
use App\Http\Livewire\MasterData\Barang;
use App\Http\Livewire\MasterData\Fakultas;
use App\Http\Livewire\MasterData\FakultasJabatan;
use App\Http\Livewire\MasterData\JenisSatuan;
use App\Http\Livewire\MasterData\Kategori;
use App\Http\Livewire\MasterData\Kelompok;
use App\Http\Livewire\MasterData\PenggunaFakultas;
use App\Http\Livewire\MasterData\PenggunaRektorat;
use App\Http\Livewire\MasterData\RefLancarKategori;
use App\Http\Livewire\MasterData\RefLancarKategoriItem;
use App\Http\Livewire\MasterData\RefLancarKategoriSub;
use App\Http\Livewire\MasterData\RefStatusProsesUntuk;
use App\Http\Livewire\MasterData\SubKategori;
use App\Http\Livewire\MasterData\SubSubKategori;
use App\Http\Livewire\MasterData\UnitRektorat;
use App\Http\Livewire\MasterData\UnitRektoratJabatan;
use App\Http\Livewire\PengadaanBarang\PengadaanBarangAktif;
use App\Http\Livewire\PengadaanBarang\PengadaanBarangAktifDetail;
use App\Http\Livewire\PermintaanBarang\Rektorat\PermintaanBarangRektoratAktif;
use App\Http\Livewire\PermintaanBarang\Rektorat\PermintaanBarangRektoratAktifDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('sinkronisasi-tkel', [Sinkronisasi::class, 't_kel']);
Route::get('sinkronisasi-tskel', [Sinkronisasi::class, 't_skel']);
Route::get('sinkronisasi-tsskel', [Sinkronisasi::class, 't_sskel']);
Route::get('sinkronisasi-tmapbmn12', [Sinkronisasi::class, 't_mapbmn12']);
Route::get('sinkronisasi-tbrg', [Sinkronisasi::class, 't_brg']);
Route::get('sinkronisasi-jenis_brg', [Sinkronisasi::class, 'jenis_brg']);
Route::get('sinkronisasi-stok-brg', [Sinkronisasi::class, 'stok_barang']);

Route::get('sinkronisasi-aset-kategori', [Sinkronisasi::class, 'aset_kategori']);
Route::get('sinkronisasi-aset-kategori-sub', [Sinkronisasi::class, 'aset_kategori_sub']);
Route::get('sinkronisasi-aset-kategori-sub_2', [Sinkronisasi::class, 'aset_kategori_sub_2']);
Route::get('sinkronisasi-aset-kategori-sub_3', [Sinkronisasi::class, 'aset_kategori_sub_3']);
Route::get('sinkronisasi-aset-barang', [Sinkronisasi::class, 'aset_barang']);
Route::get('sinkronisasi-aset-perolehan', [Sinkronisasi::class, 'aset_perolehan']);
Route::get('sinkronisasi-aset-perolehan-spm', [Sinkronisasi::class, 'aset_perolehan_spm']);

Route::get('cek-kode-kosong', [CekKodeKosong::class, 'index']);

Route::get('ajax-crud-datatable', [EmployeeController::class, 'index']);
Route::post('store', [EmployeeController::class, 'store']);
Route::post('edit', [EmployeeController::class, 'edit']);
Route::post('delete', [EmployeeController::class, 'destroy']);

Route::get('/getkecamatan', [EmployeeController::class, 'getKecamatan']);

Route::group(['auth:sanctum', 'verified'], function(){
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('beranda-inventaris', [BerandaCt::class, 'index']);
    Route::post('beranda-inventaris', [BerandaCt::class, 'store']);
    Route::get('beranda-aset', [As_BerandaCt::class, 'index']);
    Route::post('beranda-aset', [As_BerandaCt::class, 'store']);

    Route::get('/dashboard-inventaris', function () {
        return view('dashboard-inventaris');
    })->name('dashboard-inventaris');

    //Route::post('master-satuan-store', [JenisSatuanCt::class, 'store']);

    Route::resource("/profile", Profile::class);
    Route::group(['middleware'=> 'role:1dan2'], function(){  //superadmin dan kepala seksi perlengkapan
        //Route::get('master-satuan', JenisSatuan::class);
        Route::get('master-satuan', [JenisSatuanCt::class, 'index']);
        Route::post('master-satuan-edit', [JenisSatuanCt::class, 'edit']);
        Route::post('master-satuan-store', [JenisSatuanCt::class, 'store']);
        Route::post('master-satuan-delete', [JenisSatuanCt::class, 'destroy']);

        //Route::get('master-kelompok', Kelompok::class);
        Route::get('master-kelompok', [KelompokCt::class, 'index']);
        Route::post('master-kelompok-edit', [KelompokCt::class, 'edit']);
        Route::post('master-kelompok-store', [KelompokCt::class, 'store']);
        Route::post('master-kelompok-delete', [KelompokCt::class, 'destroy']);        

        //Route::get('master-subsubkategori/{kd_skt}', SubSubKategori::class);
        Route::get('master-subsubkategori/{encripted_id}', [SubSubKategoriCt::class, 'index']);
        Route::post('master-subsubkategori-edit', [SubSubKategoriCt::class, 'edit']);
        Route::post('master-subsubkategori-store', [SubSubKategoriCt::class, 'store']);
        Route::post('master-subsubkategori-delete', [SubSubKategoriCt::class, 'destroy']);

        //Route::get('master-kategoribarang/{kd_sskt}', Barang::class);
        Route::get('master-kategoribarang/{encripted_id}', [BarangCt::class, 'index']);
        Route::post('master-kategoribarang-edit', [BarangCt::class, 'edit']);
        Route::post('master-kategoribarang-store', [BarangCt::class, 'store']);
        Route::post('master-kategoribarang-delete', [BarangCt::class, 'destroy']);

        Route::get('master-ref-aset-lancar', RefLancarKategori::class);
        Route::get('master-ref-aset-lancar-subkategori/{id_lk}', RefLancarKategoriSub::class);
        Route::get('master-ref-aset-lancar-item/{id_lks}', RefLancarKategoriItem::class);

        Route::get('jabatan-penandatanganan-fk', [JabfkCt::class, 'index']);
        Route::post('jabatan-penandatanganan-fk-edit', [JabfkCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-fk-store', [JabfkCt::class, 'store']);

        Route::get('jabatan-penandatanganan-ur', [JaburCt::class, 'index']);
        Route::post('jabatan-penandatanganan-ur-edit', [JaburCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-ur-store', [JaburCt::class, 'store']);

        Route::get('jabatan-penandatanganan-urs', [JabursCt::class, 'index']);
        Route::post('jabatan-penandatanganan-urs-edit', [JabursCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-urs-store', [JabursCt::class, 'store']);

        //Route::get('barang-masuk-khusus', BarangMasukKhusus::class);
        Route::get('barang-masuk-khusus', [BarangMasukKhususCt::class, 'index']);
        Route::post('barang-masuk-khusus-store', [BarangMasukKhususCt::class, 'store']);
        Route::post('barang-masuk-khusus-delete', [BarangMasukKhususCt::class, 'destroy']);
        Route::get('barang-masuk-khusus-subkategori', [BarangMasukKhususCt::class, 'getSubkategori']);
        Route::get('barang-masuk-khusus-item', [BarangMasukKhususCt::class, 'getItem']);

        //Route::get('barang-keluar-khusus', BarangKeluarKhusus::class);
        Route::get('barang-keluar-khusus', [BarangKeluarKhususCt::class, 'index']);
        Route::post('barang-keluar-khusus-store', [BarangKeluarKhususCt::class, 'store']);
        Route::post('barang-keluar-khusus-delete', [BarangKeluarKhususCt::class, 'destroy']);
        Route::get('barang-keluar-khusus-subkategori', [BarangKeluarKhususCt::class, 'getSubkategori']);
        Route::get('barang-keluar-khusus-item', [BarangKeluarKhususCt::class, 'getItem']);

        //Route::get('pengadaan-barang-aktif', PengadaanBarangAktif::class);
        Route::get('pengadaan-barang-aktif', [PengadaanBarangAktifCt::class, 'index']);
        Route::post('pengadaan-barang-aktif-edit', [PengadaanBarangAktifCt::class, 'edit']);
        Route::post('pengadaan-barang-aktif-show', [PengadaanBarangAktifCt::class, 'show']);
        Route::post('pengadaan-barang-aktif-store', [PengadaanBarangAktifCt::class, 'store']);
        Route::post('pengadaan-barang-aktif-ajuan', [PengadaanBarangAktifCt::class, 'ajuan']);
        Route::post('pengadaan-barang-aktif-delete', [PengadaanBarangAktifCt::class, 'destroy']);
        Route::get('pengadaan-barang-aktif-history', [PengadaanBarangAktifCt::class, 'getHistory']);

        //Route::get('pengadaan-barang-aktif-detail/{id_pb}', PengadaanBarangAktifDetail::class);    
        Route::get('pengadaan-barang-aktif-detail/{encripted_id}', [PengadaanBarangAktifDetailCt::class, 'index']);
        Route::post('pengadaan-barang-aktif-detail-edit', [PengadaanBarangAktifDetailCt::class, 'edit']);
        Route::post('pengadaan-barang-aktif-detail-store', [PengadaanBarangAktifDetailCt::class, 'store']);
        Route::post('pengadaan-barang-aktif-detail-delete', [PengadaanBarangAktifDetailCt::class, 'destroy']);
        Route::get('pengadaan-barang-aktif-detail-subkategori', [PengadaanBarangAktifDetailCt::class, 'getSubkategori']);
        Route::get('pengadaan-barang-aktif-detail-item', [PengadaanBarangAktifDetailCt::class, 'getItem']);
        Route::post('pengadaan-barang-aktif-detail-validasi', [PengadaanBarangAktifDetailCt::class, 'validasi']);

        //Route::get('barang-usang-khusus', BarangUsangKhusus::class);
        Route::get('barang-usang-khusus', [BarangUsangKhususCt::class, 'index']);
        Route::post('barang-usang-khusus-store', [BarangUsangKhususCt::class, 'store']);
        Route::post('barang-usang-khusus-delete', [BarangUsangKhususCt::class, 'destroy']);
        Route::get('barang-usang-khusus-subkategori', [BarangUsangKhususCt::class, 'getSubkategori']);
        Route::get('barang-usang-khusus-item', [BarangUsangKhususCt::class, 'getItem']);

        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){ 
            //Route::get('master-kategori', Kategori::class);
            Route::get('master-kategori', [KategoriCt::class, 'index']);
            Route::post('master-kategori-edit', [KategoriCt::class, 'edit']);
            Route::post('master-kategori-store', [KategoriCt::class, 'store']);
            Route::post('master-kategori-delete', [KategoriCt::class, 'destroy']);

             //Route::get('master-subkategori/{kd_kt}', SubKategori::class);        
            Route::get('master-subkategori/{encripted_id}', [SubKategoriCt::class, 'index']);
            Route::post('master-subkategori-edit', [SubKategoriCt::class, 'edit']);
            Route::post('master-subkategori-store', [SubKategoriCt::class, 'store']);
            Route::post('master-subkategori-delete', [SubKategoriCt::class, 'destroy']);
        });    

        Route::group(['middleware'=> 'aplikasi:aset'], function(){ 
            Route::get('master-kategori-aset', [KategoriAsetCt::class, 'index']);
            Route::post('master-kategori-aset-edit', [KategoriAsetCt::class, 'edit']);
            Route::post('master-kategori-aset-store', [KategoriAsetCt::class, 'store']);
            Route::post('master-kategori-aset-delete', [KategoriAsetCt::class, 'destroy']);

            Route::get('master-kategori-s-aset/{encripted_id}', [KategoriSubAsetCt::class, 'index']);
            Route::post('master-kategori-s-aset-edit', [KategoriSubAsetCt::class, 'edit']);
            Route::post('master-kategori-s-aset-store', [KategoriSubAsetCt::class, 'store']);
            Route::post('master-kategori-s-aset-delete', [KategoriSubAsetCt::class, 'destroy']);

            Route::get('master-kategori-s2-aset/{encripted_id}', [KategoriSub2AsetCt::class, 'index']);
            Route::post('master-kategori-s2-aset-edit', [KategoriSub2AsetCt::class, 'edit']);
            Route::post('master-kategori-s2-aset-store', [KategoriSub2AsetCt::class, 'store']);
            Route::post('master-kategori-s2-aset-delete', [KategoriSub2AsetCt::class, 'destroy']);

            Route::get('master-kategori-s3-aset/{encripted_id}', [KategoriSub3AsetCt::class, 'index']);
            Route::post('master-kategori-s3-aset-edit', [KategoriSub3AsetCt::class, 'edit']);
            Route::post('master-kategori-s3-aset-store', [KategoriSub3AsetCt::class, 'store']);
            Route::post('master-kategori-s3-aset-delete', [KategoriSub3AsetCt::class, 'destroy']);

            Route::get('master-kategori-barang-aset/{encripted_id}', [BarangAsetCt::class, 'index']);
            Route::post('master-kategori-barang-aset-edit', [BarangAsetCt::class, 'edit']);
            Route::post('master-kategori-barang-aset-store', [BarangAsetCt::class, 'store']);
            Route::post('master-kategori-barang-aset-delete', [BarangAsetCt::class, 'destroy']);

            Route::get('master-lokasi-fakultas-aset', [AsetLokasiFakultasCt::class, 'index']);
            Route::post('master-lokasi-fakultas-aset-edit', [AsetLokasiFakultasCt::class, 'edit']);
            Route::post('master-lokasi-fakultas-aset-store', [AsetLokasiFakultasCt::class, 'store']);
            Route::post('master-lokasi-fakultas-aset-delete', [AsetLokasiFakultasCt::class, 'destroy']);

            Route::get('master-lokasi-rektorat-aset', [AsetLokasiRektoratCt::class, 'index']);
            Route::post('master-lokasi-rektorat-aset-edit', [AsetLokasiRektoratCt::class, 'edit']);
            Route::post('master-lokasi-rektorat-aset-store', [AsetLokasiRektoratCt::class, 'store']);
            Route::post('master-lokasi-rektorat-aset-delete', [AsetLokasiRektoratCt::class, 'destroy']);
        });
        
    });

    Route::group(['middleware'=> 'role:superadmin'], function(){ //role 1 
        //Route::get('master-status-untuk', RefStatusProsesUntuk::class);
        Route::get('master-status-untuk', [RefStatusProsesUntukCt::class, 'index']);
        Route::post('master-status-untuk-edit', [RefStatusProsesUntukCt::class, 'edit']);
        Route::post('master-status-untuk-store', [RefStatusProsesUntukCt::class, 'store']);
        Route::post('master-status-untuk-delete', [RefStatusProsesUntukCt::class, 'destroy']);

        //Route::get('master-fakultas', Fakultas::class);
        Route::get('master-fakultas', [FakultasCt::class, 'index']);
        Route::post('master-fakultas-edit', [FakultasCt::class, 'edit']);
        Route::post('master-fakultas-store', [FakultasCt::class, 'store']);
        Route::post('master-fakultas-delete', [FakultasCt::class, 'destroy']);

        //Route::get('master-fakultas-jabatan/{id_fk}',FakultasJabatan::class);
        Route::get('master-fakultas-jabatan/{encripted_id}', [FakultasJabatanCt::class, 'index']);
        Route::post('master-fakultas-jabatan-edit', [FakultasJabatanCt::class, 'edit']);
        Route::post('master-fakultas-jabatan-store', [FakultasJabatanCt::class, 'store']);
        Route::post('master-fakultas-jabatan-delete', [FakultasJabatanCt::class, 'destroy']);


        //Route::get('master-unitrektorat', UnitRektorat::class);
        Route::get('master-unitrektorat', [UnitRektoratCt::class, 'index']);
        Route::post('master-unitrektorat-edit', [UnitRektoratCt::class, 'edit']);
        Route::post('master-unitrektorat-store', [UnitRektoratCt::class, 'store']);
        Route::post('master-unitrektorat-delete', [UnitRektoratCt::class, 'destroy']);

        //Route::get('master-unitrektorat-jabatan/{id_ur}',UnitRektoratJabatan::class);
        Route::get('master-unitrektorat-jabatan/{encripted_id}', [UnitRektoratJabatanCt::class, 'index']);
        Route::post('master-unitrektorat-jabatan-edit', [UnitRektoratJabatanCt::class, 'edit']);
        Route::post('master-unitrektorat-jabatan-store', [UnitRektoratJabatanCt::class, 'store']);
        Route::post('master-unitrektorat-jabatan-delete', [UnitRektoratJabatanCt::class, 'destroy']);


        Route::get('master-unitrumahsakit', [UnitRumahSakitCt::class, 'index']);
        Route::post('master-unitrumahsakit-edit', [UnitRumahSakitCt::class, 'edit']);
        Route::post('master-unitrumahsakit-store', [UnitRumahSakitCt::class, 'store']);
        Route::post('master-unitrumahsakit-delete', [UnitRumahSakitCt::class, 'destroy']);

        Route::get('master-unitrumahsakit-jabatan/{encripted_id}', [UnitRumahSakitJabatanCt::class, 'index']);
        Route::post('master-unitrumahsakit-jabatan-edit', [UnitRumahSakitJabatanCt::class, 'edit']);
        Route::post('master-unitrumahsakit-jabatan-store', [UnitRumahSakitJabatanCt::class, 'store']);
        Route::post('master-unitrumahsakit-jabatan-delete', [UnitRumahSakitJabatanCt::class, 'destroy']);

        //Route::get('master-pengguna-rektorat', PenggunaRektorat::class);
        Route::get('master-pengguna-rektorat', [PenggunaRektoratCt::class, 'index']);
        Route::post('master-pengguna-rektorat-edit', [PenggunaRektoratCt::class, 'edit']);
        Route::post('master-pengguna-rektorat-store', [PenggunaRektoratCt::class, 'store']);
        Route::post('master-pengguna-rektorat-delete', [PenggunaRektoratCt::class, 'destroy']);
        Route::post('master-pengguna-rektorat-reset', [PenggunaRektoratCt::class, 'reset']);
        Route::get('master-pengguna-rektorat-unitjabatan', [PenggunaRektoratCt::class, 'getUnitjabatan']);

        //Route::get('master-pengguna-fakultas', PenggunaFakultas::class);
        Route::get('master-pengguna-fakultas', [PenggunaFakultasCt::class, 'index']);
        Route::post('master-pengguna-fakultas-edit', [PenggunaFakultasCt::class, 'edit']);
        Route::post('master-pengguna-fakultas-store', [PenggunaFakultasCt::class, 'store']);
        Route::post('master-pengguna-fakultas-delete', [PenggunaFakultasCt::class, 'destroy']);
        Route::post('master-pengguna-fakultas-reset', [PenggunaFakultasCt::class, 'reset']);
        Route::get('master-pengguna-fakultas-jabatan', [PenggunaFakultasCt::class, 'getFakultasjabatan']);

        Route::get('master-pengguna-rumahsakit', [PenggunaRumahSakitCt::class, 'index']);
        Route::post('master-pengguna-rumahsakit-edit', [PenggunaRumahSakitCt::class, 'edit']);
        Route::post('master-pengguna-rumahsakit-store', [PenggunaRumahSakitCt::class, 'store']);
        Route::post('master-pengguna-rumahsakit-delete', [PenggunaRumahSakitCt::class, 'destroy']);
        Route::post('master-pengguna-rumahsakit-reset', [PenggunaRumahSakitCt::class, 'reset']);
        Route::get('master-pengguna-rumahsakit-unitjabatan', [PenggunaRumahSakitCt::class, 'getUnitjabatan']);
    });

    Route::group(['middleware'=> 'role:subdit'], function(){
        Route::group(['middleware'=> 'aplikasi:aset'], function(){
            Route::get('ruangan-aset', [AsetRuanganCt::class, 'index']);

            Route::get('perolehan-pembelian-khusus-aset', [PerolehanPembelianKhususCt::class, 'index']);
            Route::get('perolehan-pembelian-khusus-aset-barang/{encripted_id}', [PerolehanPembelianKhususBarangCt::class, 'index']);
            Route::get('perolehan-pembelian-khusus-aset-rincian/{encripted_id}', [PerolehanPembelianKhususRincianCt::class, 'index']);

            Route::get('perolehan-hibah-khusus-aset', [PerolehanHibahKhususCt::class, 'index']);
            Route::get('perolehan-hibah-khusus-aset-barang/{encripted_id}', [PerolehanHibahKhususBarangCt::class, 'index']);

            Route::get('perolehan-transfermasuk-khusus-aset', [PerolehanTransferMasukKhususCt::class, 'index']);
            Route::get('perolehan-transfermasuk-khusus-aset-barang/{encripted_id}', [PerolehanTransferMasukKhususBarangCt::class, 'index']);

            Route::get('perubahan-kondisi-khusus-aset', [PerubahanKondisiKhususCt::class, 'index']);
            Route::get('perubahan-kondisi-khusus-aset-barang/{encripted_id}', [PerubahanKondisiKhususBarangCt::class, 'index']);

            Route::get('perubahan-koreksi-khusus-aset', [PerubahanKoreksiKhususCt::class, 'index']);
            Route::get('perubahan-koreksi-khusus-aset-barang/{encripted_id}', [PerubahanKoreksiKhususBarangCt::class, 'index']);


            Route::get('penghapusan-hapus-khusus-aset', [PenghapusanHapusKhususCt::class, 'index']);
            Route::get('penghapusan-hapus-khusus-aset-barang/{encripted_id}', [PenghapusanHapusKhususBarangCt::class, 'index']);

            Route::get('penghapusan-hibahkeluar-khusus-aset', [PenghapusanHibahKeluarKhususCt::class, 'index']);
            Route::get('penghapusan-hibahkeluar-khusus-aset-barang/{encripted_id}', [PenghapusanHibahKeluarKhususBarangCt::class, 'index']);

            Route::get('penghapusan-transferkeluar-khusus-aset', [PenghapusanTransferKeluarKhususCt::class, 'index']);
            Route::get('penghapusan-transferkeluar-khusus-aset-barang/{encripted_id}', [PenghapusanTransferKeluarKhususBarangCt::class, 'index']);

            Route::get('lap-trans-aset', [LapJnsTransCt::class, 'index']); 
            Route::post('lap-trans-aset', [LapJnsTransCt::class, 'cek']);
            Route::get('lap-trans-aset-print/{tgl_awal}/{tgl_akhir}/{tercatat}/{id_lokasi}', [LapJnsTransPrint::class, 'index']);

            Route::get('lap-kondisibrg-aset', [LapKondisiBrgCt::class, 'index']); 
            Route::post('lap-kondisibrg-aset', [LapKondisiBrgCt::class, 'cek']);
            Route::get('lap-kondisibrg-aset-print/{tgl_akhir}/{tercatat}/{id_lokasi}', [LapKondisiBrgPrint::class, 'index']);
        });        
    });

    Route::group(['middleware'=> 'role:oprektorat'], function(){

        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){ 
            Route::get('barang-masuk-sp2d-opr-khusus', [BmrsCt::class, 'index']);
            Route::post('barang-masuk-sp2d-opr-khusus-edit', [BmrsCt::class, 'edit']);
            Route::post('barang-masuk-sp2d-opr-khusus-store', [BmrsCt::class, 'store']);
            Route::post('barang-masuk-sp2d-opr-khusus-delete', [BmrsCt::class, 'destroy']);
            Route::post('barang-masuk-sp2d-opr-khusus-validasi', [BmrsCt::class, 'validasi']);
            Route::post('barang-masuk-sp2d-opr-khusus-lap', [BmrsCt::class, 'cek']);
            Route::get('barang-masuk-sp2d-opr-khusus-lap-print/{filter}/{filter2}', [LapBmrsPrintCt::class, 'index']);

            Route::get('barang-masuk-pesanan-opr-khusus', [BmrpCt::class, 'index']);
            Route::post('barang-masuk-pesanan-opr-khusus-edit', [BmrpCt::class, 'edit']);
            Route::post('barang-masuk-pesanan-opr-khusus-store', [BmrpCt::class, 'store']);
            Route::post('barang-masuk-pesanan-opr-khusus-delete', [BmrpCt::class, 'destroy']);
            Route::post('barang-masuk-pesanan-opr-khusus-validasi', [BmrpCt::class, 'validasi']);
            Route::post('barang-masuk-pesanan-opr-khusus-lap', [BmrpCt::class, 'cek']);
            Route::get('barang-masuk-pesanan-opr-khusus-lap-print/{filter}/{filter2}', [LapBmrsPrintCt::class, 'index']);

            Route::get('master-barang-opr', [BarangRektoratCt::class, 'index']);     

            Route::get('barang-masuk-opr-khusus/{encripted_id}', [BarangMasukRektoratKhususCt::class, 'index']);
            Route::post('barang-masuk-opr-khusus-store', [BarangMasukRektoratKhususCt::class, 'store']);
            Route::post('barang-masuk-opr-khusus-delete', [BarangMasukRektoratKhususCt::class, 'destroy']);
            Route::get('barang-masuk-opr-khusus-subkategori', [BarangMasukRektoratKhususCt::class, 'getSubkategori']);
            Route::get('barang-masuk-opr-khusus-item', [BarangMasukRektoratKhususCt::class, 'getItem']);
            Route::post('barang-masuk-opr-khusus-edit', [BarangMasukRektoratKhususCt::class, 'edit']);

            Route::get('barang-keluar-penerima-opr', [BkprCt::class, 'index']);
            Route::post('barang-keluar-penerima-opr-edit', [BkprCt::class, 'edit']);
            Route::post('barang-keluar-penerima-opr-store', [BkprCt::class, 'store']);
            Route::post('barang-keluar-penerima-opr-delete', [BkprCt::class, 'destroy']);

            Route::get('pejabat-penandatanganan-opr', [JabpenurCt::class, 'index']);
            Route::post('pejabat-penandatanganan-opr-edit', [JabpenurCt::class, 'edit']);
            Route::post('pejabat-penandatanganan-opr-store', [JabpenurCt::class, 'store']);
            Route::post('pejabat-penandatanganan-opr-delete', [JabpenurCt::class, 'destroy']);

            Route::get('barang-keluar-nota-opr-khusus', [BkrnCt::class, 'index']);
            Route::post('barang-keluar-nota-opr-khusus-edit', [BkrnCt::class, 'edit']);
            Route::post('barang-keluar-nota-opr-khusus-store', [BkrnCt::class, 'store']);
            Route::post('barang-keluar-nota-opr-khusus-delete', [BkrnCt::class, 'destroy']);
            Route::post('barang-keluar-nota-opr-khusus-validasi', [BkrnCt::class, 'validasi']);
            Route::post('barang-keluar-nota-opr-khusus-lap', [BkrnCt::class, 'cek']);
            Route::get('barang-keluar-nota-opr-khusus-lap-print/{filter}/{filter2}', [LapBkrnPrintCt::class, 'index']);

            Route::get('barang-keluar-opr-khusus/{encripted_id}', [BarangKeluarRektoratKhususCt::class, 'index']);
            Route::post('barang-keluar-opr-khusus-store', [BarangKeluarRektoratKhususCt::class, 'store']);
            Route::post('barang-keluar-opr-khusus-delete', [BarangKeluarRektoratKhususCt::class, 'destroy']);
            Route::get('barang-keluar-opr-khusus-subkategori', [BarangKeluarRektoratKhususCt::class, 'getSubkategori']);
            Route::get('barang-keluar-opr-khusus-item', [BarangKeluarRektoratKhususCt::class, 'getItem']);
            Route::post('barang-keluar-opr-khusus-edit', [BarangKeluarRektoratKhususCt::class, 'edit']);

            Route::get('opsik-opr', [OpsikUrct::class, 'index']);
            Route::post('opsik-opr-edit', [OpsikUrct::class, 'edit']);
            Route::post('opsik-opr-store', [OpsikUrct::class, 'store']);
            Route::post('opsik-opr-delete', [OpsikUrct::class, 'destroy']);
            Route::post('opsik-opr-validasi', [OpsikUrct::class, 'validasi']);
            Route::post('opsik-opr-store-upload-cek', [OpsikUrct::class, 'storeuploadcek']);
            Route::post('opsik-opr-store-upload', [OpsikUrct::class, 'storeupload']);
            Route::get('opsik-opr-lampiran/{filter}', [OpsikUrPrintCt::class, 'index']);

            Route::get('opsik-opr-detail/{encripted_id}', [OpsikUrDetCt::class, 'index']);
            Route::post('opsik-opr-detail-store', [OpsikUrDetCt::class, 'store']);
            Route::post('opsik-opr-detail-delete', [OpsikUrDetCt::class, 'destroy']);
            Route::get('opsik-opr-detail-subkategori', [OpsikUrDetCt::class, 'getSubkategori']);
            Route::get('opsik-opr-detail-item', [OpsikUrDetCt::class, 'getItem']);
            Route::post('opsik-opr-detail-edit', [OpsikUrDetCt::class, 'edit']);
        });    

        Route::group(['middleware'=> 'aplikasi:aset'], function(){ 
            Route::get('perolehan-pembelian-opr-khusus-aset', [PerolehanPembelianRektoratKhususCt::class, 'index']);
            Route::post('perolehan-pembelian-opr-khusus-aset-store', [PerolehanPembelianRektoratKhususCt::class, 'store']);
            Route::post('perolehan-pembelian-opr-khusus-aset-delete', [PerolehanPembelianRektoratKhususCt::class, 'destroy']);
            Route::get('perolehan-pembelian-opr-khusus-aset-subkategori', [PerolehanPembelianRektoratKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-pembelian-opr-khusus-aset-sub2kategori', [PerolehanPembelianRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-pembelian-opr-khusus-aset-sub3kategori', [PerolehanPembelianRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-pembelian-opr-khusus-aset-barang', [PerolehanPembelianRektoratKhususCt::class, 'getBarang']);

            Route::get('perolehan-pembelian-opr-khusus-aset-rincian/{encripted_id}', [PerolehanPembelianRektoratKhususRincianCt::class, 'index']);
            Route::post('perolehan-pembelian-opr-khusus-aset-rincian-edit', [PerolehanPembelianRektoratKhususRincianCt::class, 'edit']);
            Route::post('perolehan-pembelian-opr-khusus-aset-rincian-store', [PerolehanPembelianRektoratKhususRincianCt::class, 'store']);
            Route::post('perolehan-pembelian-opr-khusus-aset-rincian-delete', [PerolehanPembelianRektoratKhususRincianCt::class, 'destroy']);

            Route::get('perolehan-pembelian-opr-khusus-aset-barang/{encripted_id}', [PerolehanPembelianRektoratKhususBarangCt::class, 'index']);

            Route::get('perolehan-pembelian-opr-khusus-aset-kib/{encripted_id}', [PerolehanPembelianRektoratKhususKibCt::class, 'index']);
            Route::get('perolehan-pembelian-opr-khusus-aset-kib', [PerolehanPembelianRektoratKhususKibCt::class, 'getSubkategori']);
            Route::post('perolehan-pembelian-opr-khusus-aset-kib-edit', [PerolehanPembelianRektoratKhususKibCt::class, 'edit']);
            Route::post('perolehan-pembelian-opr-khusus-aset-kib-store', [PerolehanPembelianRektoratKhususKibCt::class, 'store']);

            Route::get('perolehan-hibah-opr-khusus-aset', [PerolehanHibahRektoratKhususCt::class, 'index']);
            Route::post('perolehan-hibah-opr-khusus-aset-store', [PerolehanHibahRektoratKhususCt::class, 'store']);
            Route::post('perolehan-hibah-opr-khusus-aset-delete', [PerolehanHibahRektoratKhususCt::class, 'destroy']);
            Route::get('perolehan-hibah-opr-khusus-aset-subkategori', [PerolehanHibahRektoratKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-hibah-opr-khusus-aset-sub2kategori', [PerolehanHibahRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-hibah-opr-khusus-aset-sub3kategori', [PerolehanHibahRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-hibah-opr-khusus-aset-barang', [PerolehanHibahRektoratKhususCt::class, 'getBarang']);

            Route::get('perolehan-hibah-opr-khusus-aset-barang/{encripted_id}', [PerolehanHibahRektoratKhususBarangCt::class, 'index']);

            Route::get('perolehan-transfermasuk-opr-khusus-aset', [PerolehanTransferMasukRektoratKhususCt::class, 'index']);
            Route::post('perolehan-transfermasuk-opr-khusus-aset-delete', [PerolehanTransferMasukRektoratKhususCt::class, 'destroy']);

            Route::get('perolehan-transfermasuk-opr-khusus-aset-barang/{encripted_id}', [PerolehanTransferMasukRektoratKhususBarangCt::class, 'index']);

            Route::get('perolehan-transfermasuk-opr-khusus-aset-cek', [PerolehanTransferMasukRektoratKhususCekCt::class, 'index']);
            Route::post('perolehan-transfermasuk-opr-khusus-aset-cek-edit', [PerolehanTransferMasukRektoratKhususCekCt::class, 'edit']);
            Route::post('perolehan-transfermasuk-opr-khusus-aset-store', [PerolehanTransferMasukRektoratKhususCekCt::class, 'store']);


            Route::get('perubahan-kondisi-opr-khusus-aset', [PerubahanKondisiRektoratKhususCt::class, 'index']);
            Route::post('perubahan-kondisi-opr-khusus-aset-store', [PerubahanKondisiRektoratKhususCt::class, 'store']);
            Route::post('perubahan-kondisi-opr-khusus-aset-delete', [PerubahanKondisiRektoratKhususCt::class, 'destroy']);
            Route::get('perubahan-kondisi-opr-khusus-aset-subkategori', [PerubahanKondisiRektoratKhususCt::class, 'getSubkategori']);
            Route::get('perubahan-kondisi-opr-khusus-aset-sub2kategori', [PerubahanKondisiRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('perubahan-kondisi-opr-khusus-aset-sub3kategori', [PerubahanKondisiRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('perubahan-kondisi-opr-khusus-aset-barang', [PerubahanKondisiRektoratKhususCt::class, 'getBarang']);

            Route::get('perubahan-kondisi-opr-khusus-aset-barang/{encripted_id}', [PerubahanKondisiRektoratKhususBarangCt::class, 'index']);

            Route::get('perubahan-koreksi-opr-khusus-aset', [PerubahanKoreksiRektoratKhususCt::class, 'index']);
            Route::post('perubahan-koreksi-opr-khusus-aset-store', [PerubahanKoreksiRektoratKhususCt::class, 'store']);
            Route::post('perubahan-koreksi-opr-khusus-aset-delete', [PerubahanKoreksiRektoratKhususCt::class, 'destroy']);
            Route::get('perubahan-koreksi-opr-khusus-aset-subkategori', [PerubahanKoreksiRektoratKhususCt::class, 'getSubkategori']);
            Route::get('perubahan-koreksi-opr-khusus-aset-sub2kategori', [PerubahanKoreksiRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('perubahan-koreksi-opr-khusus-aset-sub3kategori', [PerubahanKoreksiRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('perubahan-koreksi-opr-khusus-aset-barang', [PerubahanKoreksiRektoratKhususCt::class, 'getBarang']);

            Route::get('perubahan-koreksi-opr-khusus-aset-barang/{encripted_id}', [PerubahanKoreksiRektoratKhususBarangCt::class, 'index']);


            Route::get('penghapusan-hapus-opr-khusus-aset', [PenghapusanHapusRektoratKhususCt::class, 'index']);
            Route::post('penghapusan-hapus-opr-khusus-aset-store', [PenghapusanHapusRektoratKhususCt::class, 'store']);
            Route::post('penghapusan-hapus-opr-khusus-aset-delete', [PenghapusanHapusRektoratKhususCt::class, 'destroy']);
            Route::get('penghapusan-hapus-opr-khusus-aset-subkategori', [PenghapusanHapusRektoratKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-hapus-opr-khusus-aset-sub2kategori', [PenghapusanHapusRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-hapus-opr-khusus-aset-sub3kategori', [PenghapusanHapusRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-hapus-opr-khusus-aset-barang', [PenghapusanHapusRektoratKhususCt::class, 'getBarang']);

            Route::get('penghapusan-hapus-opr-khusus-aset-barang/{encripted_id}', [PenghapusanHapusRektoratKhususBarangCt::class, 'index']);

            Route::get('penghapusan-hibahkeluar-opr-khusus-aset', [PenghapusanHibahKeluarRektoratKhususCt::class, 'index']);
            Route::post('penghapusan-hibahkeluar-opr-khusus-aset-store', [PenghapusanHibahKeluarRektoratKhususCt::class, 'store']);
            Route::post('penghapusan-hibahkeluar-opr-khusus-aset-delete', [PenghapusanHibahKeluarRektoratKhususCt::class, 'destroy']);
            Route::get('penghapusan-hibahkeluar-opr-khusus-aset-subkategori', [PenghapusanHibahKeluarRektoratKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-hibahkeluar-opr-khusus-aset-sub2kategori', [PenghapusanHibahKeluarRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-hibahkeluar-opr-khusus-aset-sub3kategori', [PenghapusanHibahKeluarRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-hibahkeluar-opr-khusus-aset-barang', [PenghapusanHibahKeluarRektoratKhususCt::class, 'getBarang']);

            Route::get('penghapusan-hibahkeluar-opr-khusus-aset-barang/{encripted_id}', [PenghapusanHibahKeluarRektoratKhususBarangCt::class, 'index']);

            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'index']);
            Route::post('penghapusan-reklasifikasikeluar-opr-khusus-aset-store', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'store']);
            Route::post('penghapusan-reklasifikasikeluar-opr-khusus-aset-delete', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'destroy']);
            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset-subkategori', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset-sub2kategori', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset-sub3kategori', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset-barang', [PenghapusanReklasifikasiKeluarRektoratKhususCt::class, 'getBarang']);

            Route::get('penghapusan-reklasifikasikeluar-opr-khusus-aset-barang/{encripted_id}', [PenghapusanReklasifikasiKeluarRektoratKhususBarangCt::class, 'index']);

            Route::get('penghapusan-transferkeluar-opr-khusus-aset', [PenghapusanTransferKeluarRektoratKhususCt::class, 'index']);
            Route::post('penghapusan-transferkeluar-opr-khusus-aset-store', [PenghapusanTransferKeluarRektoratKhususCt::class, 'store']);
            Route::post('penghapusan-transferkeluar-opr-khusus-aset-delete', [PenghapusanTransferKeluarRektoratKhususCt::class, 'destroy']);
            Route::get('penghapusan-transferkeluar-opr-khusus-aset-subkategori', [PenghapusanTransferKeluarRektoratKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-transferkeluar-opr-khusus-aset-sub2kategori', [PenghapusanTransferKeluarRektoratKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-transferkeluar-opr-khusus-aset-sub3kategori', [PenghapusanTransferKeluarRektoratKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-transferkeluar-opr-khusus-aset-barang', [PenghapusanTransferKeluarRektoratKhususCt::class, 'getBarang']);

            Route::get('penghapusan-transferkeluar-opr-khusus-aset-barang/{encripted_id}', [PenghapusanTransferKeluarRektoratKhususBarangCt::class, 'index']);

            Route::get('ruangan-rektorat-aset', [AsetRuanganRektoratCt::class, 'index']);
            Route::post('ruangan-rektorat-aset-edit', [AsetRuanganRektoratCt::class, 'edit']);
            Route::post('ruangan-rektorat-aset-store', [AsetRuanganRektoratCt::class, 'store']);
            Route::post('ruangan-rektorat-aset-delete', [AsetRuanganRektoratCt::class, 'destroy']);
            Route::post('ruangan-rektorat-aset-reset', [AsetRuanganRektoratCt::class, 'reset']);

            Route::get('daftar-barang-ruangan-opr-aset', [DaftarBarangRuanganRektoratCt::class, 'index']);
            Route::post('daftar-barang-ruangan-opr-aset-store', [DaftarBarangRuanganRektoratCt::class, 'store']);
            Route::post('daftar-barang-ruangan-opr-aset-delete', [DaftarBarangRuanganRektoratCt::class, 'destroy']);
            Route::get('daftar-barang-ruangan-opr-aset-subkategori', [DaftarBarangRuanganRektoratCt::class, 'getSubkategori']);
            Route::get('daftar-barang-ruangan-opr-aset-sub2kategori', [DaftarBarangRuanganRektoratCt::class, 'getSub2kategori']);
            Route::get('daftar-barang-ruangan-opr-aset-sub3kategori', [DaftarBarangRuanganRektoratCt::class, 'getSub3kategori']);
            Route::get('daftar-barang-ruangan-opr-aset-barang', [DaftarBarangRuanganRektoratCt::class, 'getBarang']);

            Route::get('daftar-barang-ruangan-opr-aset-barang-rincian/{encripted_id}', [DaftarBarangRuanganRektoratRincianCt::class, 'index']); 

            Route::get('lap-trans-opr-aset', [LapOprJnsTransCt::class, 'index']); 
            Route::post('lap-trans-opr-aset', [LapOprJnsTransCt::class, 'cek']);
            Route::get('lap-trans-opr-aset-print/{tgl_awal}/{tgl_akhir}/{tercatat}', [LapOprJnsTransPrint::class, 'index']);

            Route::get('lap-kondisibrg-opr-aset', [LapOprKondisiBrgCt::class, 'index']); 
            Route::post('lap-kondisibrg-opr-aset', [LapOprKondisiBrgCt::class, 'cek']);
            Route::get('lap-kondisibrg-opr-aset-print/{tgl_akhir}/{tercatat}', [LapOprKondisiBrgPrint::class, 'index']);

        });
    });

    Route::group(['middleware'=> 'role:opfakultas'], function(){

        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){ 
            Route::get('master-barang-opf', [BarangFakultasCt::class, 'index']);

            Route::get('barang-masuk-sp2d-opf-khusus', [BmfsCt::class, 'index']);
            Route::post('barang-masuk-sp2d-opf-khusus-edit', [BmfsCt::class, 'edit']);
            Route::post('barang-masuk-sp2d-opf-khusus-store', [BmfsCt::class, 'store']);
            Route::post('barang-masuk-sp2d-opf-khusus-delete', [BmfsCt::class, 'destroy']);
            Route::post('barang-masuk-sp2d-opf-khusus-validasi', [BmfsCt::class, 'validasi']);
            Route::post('barang-masuk-sp2d-opf-khusus-lap', [BmfsCt::class, 'cek']);
            Route::get('barang-masuk-sp2d-opf-khusus-lap-print/{filter}/{filter2}', [LapBmfsPrintCt::class, 'index']);

            Route::get('barang-masuk-pesanan-opf-khusus', [BmfpCt::class, 'index']);
            Route::post('barang-masuk-pesanan-opf-khusus-edit', [BmfpCt::class, 'edit']);
            Route::post('barang-masuk-pesanan-opf-khusus-store', [BmfpCt::class, 'store']);
            Route::post('barang-masuk-pesanan-opf-khusus-delete', [BmfpCt::class, 'destroy']);
            Route::post('barang-masuk-pesanan-opf-khusus-validasi', [BmfpCt::class, 'validasi']);
            Route::post('barang-masuk-pesanan-opf-khusus-lap', [BmfpCt::class, 'cek']);
            Route::get('barang-masuk-pesanan-opf-khusus-lap-print/{filter}/{filter2}', [LapBmfsPrintCt::class, 'index']);

             //Route::get('barang-masuk-opf-khusus', BarangMasukFakultasKhusus::class);
            Route::get('barang-masuk-opf-khusus/{encripted_id}', [BarangMasukFakultasKhususCt::class, 'index']);
            Route::post('barang-masuk-opf-khusus-store', [BarangMasukFakultasKhususCt::class, 'store']);
            Route::post('barang-masuk-opf-khusus-delete', [BarangMasukFakultasKhususCt::class, 'destroy']);
            Route::get('barang-masuk-opf-khusus-subkategori', [BarangMasukFakultasKhususCt::class, 'getSubkategori']);
            Route::get('barang-masuk-opf-khusus-item', [BarangMasukFakultasKhususCt::class, 'getItem']);
            Route::post('barang-masuk-opf-khusus-edit', [BarangMasukFakultasKhususCt::class, 'edit']);

            Route::get('reklasifikasi-jenisbrg-opf-khusus', [RfCt::class, 'index']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-edit', [RfCt::class, 'edit']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-store', [RfCt::class, 'store']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-delete', [RfCt::class, 'destroy']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-validasi', [RfCt::class, 'validasi']);

            Route::get('reklasifikasi-jenisbrg-opf-khusus-detail/{encripted_id}', [RfdCt::class, 'index']);
            Route::get('reklasifikasi-jenisbrg-opf-khusus-detail-subkategori', [RfdCt::class, 'getSubkategori']);
            Route::get('reklasifikasi-jenisbrg-opf-khusus-detail-item', [RfdCt::class, 'getItem']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-edit-detail', [RfdCt::class, 'edit']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-store-detail', [RfdCt::class, 'store']);
            Route::post('reklasifikasi-jenisbrg-opf-khusus-delete-detail', [RfdCt::class, 'destroy']);

            Route::get('barang-keluar-penerima-opf', [BkpfCt::class, 'index']);
            Route::post('barang-keluar-penerima-opf-edit', [BkpfCt::class, 'edit']);
            Route::post('barang-keluar-penerima-opf-store', [BkpfCt::class, 'store']);
            Route::post('barang-keluar-penerima-opf-delete', [BkpfCt::class, 'destroy']);

            Route::get('pejabat-penandatanganan-opf', [JabpenfkCt::class, 'index']);
            Route::post('pejabat-penandatanganan-opf-edit', [JabpenfkCt::class, 'edit']);
            Route::post('pejabat-penandatanganan-opf-store', [JabpenfkCt::class, 'store']);
            Route::post('pejabat-penandatanganan-opf-delete', [JabpenfkCt::class, 'destroy']);

            Route::get('barang-keluar-nota-opf-khusus', [BkfnCt::class, 'index']);
            Route::post('barang-keluar-nota-opf-khusus-edit', [BkfnCt::class, 'edit']);
            Route::post('barang-keluar-nota-opf-khusus-store', [BkfnCt::class, 'store']);
            Route::post('barang-keluar-nota-opf-khusus-delete', [BkfnCt::class, 'destroy']);
            Route::post('barang-keluar-nota-opf-khusus-validasi', [BkfnCt::class, 'validasi']);
            Route::post('barang-keluar-nota-opf-khusus-lap', [BkfnCt::class, 'cek']);
            Route::get('barang-keluar-nota-opf-khusus-lap-print/{filter}/{filter2}', [LapBkfnPrintCt::class, 'index']);

            //Route::get('barang-keluar-opf-khusus', BarangKeluarFakultasKhusus::class);
            Route::get('barang-keluar-opf-khusus/{encripted_id}', [BarangKeluarFakultasKhususCt::class, 'index']);
            Route::post('barang-keluar-opf-khusus-store', [BarangKeluarFakultasKhususCt::class, 'store']);
            Route::post('barang-keluar-opf-khusus-delete', [BarangKeluarFakultasKhususCt::class, 'destroy']);
            Route::get('barang-keluar-opf-khusus-subkategori', [BarangKeluarFakultasKhususCt::class, 'getSubkategori']);
            Route::get('barang-keluar-opf-khusus-item', [BarangKeluarFakultasKhususCt::class, 'getItem']);
            Route::post('barang-keluar-opf-khusus-edit', [BarangKeluarFakultasKhususCt::class, 'edit']);

            Route::get('opsik-opf', [OpsikFkCt::class, 'index']);
            Route::post('opsik-opf-edit', [OpsikFkCt::class, 'edit']);
            Route::post('opsik-opf-store', [OpsikFkCt::class, 'store']);
            Route::post('opsik-opf-delete', [OpsikFkCt::class, 'destroy']);
            Route::post('opsik-opf-validasi', [OpsikFkCt::class, 'validasi']);
            Route::post('opsik-opf-store-upload-cek', [OpsikFkCt::class, 'storeuploadcek']);
            Route::post('opsik-opf-store-upload', [OpsikFkCt::class, 'storeupload']);
            Route::get('opsik-opf-lampiran/{filter}', [OpsikFkPrintCt::class, 'index']);
            Route::get('opsik-opf-persediaan/{filter}', [OpsikFkPersediaanPrintCt::class, 'index']);

            Route::get('opsik-opf-detail/{encripted_id}', [OpsikFkDetCt::class, 'index']);
            Route::post('opsik-opf-detail-store', [OpsikFkDetCt::class, 'store']);
            Route::post('opsik-opf-detail-delete', [OpsikFkDetCt::class, 'destroy']);
            Route::get('opsik-opf-detail-subkategori', [OpsikFkDetCt::class, 'getSubkategori']);
            Route::get('opsik-opf-detail-item', [OpsikFkDetCt::class, 'getItem']);
            Route::post('opsik-opf-detail-edit', [OpsikFkDetCt::class, 'edit']);

            //Route::get('lap-posisi-opf-persediaan', LapPosisiOpfPersediaan::class);
            Route::get('lap-posisi-opf-persediaan', [LapPosisiOpfPersediaanCt::class, 'index']);
            Route::post('lap-posisi-opf-persediaan', [LapPosisiOpfPersediaanCt::class, 'cek']);
            Route::get('lap-posisi-opf-persediaan-print/{filter}', [LapPosisiOpfPersediaanPrint::class, 'index']);

            //Route::get('lap-opf-persediaan', LapOpfPersediaan::class);
            Route::get('lap-opf-persediaan', [LapOpfPersediaanCt::class, 'index']);
            Route::post('lap-opf-persediaan', [LapOpfPersediaanCt::class, 'cek']);
            Route::get('lap-opf-persediaan-print/{filter}', [LapOpfPersediaanPrint::class, 'index']);
        });    

        Route::group(['middleware'=> 'aplikasi:aset'], function(){ 
            Route::get('perolehan-pembelian-opf-khusus-aset', [PerolehanPembelianFakultasKhususCt::class, 'index']);
            Route::post('perolehan-pembelian-opf-khusus-aset-store', [PerolehanPembelianFakultasKhususCt::class, 'store']);
            Route::post('perolehan-pembelian-opf-khusus-aset-delete', [PerolehanPembelianFakultasKhususCt::class, 'destroy']);
            Route::get('perolehan-pembelian-opf-khusus-aset-subkategori', [PerolehanPembelianFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-pembelian-opf-khusus-aset-sub2kategori', [PerolehanPembelianFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-pembelian-opf-khusus-aset-sub3kategori', [PerolehanPembelianFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-pembelian-opf-khusus-aset-barang', [PerolehanPembelianFakultasKhususCt::class, 'getBarang']);

            Route::get('perolehan-pembelian-opf-khusus-aset-rincian/{encripted_id}', [PerolehanPembelianFakultasKhususRincianCt::class, 'index']);
            Route::post('perolehan-pembelian-opf-khusus-aset-rincian-edit', [PerolehanPembelianFakultasKhususRincianCt::class, 'edit']);
            Route::post('perolehan-pembelian-opf-khusus-aset-rincian-store', [PerolehanPembelianFakultasKhususRincianCt::class, 'store']);
            Route::post('perolehan-pembelian-opf-khusus-aset-rincian-delete', [PerolehanPembelianFakultasKhususRincianCt::class, 'destroy']);

            Route::get('perolehan-pembelian-opf-khusus-aset-barang/{encripted_id}', [PerolehanPembelianFakultasKhususBarangCt::class, 'index']);

            Route::get('perolehan-pembelian-opf-khusus-aset-kib/{encripted_id}', [PerolehanPembelianFakultasKhususKibCt::class, 'index']);
            Route::get('perolehan-pembelian-opf-khusus-aset-kib', [PerolehanPembelianFakultasKhususKibCt::class, 'getSubkategori']);
            Route::post('perolehan-pembelian-opf-khusus-aset-kib-edit', [PerolehanPembelianFakultasKhususKibCt::class, 'edit']);
            Route::post('perolehan-pembelian-opf-khusus-aset-kib-store', [PerolehanPembelianFakultasKhususKibCt::class, 'store']);

            Route::get('perolehan-hibah-opf-khusus-aset', [PerolehanHibahFakultasKhususCt::class, 'index']);
            Route::post('perolehan-hibah-opf-khusus-aset-store', [PerolehanHibahFakultasKhususCt::class, 'store']);
            Route::post('perolehan-hibah-opf-khusus-aset-delete', [PerolehanHibahFakultasKhususCt::class, 'destroy']);
            Route::get('perolehan-hibah-opf-khusus-aset-subkategori', [PerolehanHibahFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-hibah-opf-khusus-aset-sub2kategori', [PerolehanHibahFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-hibah-opf-khusus-aset-sub3kategori', [PerolehanHibahFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-hibah-opf-khusus-aset-barang', [PerolehanHibahFakultasKhususCt::class, 'getBarang']);

            Route::get('perolehan-hibah-opf-khusus-aset-barang/{encripted_id}', [PerolehanHibahFakultasKhususBarangCt::class, 'index']);

            Route::get('perolehan-transfermasuk-opf-khusus-aset', [PerolehanTransferMasukFakultasKhususCt::class, 'index']);
            Route::post('perolehan-transfermasuk-opf-khusus-aset-delete', [PerolehanTransferMasukFakultasKhususCt::class, 'destroy']);

            Route::get('perolehan-transfermasuk-opf-khusus-aset-barang/{encripted_id}', [PerolehanTransferMasukFakultasKhususBarangCt::class, 'index']);

            Route::get('perolehan-transfermasuk-opf-khusus-aset-cek', [PerolehanTransferMasukFakultasKhususCekCt::class, 'index']);
            Route::post('perolehan-transfermasuk-opf-khusus-aset-cek-edit', [PerolehanTransferMasukFakultasKhususCekCt::class, 'edit']);
            Route::post('perolehan-transfermasuk-opf-khusus-aset-store', [PerolehanTransferMasukFakultasKhususCekCt::class, 'store']);

            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'index']);
            Route::post('perolehan-penyelesaiankdp-opf-khusus-aset-store', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'store']);
            Route::post('perolehan-penyelesaiankdp-opf-khusus-aset-delete', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'destroy']);
            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset-subkategori', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset-sub2kategori', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset-sub3kategori', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset-barang', [PerolehanPenyelesaianKdpFakultasKhususCt::class, 'getBarang']);

            Route::get('perolehan-penyelesaiankdp-opf-khusus-aset-barang/{encripted_id}', [PerolehanPenyelesaianKdpFakultasKhususBarangCt::class, 'index']);

            Route::get('perolehan-penyelesaianlangsung-opf-khusus-aset', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'index']);
            Route::post('perolehan-penyelesaianlangsung-opf-khusus-aset-store', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'store']);
            Route::post('perolehan-penyelesaianlangsung-opf-khusus-aset-delete', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'destroy']);
            Route::get('perolehan-penyelesaianlangsung-opf-khusus-aset-subkategori', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perolehan-penyelesaianlangsung-opf-khusus-aset-sub2kategori', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perolehan-penyelesaianlangsung-opf-khusus-aset-sub3kategori', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perolehan-penyelesaianlangsung-opf-khusus-aset-barang', [PerolehanPenyelesaianLangsungFakultasKhususCt::class, 'getBarang']);


            Route::get('perubahan-kondisi-opf-khusus-aset', [PerubahanKondisiFakultasKhususCt::class, 'index']);
            Route::post('perubahan-kondisi-opf-khusus-aset-store', [PerubahanKondisiFakultasKhususCt::class, 'store']);
            Route::post('perubahan-kondisi-opf-khusus-aset-delete', [PerubahanKondisiFakultasKhususCt::class, 'destroy']);
            Route::get('perubahan-kondisi-opf-khusus-aset-subkategori', [PerubahanKondisiFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perubahan-kondisi-opf-khusus-aset-sub2kategori', [PerubahanKondisiFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perubahan-kondisi-opf-khusus-aset-sub3kategori', [PerubahanKondisiFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perubahan-kondisi-opf-khusus-aset-barang', [PerubahanKondisiFakultasKhususCt::class, 'getBarang']);

            Route::get('perubahan-kondisi-opf-khusus-aset-barang/{encripted_id}', [PerubahanKondisiFakultasKhususBarangCt::class, 'index']);

            Route::get('perubahan-koreksi-opf-khusus-aset', [PerubahanKoreksiFakultasKhususCt::class, 'index']);
            Route::post('perubahan-koreksi-opf-khusus-aset-store', [PerubahanKoreksiFakultasKhususCt::class, 'store']);
            Route::post('perubahan-koreksi-opf-khusus-aset-delete', [PerubahanKoreksiFakultasKhususCt::class, 'destroy']);
            Route::get('perubahan-koreksi-opf-khusus-aset-subkategori', [PerubahanKoreksiFakultasKhususCt::class, 'getSubkategori']);
            Route::get('perubahan-koreksi-opf-khusus-aset-sub2kategori', [PerubahanKoreksiFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('perubahan-koreksi-opf-khusus-aset-sub3kategori', [PerubahanKoreksiFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('perubahan-koreksi-opf-khusus-aset-barang', [PerubahanKoreksiFakultasKhususCt::class, 'getBarang']);

            Route::get('perubahan-koreksi-opf-khusus-aset-barang/{encripted_id}', [PerubahanKoreksiFakultasKhususBarangCt::class, 'index']);

            Route::get('penghapusan-hapus-opf-khusus-aset', [PenghapusanHapusFakultasKhususCt::class, 'index']);
            Route::post('penghapusan-hapus-opf-khusus-aset-store', [PenghapusanHapusFakultasKhususCt::class, 'store']);
            Route::post('penghapusan-hapus-opf-khusus-aset-delete', [PenghapusanHapusFakultasKhususCt::class, 'destroy']);
            Route::get('penghapusan-hapus-opf-khusus-aset-subkategori', [PenghapusanHapusFakultasKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-hapus-opf-khusus-aset-sub2kategori', [PenghapusanHapusFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-hapus-opf-khusus-aset-sub3kategori', [PenghapusanHapusFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-hapus-opf-khusus-aset-barang', [PenghapusanHapusFakultasKhususCt::class, 'getBarang']);

            Route::get('penghapusan-hapus-opf-khusus-aset-barang/{encripted_id}', [PenghapusanHapusFakultasKhususBarangCt::class, 'index']);

            Route::get('penghapusan-hibahkeluar-opf-khusus-aset', [PenghapusanHibahKeluarFakultasKhususCt::class, 'index']);
            Route::post('penghapusan-hibahkeluar-opf-khusus-aset-store', [PenghapusanHibahKeluarFakultasKhususCt::class, 'store']);
            Route::post('penghapusan-hibahkeluar-opf-khusus-aset-delete', [PenghapusanHibahKeluarFakultasKhususCt::class, 'destroy']);
            Route::get('penghapusan-hibahkeluar-opf-khusus-aset-subkategori', [PenghapusanHibahKeluarFakultasKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-hibahkeluar-opf-khusus-aset-sub2kategori', [PenghapusanHibahKeluarFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-hibahkeluar-opf-khusus-aset-sub3kategori', [PenghapusanHibahKeluarFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-hibahkeluar-opf-khusus-aset-barang', [PenghapusanHibahKeluarFakultasKhususCt::class, 'getBarang']);

            Route::get('penghapusan-hibahkeluar-opf-khusus-aset-barang/{encripted_id}', [PenghapusanHibahKeluarFakultasKhususBarangCt::class, 'index']);

            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'index']);
            Route::post('penghapusan-reklasifikasikeluar-opf-khusus-aset-store', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'store']);
            Route::post('penghapusan-reklasifikasikeluar-opf-khusus-aset-delete', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'destroy']);
            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset-subkategori', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset-sub2kategori', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset-sub3kategori', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset-barang', [PenghapusanReklasifikasiKeluarFakultasKhususCt::class, 'getBarang']);

            Route::get('penghapusan-reklasifikasikeluar-opf-khusus-aset-barang/{encripted_id}', [PenghapusanReklasifikasiKeluarFakultasKhususBarangCt::class, 'index']);

            Route::get('penghapusan-transferkeluar-opf-khusus-aset', [PenghapusanTransferKeluarFakultasKhususCt::class, 'index']);
            Route::post('penghapusan-transferkeluar-opf-khusus-aset-store', [PenghapusanTransferKeluarFakultasKhususCt::class, 'store']);
            Route::post('penghapusan-transferkeluar-opf-khusus-aset-delete', [PenghapusanTransferKeluarFakultasKhususCt::class, 'destroy']);
            Route::get('penghapusan-transferkeluar-opf-khusus-aset-subkategori', [PenghapusanTransferKeluarFakultasKhususCt::class, 'getSubkategori']);
            Route::get('penghapusan-transferkeluar-opf-khusus-aset-sub2kategori', [PenghapusanTransferKeluarFakultasKhususCt::class, 'getSub2kategori']);
            Route::get('penghapusan-transferkeluar-opf-khusus-aset-sub3kategori', [PenghapusanTransferKeluarFakultasKhususCt::class, 'getSub3kategori']);
            Route::get('penghapusan-transferkeluar-opf-khusus-aset-barang', [PenghapusanTransferKeluarFakultasKhususCt::class, 'getBarang']);

            Route::get('penghapusan-transferkeluar-opf-khusus-aset-barang/{encripted_id}', [PenghapusanTransferKeluarFakultasKhususBarangCt::class, 'index']);



            Route::get('ruangan-fakultas-aset', [AsetRuanganFakultasCt::class, 'index']);
            Route::post('ruangan-fakultas-aset-edit', [AsetRuanganFakultasCt::class, 'edit']);
            Route::post('ruangan-fakultas-aset-store', [AsetRuanganFakultasCt::class, 'store']);
            Route::post('ruangan-fakultas-aset-delete', [AsetRuanganFakultasCt::class, 'destroy']);
            Route::post('ruangan-fakultas-aset-reset', [AsetRuanganFakultasCt::class, 'reset']);

            Route::get('daftar-barang-ruangan-opf-aset', [DaftarBarangRuanganFakultasCt::class, 'index']);
            Route::post('daftar-barang-ruangan-opf-aset-store', [DaftarBarangRuanganFakultasCt::class, 'store']);
            Route::post('daftar-barang-ruangan-opf-aset-delete', [DaftarBarangRuanganFakultasCt::class, 'destroy']);
            Route::get('daftar-barang-ruangan-opf-aset-subkategori', [DaftarBarangRuanganFakultasCt::class, 'getSubkategori']);
            Route::get('daftar-barang-ruangan-opf-aset-sub2kategori', [DaftarBarangRuanganFakultasCt::class, 'getSub2kategori']);
            Route::get('daftar-barang-ruangan-opf-aset-sub3kategori', [DaftarBarangRuanganFakultasCt::class, 'getSub3kategori']);
            Route::get('daftar-barang-ruangan-opf-aset-barang', [DaftarBarangRuanganFakultasCt::class, 'getBarang']);


            Route::get('daftar-barang-ruangan-opf-aset-barang-rincian/{encripted_id}', [DaftarBarangRuanganFakultasRincianCt::class, 'index']); 
            //Route::get('daftar-barang-ruangan-opf-aset-barang-rincian/{encripted_id}', [DaftarBarangRuanganFakultasBarangRincianCt::class, 'index']);
            
        });
    });

    Route::group(['middleware'=> 'role:oprumahsakit'], function(){
        Route::get('barang-masuk-sp2d-opr-khusus', [BmrsCt::class, 'index']);
        Route::post('barang-masuk-sp2d-opr-khusus-edit', [BmrsCt::class, 'edit']);
        Route::post('barang-masuk-sp2d-opr-khusus-store', [BmrsCt::class, 'store']);
        Route::post('barang-masuk-sp2d-opr-khusus-delete', [BmrsCt::class, 'destroy']);
        Route::post('barang-masuk-sp2d-opr-khusus-validasi', [BmrsCt::class, 'validasi']);
        Route::post('barang-masuk-sp2d-opr-khusus-lap', [BmrsCt::class, 'cek']);
        Route::get('barang-masuk-sp2d-opr-khusus-lap-print/{filter}/{filter2}', [LapBmrsPrintCt::class, 'index']);

        Route::get('barang-masuk-pesanan-oprs-khusus', [BmrspCt::class, 'index']);
        Route::post('barang-masuk-pesanan-oprs-khusus-edit', [BmrspCt::class, 'edit']);
        Route::post('barang-masuk-pesanan-oprs-khusus-store', [BmrspCt::class, 'store']);
        Route::post('barang-masuk-pesanan-oprs-khusus-delete', [BmrspCt::class, 'destroy']);
        Route::post('barang-masuk-pesanan-oprs-khusus-validasi', [BmrspCt::class, 'validasi']);
        Route::post('barang-masuk-pesanan-oprs-khusus-lap', [BmrspCt::class, 'cek']);
        Route::get('barang-masuk-pesanan-oprs-khusus-lap-print/{filter}/{filter2}', [LapBmrssPrintCt::class, 'index']);

        Route::get('master-barang-opr', [BarangRektoratCt::class, 'index']);     

        Route::get('barang-masuk-oprs-khusus/{encripted_id}', [BarangMasukRumahSakitKhususCt::class, 'index']);
        Route::post('barang-masuk-oprs-khusus-store', [BarangMasukRumahSakitKhususCt::class, 'store']);
        Route::post('barang-masuk-oprs-khusus-delete', [BarangMasukRumahSakitKhususCt::class, 'destroy']);
        Route::get('barang-masuk-oprs-khusus-subkategori', [BarangMasukRumahSakitKhususCt::class, 'getSubkategori']);
        Route::get('barang-masuk-oprs-khusus-item', [BarangMasukRumahSakitKhususCt::class, 'getItem']);
        Route::post('barang-masuk-oprs-khusus-edit', [BarangMasukRumahSakitKhususCt::class, 'edit']);


        Route::get('barang-keluar-penerima-oprs', [BkprsCt::class, 'index']);
        Route::post('barang-keluar-penerima-oprs-edit', [BkprsCt::class, 'edit']);
        Route::post('barang-keluar-penerima-oprs-store', [BkprsCt::class, 'store']);
        Route::post('barang-keluar-penerima-oprs-delete', [BkprsCt::class, 'destroy']);

        Route::get('pejabat-penandatanganan-oprs', [JabpenursCt::class, 'index']);
        Route::post('pejabat-penandatanganan-oprs-edit', [JabpenursCt::class, 'edit']);
        Route::post('pejabat-penandatanganan-oprs-store', [JabpenursCt::class, 'store']);
        Route::post('pejabat-penandatanganan-oprs-delete', [JabpenursCt::class, 'destroy']);

        Route::get('barang-keluar-nota-oprs-khusus', [BkrsnCt::class, 'index']);
        Route::post('barang-keluar-nota-oprs-khusus-edit', [BkrsnCt::class, 'edit']);
        Route::post('barang-keluar-nota-oprs-khusus-store', [BkrsnCt::class, 'store']);
        Route::post('barang-keluar-nota-oprs-khusus-delete', [BkrsnCt::class, 'destroy']);
        Route::post('barang-keluar-nota-oprs-khusus-validasi', [BkrsnCt::class, 'validasi']);
        Route::post('barang-keluar-nota-oprs-khusus-lap', [BkrsnCt::class, 'cek']);
        Route::get('barang-keluar-nota-oprs-khusus-lap-print/{filter}/{filter2}', [LapBkrnPrintCt::class, 'index']);

        Route::get('barang-keluar-oprs-khusus/{encripted_id}', [BarangKeluarRumahSakitKhususCt::class, 'index']);
        Route::post('barang-keluar-oprs-khusus-store', [BarangKeluarRumahSakitKhususCt::class, 'store']);
        Route::post('barang-keluar-oprs-khusus-delete', [BarangKeluarRumahSakitKhususCt::class, 'destroy']);
        Route::get('barang-keluar-oprs-khusus-subkategori', [BarangKeluarRumahSakitKhususCt::class, 'getSubkategori']);
        Route::get('barang-keluar-oprs-khusus-item', [BarangKeluarRumahSakitKhususCt::class, 'getItem']);
        Route::post('barang-keluar-oprs-khusus-edit', [BarangKeluarRumahSakitKhususCt::class, 'edit']);


        Route::get('opsik-oprs', [OpsikUrsct::class, 'index']);
        Route::post('opsik-oprs-edit', [OpsikUrsct::class, 'edit']);
        Route::post('opsik-oprs-store', [OpsikUrsct::class, 'store']);
        Route::post('opsik-oprs-delete', [OpsikUrsct::class, 'destroy']);
        Route::post('opsik-oprs-validasi', [OpsikUrsct::class, 'validasi']);
        Route::post('opsik-oprs-store-upload-cek', [OpsikUrsct::class, 'storeuploadcek']);
        Route::post('opsik-oprs-store-upload', [OpsikUrsct::class, 'storeupload']);
        Route::get('opsik-oprs-lampiran/{filter}', [OpsikUrsct::class, 'index']);

        Route::get('opsik-oprs-detail/{encripted_id}', [OpsikUrsDetCt::class, 'index']);
        Route::post('opsik-oprs-detail-store', [OpsikUrsDetCt::class, 'store']);
        Route::post('opsik-oprs-detail-delete', [OpsikUrsDetCt::class, 'destroy']);
        Route::get('opsik-oprs-detail-subkategori', [OpsikUrsDetCt::class, 'getSubkategori']);
        Route::get('opsik-oprs-detail-item', [OpsikUrsDetCt::class, 'getItem']);
        Route::post('opsik-oprs-detail-edit', [OpsikUrsDetCt::class, 'edit']);

        Route::get('lap-posisi-oprs-persediaan', [LapPosisiOprsPersediaanCt::class, 'index']);
        Route::post('lap-posisi-oprs-persediaan', [LapPosisiOprsPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-oprs-persediaan-print/{filter}', [LapPosisiOprsPersediaanPrint::class, 'index']);

        Route::get('lap-oprs-persediaan', [LapOprsPersediaanCt::class, 'index']);
        Route::post('lap-oprs-persediaan', [LapOprsPersediaanCt::class, 'cek']);
        Route::get('lap-oprs-persediaan-print/{filter}', [LapOprsPersediaanPrint::class, 'index']);

    });

    Route::group(['middleware'=> 'role:5dan6'], function(){ //role 1 
        Route::get('permintaan-barang-rektorat-aktif', PermintaanBarangRektoratAktif::class);
        Route::get('permintaan-barang-rektorat-aktif-detail/{id_pbr}', PermintaanBarangRektoratAktifDetail::class);

        //Route::get('barang-keluar-opr-khusus', BarangKeluarRektoratKhusus::class);
        Route::get('barang-keluar-opr-khusus', [BarangKeluarRektoratKhususCt::class, 'index']);
        Route::post('barang-keluar-opr-khusus-store', [BarangKeluarRektoratKhususCt::class, 'store']);
        Route::post('barang-keluar-opr-khusus-delete', [BarangKeluarRektoratKhususCt::class, 'destroy']);
        Route::get('barang-keluar-opr-khusus-subkategori', [BarangKeluarRektoratKhususCt::class, 'getSubkategori']);
        Route::get('barang-keluar-opr-khusus-item', [BarangKeluarRektoratKhususCt::class, 'getItem']);

        //Route::get('lap-posisi-opr-persediaan', LapPosisiOprPersediaan::class);
        Route::get('lap-posisi-opr-persediaan', [LapPosisiOprPersediaanCt::class, 'index']);
        Route::post('lap-posisi-opr-persediaan', [LapPosisiOprPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-opr-persediaan-print/{filter}', [LapPosisiOprPersediaanPrint::class, 'index']);

        //Route::get('lap-opr-persediaan', LapOprPersediaan::class);
        Route::get('lap-opr-persediaan', [LapOprPersediaanCt::class, 'index']);
        Route::post('lap-opr-persediaan', [LapOprPersediaanCt::class, 'cek']);
        Route::get('lap-opr-persediaan-print/{filter}', [LapOprPersediaanPrint::class, 'index']);
    });

    Route::group(['middleware'=> 'role:2dan9'], function(){
        //Route::get('lap-posisi-persediaan', LapPosisiPersediaan::class);
        Route::get('lap-posisi-persediaan', [LapPosisiPersediaanCt::class, 'index']);
        Route::post('lap-posisi-persediaan', [LapPosisiPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-persediaan-print/{filter}/{lokasi}', [LapPosisiPersediaanPrint::class, 'index']);

        //Route::get('lap-persediaan', LapPersediaan::class);
        Route::get('lap-persediaan', [LapPersediaanCt::class, 'index']);
        Route::post('lap-persediaan', [LapPersediaanCt::class, 'cek']);
        Route::get('lap-persediaan-print/{filter}/{lokasi}', [LapPersediaanPrint::class, 'index']);

        Route::get('opsik-opu', [OpsikUvCt::class, 'index']);
        Route::get('opsik-opu-keseluruhan/{filter}', [OpsikUvPrintCt::class, 'index']);
        Route::get('opsik-opu-selisih/{filter}', [OpsikUvSelisihPrintCt::class, 'index']);
        Route::get('opsik-opu-persediaan/{filter}', [OpsikUvPersediaanPrintCt::class, 'index']);
    });
});