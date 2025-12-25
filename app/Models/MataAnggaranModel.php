<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataAnggaranModel extends Model
{
    use HasFactory;
    protected $table = "mata_anggaran_kegiatan";
    protected $primaryKey = 'id_mak';
}
