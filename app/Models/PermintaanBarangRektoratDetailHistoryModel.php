<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangRektoratDetailHistoryModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat_detail_history";
    protected $fillable = ["id_pbrd, jmlh_pbrdh, jmlh_pbrdh_awal, ket_pbrdh, id_rspd, role_id, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_pbrdh';
}
