<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangModel extends Model
{
    use HasFactory;
    protected $table = "bidang";
    protected $fillable = ["kd_bd, kd_gl, no_bd, nm_bd, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_bd';
}
