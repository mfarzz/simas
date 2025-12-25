<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangUsangModel extends Model
{
    use HasFactory;
    protected $table = "barang_usang";
    protected $primaryKey = 'id';
}
