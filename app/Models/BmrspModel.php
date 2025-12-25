<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmrspModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rumah_sakit_pesanan";
    protected $primaryKey = 'id_bmrsp';
}
