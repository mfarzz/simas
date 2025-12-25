<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmrssModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rumah_sakit_sp2d";
    protected $primaryKey = 'id_bmrss';
}
