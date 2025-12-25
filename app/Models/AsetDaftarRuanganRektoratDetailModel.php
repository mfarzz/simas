<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDaftarRuanganRektoratDetailModel extends Model
{
    use HasFactory;
    protected $table = "aset_daftar_ruangan_rektorat_detail";
    protected $primaryKey = 'a_id_adrrd';
}
