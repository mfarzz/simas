<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarRektoratModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_rektorat";
    protected $primaryKey = 'id_bkr';
}
