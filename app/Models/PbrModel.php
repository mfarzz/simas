<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbrModel extends Model
{
    use HasFactory;
    protected $table = "permintaan_barang_rektorat";
    protected $primaryKey = 'id_pbr';
}
