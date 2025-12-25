<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengadaanBarangDetailModel extends Model
{
    use HasFactory;
    protected $table = "pengadaan_barang_detail";    
    protected $primaryKey = 'id';
}
