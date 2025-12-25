<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangRektoratHistoryModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat_history";
    protected $fillable = ["id_pbr, id_rspu, ket_pbrh, role_id, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_pbrh';
}
