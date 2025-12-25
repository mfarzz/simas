<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkprsModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_penerima_rumah_sakit";
    protected $primaryKey = 'id_bkprs';
}
