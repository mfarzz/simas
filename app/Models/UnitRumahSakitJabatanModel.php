<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitRumahSakitJabatanModel extends Model
{
    use HasFactory;
    protected $table = "unit_rumah_sakit_jabatan";
    protected $primaryKey = 'id_ursj';
}
