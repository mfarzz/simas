<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetRuanganFakultasModel extends Model
{
    use HasFactory;
    protected $table = "aset_ruangan_fakultas";
    protected $primaryKey = 'a_id_arf';
}
