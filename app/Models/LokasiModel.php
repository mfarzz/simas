<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiModel extends Model
{
    use HasFactory;
    protected $table = "lokasi";
    protected $fillable = ["kd_lks, no_lks, kdjk_lks, nm_lks, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_lks';
}
