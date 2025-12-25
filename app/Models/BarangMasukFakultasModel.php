<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukFakultasModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_fakultas";
    protected $primaryKey = 'id_bmf';
}
