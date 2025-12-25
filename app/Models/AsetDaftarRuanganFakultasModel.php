<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDaftarRuanganFakultasModel extends Model
{
    use HasFactory;
    protected $table = "aset_daftar_ruangan_fakultas";
    protected $primaryKey = 'a_id_adrf';
}
