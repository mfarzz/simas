<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmfpModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_fakultas_pesanan";
    protected $primaryKey = 'id_bmfp';
}
