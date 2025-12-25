<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolonganModel extends Model
{
    use HasFactory;
    protected $table = "golongan";
    protected $fillable = ["kd_gl, nm_gl, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_gl';
}
