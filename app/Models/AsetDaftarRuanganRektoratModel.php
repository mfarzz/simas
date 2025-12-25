<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDaftarRuanganRektoratModel extends Model
{
    use HasFactory;
    protected $table = "aset_daftar_ruangan_rektorat";
    protected $primaryKey = 'a_id_adrr';
}
