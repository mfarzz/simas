<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukRumahSakitModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rumah_sakit";    
    protected $primaryKey = 'id_bmrs';
}
