<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukRektoratModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk_rektorat";    
    protected $primaryKey = 'id_bmr';
}
