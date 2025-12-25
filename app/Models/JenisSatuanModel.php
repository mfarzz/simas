<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSatuanModel extends Model
{
    use HasFactory;
    protected $table = "jenis_satuan";
    protected $primaryKey = 'id_js';
}
