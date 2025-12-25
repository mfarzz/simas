<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengadaanBarangModel extends Model
{
    use HasFactory;
    protected $table = "pengadaan_barang";    
    protected $primaryKey = 'id';
}
