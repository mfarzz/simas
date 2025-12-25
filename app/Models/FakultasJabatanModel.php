<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakultasJabatanModel extends Model
{
    use HasFactory;
    protected $table = "fakultas_jabatan";
    protected $primaryKey = 'id_fkj';
}
