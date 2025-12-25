<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarFakultasDetailModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_fakultas_detail";
    protected $primaryKey = 'id_bkfd';
}
