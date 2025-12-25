<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LancarKategoriSubModel extends Model
{
    use HasFactory;
    protected $table = "lancar_kategori_sub";
    protected $fillable = ["id_lk, kd_lks, nm_lks, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_lks';
}
