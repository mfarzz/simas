<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefPosisiKegiatanModel extends Model
{
    use HasFactory;
    protected $table = "ref_posisi_kegiatan";    
    protected $primaryKey = 'id';
}
