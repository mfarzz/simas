<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarFakultasModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_fakultas";
    protected $primaryKey = 'id_bkf';
}
