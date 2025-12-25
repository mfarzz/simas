<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangUsangDetailModel extends Model
{
    use HasFactory;
    protected $table = "barang_usang_detail";
    protected $primaryKey = 'id';
}
