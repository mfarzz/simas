<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarDetailModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_detail";
    protected $primaryKey = 'id';
}
