<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengadaanBarangHistoryModel extends Model
{
    use HasFactory;
    protected $table = "pengadaan_barang_history";    
    protected $primaryKey = 'id';
}
