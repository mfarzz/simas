<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukFakultasDetailModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_fakultas_detail";
    protected $primaryKey = 'id_bmfd';
}
