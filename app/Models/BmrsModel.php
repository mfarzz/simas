<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmrsModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rektorat_sp2d";
    protected $primaryKey = 'id_bmrs';
}
