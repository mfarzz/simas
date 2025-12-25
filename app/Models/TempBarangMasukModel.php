<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempBarangMasukModel extends Model
{
    use HasFactory;
    protected $table = "temp_barang_masuk";
    protected $fillable = ["kd_brg, sisa_tbm, hrg_tbm, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_tbm';
}
