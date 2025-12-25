<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarRumahSakitDetailModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_rumah_sakit_detail";
    protected $primaryKey = 'id_bkrsd';
}
