<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangRektoratModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat";
    protected $fillable = ["id_rspu, nm_pbr, tgl_pbr, status_pbr, role_id, posisi_pbr, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_pbr';
}
