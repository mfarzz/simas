<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LancarKategoriModel extends Model
{
    use HasFactory;
    protected $table = "lancar_kategori";
    protected $fillable = ["kd_lk, nm_lk, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_lk';
}
