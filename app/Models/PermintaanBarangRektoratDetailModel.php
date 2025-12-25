<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangRektoratDetailModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat_detail";
    protected $fillable = ["id_pbr, id_lki, jmlh_pbrd, jmlh_pbrd_awal, id_rspd, ket_pdrb, status_pbrd, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_pbrd';
}
