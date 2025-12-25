<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmfsModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_fakultas_sp2d";
    protected $primaryKey = 'id_bmfs';
}
