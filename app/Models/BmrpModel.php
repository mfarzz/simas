<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmrpModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rektorat_pesanan";
    protected $primaryKey = 'id_bmrp';
}
