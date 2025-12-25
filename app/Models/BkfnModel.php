<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkfnModel extends Model
{
    use HasFactory;
    protected $table = "barang_keluar_fakultas_nota";
    protected $primaryKey = 'id_bkfn';
}
