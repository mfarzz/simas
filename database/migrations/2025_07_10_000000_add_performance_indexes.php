<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes for all frequently-queried columns to resolve slow query performance.
     */
    public function up(): void
    {
        // ── barang ────────────────────────────────────────────────────────────
        Schema::table('barang', function (Blueprint $table) {
            $table->index('kd_brg',  'idx_barang_kd_brg');
            $table->index('kd_kt',   'idx_barang_kd_kt');
        });

        // ── temp_barang_masuk ─────────────────────────────────────────────────
        Schema::table('temp_barang_masuk', function (Blueprint $table) {
            $table->index(['user_id', 'jns_tbm'],          'idx_tbm_user_jns');
            $table->index(['user_id', 'jns_tbm', 'kd_lks'],'idx_tbm_user_jns_lks');
        });

        // ── barang_masuk_fakultas ─────────────────────────────────────────────
        Schema::table('barang_masuk_fakultas', function (Blueprint $table) {
            $table->index('kd_brg',           'idx_bmf_kd_brg');
            $table->index('id_fk',            'idx_bmf_id_fk');
            $table->index('tglperolehan_bmf', 'idx_bmf_tgl');
            $table->index('kd_lks',           'idx_bmf_kd_lks');
            $table->index('sisa_bmf',         'idx_bmf_sisa');
        });

        // ── barang_masuk_rektorat ─────────────────────────────────────────────
        Schema::table('barang_masuk_rektorat', function (Blueprint $table) {
            $table->index('kd_brg',           'idx_bmr_kd_brg');
            $table->index('id_ur',            'idx_bmr_id_ur');
            $table->index('tglperolehan_bmr', 'idx_bmr_tgl');
            $table->index('sisa_bmr',         'idx_bmr_sisa');
        });

        // ── barang_masuk_rumah_sakit ──────────────────────────────────────────
        Schema::table('barang_masuk_rumah_sakit', function (Blueprint $table) {
            $table->index('kd_brg',            'idx_bmrs_kd_brg');
            $table->index('id_urs',            'idx_bmrs_id_urs');
            $table->index('tglperolehan_bmrs', 'idx_bmrs_tgl');
            $table->index('sisa_bmrs',         'idx_bmrs_sisa');
        });

        // ── barang_keluar_fakultas ────────────────────────────────────────────
        Schema::table('barang_keluar_fakultas', function (Blueprint $table) {
            $table->index('id_fk',        'idx_bkf_id_fk');
            $table->index('tglambil_bkf', 'idx_bkf_tgl');
            $table->index('kd_brg',       'idx_bkf_kd_brg');
            $table->index('id_bkfn',      'idx_bkf_id_bkfn');
        });

        // ── barang_keluar_rektorat ────────────────────────────────────────────
        Schema::table('barang_keluar_rektorat', function (Blueprint $table) {
            $table->index('id_ur',        'idx_bkr_id_ur');
            $table->index('tglambil_bkr', 'idx_bkr_tgl');
            $table->index('kd_brg',       'idx_bkr_kd_brg');
            $table->index('id_bkrn',      'idx_bkr_id_bkrn');
        });

        // ── barang_keluar_rumah_sakit ─────────────────────────────────────────
        Schema::table('barang_keluar_rumah_sakit', function (Blueprint $table) {
            $table->index('id_urs',        'idx_bkrs_id_urs');
            $table->index('tglambil_bkrs', 'idx_bkrs_tgl');
            $table->index('kd_brg',        'idx_bkrs_kd_brg');
            $table->index('id_bkrsn',      'idx_bkrs_id_bkrsn');
        });

        // ── barang_keluar_fakultas_detail ─────────────────────────────────────
        Schema::table('barang_keluar_fakultas_detail', function (Blueprint $table) {
            $table->index('id_bmf', 'idx_bkfd_id_bmf');
            $table->index('id_bkf', 'idx_bkfd_id_bkf');
        });

        // ── barang_keluar_rektorat_detail ─────────────────────────────────────
        Schema::table('barang_keluar_rektorat_detail', function (Blueprint $table) {
            $table->index('id_bmr', 'idx_bkrd_id_bmr');
            $table->index('id_bkr', 'idx_bkrd_id_bkr');
        });

        // ── barang_keluar_rumah_sakit_detail ──────────────────────────────────
        Schema::table('barang_keluar_rumah_sakit_detail', function (Blueprint $table) {
            $table->index('id_bmrs', 'idx_bkrsd_id_bmrs');
            $table->index('id_bkrs', 'idx_bkrsd_id_bkrs');
        });

        // ── opfik_fakultas_detail_item ────────────────────────────────────────
        Schema::table('opfik_fakultas_detail_item', function (Blueprint $table) {
            $table->index('id_bmf',     'idx_opfkdetitm_id_bmf');
            $table->index('id_opfkdet', 'idx_opfkdetitm_id_opfkdet');
        });

        // ── opfik_rektorat_detail_item ────────────────────────────────────────
        Schema::table('opfik_rektorat_detail_item', function (Blueprint $table) {
            $table->index('id_bmr',    'idx_opurdetitm_id_bmr');
            $table->index('id_opurdet','idx_opurdetitm_id_opurdet');
        });

        // ── opfik_rumah_sakit_detail_item ─────────────────────────────────────
        Schema::table('opfik_rumah_sakit_detail_item', function (Blueprint $table) {
            $table->index('id_bmrs',     'idx_opursdetitm_id_bmrs');
            $table->index('id_opursdet', 'idx_opursdetitm_id_opursdet');
        });

        // ── opsik_fakultas ────────────────────────────────────────────────────
        Schema::table('opsik_fakultas', function (Blueprint $table) {
            $table->index('tgl_opfk', 'idx_opfk_tgl');
        });

        // ── opsik_rektorat ────────────────────────────────────────────────────
        Schema::table('opsik_rektorat', function (Blueprint $table) {
            $table->index('tgl_opur', 'idx_opur_tgl');
        });

        // ── opsik_rumah_sakit ─────────────────────────────────────────────────
        Schema::table('opsik_rumah_sakit', function (Blueprint $table) {
            $table->index('tgl_opurs', 'idx_opurs_tgl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang',                         fn($t) => [$t->dropIndex('idx_barang_kd_brg'),       $t->dropIndex('idx_barang_kd_kt')]);
        Schema::table('temp_barang_masuk',              fn($t) => [$t->dropIndex('idx_tbm_user_jns'),        $t->dropIndex('idx_tbm_user_jns_lks')]);
        Schema::table('barang_masuk_fakultas',          fn($t) => [$t->dropIndex('idx_bmf_kd_brg'),          $t->dropIndex('idx_bmf_id_fk'),          $t->dropIndex('idx_bmf_tgl'),    $t->dropIndex('idx_bmf_kd_lks'), $t->dropIndex('idx_bmf_sisa')]);
        Schema::table('barang_masuk_rektorat',          fn($t) => [$t->dropIndex('idx_bmr_kd_brg'),          $t->dropIndex('idx_bmr_id_ur'),          $t->dropIndex('idx_bmr_tgl'),    $t->dropIndex('idx_bmr_sisa')]);
        Schema::table('barang_masuk_rumah_sakit',       fn($t) => [$t->dropIndex('idx_bmrs_kd_brg'),         $t->dropIndex('idx_bmrs_id_urs'),         $t->dropIndex('idx_bmrs_tgl'),   $t->dropIndex('idx_bmrs_sisa')]);
        Schema::table('barang_keluar_fakultas',         fn($t) => [$t->dropIndex('idx_bkf_id_fk'),           $t->dropIndex('idx_bkf_tgl'),            $t->dropIndex('idx_bkf_kd_brg'), $t->dropIndex('idx_bkf_id_bkfn')]);
        Schema::table('barang_keluar_rektorat',         fn($t) => [$t->dropIndex('idx_bkr_id_ur'),           $t->dropIndex('idx_bkr_tgl'),            $t->dropIndex('idx_bkr_kd_brg'), $t->dropIndex('idx_bkr_id_bkrn')]);
        Schema::table('barang_keluar_rumah_sakit',      fn($t) => [$t->dropIndex('idx_bkrs_id_urs'),         $t->dropIndex('idx_bkrs_tgl'),           $t->dropIndex('idx_bkrs_kd_brg'),$t->dropIndex('idx_bkrs_id_bkrsn')]);
        Schema::table('barang_keluar_fakultas_detail',  fn($t) => [$t->dropIndex('idx_bkfd_id_bmf'),         $t->dropIndex('idx_bkfd_id_bkf')]);
        Schema::table('barang_keluar_rektorat_detail',  fn($t) => [$t->dropIndex('idx_bkrd_id_bmr'),         $t->dropIndex('idx_bkrd_id_bkr')]);
        Schema::table('barang_keluar_rumah_sakit_detail',fn($t)=> [$t->dropIndex('idx_bkrsd_id_bmrs'),       $t->dropIndex('idx_bkrsd_id_bkrs')]);
        Schema::table('opfik_fakultas_detail_item',     fn($t) => [$t->dropIndex('idx_opfkdetitm_id_bmf'),   $t->dropIndex('idx_opfkdetitm_id_opfkdet')]);
        Schema::table('opfik_rektorat_detail_item',     fn($t) => [$t->dropIndex('idx_opurdetitm_id_bmr'),   $t->dropIndex('idx_opurdetitm_id_opurdet')]);
        Schema::table('opfik_rumah_sakit_detail_item',  fn($t) => [$t->dropIndex('idx_opursdetitm_id_bmrs'), $t->dropIndex('idx_opursdetitm_id_opursdet')]);
        Schema::table('opsik_fakultas',                 fn($t) => $t->dropIndex('idx_opfk_tgl'));
        Schema::table('opsik_rektorat',                 fn($t) => $t->dropIndex('idx_opur_tgl'));
        Schema::table('opsik_rumah_sakit',              fn($t) => $t->dropIndex('idx_opurs_tgl'));
    }
};
