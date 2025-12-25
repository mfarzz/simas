<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitRektoratJabatanModel extends Model
{
    use HasFactory;
    protected $table = "unit_rektorat_jabatan";
    protected $fillable = ["id_urj, nm_urj, user_id, created_at, updated_at"];
    protected $primaryKey = 'id_urj';
}
