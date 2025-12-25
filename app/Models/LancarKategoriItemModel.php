<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LancarKategoriItemModel extends Model
{
    use HasFactory;
    protected $table = "lancar_kategori_item";
    protected $fillable = ["id_lks, kd_lki, nm_lki, barcode_lki, stok_lki, nilai_lki, id_js,  user_id, created_at, updated_at"];
    protected $primaryKey = 'id_lki';
}
