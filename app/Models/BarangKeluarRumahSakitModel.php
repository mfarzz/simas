<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarRumahSakitModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_rumah_sakit";
    protected $primaryKey = 'id_bkrs';
}
