<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkrsnModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_rumah_sakit_nota";
    protected $primaryKey = 'id_bkrsn';
}
