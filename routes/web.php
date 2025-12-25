<?php

use App\Http\Controllers\BarangKeluar\BarangKeluarFakultasKhususCt;
use App\Http\Controllers\BarangKeluar\BarangKeluarRektoratKhususCt;
use App\Http\Controllers\BarangKeluar\BarangKeluarRumahSakitKhususCt;
use App\Http\Controllers\BarangKeluar\BkfnCt;
use App\Http\Controllers\BarangKeluar\BkrnCt;
use App\Http\Controllers\BarangKeluar\BkrsnCt;
use App\Http\Controllers\BarangKeluar\LapBkfnPrintCt;
use App\Http\Controllers\BarangKeluar\LapBkrnPrintCt;
use App\Http\Controllers\BarangMasuk\BarangMasukFakultasKhususCt;
use App\Http\Controllers\BarangMasuk\BarangMasukRektoratKhususCt;
use App\Http\Controllers\BarangMasuk\BarangMasukRumahSakitKhususCt;
use App\Http\Controllers\BarangMasuk\BmfpCt;
use App\Http\Controllers\BarangMasuk\BmrpCt;
use App\Http\Controllers\BarangMasuk\BmrspCt;
use App\Http\Controllers\BarangMasuk\LapBmfsPrintCt;
use App\Http\Controllers\BarangMasuk\LapBmrsPrintCt;
use App\Http\Controllers\BarangMasuk\LapBmrssPrintCt;
use App\Http\Controllers\BerandaCt;
use App\Http\Controllers\CekSisa;
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
use App\Http\Controllers\MappingCt;
use App\Http\Controllers\MasterData\BarangCt;
use App\Http\Controllers\MasterData\BarangFakultasCt;
use App\Http\Controllers\MasterData\BarangRektoratCt;
use App\Http\Controllers\MasterData\BkprCt;
use App\Http\Controllers\MasterData\BkpfCt;
use App\Http\Controllers\MasterData\BkprsCt;
use App\Http\Controllers\MasterData\FakultasCt;
use App\Http\Controllers\MasterData\FakultasJabatanCt;
use App\Http\Controllers\MasterData\JabfkCt;
use App\Http\Controllers\MasterData\JabpenfkCt;
use App\Http\Controllers\MasterData\JabpenurCt;
use App\Http\Controllers\MasterData\JabpenursCt;
use App\Http\Controllers\MasterData\JabpenuuCt;
use App\Http\Controllers\MasterData\JaburCt;
use App\Http\Controllers\MasterData\JabursCt;
use App\Http\Controllers\MasterData\JabuuCt;
use App\Http\Controllers\MasterData\JenisSatuanCt;
use App\Http\Controllers\MasterData\KategoriCt;
use App\Http\Controllers\MasterData\MakCt;
use App\Http\Controllers\MasterData\PenggunaFakultasCt;
use App\Http\Controllers\MasterData\PenggunaRektoratCt;
use App\Http\Controllers\MasterData\PenggunaRumahSakitCt;
use App\Http\Controllers\MasterData\UnitRektoratCt;
use App\Http\Controllers\MasterData\UnitRektoratJabatanCt;
use App\Http\Controllers\MasterData\UnitRumahSakitCt;
use App\Http\Controllers\MasterData\UnitRumahSakitJabatanCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkDetCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkPersediaanPrintCt;
use App\Http\Controllers\OpnameFisik\Fakultas\OpsikFkPrintCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrDetCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrPersediaanPrintCt;
use App\Http\Controllers\OpnameFisik\Rektorat\OpsikUrPrintCt;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsCt;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsDetCt;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsPersediaanPrintCt;
use App\Http\Controllers\OpnameFisik\RumahSakit\OpsikUrsPrintCt;
use App\Http\Controllers\Perbaikan;
use App\Http\Controllers\PerbaikanCt;
use App\Http\Controllers\PermintaanBarang\PbfCt;
use App\Http\Controllers\PermintaanBarang\PbfdCt;
use App\Http\Controllers\PermintaanBarang\PbfdkaCt;
use App\Http\Controllers\PermintaanBarang\PbfdKgCt;
use App\Http\Controllers\PermintaanBarang\PbfkaCt;
use App\Http\Controllers\PermintaanBarang\PbfKgCt;
use App\Http\Controllers\PermintaanBarang\PbflCt;
use App\Http\Controllers\PermintaanBarang\PbflKaCt;
use App\Http\Controllers\PermintaanBarang\PbflKgCt;
use App\Http\Controllers\PermintaanBarang\PbrCt;
use App\Http\Controllers\PermintaanBarang\PbrdCt;
use App\Http\Controllers\PermintaanBarang\PbrdKaCt;
use App\Http\Controllers\PermintaanBarang\PbrdKgCt;
use App\Http\Controllers\PermintaanBarang\PbrKaCt;
use App\Http\Controllers\PermintaanBarang\PbrKgCt;
use App\Http\Controllers\PermintaanBarang\PbrlCt;
use App\Http\Controllers\PermintaanBarang\PbrlKaCt;
use App\Http\Controllers\PermintaanBarang\PbrlKgCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfdkaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfdKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfdSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfkaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbflKaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbflKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbflSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbfSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrdKaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrdKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrdSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrKaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrlKaSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrlKgSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrlSpCt;
use App\Http\Controllers\PermintaanBarang\SelesaiProses\PbrSpCt;
use App\Http\Controllers\Profile;
use App\Http\Controllers\Sis_aset\As_BerandaCt;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('mapping', [MappingCt::class, 'index']);
Route::get('perbaikanct/{id_opfk}/{kunci}', [PerbaikanCt::class, 'index']);
Route::get('perbaikan/{id_fk}/{tgl_akhir}/{kunci}', [Perbaikan::class, 'index']);
Route::get('perbaikan_cek', [Perbaikan::class, 'cek']);

Route::get('cek_sisa/{id_fk}/{user_id}/{lokasi}', [CekSisa::class, 'index']);

Route::get('perbaikan_sisa/{id_opfk}/{id_fk}/{kunci}', [Perbaikan::class, 'perbaikan_sisa']);
Route::get('perbaikan_cek_barang_masuk_keluar', [Perbaikan::class, 'perbaikan_cek_barang_masuk_keluar']);

Route::get('perbaikan_sisa_rektorat', [Perbaikan::class, 'perbaikan_sisa_rektorat']);
Route::get('perbaikan_rektorat', [Perbaikan::class, 'index_rektorat']);

Route::get('perbaikan_sisa_rumah_sakit', [Perbaikan::class, 'perbaikan_sisa_rumah_sakit']);
Route::get('perbaikan_rumah_sakit', [Perbaikan::class, 'index_rumah_sakit']);

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

    Route::resource("/profile", Profile::class);
    Route::group(['middleware'=> 'role:1dan2'], function(){  //superadmin dan kepala seksi perlengkapan

    });

    Route::group(['middleware'=> 'role:1dan9dan12dan14'], function(){
        //Route::get('lap-posisi-persediaan', LapPosisiPersediaan::class);
        Route::get('lap-posisi-persediaan', [LapPosisiPersediaanCt::class, 'index']);
        Route::post('lap-posisi-persediaan', [LapPosisiPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-persediaan-print/{filter}/{lokasi}', [LapPosisiPersediaanPrint::class, 'index']);

        Route::get('lap-persediaan', [LapPersediaanCt::class, 'index']);
        Route::post('lap-persediaan', [LapPersediaanCt::class, 'cek']);
        Route::get('lap-persediaan-print/{filter}/{lokasi}', [LapPersediaanPrint::class, 'index']);
    });

    Route::group(['middleware'=> 'role:7dan8'], function(){  
        //Route::get('lap-posisi-opf-persediaan', LapPosisiOpfPersediaan::class);
        Route::get('lap-posisi-opf-persediaan', [LapPosisiOpfPersediaanCt::class, 'index']);
        Route::post('lap-posisi-opf-persediaan', [LapPosisiOpfPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-opf-persediaan-print/{filter}', [LapPosisiOpfPersediaanPrint::class, 'index']);

        //Route::get('lap-opf-persediaan', LapOpfPersediaan::class);
        Route::get('lap-opf-persediaan', [LapOpfPersediaanCt::class, 'index']);
        Route::post('lap-opf-persediaan', [LapOpfPersediaanCt::class, 'cek']);
        Route::get('lap-opf-persediaan-print/{filter}', [LapOpfPersediaanPrint::class, 'index']);

    });

    Route::group(['middleware'=> 'role:5dan6dan13'], function(){ 
        //Route::get('lap-posisi-opr-persediaan', LapPosisiOprPersediaan::class);
        Route::get('lap-posisi-opr-persediaan', [LapPosisiOprPersediaanCt::class, 'index']);
        Route::post('lap-posisi-opr-persediaan', [LapPosisiOprPersediaanCt::class, 'cek']);
        Route::get('lap-posisi-opr-persediaan-print/{filter}', [LapPosisiOprPersediaanPrint::class, 'index']);

        //Route::get('lap-opr-persediaan', LapOprPersediaan::class);
        Route::get('lap-opr-persediaan', [LapOprPersediaanCt::class, 'index']);
        Route::post('lap-opr-persediaan', [LapOprPersediaanCt::class, 'cek']);
        Route::get('lap-opr-persediaan-print/{filter}', [LapOprPersediaanPrint::class, 'index']);
    });

    Route::group(['middleware'=> 'role:superadmin'], function(){ //role 1 
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

        Route::get('master-unitrumahsakit', [UnitRumahSakitCt::class, 'index']);
        Route::post('master-unitrumahsakit-edit', [UnitRumahSakitCt::class, 'edit']);
        Route::post('master-unitrumahsakit-store', [UnitRumahSakitCt::class, 'store']);
        Route::post('master-unitrumahsakit-delete', [UnitRumahSakitCt::class, 'destroy']);

        Route::get('master-unitrumahsakit-jabatan/{encripted_id}', [UnitRumahSakitJabatanCt::class, 'index']);
        Route::post('master-unitrumahsakit-jabatan-edit', [UnitRumahSakitJabatanCt::class, 'edit']);
        Route::post('master-unitrumahsakit-jabatan-store', [UnitRumahSakitJabatanCt::class, 'store']);
        Route::post('master-unitrumahsakit-jabatan-delete', [UnitRumahSakitJabatanCt::class, 'destroy']);

        Route::get('master-mata-anggaran', [MakCt::class, 'index']);
        Route::post('master-mata-anggaran-edit', [MakCt::class, 'edit']);
        Route::post('master-mata-anggaran-store', [MakCt::class, 'store']);
        Route::post('master-mata-anggaran-delete', [MakCt::class, 'destroy']);

        Route::get('master-satuan', [JenisSatuanCt::class, 'index']);
        Route::post('master-satuan-edit', [JenisSatuanCt::class, 'edit']);
        Route::post('master-satuan-store', [JenisSatuanCt::class, 'store']);
        Route::post('master-satuan-delete', [JenisSatuanCt::class, 'destroy']);

        //Route::get('master-kategori', Kategori::class);
        Route::get('master-kategori', [KategoriCt::class, 'index']);
        Route::post('master-kategori-edit', [KategoriCt::class, 'edit']);
        Route::post('master-kategori-store', [KategoriCt::class, 'store']);
        Route::post('master-kategori-delete', [KategoriCt::class, 'destroy']);

        Route::get('master-kategoribarang/{encripted_id}', [BarangCt::class, 'index']);
        Route::post('master-kategoribarang-edit', [BarangCt::class, 'edit']);
        Route::post('master-kategoribarang-store', [BarangCt::class, 'store']);
        Route::post('master-kategoribarang-delete', [BarangCt::class, 'destroy']);

        Route::get('jabatan-penandatanganan-uu', [JabuuCt::class, 'index']);
        Route::post('jabatan-penandatanganan-uu-edit', [JabuuCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-uu-store', [JabuuCt::class, 'store']);

        Route::get('jabatan-penandatanganan-ur', [JaburCt::class, 'index']);
        Route::post('jabatan-penandatanganan-ur-edit', [JaburCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-ur-store', [JaburCt::class, 'store']);

        Route::get('jabatan-penandatanganan-fk', [JabfkCt::class, 'index']);
        Route::post('jabatan-penandatanganan-fk-edit', [JabfkCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-fk-store', [JabfkCt::class, 'store']);

        Route::get('jabatan-penandatanganan-urs', [JabursCt::class, 'index']);
        Route::post('jabatan-penandatanganan-urs-edit', [JabursCt::class, 'edit']);
        Route::post('jabatan-penandatanganan-urs-store', [JabursCt::class, 'store']);

        Route::get('pejabat-penandatanganan-opuu', [JabpenuuCt::class, 'index']);
        Route::post('pejabat-penandatanganan-opuu-edit', [JabpenuuCt::class, 'edit']);
        Route::post('pejabat-penandatanganan-opuu-store', [JabpenuuCt::class, 'store']);
        Route::post('pejabat-penandatanganan-opuu-delete', [JabpenuuCt::class, 'destroy']);


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

    Route::group(['middleware'=> 'role:opfakultas'], function(){

        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){ 
            Route::get('master-barang-opf', [BarangFakultasCt::class, 'index']);

            Route::get('barang-masuk-pesanan-opf-khusus', [BmfpCt::class, 'index']);
            Route::post('barang-masuk-pesanan-opf-khusus-edit', [BmfpCt::class, 'edit']);
            Route::post('barang-masuk-pesanan-opf-khusus-store', [BmfpCt::class, 'store']);
            Route::post('barang-masuk-pesanan-opf-khusus-delete', [BmfpCt::class, 'destroy']);
            Route::post('barang-masuk-pesanan-opf-khusus-validasi', [BmfpCt::class, 'validasi']);
            Route::post('barang-masuk-pesanan-opf-khusus-lap', [BmfpCt::class, 'cek']);
            Route::get('barang-masuk-pesanan-opf-khusus-lap-print/{filter}/{filter2}', [LapBmfsPrintCt::class, 'index']);

            Route::get('barang-masuk-opf-khusus/{encripted_id}', [BarangMasukFakultasKhususCt::class, 'index']);
            Route::post('barang-masuk-opf-khusus-store', [BarangMasukFakultasKhususCt::class, 'store']);
            Route::post('barang-masuk-opf-khusus-delete', [BarangMasukFakultasKhususCt::class, 'destroy']);
            Route::get('barang-masuk-opf-khusus-subkategori', [BarangMasukFakultasKhususCt::class, 'getSubkategori']);
            Route::get('barang-masuk-opf-khusus-item', [BarangMasukFakultasKhususCt::class, 'getItem']);
            Route::post('barang-masuk-opf-khusus-edit', [BarangMasukFakultasKhususCt::class, 'edit']);

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

            
        });     
    });

    Route::group(['middleware'=> 'role:oprektorat'], function(){

        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){ 
            Route::get('master-barang-opr', [BarangRektoratCt::class, 'index']);

            Route::get('barang-keluar-penerima-opr', [BkprCt::class, 'index']);
            Route::post('barang-keluar-penerima-opr-edit', [BkprCt::class, 'edit']);
            Route::post('barang-keluar-penerima-opr-store', [BkprCt::class, 'store']);
            Route::post('barang-keluar-penerima-opr-delete', [BkprCt::class, 'destroy']);

            Route::get('pejabat-penandatanganan-opr', [JabpenurCt::class, 'index']);
            Route::post('pejabat-penandatanganan-opr-edit', [JabpenurCt::class, 'edit']);
            Route::post('pejabat-penandatanganan-opr-store', [JabpenurCt::class, 'store']);
            Route::post('pejabat-penandatanganan-opr-delete', [JabpenurCt::class, 'destroy']);

            Route::get('barang-masuk-pesanan-opr-khusus', [BmrpCt::class, 'index']);
            Route::post('barang-masuk-pesanan-opr-khusus-edit', [BmrpCt::class, 'edit']);
            Route::post('barang-masuk-pesanan-opr-khusus-store', [BmrpCt::class, 'store']);
            Route::post('barang-masuk-pesanan-opr-khusus-delete', [BmrpCt::class, 'destroy']);
            Route::post('barang-masuk-pesanan-opr-khusus-validasi', [BmrpCt::class, 'validasi']);
            Route::post('barang-masuk-pesanan-opr-khusus-lap', [BmrpCt::class, 'cek']);
            Route::get('barang-masuk-pesanan-opr-khusus-lap-print/{filter}/{filter2}', [LapBmrsPrintCt::class, 'index']);

            Route::get('barang-masuk-opr-khusus/{encripted_id}', [BarangMasukRektoratKhususCt::class, 'index']);
            Route::post('barang-masuk-opr-khusus-store', [BarangMasukRektoratKhususCt::class, 'store']);
            Route::post('barang-masuk-opr-khusus-delete', [BarangMasukRektoratKhususCt::class, 'destroy']);
            Route::get('barang-masuk-opr-khusus-subkategori', [BarangMasukRektoratKhususCt::class, 'getSubkategori']);
            Route::get('barang-masuk-opr-khusus-item', [BarangMasukRektoratKhususCt::class, 'getItem']);
            Route::post('barang-masuk-opr-khusus-edit', [BarangMasukRektoratKhususCt::class, 'edit']);

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


            Route::get('opsik-opr', [OpsikUrCt::class, 'index']);
            Route::post('opsik-opr-edit', [OpsikUrCt::class, 'edit']);
            Route::post('opsik-opr-store', [OpsikUrCt::class, 'store']);
            Route::post('opsik-opr-delete', [OpsikUrCt::class, 'destroy']);
            Route::post('opsik-opr-validasi', [OpsikUrCt::class, 'validasi']);
            Route::post('opsik-opr-store-upload-cek', [OpsikUrCt::class, 'storeuploadcek']);
            Route::post('opsik-opr-store-upload', [OpsikUrCt::class, 'storeupload']);
            Route::get('opsik-opr-lampiran/{filter}', [OpsikUrPrintCt::class, 'index']);
            Route::get('opsik-opr-persediaan/{filter}', [OpsikUrPersediaanPrintCt::class, 'index']);

            Route::get('opsik-opr-detail/{encripted_id}', [OpsikUrDetCt::class, 'index']);
            Route::post('opsik-opr-detail-store', [OpsikUrDetCt::class, 'store']);
            Route::post('opsik-opr-detail-delete', [OpsikUrDetCt::class, 'destroy']);
            Route::get('opsik-opr-detail-subkategori', [OpsikUrDetCt::class, 'getSubkategori']);
            Route::get('opsik-opr-detail-item', [OpsikUrDetCt::class, 'getItem']);
            Route::post('opsik-opr-detail-edit', [OpsikUrDetCt::class, 'edit']);
        });
    });    
    
    Route::group(['middleware'=> 'role:oprumahsakit'], function(){
        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){
            Route::get('barang-keluar-penerima-oprs', [BkprsCt::class, 'index']);
            Route::post('barang-keluar-penerima-oprs-edit', [BkprsCt::class, 'edit']);
            Route::post('barang-keluar-penerima-oprs-store', [BkprsCt::class, 'store']);
            Route::post('barang-keluar-penerima-oprs-delete', [BkprsCt::class, 'destroy']);

            Route::get('pejabat-penandatanganan-oprs', [JabpenursCt::class, 'index']);
            Route::post('pejabat-penandatanganan-oprs-edit', [JabpenursCt::class, 'edit']);
            Route::post('pejabat-penandatanganan-oprs-store', [JabpenursCt::class, 'store']);
            Route::post('pejabat-penandatanganan-oprs-delete', [JabpenursCt::class, 'destroy']);

            Route::get('barang-masuk-pesanan-oprs-khusus', [BmrspCt::class, 'index']);
            Route::post('barang-masuk-pesanan-oprs-khusus-edit', [BmrspCt::class, 'edit']);
            Route::post('barang-masuk-pesanan-oprs-khusus-store', [BmrspCt::class, 'store']);
            Route::post('barang-masuk-pesanan-oprs-khusus-delete', [BmrspCt::class, 'destroy']);
            Route::post('barang-masuk-pesanan-oprs-khusus-validasi', [BmrspCt::class, 'validasi']);
            Route::post('barang-masuk-pesanan-oprs-khusus-lap', [BmrspCt::class, 'cek']);
            Route::get('barang-masuk-pesanan-oprs-khusus-lap-print/{filter}/{filter2}', [LapBmrssPrintCt::class, 'index']);

            Route::get('barang-masuk-oprs-khusus/{encripted_id}', [BarangMasukRumahSakitKhususCt::class, 'index']);
            Route::post('barang-masuk-oprs-khusus-store', [BarangMasukRumahSakitKhususCt::class, 'store']);
            Route::post('barang-masuk-oprs-khusus-delete', [BarangMasukRumahSakitKhususCt::class, 'destroy']);
            Route::get('barang-masuk-oprs-khusus-subkategori', [BarangMasukRumahSakitKhususCt::class, 'getSubkategori']);
            Route::get('barang-masuk-oprs-khusus-item', [BarangMasukRumahSakitKhususCt::class, 'getItem']);
            Route::post('barang-masuk-oprs-khusus-edit', [BarangMasukRumahSakitKhususCt::class, 'edit']);

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

            Route::get('opsik-oprs', [OpsikUrsCt::class, 'index']);
            Route::post('opsik-oprs-edit', [OpsikUrsCt::class, 'edit']);
            Route::post('opsik-oprs-store', [OpsikUrsCt::class, 'store']);
            Route::post('opsik-oprs-delete', [OpsikUrsCt::class, 'destroy']);
            Route::post('opsik-oprs-validasi', [OpsikUrsCt::class, 'validasi']);
            Route::post('opsik-oprs-store-upload-cek', [OpsikUrsCt::class, 'storeuploadcek']);
            Route::post('opsik-oprs-store-upload', [OpsikUrsCt::class, 'storeupload']);
            Route::get('opsik-oprs-lampiran/{filter}', [OpsikUrsPrintCt::class, 'index']);
            Route::get('opsik-oprs-persediaan/{filter}', [OpsikUrsPersediaanPrintCt::class, 'index']);

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
    }); 
    
    Route::group(['middleware'=> 'role:kasilogistik'], function(){ 
        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){
            Route::get('permintaan-barang-kasilogistik-pimf', [PbfkaCt::class, 'index']);
            Route::post('permintaan-barang-kasilogistik-pimf-cektolak', [PbfkaCt::class, 'cektolak']);
            Route::post('permintaan-barang-kasilogistik-pimf-store', [PbfkaCt::class, 'store']);
            Route::post('permintaan-barang-kasilogistik-pimf-delete', [PbfkaCt::class, 'destroy']);
            Route::post('permintaan-barang-kasilogistik-pimf-validasi', [PbfkaCt::class, 'validasi']);

            Route::get('permintaan-barang-kasilogistik-pimf-daftar/{encripted_id}', [PbfdkaCt::class, 'index']);
            Route::post('permintaan-barang-kasilogistik-pimf-daftar-store', [PbfdkaCt::class, 'store']);
            Route::post('permintaan-barang-kasilogistik-pimf-daftar-edit', [PbfdkaCt::class, 'edit']);

            Route::get('permintaan-barang-kasilogistik-pimf-log/{encripted_id}', [PbflKaCt::class, 'index']);

            Route::get('permintaan-barang-kasilogistik-pimr', [PbrKaCt::class, 'index']);
            Route::post('permintaan-barang-kasilogistik-pimr-cektolak', [PbrKaCt::class, 'cektolak']);
            Route::post('permintaan-barang-kasilogistik-pimr-store', [PbrKaCt::class, 'store']);
            Route::post('permintaan-barang-kasilogistik-pimr-delete', [PbrKaCt::class, 'destroy']);
            Route::post('permintaan-barang-kasilogistik-pimr-validasi', [PbrKaCt::class, 'validasi']);

            Route::get('permintaan-barang-kasilogistik-pimr-daftar/{encripted_id}', [PbrdkaCt::class, 'index']);
            Route::post('permintaan-barang-kasilogistik-pimr-daftar-store', [PbrdkaCt::class, 'store']);
            Route::post('permintaan-barang-kasilogistik-pimr-daftar-edit', [PbrdkaCt::class, 'edit']);

            Route::get('permintaan-barang-kasilogistik-pimr-log/{encripted_id}', [PbrlKaCt::class, 'index']);

            Route::get('permintaan-barang-kasilogistik-selesaiproses-pimr', [PbrKaSpCt::class, 'index']);
            Route::get('permintaan-barang-kasilogistik-pimr-selesaiproses-daftar/{encripted_id}', [PbrdKaSpCt::class, 'index']);
            Route::get('permintaan-barang-kasilogistik-pimr-selesaiproses-log/{encripted_id}', [PbrlKaSpCt::class, 'index']);

            Route::get('permintaan-barang-kasilogistik-selesaiproses-pimf', [PbfkaSpCt::class, 'index']);
            Route::get('permintaan-barang-kasilogistik-pimf-selesaiproses-daftar/{encripted_id}', [PbfdkaSpCt::class, 'index']);
            Route::get('permintaan-barang-kasilogistik-pimf-selesaiproses-log/{encripted_id}', [PbflKaSpCt::class, 'index']);
        });
    });

    Route::group(['middleware'=> 'role:pimfakultas'], function(){ 
        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){
            Route::get('permintaan-barang-pimf', [PbfCt::class, 'index']);
            Route::post('permintaan-barang-pimf-edit', [PbfCt::class, 'edit']);
            Route::post('permintaan-barang-pimf-store', [PbfCt::class, 'store']);
            Route::post('permintaan-barang-pimf-delete', [PbfCt::class, 'destroy']);
            Route::post('permintaan-barang-pimf-validasi', [PbfCt::class, 'validasi']);

            Route::get('permintaan-barang-pimf-daftar/{encripted_id}', [PbfdCt::class, 'index']);
            Route::post('permintaan-barang-pimf-daftar-store', [PbfdCt::class, 'store']);
            Route::post('permintaan-barang-pimf-daftar-delete', [PbfdCt::class, 'destroy']);
            Route::get('permintaan-barang-pimf-daftar-subkategori', [PbfdCt::class, 'getSubkategori']);
            Route::get('permintaan-barang-pimf-daftar-item', [PbfdCt::class, 'getItem']);
            Route::post('permintaan-barang-pimf-daftar-edit', [PbfdCt::class, 'edit']);

            Route::get('permintaan-barang-pimf-log/{encripted_id}', [PbflCt::class, 'index']);

            Route::get('permintaan-barang-selesaiproses-pimf', [PbfSpCt::class, 'index']);
            Route::get('permintaan-barang-pimf-selesaiproses-daftar/{encripted_id}', [PbfdSpCt::class, 'index']);
            Route::get('permintaan-barang-pimf-selesaiproses-log/{encripted_id}', [PbflSpCt::class, 'index']);

        });
    });

    Route::group(['middleware'=> 'role:pimunitrektorat'], function(){ 
        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){
            Route::get('permintaan-barang-pimr', [PbrCt::class, 'index']);
            Route::post('permintaan-barang-pimr-edit', [PbrCt::class, 'edit']);
            Route::post('permintaan-barang-pimr-store', [PbrCt::class, 'store']);
            Route::post('permintaan-barang-pimr-delete', [PbrCt::class, 'destroy']);
            Route::post('permintaan-barang-pimr-validasi', [PbrCt::class, 'validasi']);

            Route::get('permintaan-barang-pimr-daftar/{encripted_id}', [PbrdCt::class, 'index']);
            Route::post('permintaan-barang-pimr-daftar-store', [PbrdCt::class, 'store']);
            Route::post('permintaan-barang-pimr-daftar-delete', [PbrdCt::class, 'destroy']);
            Route::get('permintaan-barang-pimr-daftar-subkategori', [PbrdCt::class, 'getSubkategori']);
            Route::get('permintaan-barang-pimr-daftar-item', [PbrdCt::class, 'getItem']);
            Route::post('permintaan-barang-pimr-daftar-edit', [PbrdCt::class, 'edit']);

            Route::get('permintaan-barang-pimr-log/{encripted_id}', [PbrlCt::class, 'index']);

            Route::get('permintaan-barang-selesaiproses-pimr', [PbrSpCt::class, 'index']);
            Route::get('permintaan-barang-pimr-selesaiproses-daftar/{encripted_id}', [PbrdSpCt::class, 'index']);
            Route::get('permintaan-barang-pimr-selesaiproses-log/{encripted_id}', [PbrlSpCt::class, 'index']);
        });
    });

    Route::group(['middleware'=> 'role:kepalagudang'], function(){ 
        Route::group(['middleware'=> 'aplikasi:inventaris'], function(){
            Route::get('permintaan-barang-kg-pimf', [PbfKgCt::class, 'index']);
            Route::post('permintaan-barang-kg-pimf-edit', [PbfKgCt::class, 'edit']);
            Route::post('permintaan-barang-kg-pimf-store', [PbfKgCt::class, 'store']);
            Route::post('permintaan-barang-kg-pimf-delete', [PbfKgCt::class, 'destroy']);
            Route::post('permintaan-barang-kg-pimf-validasi', [PbfKgCt::class, 'validasi']);

            Route::get('permintaan-barang-kg-pimf-daftar/{encripted_id}', [PbfdKgCt::class, 'index']);

            Route::get('permintaan-barang-kg-pimf-log/{encripted_id}', [PbflKgCt::class, 'index']);

            Route::get('permintaan-barang-kg-pimr', [PbrKgCt::class, 'index']);
            Route::post('permintaan-barang-kg-pimr-edit', [PbrKgCt::class, 'edit']);
            Route::post('permintaan-barang-kg-pimr-store', [PbrKgCt::class, 'store']);
            Route::post('permintaan-barang-kg-pimr-delete', [PbrKgCt::class, 'destroy']);
            Route::post('permintaan-barang-kg-pimr-validasi', [PbrKgCt::class, 'validasi']);

            Route::get('permintaan-barang-kg-pimr-daftar/{encripted_id}', [PbrdKgCt::class, 'index']);
            Route::get('permintaan-barang-kg-pimr-log/{encripted_id}', [PbrlKgCt::class, 'index']);

            Route::get('permintaan-barang-kg-selesaiproses-pimr', [PbrKgSpCt::class, 'index']);
            Route::get('permintaan-barang-kg-pimr-selesaiproses-daftar/{encripted_id}', [PbrdKgSpCt::class, 'index']);
            Route::get('permintaan-barang-kg-pimr-selesaiproses-log/{encripted_id}', [PbrlKgSpCt::class, 'index']);

            Route::get('permintaan-barang-kg-selesaiproses-pimf', [PbfKgSpCt::class, 'index']);
            Route::get('permintaan-barang-kg-pimf-selesaiproses-daftar/{encripted_id}', [PbfdKgSpCt::class, 'index']);
            Route::get('permintaan-barang-kg-pimf-selesaiproses-log/{encripted_id}', [PbflKgSpCt::class, 'index']);

        });
    });
});