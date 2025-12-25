<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukModel extends Model
{
    use HasFactory;
    protected $table = "barang_masuk";
    //protected $fillable = ["jmlh_awal_bm, created_at, updated_at"];
    protected $primaryKey = 'id';
}
