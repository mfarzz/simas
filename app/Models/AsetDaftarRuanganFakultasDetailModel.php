<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDaftarRuanganFakultasDetailModel extends Model
{
    use HasFactory;
    protected $table = "aset_daftar_ruangan_fakultas_detail";
    protected $primaryKey = 'a_id_adrfd';
}
